<?php

namespace Modangci\Commands;

use Modangci\Commands as Commands;

class Import extends Commands
{
    function __construct($ci, $resource)
    {
        parent::__construct($ci, $resource);
    }

    public function model($name = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));
            $this->_command = 'Model';

            self::copy('vendor/agusth24/modangci/src/application/core/MY_Model.php', 'application/core/MY_Model.php');
            self::copy('vendor/agusth24/modangci/src/application/core/Model_' . $this->_name . '.php', 'application/core/Model_' . $this->_name . '.php');
        } else
            $this->index();
    }

    public function helper($name = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  strtolower($name);
            $this->_command = 'Helper';

            self::copy('vendor/agusth24/modangci/src/application/helpers/' . $this->_name . '_helper.php', 'application/helpers/' . $this->_name . '_helper.php');
        } else
            $this->index();
    }

    public function libraries($name = null)
    {
        $require_composer = ['Pdfgenerator' => 'require dompdf/dompdf'];

        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));
            $this->_command = 'Libraries';

            if (array_key_exists($this->_name, $require_composer))
                $output = shell_exec('composer ' . $require_composer[$this->_name]);

            self::copy('vendor/agusth24/modangci/src/application/libraries/' . $this->_name . '.php', 'application/libraries/' . $this->_name . '.php');
        } else
            $this->index();
    }
}
