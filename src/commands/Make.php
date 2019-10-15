<?php

namespace Modangci\Commands;

use Modangci\Commands as Commands;

class Make extends Commands
{
    private $_crud = false;

    function __construct($ci, $resource)
    {
        parent::__construct($ci, $resource);
    }

    public function controller($name = null, $extends = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));;
            $this->_command = 'Controller';

            $crud_script = "";
            if (in_array('-r', $this->_resource)) {
                $crud_script = "    public function response()\n"
                    . "    {\n"
                    . "        echo \"Hello Response " . $this->_name . "\";\n"
                    . "    }\n\n"
                    . "    public function create()\n"
                    . "    {\n"
                    . "        echo \"Hello Create " . $this->_name . "\";\n"
                    . "    }\n\n"
                    . "    public function update()\n"
                    . "    {\n"
                    . "        echo \"Hello Update " . $this->_name . "\";\n"
                    . "    }\n\n"
                    . "    public function save()\n"
                    . "    {\n"
                    . "        echo \"Hello Save " . $this->_name . "\";\n"
                    . "    }\n\n"
                    . "    public function delete()\n"
                    . "    {\n"
                    . "        echo \"Hello Delete " . $this->_name . "\";\n"
                    . "    }\n\n";
            }

            $load_model = $load_datas = "";
            if ($this->_crud) {
                $load_model = "        \$this->load->model('model_" . strtolower($this->_name) . "','',TRUE);\n";
                $load_datas = "        \$data['datas'] = \$this->model_" . strtolower($this->_name) . "->all();\n"
                    . "        \$this->load->view('" . strtolower($this->_name) . "/index.php', \$data);\n";
            } else
                $load_datas = "        echo \"Hello Controller " . $this->_name . "\";\n";

            $file = "<?php\n"
                . "defined('BASEPATH') OR exit('No direct script access allowed');\n"
                . "class " . $this->_name . " extends " . (($extends != null and $extends != "null") ? $extends : 'CI_Controller') . "\n"
                . "{\n"
                . "    public function __construct()\n"
                . "    {\n"
                . "        parent::__construct();\n\n"
                . $load_model
                . "    }\n\n"
                . "    public function index()\n"
                . "    {\n"
                . $load_datas
                . "    }\n\n"
                . $crud_script
                . "}\n";

            $create_file = $this->_create_file($file, 'controllers/' . $this->_name);
        } else
            $this->index();
    }

    public function model($name = null, $extends = null, $table = null, $primary = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));;
            $this->_command = 'Model';

            $function = "";
            $variable = "";

            if ($table != null and $table != "null") {
                $variable .= "    protected \$table = '$table';\n";
                $function .= "    function all()\n"
                    . "    {\n"
                    . "        \$this->db->select('*');\n"
                    . "        \$this->db->from(\$this->table);\n"
                    . "        \$qr=\$this->db->get();\n"
                    . "        if(\$qr->num_rows()>0)\n"
                    . "            return \$qr->result();\n"
                    . "        else\n"
                    . "            return false;\n"
                    . "    }\n\n";
            }

            if ($table != null and $primary != null and $table != "null" and $primary != "null") {
                $variable .= "    protected \$primary = '$primary';\n";
                $function .= "    function by_id(\$id)\n"
                    . "    {\n"
                    . "        \$this->db->select('*');\n"
                    . "        \$this->db->from(\$this->table);\n"
                    . "        \$this->db->where(\$primary_key,\$id);\n"
                    . "        \$qr=\$this->db->get();\n"
                    . "        if(\$qr->num_rows()==1)\n"
                    . "            return \$qr->row();\n"
                    . "        else\n"
                    . "            return false;\n"
                    . "    }\n\n";
            }

            $file = "<?php\n"
                . "class Model_" . strtolower($this->_name) . " extends " . (($extends != null and $extends != "null") ? $extends : 'CI_Model') . "\n"
                . "{\n"
                . $variable . "\n"
                . "    public function __construct()\n"
                . "    {\n"
                . "        parent::__construct();\n"
                . "    }\n\n"
                . $function . "\n\n"
                . "    // your code goes here..\n"
                . "}\n";
            $create_file = $this->_create_file($file, 'models/Model_' . strtolower($this->_name));
        } else
            $this->index();
    }

    public function helper($name = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));;
            $this->_command = 'Helper';

            $file = "<?php\n"
                . "defined('BASEPATH') OR exit('No direct script access allowed');\n\n"
                . "if(!function_exists('" . $this->_name . "'))\n"
                . "{\n"
                . "    function " . $this->_name . "()\n"
                . "    {\n"
                . "        // your code goes here..\n"
                . "        return true;\n"
                . "    }\n"
                . "}\n";
            $create_file = $this->_create_file($file, 'helpers/' . $this->_name . "_helper");
        } else
            $this->index();
    }

    public function libraries($name = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));;
            $this->_command = 'Libraries';

            $file = "<?php\n"
                . "defined('BASEPATH') OR exit('No direct script access allowed');\n\n"
                . "class " . $this->_name . "\n"
                . "{\n"
                . "    public function __construct()\n"
                . "    {\n"
                . "        //if need instance codeigniter (load model, helper and etc..)\n"
                . "        \$this->CI = get_instance();\n"
                . "    }\n\n"
                . "    // your code goes here..\n"
                . "}\n";
            $create_file = $this->_create_file($file, 'libraries/' . $this->_name);
        } else
            $this->index();
    }

    public function view($name = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));;
            $this->_command = 'View';

            if ($this->_crud) {
                $load_datas = "    <?php print_r(\$datas); ?>\n";
            } else
                $load_datas = "    <h1>Hello World!!</h1>\n";

            $file = "<html>\n"
                . "<head>\n"
                . "    <title>" . $this->_name . "</title>\n"
                . "</head>\n"
                . "<body>\n"
                . $load_datas
                . "</body>\n";
            $create_folder = $this->_create_folder('views/' . strtolower($this->_name));
            $create_file = $this->_create_file($file, 'views/' . strtolower($this->_name) . '/index');
        } else
            $this->index();
    }

    public function crud($name = null)
    {
        if ($name != null and $name != "null") {
            $this->_name =  ucfirst(strtolower($name));;
            $this->_command = 'CRUD';
            $this->_crud = true;
            $this->_resource = ['-r'];

            $this->controller($name);
            $this->model($name, null, $name);
            $this->view($name);
        } else
            $this->index();
    }
}
