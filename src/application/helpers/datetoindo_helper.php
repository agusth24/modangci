<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('datetoindo'))
{
	function datetoindo($date){
		if($date!='0000-00-00') {
			$BulanIndo = array("Januari", "Februari", "Maret",
							   "April", "Mei", "Juni",
							   "Juli", "Agustus", "September",
							   "Oktober", "Nopember", "Desember");
		
			$tahun = substr($date, 0, 4);
			$bulan = substr($date, 5, 2);
			$tgl   = substr($date, 8, 2);
			
			$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;		
			return($result);
		} else
			return(false);
	}
}
?>