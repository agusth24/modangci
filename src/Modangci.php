<?php

namespace Modangci;

use Modangci\Commands as Commands;

class Modangci
{
    public $ci = null;
    public function __construct($argc, $argv, $ci)
    {
        $ci->load->database();
        $ci->load->helper('file');
        if (!$ci->input->is_cli_request()) {
            echo "This is command line interface tool.";
            exit();
            return false;
        }

        $allow_param = ['-r', '--resource'];
        $resource = [];

        $argv = array_map('strtolower', $argv);;
        foreach ($argv as $key => $value) {
            if (!preg_match("/^[a-zA-Z_]+$/", $value)) {
                if (!in_array($value, $allow_param)) {
                    $this->_message($value . " Not Allowed Parameter!!");
                    exit();
                } else {
                    $resource[] = $argv[$key];
                    unset($argv[$key]);
                }
            }
        }

        array_shift($argv);
        $className = '\Modangci\Commands\\' . ucfirst(strtolower(array_shift($argv)));
        $funcName = array_shift($argv);

        $this->_message("Calling $className::$funcName...");
        $this->call_function($ci, $className, $funcName, $resource, $argv);
    }

    public function call_function($ci, $class, $method, $resource, $argv)
    {
        if (method_exists($class, $method)) {

            $class_call = new $class($ci, $resource);
            return call_user_func_array([$class_call, $method], $argv);
        } else {
            $commands = new Commands(null, null);
            $commands->index();
        }
    }

    protected function _message($msg)
    {
        echo $msg . "\n";
    }
}
