<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Encryptions {
	var $skey 	= "SuPer_Enc-Key2010"; // you can change it

    public function safe_b64encode($string) {
	
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
 
	public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
	
    public function encode($value,$key=""){ 
        $CI = get_instance();
        $CI->load->library('encryption');
		if(empty($key)) $key = $this->skey;
        $CI->encryption->initialize(
                array(
                        'cipher' => 'aes-256',
                        'mode' => 'cbc',
                        'key' => $key
                )
        );
	    if(!$value){return false;}
        $text = $value;
        $crypttext = $CI->encryption->encrypt($text);
        return trim($this->safe_b64encode($crypttext)); 
    }
    
    public function decode($value,$key=""){
        $CI = get_instance();
        $CI->load->library('encryption');
		if(empty($key)) $key = $this->skey;
        $CI->encryption->initialize(
                array(
                        'cipher' => 'aes-256',
                        'mode' => 'cbc',
                        'key' => $key
                )
        );
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $decrypttext = $CI->encryption->decrypt($crypttext);
        return trim($decrypttext);
    }
}
?>
