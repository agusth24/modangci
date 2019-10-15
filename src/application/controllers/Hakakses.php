<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class hakakses extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_template = 'layouts/template';
        $this->_path_page = 'pages/hakakses/';
        $this->_path_js = null;
        $this->_judul = 'Hak Akses';
        $this->_controller_name = 'hakakses';
        $this->_model_name = 'model_hakakses';
        $this->_page_index = 'index';

        $this->load->model($this->_model_name, '', TRUE);
    }

    public function index()
    {
        $data = $this->get_master($this->_path_page . $this->_page_index);
        $data['scripts'] = [];
        $data['datas'] = $this->{$this->_model_name}->get_ref_table('s_user_group');
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
        $this->load->view($this->_template, $data);
    }

    public function update()
    {
        $data = $this->get_master($this->_path_page . 'form');
        $keyS = $this->encryptions->decode($this->uri->segment(3), $this->config->item('encryption_key'));
        $data['scripts'] = [];
        $data['save_url'] = site_url($this->_controller_name . '/save') . '/';
        $data['status_page'] = 'Update';
        $key = ['sgroupNama' => $keyS];
        $data['datas'] = $this->{$this->_model_name}->get_by_id('s_user_group', $key);
        $this->load->view($this->_template, $data);
    }

    public function save()
    {
        $sgroupNamaOld = $this->input->post('sgroupNamaOld');
        if(empty($sgroupNamaOld))
            $this->form_validation->set_rules('sgroupNama', 'Hak Akses', 'trim|xss_clean|required|is_unique[s_user_group.sgroupNama]');
        else
            $this->form_validation->set_rules('sgroupNama', 'Hak Akses', 'trim|xss_clean|required');
            
        $this->form_validation->set_rules('sgroupKeterangan', 'Keterangan', 'trim|xss_clean|required');

        if ($this->form_validation->run()) {
            if (IS_AJAX) {
                $sgroupNama = $this->input->post('sgroupNama');
                $sgroupKeterangan = $this->input->post('sgroupKeterangan');


                $param = array(
                    'sgroupNama' => $sgroupNama,
                    'sgroupKeterangan' => $sgroupKeterangan,

                );

                if (empty($sgroupNamaOld)) {
                    $proses = $this->{$this->_model_name}->insert('s_user_group', $param);
                } else {
                    $key = array('sgroupNama' => $sgroupNamaOld);
                    $proses = $this->{$this->_model_name}->update('s_user_group', $param, $key);
                }

                if ($proses)
                    message($this->_judul . ' Berhasil Disimpan', 'success');
                else {
                    $error = $this->db->error();
                    message($this->_judul . ' Gagal Disimpan, ' . $error['code'] . ': ' . $error['message'], 'error');
                }
            }
        } else {
            message('Ooops!! Something Wrong!!' . validation_errors(), 'error');
        }
    }

    public function delete()
    {
        $keyS = $this->encryptions->decode($this->uri->segment(3), $this->config->item('encryption_key'));
        $key = ['sgroupNama' => $keyS];
        $proses = $this->{$this->_model_name}->delete('s_user_group', $key);
        if ($proses)
            message($this->_judul . ' Berhasil Dihapus', 'success');
        else {
            $error = $this->db->error();
            message($this->_judul . ' Gagal Dihapus, ' . $error['code'] . ': ' . $error['message'], 'error');
        }
    }
}
