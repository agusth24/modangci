<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class pengguna extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_template = 'layouts/template';
        $this->_path_page = 'pages/pengguna/';
        $this->_path_js = 'user/';
        $this->_judul = 'Pengguna';
        $this->_controller_name = 'pengguna';
        $this->_model_name = 'model_pengguna';
        $this->_page_index = 'index';

        $this->load->model($this->_model_name, '', TRUE);
    }

    public function index()
    {
        $data = $this->get_master($this->_path_page . $this->_page_index);
        $data['scripts'] = [($this->_path_js . $this->_controller_name)];
        $data['datas'] = $this->{$this->_model_name}->all();
        $data['create_url'] = site_url($this->_controller_name . '/create') . '/';
        $data['update_url'] = site_url($this->_controller_name . '/update') . '/';
        $data['delete_url'] = site_url($this->_controller_name . '/delete') . '/';
        $data['resetpassword_url'] = site_url($this->_controller_name . '/resetpassword') . '/';
        $this->load->view($this->_template, $data);
    }

    public function create()
    {
        $data = $this->get_master($this->_path_page . 'form');
        $data['scripts'] = [($this->_path_js . $this->_controller_name)];
        $data['save_url'] = site_url($this->_controller_name . '/save') . '/';
        $data['status_page'] = 'Create';
        $data['datas'] = false;
        $data['s_user_group'] = $this->{$this->_model_name}->get_ref_table('s_user_group');

        $this->load->view($this->_template, $data);
    }

    public function update()
    {
        $data = $this->get_master($this->_path_page . 'form');
        $keyS = $this->encryptions->decode($this->uri->segment(3), $this->config->item('encryption_key'));
        $data['scripts'] = [($this->_path_js . $this->_controller_name)];
        $data['save_url'] = site_url($this->_controller_name . '/save') . '/';
        $data['status_page'] = 'Update';
        $key = ['susrNama' => $keyS];
        $data['datas'] = $this->{$this->_model_name}->by_id($key);
        $data['s_user_group'] = $this->{$this->_model_name}->get_ref_table('s_user_group');

        $this->load->view($this->_template, $data);
    }

    public function save()
    {
        $susrNamaOld = $this->input->post('susrNamaOld');
        if(empty($susrNamaOld))
            $this->form_validation->set_rules('susrNama', 'Username', 'trim|xss_clean|required|is_unique[s_user.susrNama]');
        else
            $this->form_validation->set_rules('susrNama', 'Username', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrSgroupNama', 'Hak Akses', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrProfil', 'Profile Name', 'trim|xss_clean|required');

        if ($this->form_validation->run()) {
            if (IS_AJAX) {
                $susrNama = $this->input->post('susrNama');
                $susrSgroupNama = $this->input->post('susrSgroupNama');
                $susrProfil = $this->input->post('susrProfil');


                $param = array(
                    'susrNama' => $susrNama,
                    'susrSgroupNama' => $susrSgroupNama,
                    'susrProfil' => $susrProfil,

                );

                if (empty($susrNamaOld)) {
                    $proses = $this->{$this->_model_name}->insert('s_user', $param);
                } else {
                    $key = array('susrNama' => $susrNamaOld);
                    $proses = $this->{$this->_model_name}->update('s_user', $param, $key);
                }

                if ($proses)
                    message($this->_judul . ' Berhasil Disimpan', 'success');
                else {
                    $error = $this->db->error();
                    message($this->_judul . ' Gagal Disimpan, ' . $error['code'] . ': ' . $error['message'], 'error');
                }
            }
        } else {
            message('Ooops!! Something Wrong!! ' . validation_errors(), 'error');
        }
    }

    public function delete()
    {
        $keyS = $this->encryptions->decode($this->uri->segment(3), $this->config->item('encryption_key'));
        $key = ['susrNama' => $keyS];
        $proses = $this->{$this->_model_name}->delete('s_user', $key);
        if ($proses)
            message($this->_judul . ' Berhasil Dihapus', 'success');
        else {
            $error = $this->db->error();
            message($this->_judul . ' Gagal Dihapus, ' . $error['code'] . ': ' . $error['message'], 'error');
        }
    }

    public function resetpassword()
    {
        if (IS_AJAX) {
            $this->load->helper("generatepassword");
            $key = $this->encryptions->decode($this->uri->segment(3), $this->config->item('encryption_key'));
            $password = generatepassword();

            $param = array(
                'susrPassword' => password_hash($password, PASSWORD_DEFAULT)
            );
            $keys = array('susrNama' => $key);
            $proses = $this->{$this->_model_name}->update('s_user', $param, $keys);

            if ($proses)
                message('Password ' . $this->_judul . ' Berhasil Diubah!! Username: <strong>' . $key . '</strong>, Password: <strong>' . $password . '</strong>', 'success');
            else
                message('Password ' . $this->_judul . ' Gagal Diubah!!', 'error');
        }
    }
}
