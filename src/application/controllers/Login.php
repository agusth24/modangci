<?php
defined('BASEPATH') or exit('No direct script access allowed');

class login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('logged_in') == true) //cek user logged
			redirect('home', 'refresh');
	}

	public function index()
	{
		$this->load->view('layouts/login');
	}
}
