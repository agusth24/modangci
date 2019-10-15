<?php

namespace Modangci;

use Modangci\Modangci as Modangci;

class Commands
{
    protected $_ci = null;
    protected $_resource = null;
    protected $_command = null;
    protected $_name = null;

    public function __construct($ci, $resource)
    {
        $this->_ci = $ci;
        $this->_resource = $resource;
    }

    protected static function copy($src, $dst)
    {
        if (file_exists($src)) {
            $success = copy($src, $dst);
            if ($success) {
                echo 'copied: ' . $dst . PHP_EOL;
            }
        } else
            echo 'file not found: ' . $src . PHP_EOL;
    }

    /**
     * Recursive Copy
     *
     * @param string $src
     * @param string $dst
     */
    protected static function recursiveCopy($src, $dst)
    {
        @mkdir($dst, 0755);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @mkdir($dst . '/' . $iterator->getSubPathName());
            } else {
                $success = copy($file, $dst . '/' . $iterator->getSubPathName());
                // if ($success) {
                //     echo 'copied: ' . $dst . '/' . $iterator->getSubPathName() . PHP_EOL;
                // }
            }
        }
        echo 'copied: ' . $dst . PHP_EOL;
    }

    protected function _create_folder($path = null)
    {
        // Check folder is exist.
        if (is_dir(APPPATH . $path)) {
            $this->_message("This " . $this->_command . ": " . $this->_name . " folder " . $path . " already exists.");
            return false;
        } else {
            $mkdir = mkdir(APPPATH . $path, 0755, TRUE);
            // If unable to write folder in path
            if (!$mkdir) {
                $this->_message("Unable to write the folder " . $path . " " . $this->_command . ": " . $this->_name . ".");
                return false;
            } else
                return true;
        }
    }

    protected function _create_file($file = null, $path = null)
    {
        // Check file is exist.
        if (file_exists(APPPATH . $path . '.php')) {
            $this->_message("This " . $this->_command . ": " . $this->_name . " file already exists.");
            return false;
        } else {
            $write = write_file(APPPATH . $path . '.php', $file);
            // If unable to write file in path
            if (!$write) {
                $this->_message("Unable to write the file " . $this->_command . ": " . $this->_name . ".");
                return false;
            } else
                echo $this->_message($this->_command . ": " . $this->_name . " was created!!");
            return true;
        }
    }

    protected function _message($msg)
    {
        echo $msg . "\n";
    }

    public function index()
    {
        echo "List of Command Make:" . PHP_EOL;
        echo "- Make controller: make controller name [extends_name] [-r]" . PHP_EOL;
        echo '- Make model: make model name [extends_name] [table_name] [primary_key]' . PHP_EOL;
        echo "- Make helper: make helper name" . PHP_EOL;
        echo "- Make libraries: make libraries name" . PHP_EOL;
        echo "- Make view: make view name" . PHP_EOL;
        echo "- Make view: make crud name" . PHP_EOL;
        echo PHP_EOL;
        echo "--------------------------------------:" . PHP_EOL;
        echo PHP_EOL;
        echo "List of Command Import:" . PHP_EOL;
        echo "- Import model master: import model master" . PHP_EOL;
        echo "- Import helper format date Indonesia: import helper datetoindo" . PHP_EOL;
        echo "- Import helper format hari Indonesia: import helper daystoindo" . PHP_EOL;
        echo "- Import helper format bulan Indonesia: import helper monthtoindo" . PHP_EOL;
        echo "- Import helper generate password number: import helper generatepassword" . PHP_EOL;
        echo "- Import helper logging crud: import helper debuglog" . PHP_EOL;
        echo "- Import helper terbilang: import helper terbilang" . PHP_EOL;
        echo "- Import helper message alert: import helper message" . PHP_EOL;
        echo "- Import libraries create pdf document: import libraries pdfgenerator" . PHP_EOL;
        echo "- Import libraries encryptions: import libraries encryptions" . PHP_EOL;
        echo PHP_EOL;
        echo "--------------------------------------:" . PHP_EOL;
        echo PHP_EOL;
        echo "List of Command Init:" . PHP_EOL;
        echo "- Scaffolding Authentication Login: init auth" . PHP_EOL;
        echo "- Scaffolding Controller: init controller table_name controller_class controller_display" . PHP_EOL;
        echo "- Scaffolding Model: init model table_name model_class" . PHP_EOL;
        echo "- Scaffolding View: init view table_name folder_name" . PHP_EOL;
        echo "- Scaffolding CRUD: init crud table_name class_name display_name" . PHP_EOL;

        echo PHP_EOL;
    }
}
