<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

use Dompdf\Dompdf;
class PdfGenerator
{
  public function generate($html,$filename,$orientation,$size='F4')
  { 
  	ini_set('max_execution_time', 0);
  	ini_set('memory_limit','256M');
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper($size, $orientation);
    $dompdf->render();
    $dompdf->stream($filename.'.pdf',array("Attachment"=>0));	
  }

}