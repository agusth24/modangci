<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class unit extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_template = 'layouts/template';
        $this->_path_page = 'pages/unit/';
        $this->_path_js = null;
        $this->_judul = 'Unit Kerja';
        $this->_controller_name = 'unit';
        $this->_model_name = 'model_unit';
        $this->_page_index = 'index';

        $this->load->model($this->_model_name, '', TRUE);
    }

    public function index()
    {
        $data = $this->get_master($this->_path_page . $this->_page_index);
        $data['scripts'] = [];
        $data['datas'] = $this->{$this->_model_name}->get_ref_table('s_unit');
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
        $key = ['unitId' => $keyS];
        $data['datas'] = $this->{$this->_model_name}->get_by_id('s_unit', $key);

        $this->load->view($this->_template, $data);
    }

    public function save()
    {
        $unitIdOld = $this->input->post('unitIdOld');
        if(empty($unitIdOld))
           $this->form_validation->set_rules('unitKode', 'Kode Unit', 'trim|xss_clean|required|is_unique[s_unit.unitKode]');
        else
            $this->form_validation->set_rules('unitKode', 'Kode Unit', 'trim|xss_clean|required');
        $this->form_validation->set_rules('unitNama', 'Nama UNit', 'trim|xss_clean|required');

        if ($this->form_validation->run()) {
            if (IS_AJAX) {
                $unitKode = $this->input->post('unitKode');
                $unitNama = $this->input->post('unitNama');


                $param = array(
                    'unitKode' => $unitKode,
                    'unitNama' => $unitNama,

                );

                if (empty($unitIdOld)) {
                    $proses = $this->{$this->_model_name}->insert('s_unit', $param);
                } else {
                    $key = array('unitId' => $unitIdOld);
                    $proses = $this->{$this->_model_name}->update('s_unit', $param, $key);
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
        $key = ['unitId' => $keyS];
        $proses = $this->{$this->_model_name}->delete('s_unit', $key);
        if ($proses)
            message($this->_judul . ' Berhasil Dihapus', 'success');
        else {
            $error = $this->db->error();
            message($this->_judul . ' Gagal Dihapus, ' . $error['code'] . ': ' . $error['message'], 'error');
        }
    }
}
