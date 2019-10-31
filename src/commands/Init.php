<?php

namespace Modangci\Commands;

use Modangci\Commands as Commands;

class Init extends Commands
{
    private $_is = null;
    function __construct($ci, $resource)
    {
        parent::__construct($ci, $resource);
        $this->_ci->load->database();

        $config['hostname'] = $this->_ci->db->hostname;
        $config['username'] = $this->_ci->db->username;
        $config['password'] = $this->_ci->db->password;
        $config['database'] = 'information_schema';
        $config['dbdriver'] = $this->_ci->db->dbdriver;
        $config['dbprefix'] = $this->_ci->db->dbprefix;
        $config['pconnect'] = $this->_ci->db->pconnect;
        $config['db_debug'] = $this->_ci->db->db_debug;
        $config['cache_on'] = $this->_ci->db->cache_on;
        $config['cachedir'] = $this->_ci->db->cachedir;
        $config['char_set'] = $this->_ci->db->char_set;
        $config['dbcollat'] = $this->_ci->db->dbcollat;

        $this->_is = $this->_ci->load->database($config, true);
    }

    private function insertDatas($table, $datas)
    {
        $this->_ci->db->trans_start();
        $this->_ci->db->insert($table, $datas);
        $this->_ci->db->trans_complete();

        if ($this->_ci->db->trans_status() === FALSE)
            return false;
        else
            return true;
    }

    private function get_by_id($table, $key)
    {
        $this->_ci->db->select('*');
        $this->_ci->db->from($table);
        $this->_ci->db->where($key);

        $qr = $this->_ci->db->get();

        if ($qr->num_rows() == 1)
            return $qr->row();
        else
            return false;
    }

