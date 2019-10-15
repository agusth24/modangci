<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class modulgroup extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_template = 'layouts/template';
        $this->_path_page = 'pages/modulgroup/';
        $this->_path_js = null;
        $this->_judul = 'Modul Group';
        $this->_controller_name = 'modulgroup';
        $this->_model_name = 'model_modulgroup';
        $this->_page_index = 'index';

        $this->load->model($this->_model_name, '', TRUE);
    }

    public function index()
    {
        $data = $this->get_master($this->_path_page . $this->_page_index);
        $data['scripts'] = [];
        $data['datas'] = $this->{$this->_model_name}->get_ref_table('s_user_modul_group_ref');
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
        $key = ['susrmdgroupNama' => $keyS];
        $data['datas'] = $this->{$this->_model_name}->get_by_id('s_user_modul_group_ref', $key);

        $this->load->view($this->_template, $data);
    }

    public function save()
    {
        $susrmdgroupNamaOld = $this->input->post('susrmdgroupNamaOld');
        if(empty($susrmdgroupNamaOld))
            $this->form_validation->set_rules('susrmdgroupNama', 'Modul Group', 'trim|xss_clean|required|is_unique[s_user_modul_group_ref.susrmdgroupNama]');
        else
            $this->form_validation->set_rules('susrmdgroupNama', 'Modul Group', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrmdgroupDisplay', 'Group Display', 'trim|xss_clean|required');
        $this->form_validation->set_rules('susrmdgroupIcon', 'Group Icons', 'trim|xss_clean|required');

        if ($this->form_validation->run()) {
            if (IS_AJAX) {
                $susrmdgroupNama = $this->input->post('susrmdgroupNama');
                $susrmdgroupDisplay = $this->input->post('susrmdgroupDisplay');
                $susrmdgroupIcon = $this->input->post('susrmdgroupIcon');


                $param = array(
                    'susrmdgroupNama' => $susrmdgroupNama,
                    'susrmdgroupDisplay' => $susrmdgroupDisplay,
                    'susrmdgroupIcon' => $susrmdgroupIcon,

                );

                if (empty($susrmdgroupNamaOld)) {
                    $proses = $this->{$this->_model_name}->insert('s_user_modul_group_ref', $param);
                } else {
                    $key = array('susrmdgroupNama' => $susrmdgroupNamaOld);
                    $proses = $this->{$this->_model_name}->update('s_user_modul_group_ref', $param, $key);
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
        $key = ['susrmdgroupNama' => $keyS];
        $proses = $this->{$this->_model_name}->delete('s_user_modul_group_ref', $key);
        if ($proses)
            message($this->_judul . ' Berhasil Dihapus', 'success');
        else {
            $error = $this->db->error();
            message($this->_judul . ' Gagal Dihapus, ' . $error['code'] . ': ' . $error['message'], 'error');
        }
    }
}
