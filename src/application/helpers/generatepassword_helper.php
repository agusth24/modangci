<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('generatepassword'))
{
	function generatepassword($length = 6) {
		$password = "";
		$possible = "1234567890";
		$maxlength = strlen($possible);
	
		if ($length > $maxlength) {
		  $length = $maxlength;
		}
		
		$i = 0;     
		while ($i < $length) { 
		  $char = substr($possible, mt_rand(0, $maxlength-1), 1);
		  if (!strstr($password, $char)) { 
			$password .= $char;
			$i++;
		  }
		}
		return $password;
	}
}
?>