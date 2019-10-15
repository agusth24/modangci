<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('message'))
{
	function message($msg="",$tipe="")
	{
		$CI = get_instance();
		$result['status'] = $tipe;
        $result['message'] = $msg;
        $result['response'] = '<div class="alert alert-'.($tipe=='error'?'danger':$tipe).' alert-dismissible fade show" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
									'.strtoupper($msg).'
								</div>';
		$CI->output->set_content_type('application/json');
	    $CI->output->set_output(json_encode($result));
	    echo $CI->output->get_output();
	    exit();
	}
}

?>