<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class hakaksesunit extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_template = 'layouts/template';
        $this->_path_page = 'pages/hakaksesunit/';
        $this->_path_js = null;
        $this->_judul = 'Hak Akses Unit';
        $this->_controller_name = 'hakaksesunit';
        $this->_model_name = 'model_hakaksesunit';
        $this->_page_index = 'index';

        $this->load->model($this->_model_name, '', TRUE);
    }

    public function index()
    {
        $data = $this->get_master($this->_path_page . $this->_page_index);
        $data['scripts'] = ['user/' . $this->_controller_name];
        $data['s_user_group'] = $this->{$this->_model_name}->get_ref_table('s_user_group');
        $data['show_url'] = site_url($this->_controller_name . '/response') . '/';
        $this->load->view($this->_template, $data);
    }

    public function response()
    {
        $this->form_validation->set_rules('hakakses', 'hakakses', 'trim|required|xss_clean');

        if ($this->form_validation->run()) {
            if (IS_AJAX) {
                $hakakses = $this->input->post('hakakses');

                $data['datas'] = $this->{$this->_model_name}->by_id($hakakses);
                $data['sgroupNama'] = $hakakses;
                $data['save_url'] = site_url($this->_controller_name . '/save') . '/';
                $pages = $this->_path_page . 'response';
                $this->load->view($pages, $data);
            }
        } else {
            message('Ooops!! Something Wrong!!', 'error');
        }
    }

    public function save()
    {
        $this->form_validation->set_rules('cekModul[]','Modul','trim|required|xss_clean');
		$this->form_validation->set_rules('sgroupNama','Hak Akses','trim|required|xss_clean');
		if($this->form_validation->run()) 
		{	
			if(IS_AJAX)
	        {
	        	$cekModul = $this->input->post('cekModul');	
	        	$sgroupNama = $this->input->post('sgroupNama');	

	        	if(count($cekModul)>0)
		        {
		        	$this->{$this->_model_name}->delete('s_user_group_unit',array('sgroupunitSgroupNama'=>$sgroupNama));

		        	foreach ($cekModul as $cek => $modul) {		
		        		$param = array(
		        			'sgroupunitSgroupNama'=>$sgroupNama,
		        			'sgroupunitUnitId'=>$modul,
		        			'sgroupunitUnitRead'=>1,
		        		);	 
						$this->{$this->_model_name}->insert('s_user_group_unit',$param);
				  	}

	        		message($this->_judul.' Berhasil Disimpan','success');
	        	} else
	        		message('Pilih Unit!! Minimal 1','error');
	        }
        } else {
            message('Ooops!! Something Wrong!! ' . validation_errors(), 'error');
        }
    }
}
