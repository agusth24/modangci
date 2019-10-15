<?php
defined('BASEPATH') or exit('No direct script access allowed');

class home extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->_template = 'layouts/template';
		$this->_path_page = "pages/home/";;
		$this->_path_js = null;
		$this->_judul = 'Dashboard';
		$this->_controller_name = 'home';
		$this->_model_name = 'model_home';
		$this->_page_index = 'index';

		$this->load->model($this->_model_name, '', TRUE);
	}

	public function index()
	{
		$data = $this->get_master($this->_path_page . $this->_page_index);
		$data['scripts'] = [];
		$this->load->view($this->_template, $data);
	}

	function logout()
	{
		$this->session->unset_userdata('logged_in');
		session_destroy();
		redirect('home', 'refresh');
	}

	function ubahpass()
	{
		$data = $this->get_master($this->_path_page . 'ubahpassword');
		$data['save_url'] = site_url($this->_controller_name . '/prosesubahpassword');
		$data['judul'] = 'Ubah Password';
		$data['scripts'] = [];
		$this->load->view($this->_template, $data);
	}

	function ubahhakakses()
	{
		$session_data = $this->session->userdata('logged_in');
		if ($session_data['susrSgroupNama_ori'] == 'ADMIN') {
			$data = $this->get_master($this->_path_page . 'ubahhakakses');
			$data['save_url'] = site_url($this->_controller_name . '/prosesubahhakakses');
			$data['judul'] = 'Ubah Hak Akses';
			$data['hakakses'] = $this->{$this->_model_name}->get_ref_table('s_user_group');
			$data['scripts'] = [];
			$this->load->view($this->_template, $data);
		} else
			redirect('login', 'refresh');
	}

	function prosesubahpassword()
	{
		$session_data = $this->session->userdata('logged_in');

		$this->form_validation->set_rules('susrPasswordOld', 'Old Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('susrPasswordNew', 'New Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('susrPasswordNewConfirm', 'Confirmation New Password', 'trim|required|xss_clean');

		if ($this->form_validation->run()) {
			$susrPasswordOld = $this->input->post('susrPasswordOld');
			$susrPasswordNew = $this->input->post('susrPasswordNew');
			$susrPasswordNewConfirm = $this->input->post('susrPasswordNewConfirm');

			$susrNama = $session_data['susrNama'];
			$susrSgroupNama = $session_data['susrSgroupNama'];
			$susrProfil = $session_data['susrProfil'];

			$key = array('susrNama' => $susrNama);
			$cek_user = $this->{$this->_model_name}->get_by_id('s_user', $key);

			if ($cek_user != false) {
				if (password_verify($susrPasswordOld, $cek_user->susrPassword)) {
					if ($susrPasswordNew == $susrPasswordNewConfirm) {
						$susrPasswordNew = password_hash($susrPasswordNew, PASSWORD_DEFAULT);
						$param = array('susrPassword' => $susrPasswordNew);
						$ubahPass = $this->{$this->_model_name}->update('s_user', $param, $key);
						if ($ubahPass)
							message('Password Berhasil Diubah', 'success');
						else
							message('Password Gagal diubah', 'error');
					} else
						message('Password Baru Tidak Sama Dengan Konfirmasi', 'error');
				} else
					message('Username dan Password Lama Salah', 'error');
			} else
				message('Username/Password Lama Salah', 'error');
		} else
			message('Ooops!! Something Wrong!!' . validation_errors(), 'error');
	}

	function prosesubahhakakses()
	{
		$session_data = $this->session->userdata('logged_in');

		$this->form_validation->set_rules('hakakses', 'Role', 'trim|required|xss_clean');

		if ($this->form_validation->run()) {
			if ($session_data['susrSgroupNama_ori'] == 'ADMIN') {
				$hakakses_new = $this->input->post('hakakses');
				$sess_array = array(
					'susrNama' => $session_data['susrNama'],
					'susrSgroupNama' => $hakakses_new,
					'susrSgroupNama_ori' => $session_data['susrSgroupNama_ori'],
					'susrProfil' => $session_data['susrProfil']
				);
				$this->session->set_userdata('logged_in', $sess_array);
				message('Hak Akses Berhasil Diubah', 'success');
			} else
				redirect('login', 'refresh');
		} else
			message('Ooops!! Something Wrong!!' . validation_errors(), 'error');
	}
}
