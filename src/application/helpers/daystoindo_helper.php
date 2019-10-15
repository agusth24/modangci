<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('daystoindo'))
{
	function daystoindo($days){
		if(!empty($days)) {
			$hari = array(
			"1"=>"Senin",
			"2"=>"Selasa",
			"3"=>"Rabu",
			"4"=>"Kamis",
			"5"=>"Jumat",
			"6"=>"Sabtu",
			"7"=>"Minggu"
			);
		
			return($hari[$days]);
		} else
			return(false);
	}
}
?>