    private function getConstraint($database, $table, $where)
    {
        $this->_is->select('KEY_COLUMN_USAGE.COLUMN_NAME,
        KEY_COLUMN_USAGE.TABLE_NAME,
        KEY_COLUMN_USAGE.CONSTRAINT_NAME, 
        KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME,
        KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME');
        $this->_is->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE');
        $this->_is->where('KEY_COLUMN_USAGE.TABLE_SCHEMA', $database);
        $this->_is->where('KEY_COLUMN_USAGE.TABLE_NAME', $table);
        if (!empty($where))
            $this->_is->where($where);

        $qr = $this->_is->get();

        if ($qr->num_rows() > 0) {
            $result = $qr->result();
            return $qr->result();
        } else
            return false;
    }

    private function getSchemaTable($database, $table)
    {
        $this->_is->select('COLUMNS.COLUMN_NAME,
                        COLUMNS.COLUMN_DEFAULT,
                        COLUMNS.IS_NULLABLE,DATA_TYPE,
                        COLUMNS.CHARACTER_MAXIMUM_LENGTH,
                        COLUMNS.NUMERIC_PRECISION,
                        COLUMNS.COLUMN_KEY,
                        COLUMNS.EXTRA,
                        COLUMNS.COLUMN_COMMENT,
                        KEY_COLUMN_USAGE.TABLE_NAME,
                        KEY_COLUMN_USAGE.CONSTRAINT_NAME, 
                        KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME,
                        KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME');
        $this->_is->from('INFORMATION_SCHEMA.COLUMNS');
        $this->_is->join('INFORMATION_SCHEMA.KEY_COLUMN_USAGE', '
        COLUMNS.TABLE_SCHEMA = KEY_COLUMN_USAGE.TABLE_SCHEMA AND 
        COLUMNS.TABLE_NAME = KEY_COLUMN_USAGE.TABLE_NAME AND
        COLUMNS.COLUMN_NAME = KEY_COLUMN_USAGE.COLUMN_NAME', 'LEFT');
        $this->_is->where('COLUMNS.TABLE_SCHEMA', $database);
        $this->_is->where('COLUMNS.TABLE_NAME', $table);

        $qr = $this->_is->get();

        if ($qr->num_rows() > 0) {
            $result = $qr->result();
            return $qr->result();
        } else
            return false;
    }

    public function showdatabase()
    {
        $schemaTable = $this->_ci->db->list_tables();
        print_r($schemaTable);
    }

    public function showtables($table = null)
    {
        if ($table != null and $table != "null") {
            $tables = $this->getSchemaTable($this->_ci->db->database, $table);
            print_r($tables);
        } else
            $this->index();
    }

    public function auth()
    {
        $this->_command = "Init";
        $this->_name = "Auth";

        $default_modul = [
            'modulgroup' => 'Modul Group',
            'modul' => 'Modul',
            'unit' => 'Unit',
            'hakakses' => 'Hak Akses',
            'hakaksesmodul' => 'Hak Akses Modul',
            'hakaksesunit' => 'Hak Akses Unit',
            'pengguna' => 'Pengguna'
        ];

        $this->_ci->load->dbforge();
        $attributes = array('ENGINE' => 'InnoDB');
        //Create Table s_user_group
        $this->_message("Creating Table s_user_group...");
        $fields = [
            'sgroupNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'sgroupKeterangan' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('sgroupNama', TRUE);
        $this->_ci->dbforge->create_table('s_user_group', TRUE, $attributes);
        $checkDatas = $this->get_by_id('s_user_group', ['sgroupNama' => 'ADMIN']);
        if (!$checkDatas)
            $this->insertDatas('s_user_group', [
                'sgroupNama' => 'ADMIN',
                'sgroupKeterangan' => 'ADMINISTRATOR'
            ]);


        //Create Table s_unit
        $this->_message("Creating Table s_unit...");
        $fields = [
            'unitId' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE,
            ),
            'unitKode' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'unitNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '75',
                'default' => '',
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('unitId', TRUE);
        $this->_ci->dbforge->create_table('s_unit', TRUE, $attributes);


        //Create Table s_user_modul_group_ref
        $this->_message("Creating Table s_user_modul_group_ref...");
        $fields = [
            'susrmdgroupNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'susrmdgroupDisplay' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
                'null' => FALSE,
            ),
            'susrmdgroupIcon' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
                'null' => FALSE,
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('susrmdgroupNama', TRUE);
        $this->_ci->dbforge->create_table('s_user_modul_group_ref', TRUE, $attributes);
        $checkDatas = $this->get_by_id('s_user_modul_group_ref', ['susrmdgroupNama' => 'admin']);
        if (!$checkDatas)
            $this->insertDatas('s_user_modul_group_ref', [
                'susrmdgroupNama' => 'admin',
                'susrmdgroupDisplay' => 'Administrator',
                'susrmdgroupIcon' => '<i class="la la-desktop"></i>'
            ]);

        //Create Table s_user_modul_ref
        $this->_message("Creating Table s_user_modul_ref...");
        $fields = [
            'susrmodulNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'susrmodulNamaDisplay' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ),
            'susrmodulSusrmdgroupNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE,
            ),
            'susrmodulIsLogin' => array(
                'type' => 'INT',
                'constraint' => 4,
                'default' => '1',
            ),
            'susrmodulUrut' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('susrmodulNama', TRUE);
        $this->_ci->dbforge->create_table('s_user_modul_ref', TRUE, $attributes);
        $checkConstraint = $this->getConstraint($this->_ci->db->database, 's_user_modul_ref', ['CONSTRAINT_NAME' => 's_user_modul_ref_ibfk_1']);
        if (!$checkConstraint)
            $this->_ci->dbforge->add_column('s_user_modul_ref', [
                'CONSTRAINT s_user_modul_ref_ibfk_1 FOREIGN KEY (susrmodulSusrmdgroupNama) REFERENCES s_user_modul_group_ref (susrmdgroupNama) ON UPDATE CASCADE'
            ]);
        // Insert Datas s_user_modul_ref
        $i = 1;
        foreach ($default_modul as $modul => $display) {
            $checkDatas = $this->get_by_id('s_user_modul_ref', ['susrmodulNama' => $modul]);
            if (!$checkDatas)
                $this->insertDatas('s_user_modul_ref', [
                    'susrmodulNama' => $modul,
                    'susrmodulNamaDisplay' => $display,
                    'susrmodulSusrmdgroupNama' => 'admin',
                    'susrmodulIsLogin' => '1',
                    'susrmodulUrut' => $i++,
                ]);
        }

        //Create Table s_user_group_modul
        $this->_message("Creating Table s_user_group_modul...");
        $fields = [
            'sgroupmodulSgroupNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'sgroupmodulSusrmodulNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'sgroupmodulSusrmodulRead' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('sgroupmodulSgroupNama', TRUE);
        $this->_ci->dbforge->add_key('sgroupmodulSusrmodulNama', TRUE);
        $this->_ci->dbforge->create_table('s_user_group_modul', TRUE, $attributes);
        $checkConstraint = $this->getConstraint($this->_ci->db->database, 's_user_group_modul', ['CONSTRAINT_NAME' => 's_user_group_modul_ibfk_1']);
        $checkConstraint2 = $this->getConstraint($this->_ci->db->database, 's_user_group_modul', ['CONSTRAINT_NAME' => 's_user_group_modul_ibfk_2']);
        if (!$checkConstraint and !$checkConstraint2)
            $this->_ci->dbforge->add_column('s_user_group_modul', [
                'CONSTRAINT s_user_group_modul_ibfk_1 FOREIGN KEY (sgroupmodulSusrmodulNama) REFERENCES s_user_modul_ref (susrmodulNama) ON UPDATE CASCADE',
                'CONSTRAINT s_user_group_modul_ibfk_2 FOREIGN KEY (sgroupmodulSgroupNama) REFERENCES s_user_group (sgroupNama) ON DELETE NO ACTION ON UPDATE CASCADE'
            ]);
        // Insert Datas s_user_group_modul
        foreach ($default_modul as $modul => $display) {
            $checkDatas = $this->get_by_id('s_user_group_modul', ['sgroupmodulSgroupNama' => 'ADMIN', 'sgroupmodulSusrmodulNama' => $modul]);
            if (!$checkDatas)
                $this->insertDatas('s_user_group_modul', [
                    'sgroupmodulSgroupNama' => 'ADMIN',
                    'sgroupmodulSusrmodulNama' => $modul,
                    'sgroupmodulSusrmodulRead' => '1'
                ]);
        }

        //Create Table s_user_group_unit
        $this->_message("Creating Table s_user_group_unit...");
        $fields = [
            'sgroupunitSgroupNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'sgroupunitUnitId' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'sgroupunitUnitRead' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '1',
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('sgroupunitSgroupNama', TRUE);
        $this->_ci->dbforge->add_key('sgroupunitUnitId', TRUE);
        $this->_ci->dbforge->create_table('s_user_group_unit', TRUE, $attributes);
        $checkConstraint = $this->getConstraint($this->_ci->db->database, 's_user_group_unit', ['CONSTRAINT_NAME' => 's_user_group_unit_ibfk_1']);
        $checkConstraint2 = $this->getConstraint($this->_ci->db->database, 's_user_group_unit', ['CONSTRAINT_NAME' => 's_user_group_unit_ibfk_2']);
        if (!$checkConstraint and !$checkConstraint2)
            $this->_ci->dbforge->add_column('s_user_group_unit', [
                'CONSTRAINT s_user_group_unit_ibfk_1 FOREIGN KEY (sgroupunitUnitId) REFERENCES s_unit (unitId) ON UPDATE CASCADE',
                'CONSTRAINT s_user_group_unit_ibfk_2 FOREIGN KEY (sgroupunitSgroupNama) REFERENCES s_user_group (sgroupNama) ON DELETE NO ACTION ON UPDATE CASCADE'
            ]);


        //Create Table s_user
        $this->_message("Creating Table s_user...");
        $fields = [
            'susrNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'susrPassword' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'susrSgroupNama' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'susrProfil' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'susrPertanyaan' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
                'null' => FALSE,
            ),
            'susrJawaban' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
                'null' => FALSE,
            ),
            'susrAvatar' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
                'null' => FALSE,
            ),
            'susrRefIndex' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
                'null' => FALSE,
            ),
            'susrLastLogin' => array(
                'type' => 'DATETIME',
            ),
        ];
        $this->_ci->dbforge->add_field($fields);
        $this->_ci->dbforge->add_key('susrNama', TRUE);
        $this->_ci->dbforge->create_table('s_user', TRUE, $attributes);
        $checkConstraint = $this->getConstraint($this->_ci->db->database, 's_user', ['CONSTRAINT_NAME' => 's_user_ibfk_1']);
        if (!$checkConstraint)
            $this->_ci->dbforge->add_column('s_user', [
                'CONSTRAINT s_user_ibfk_1 FOREIGN KEY (susrSgroupNama) REFERENCES s_user_group (sgroupNama) ON UPDATE CASCADE'
            ]);
        $checkDatas = $this->get_by_id('s_user', ['susrNama' => 'admin']);
        if (!$checkDatas)
            $this->insertDatas('s_user', [
                'susrNama' => 'admin',
                'susrSgroupNama' => 'ADMIN',
                'susrProfil' => 'Administrator',
                'susrPassword' => password_hash('admin', PASSWORD_DEFAULT)
            ]);

        // Create Folder Sessions
        $this->_message("Creating Folder Sessions...");
        $this->_create_folder('sessions');

        //Import File
        //Controller Basic
        self::copy('vendor/agusth24/modangci/src/application/controllers/Home.php', 'application/controllers/Home.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Login.php', 'application/controllers/Login.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Otentifikasi.php', 'application/controllers/Otentifikasi.php');
        //Controller Multi Role
        self::copy('vendor/agusth24/modangci/src/application/controllers/Hakakses.php', 'application/controllers/Hakakses.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Hakaksesmodul.php', 'application/controllers/Hakaksesmodul.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Hakaksesunit.php', 'application/controllers/Hakaksesunit.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Modul.php', 'application/controllers/Modul.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Modulgroup.php', 'application/controllers/Modulgroup.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Pengguna.php', 'application/controllers/Pengguna.php');
        self::copy('vendor/agusth24/modangci/src/application/controllers/Unit.php', 'application/controllers/Unit.php');

        //Core
        self::copy('vendor/agusth24/modangci/src/application/core/Model_Master.php', 'application/core/Model_Master.php');
        self::copy('vendor/agusth24/modangci/src/application/core/MY_Controller.php', 'application/core/MY_Controller.php');
        self::copy('vendor/agusth24/modangci/src/application/core/MY_Model.php', 'application/core/MY_Model.php');

        //Helper
        self::copy('vendor/agusth24/modangci/src/application/helpers/message_helper.php', 'application/helpers/message_helper.php');
        self::copy('vendor/agusth24/modangci/src/application/helpers/generatepassword_helper.php', 'application/helpers/generatepassword_helper.php');

        //Libraries
        self::copy('vendor/agusth24/modangci/src/application/libraries/Encryptions.php', 'application/libraries/Encryptions.php');

        //Models Basic
        self::copy('vendor/agusth24/modangci/src/application/models/Model_home.php', 'application/models/Model_home.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_login.php', 'application/models/Model_login.php');
        //Models Multi Role
        self::copy('vendor/agusth24/modangci/src/application/models/Model_hakakses.php', 'application/models/Model_hakakses.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_hakaksesmodul.php', 'application/models/Model_hakaksesmodul.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_hakaksesunit.php', 'application/models/Model_hakaksesunit.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_modul.php', 'application/models/Model_modul.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_modulgroup.php', 'application/models/Model_modulgroup.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_pengguna.php', 'application/models/Model_pengguna.php');
        self::copy('vendor/agusth24/modangci/src/application/models/Model_unit.php', 'application/models/Model_unit.php');

        //Views
        self::recursiveCopy('vendor/agusth24/modangci/src/application/views/', 'application/views/');

        //Assets
        self::recursiveCopy('vendor/agusth24/modangci/src/public/', 'public/');

        echo PHP_EOL;
        echo "Set Autoload on application/config/autolod.php:" . PHP_EOL;
        echo "- \$autoload['libraries'] = array('database','session','form_validation','encryptions');" . PHP_EOL;
        echo "- \$autoload['helper'] = array('url','form','security','message');" . PHP_EOL;
        echo PHP_EOL;
        echo "Set Config on application/config/config.php:" . PHP_EOL;
        echo "- \$config['base_url'] = 'http://examples.test/';" . PHP_EOL;
        echo "- \$config['sess_save_path'] = APPPATH.'sessions';" . PHP_EOL;
    }

    public function controller($table, $cname, $dname)
    {
        if (!empty($table) and !empty($cname) and !empty($dname)) {
            $this->_command = "Init";
            $this->_name = "Controller - " . $cname;

            $table = strtolower($table);
            $cname = strtolower($cname);
            $dname = ucfirst(strtolower($dname));

            $primary_key = $this->getConstraint($this->_ci->db->database, $table, ['CONSTRAINT_NAME' => 'PRIMARY']);
            $foreign_key = $this->getConstraint($this->_ci->db->database, $table, 'REFERENCED_COLUMN_NAME IS NOT NULL');
            $pKey = ($primary_key != false ? $primary_key[0]->COLUMN_NAME : '');

            $getSchmema = $this->getSchemaTable($this->_ci->db->database, $table);
            if ($getSchmema != false) {
                if ($foreign_key != false) {
                    $get_all = "\$this->{\$this->_model_name}->all();";
                    $get_by_id = "\$this->{\$this->_model_name}->by_id(\$key);";
                } else {
                    $get_all = "\$this->{\$this->_model_name}->get_ref_table('$table');";
                    $get_by_id = "\$this->{\$this->_model_name}->get_by_id('$table',\$key);";
                }

                $post = $params = $load_foreign_table = '';
                $form_validation = "\$" . $pKey . "Old = \$this->input->post('" . $pKey . "Old');";
                foreach ($getSchmema as $row) {
                    if ($row->EXTRA != 'auto_increment') {
                        if ($row->COLUMN_NAME == $pKey) {
                            $form_validation .= "
                            if(empty(\$" . $pKey . "Old))
                                \$this->form_validation->set_rules('$pKey', '" . (!empty($row->COLUMN_COMMENT) ? $row->COLUMN_COMMENT : $row->COLUMN_NAME) . "', 'trim|xss_clean|required|is_unique[$table.$pKey]');
                            else
                                \$this->form_validation->set_rules('$pKey', '" . (!empty($row->COLUMN_COMMENT) ? $row->COLUMN_COMMENT : $row->COLUMN_NAME) . "', 'trim|xss_clean|required');
                            ";
                        } else {
                            $form_validation .= "\$this->form_validation->set_rules('$row->COLUMN_NAME','" . (!empty($row->COLUMN_COMMENT) ? $row->COLUMN_COMMENT : $row->COLUMN_NAME) . "','trim|xss_clean" . ($row->IS_NULLABLE == 'NO' ? '|required' : '') . "');\n";
                        }

                        $post .= "$" . $row->COLUMN_NAME . " = \$this->input->post('" . $row->COLUMN_NAME . "');\n";
                        $params .= "'$row->COLUMN_NAME'=>$$row->COLUMN_NAME,\n";
                    }

                    if (!empty($row->REFERENCED_TABLE_NAME))
                        $load_foreign_table .= "\$data['$row->REFERENCED_TABLE_NAME'] = \$this->{\$this->_model_name}->get_ref_table('$row->REFERENCED_TABLE_NAME');\n";
                }

                $file = "
                <?php
                defined('BASEPATH') OR exit('No direct script access allowed');
                define('IS_AJAX', isset(\$_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(\$_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

                class $cname extends MY_Controller {
                    function __construct()
                    {
                        parent::__construct();

                        \$this->_template = 'layouts/template';
                        \$this->_path_page = 'pages/$cname/';
                        \$this->_path_js = null;
                        \$this->_judul = '$dname';
                        \$this->_controller_name = '$cname';
                        \$this->_model_name = 'model_$cname';
                        \$this->_page_index = 'index';

                        \$this->load->model(\$this->_model_name,'',TRUE);
                    }

                    public function index()
                    {
                        \$data = \$this->get_master(\$this->_path_page.\$this->_page_index);
                        \$data['scripts'] = [];
                        \$data['datas'] = $get_all
                        \$data['create_url'] = site_url(\$this->_controller_name.'/create').'/';
                        \$data['update_url'] = site_url(\$this->_controller_name.'/update').'/';
                        \$data['delete_url'] = site_url(\$this->_controller_name.'/delete').'/';
                        \$this->load->view(\$this->_template, \$data);
                    }

                    public function create()
                    {	
                        \$data = \$this->get_master(\$this->_path_page.'form');	
                        \$data['scripts'] = [];	
                        \$data['save_url'] = site_url(\$this->_controller_name.'/save').'/';	
                        \$data['status_page'] = 'Create';
                        \$data['datas'] = false;
                        $load_foreign_table	
                        \$this->load->view(\$this->_template, \$data);
                    }

                    public function update()
                    {		
                        \$data = \$this->get_master(\$this->_path_page.'form');	
                        \$keyS = \$this->encryptions->decode(\$this->uri->segment(3),\$this->config->item('encryption_key'));
                        \$data['scripts'] = [];	
                        \$data['save_url'] = site_url(\$this->_controller_name.'/save').'/';	
                        \$data['status_page'] = 'Update';
                        \$key = ['$pKey'=>\$keyS];
                        \$data['datas'] = $get_by_id
                        $load_foreign_table	
                        \$this->load->view(\$this->_template, \$data);
                    }

                    public function save()
                    {		
                        $form_validation
                        if(\$this->form_validation->run()) 
                        {	
                            if(IS_AJAX)
                            {
                                $post	

                                \$param = array(
                                    $params
                                );

                                if(empty($" . $pKey . "Old))
                                {
                                    \$proses = \$this->{\$this->_model_name}->insert('$table',\$param);
                                } else {
                                    \$key = array('$pKey'=>$" . $pKey . "Old);
                                    \$proses = \$this->{\$this->_model_name}->update('$table',\$param,\$key);
                                }

                                if(\$proses)
                                    message(\$this->_judul.' Berhasil Disimpan','success');
                                else
                                {
                                    \$error = \$this->db->error();
                                    message(\$this->_judul.' Gagal Disimpan, '.\$error['code'].': '.\$error['message'],'error');
                                }
                            }
                        } else {
                            message('Ooops!! Something Wrong!! '.validation_errors(),'error');
                        }
                    }

                    public function delete()
                    {
                        \$keyS = \$this->encryptions->decode(\$this->uri->segment(3),\$this->config->item('encryption_key'));
                        \$key = ['$pKey'=>\$keyS];
                        \$proses = \$this->{\$this->_model_name}->delete('$table',\$key);
                        if (\$proses) 
                            message(\$this->_judul.' Berhasil Dihapus','success');
                        else
                        {
                            \$error = \$this->db->error();
                            message(\$this->_judul.' Gagal Dihapus, '.\$error['code'].': '.\$error['message'],'error');
                        }
                    }
                }
                ";
                $create_file = $this->_create_file($file, 'controllers/' . ucfirst($cname));
                return true;
            } else {
                $this->_message('Table Not Found!!');
                return false;
            }
        } else
            $this->index();
    }

    public function model($table, $cname)
    {
        if (!empty($cname) and !empty($table)) {
            $this->_command = "Init";
            $this->_name = "Model - " . $cname;

            $table = strtolower($table);
            $cname = strtolower($cname);

            $variable = null;
            $function = null;
            $join = null;

            $foreign_key = $this->getConstraint($this->_ci->db->database, $table, 'REFERENCED_COLUMN_NAME IS NOT NULL');
            if ($foreign_key != false) {
                foreach ($foreign_key as $row) {
                    $join .= "        \$this->db->join('" . $row->REFERENCED_TABLE_NAME . "','" . $row->COLUMN_NAME . " = " . $row->REFERENCED_COLUMN_NAME . "','LEFT');\n";
                }
                $variable .= "    protected \$table = '$table';\n";
                $function .= "    function all()\n"
                    . "    {\n"
                    . "        \$this->db->select('*');\n"
                    . "        \$this->db->from(\$this->table);\n"
                    . $join
                    . "        \$qr=\$this->db->get();\n"
                    . "        if(\$qr->num_rows()>0)\n"
                    . "            return \$qr->result();\n"
                    . "        else\n"
                    . "            return false;\n"
                    . "    }\n\n";

                $function .= "    function by_id(\$id)\n"
                    . "    {\n"
                    . "        \$this->db->select('*');\n"
                    . "        \$this->db->from(\$this->table);\n"
                    . $join
                    . "        \$this->db->where(\$id);\n"
                    . "        \$qr=\$this->db->get();\n"
                    . "        if(\$qr->num_rows()==1)\n"
                    . "            return \$qr->row();\n"
                    . "        else\n"
                    . "            return false;\n"
                    . "    }\n\n";
            }
            $file = "
            <?php
            class Model_$cname extends Model_Master
            {
                $variable\n
                public function __construct()
                {
                    parent::__construct();
                }       
                $function\n
            }
            ";
            $create_file = $this->_create_file($file, 'models/Model_' . strtolower($cname));
        } else
            $this->index();
    }

    public function view($table, $cname)
    {
        if (!empty($cname) and !empty($table)) {
            $this->_command = "Init";
            $this->_name = "View - " . $cname;

            $table = strtolower($table);
            $cname = strtolower($cname);

            $primary_key = $this->getConstraint($this->_ci->db->database, $table, ['CONSTRAINT_NAME' => 'PRIMARY']);
            $foreign_key = $this->getConstraint($this->_ci->db->database, $table, 'REFERENCED_COLUMN_NAME IS NOT NULL');
            $pKey = ($primary_key != false ? $primary_key[0]->COLUMN_NAME : '');
            $create_folder = $this->_create_folder('views/pages/' . strtolower($cname));

            $getSchmema = $this->getSchemaTable($this->_ci->db->database, $table);
            $thead = $tbody = $form_body = null;
            if ($getSchmema != false) {
                foreach ($getSchmema as $row) {
                    if ($row->EXTRA != 'auto_increment') {
                        $label = !empty($row->COLUMN_COMMENT) ? $row->COLUMN_COMMENT : $row->COLUMN_NAME;
                        $thead .= "<th>$label</th>\n";
                        $tbody .= "<td><?=\$row->" . $row->COLUMN_NAME . "?></td>";

                        if (!empty($row->REFERENCED_TABLE_NAME)) {
                            $form_body .= "
                                <div class=\"form-group\">
                                    <label>$label</label>
                                    <select class=\"form-control m-select2\" name=\"" . $row->COLUMN_NAME . "\">
                                            <option value=\"\"></option>
                                    <?php 
                                    foreach(\$$row->REFERENCED_TABLE_NAME as \$row):
                                        echo '<option value=\"'.\$row->$row->REFERENCED_COLUMN_NAME.'\" ' . (\$datas != false ? \$datas->susrmodulSusrmdgroupNama == \$row->susrmdgroupNama ? 'selected' : '' : '') . '>'.\$row->$row->REFERENCED_COLUMN_NAME.'</option>';
                                    endforeach;
                                    ?>
                                    </select>
                                    
                                </div>
                            ";
                        } else {
                            $form_body .= "
                                <div class=\"form-group\">
                                    <label>$label</label>
                                    <input type=\"text\" class=\"form-control\" name=\"" . $row->COLUMN_NAME . "\" placeholder=\"$label\" aria-describedby=\"$label\" value=\"<?=\$datas!=false?\$datas->" . $row->COLUMN_NAME . ":''?>\">
                                </div>
                            ";
                        }
                    }
                }
            }

            $index = "
            <!-- BEGIN: Subheader -->
            <?php \$this->load->view('layouts/subheader'); ?>
            <!-- END: Subheader -->
            
            <!--Begin::Row-->
            <!-- begin:: Content -->
            <div class=\"kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid\">
                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <div id=\"response\"></div>
                        <!--begin::Portlet-->
                        <div class=\"kt-portlet\">
                            <div class=\"kt-portlet__head\">
                                <div class=\"kt-portlet__head-label\">
                                    <h3 class=\"kt-portlet__head-title\">
                                        <?=strtoupper(\$page_judul)?>
                                    </h3>
                                </div>
                                <div class=\"kt-portlet__head-toolbar\">
                                    <div class=\"kt-portlet__head-actions\">
                                        <a href=\"<?=\$create_url?>\" class=\"btn btn-outline-primary\">
                                            <span>
                                                <i class=\"flaticon2-plus\"></i>
                                                <span>Create</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
            
                            <div class=\"kt-portlet__body\">
            
                                <!--begin::Section-->
                                <div class=\"kt-section\">
                                    <div class=\"kt-section__content\">
                                        <div class=\"table-responsive\">
                                            <table class=\"table table-hover\">
                                                <thead class=\"thead-light\">
                                                    <tr>
                                                        <th>No</th>
                                                        $thead
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if(\$datas!=false)
                                                {
                                                    \$i = 1;
                                                    foreach(\$datas as \$row)
                                                    {
                                                        \$key = \$this->encryptions->encode(\$row->$pKey,\$this->config->item('encryption_key'));
                                                ?>
                                                    <tr>
                                                        <th scope=\"row\"><?=\$i++?></th>
                                                        $tbody
                                                        <td>
                                                        <a href=\"<?=\$update_url.\$key?>\" title=\"Update\" class=\"btn btn-sm btn-outline-primary btn-elevate btn-circle btn-icon\">
                                                            <span>
                                                                <i class=\"fa fa-pencil-alt\"></i>
                                                            </span>
                                                        </a>
                                                        <a href=\"<?=\$delete_url.\$key?>\" title=\"Delete\" id='ts_remove_row<?= \$i; ?>' class=\"ts_remove_row btn btn-sm btn-outline-danger btn-elevate btn-circle btn-icon\">
                                                            <span>
                                                                <i class=\"fa fa-trash-alt\"></i>
                                                            </span>
                                                        </a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
            
                                <!--end::Section-->
                            </div>
                        </div>
            
                        <!--end::Portlet-->
                    </div>
                </div>
            </div>
            <!--End::Row-->
            ";

            $create_file = $this->_create_file($index, 'views/pages/' . strtolower($cname) . '/index');

            $form = "
            <!-- BEGIN: Subheader -->
            <?php \$this->load->view('layouts/subheader'); ?>
            <!-- END: Subheader -->

            <!--Begin::Row-->
            <!-- begin:: Content -->
            <div class=\"kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid\">
                <div class=\"row\">
                    <div class=\"col-md-12\">
                        <div id=\"response\"></div>
                        <!--begin::Portlet-->
                        <div class=\"kt-portlet\">
                            <div class=\"kt-portlet__head\">
                                <div class=\"kt-portlet__head-label\">
                                    <h3 class=\"kt-portlet__head-title\">
                                        <?=strtoupper(\$page_judul)?>
                                    </h3>
                                </div>
                            </div>

                            <!--begin::Form-->
                            <form class=\"kt-form\" action=\"<?=\$save_url?>\" method=\"post\" id=\"form_form\">
                                <div class=\"kt-portlet__body\">
                                    <input type=\"hidden\" name=\"" . $pKey . "Old\" value=\"<?=\$datas!=false?\$datas->$pKey:''?>\">
                                    $form_body
                                </div>
                                <div class=\"kt-portlet__foot\">
                                    <div class=\"kt-form__actions\">
                                        <button type=\"submit\" id=\"btn_save\" class=\"btn btn-primary\">Save</button>
                                        <button type=\"reset\" class=\"btn btn-secondary\">Cancel</button>
                                    </div>
                                </div>
                            </form>

                            <!--end::Form-->
                        </div>

                        <!--end::Portlet-->
                    </div>
                </div>
            </div>
            <!--End::Row-->
            ";
            $create_file = $this->_create_file($form, 'views/pages/' . strtolower($cname) . '/form');
        }
    }

    public function crud($table, $cname, $dname)
    {
        if (!empty($table) and !empty($cname) and !empty($dname)) {
            $controller = $this->controller($table, $cname, $dname);
            if ($controller != false) {
                $this->model($table, $cname);
                $this->view($table, $cname);
            }
        }
    }
}
