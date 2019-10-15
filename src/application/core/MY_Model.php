<?php

class MY_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

}

if(file_exists(APPPATH.'/core/Model_Master.php'))
{
	require APPPATH.'/core/Model_Master.php';
}
	




