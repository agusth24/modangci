<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('debuglog'))
{
    function debuglog($o,$d='') {
        // ini adalah fungsi untuk menulis log setiap request dan response ke dalam sebuah file.
        // Jika ada waktu, silahkan buat log nya ke dalam database.
        $CI = get_instance();
        $session_data = $CI->session->userdata('logged_in');
        //$file_debug =  'C:/xampp/htdocs/simkinerjav2/assets/log' . date("Y-m-d") . '.log';
        //$file_debug =  '/var/log/simkinerja/debug-' . date("Y-m-d") . '.log';
        ob_start();
        var_dump(date("Y-m-d h:i:s"));
        var_dump($session_data['susrNama']);
        var_dump($CI->input->ip_address());
        var_dump($o);
        if(!empty($d))
            var_dump(json_encode($d));
        $c = ob_get_contents();
        ob_end_clean();

        if(isset($file_debug))
        {
            $f = fopen($file_debug, "a");
            fputs($f, "$c\n");
            fflush($f);
            fclose($f);
        }
    }
}

/* End of file log_helper.php */
/* Location: ./application/helpers/log_helper.php */