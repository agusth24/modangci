<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class modul extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_template = 'layouts/template';
        $this->_path_page = 'pages/modul/';
        $this->_path_js = null;
        $this->_judul = 'Modul';
        $this->_controller_name = 'modul';
        $this->_model_name = 'model_modul';
        $this->_page_index = 'index';

        $this->load->model($this->_model_name, '', TRUE);
    }

    public function index()
    {
        $data = $this->get_master($this->_path_page . $this->_page_index);
        $data['scripts'] = [];
        $data['datas'] = $this->{$this->_model_name}->all();
        $data['create_url'] = site_url($this->_controller_name . '/create') . '/';
        $data['update_url'] = site_url($this->_controller_name . '/update') . '/';
        $data['delete_url'] = site_url($this->_controller_name . '/delete') . '/';
        $this->load->view($this->_template, $data);
    }

    public function create()
    {
        $data = $this->get_master($this->_path_page . 'form');
        $data['scripts'] = [];
        $data['save_url'] = site_url($this->_controller_name . '/save') . '/';
        $data['status_page'] = 'Create';
        $data['datas'] = false;
        $data['s_user_modul_group_ref'] = $this->{$this->_model_name}->get_ref_table('s_user_modul_group_ref');

        $this->load->view($this->_template, $data);
    }

    public function update()
    {
        $data = $this->get_master($this->_path_page . 'form');
        $keyS = $this->encryptions->decode($this->uri->segment(3), $this->config->item('encryption_key'));
        $data['scripts'] = [];
        $data['save_url'] = site_url($this->_controller_name . '/save') . '/';
        $data['status_page'] = 'Update';
        $key = ['susrmodulNama' => $keyS];
        $data['datas'] = $this->{$this->_model_name}->by_id($key);
        $data['s_user_modul_group_ref'] = $this->{$this->_model_name}->get_ref_table('s_user_modul_group_ref');

        $this->load->view($this->_template, $data);
    }

    public function save()
    {        
        $susrmodulNamaOld = $this->input->post('susrmodulNamaOld');
        if(empty($susrmodulNamaOld))
            $this->form_validation->set_rules('susrmodulNama', 'Modul', 'trim|xss_clean|required|is_unique[s_user_modul_ref.susrmodulNama]');
        else
            $this->form_validation->set_rules('susrmodulNama', 'Modul', 'trim|xss_clean|required');

        $this->form_validation->set_rules('susrmodulSusrmdgroupNama', 'Modul Group', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrmodulNamaDisplay', 'Modul Display', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrmodulIsLogin', 'Is Login ?', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrmodulUrut', 'Modul Urut', 'trim|xss_clean|required');

        if ($this->form_validation->run()) {
            if (IS_AJAX) {
                $susrmodulNama = $this->input->post('susrmodulNama');
                $susrmodulSusrmdgroupNama = $this->input->post('susrmodulSusrmdgroupNama');
                $susrmodulNamaDisplay = $this->input->post('susrmodulNamaDisplay');
                $susrmodulIsLogin = $this->input->post('susrmodulIsLogin');
                $susrmodulUrut = $this->input->post('susrmodulUrut');


                $param = array(
                    'susrmodulNama' => $susrmodulNama,
                    'susrmodulSusrmdgroupNama' => $susrmodulSusrmdgroupNama,
                    'susrmodulNamaDisplay' => $susrmodulNamaDisplay,
                    'susrmodulIsLogin' => $susrmodulIsLogin,
                    'susrmodulUrut' => $susrmodulUrut,

                );

                if (empty($susrmodulNamaOld)) {
                    $proses = $this->{$this->_model_name}->insert('s_user_modul_ref', $param);
                } else {
                    $key = array('susrmodulNama' => $susrmodulNamaOld);
                    $proses = $this->{$this->_model_name}->update('s_user_modul_ref', $param, $key);
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
        $key = ['susrmodulNama' => $keyS];
        $proses = $this->{$this->_model_name}->delete('s_user_modul_ref', $key);
        if ($proses)
            message($this->_judul . ' Berhasil Dihapus', 'success');
        else {
            $error = $this->db->error();
            message($this->_judul . ' Gagal Dihapus, ' . $error['code'] . ': ' . $error['message'], 'error');
        }
    }
}
