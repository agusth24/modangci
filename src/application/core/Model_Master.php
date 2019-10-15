<?php
class Model_Master extends MY_Model
{
    public function __construct()
	{
		parent::__construct();
    }
    
    function get_by_id($table,$key)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($key);

		$qr=$this->db->get();

		if($qr->num_rows()==1)
			return $qr->row();
		else
			return false;
	}

	function get_by_last_id($table,$key,$where='')
	{
		$this->db->select('MAX('.$key.') '.$key);
		$this->db->from($table);
		if(!empty($where))
			$this->db->where($where);
		$this->db->order_by($key.' DESC');

		$qr=$this->db->get();

		if($qr->num_rows()>0)
			return $qr->row();
		else
			return false;
	}

	function get_ref_table($table,$order='',$where='')
	{
		$this->db->select('*');
		$this->db->from($table);
		if(!empty($where))
			$this->db->where($where);
		if(!empty($order))
			$this->db->order_by($order);

		$qr=$this->db->get();

		if($qr->num_rows()>0)
			return $qr->result();
		else
			return false;
	}

	function insert($table,$param)
	{		
		$this->db->trans_start();
		$this->db->insert($table,$param);
        $query = $this->db->last_query();
        if(function_exists('debuglog'))
            debuglog($query);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
			return false;
		else
			return true;			
	}

	function insert_batch($table,$param)
	{		
		$this->db->trans_start();
		$this->db->insert_batch($table,$param);
		$query = $this->db->last_query();
		if(function_exists('debuglog'))
            debuglog($query);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
			return false;
		else
			return true;			
	}

	function replace($table,$param)
	{		
		$this->db->trans_start();
		$this->db->replace($table,$param);
		$query = $this->db->last_query();
		if(function_exists('debuglog'))
            debuglog($query);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
			return false;
		else
			return true;			
	}

	function update($table,$param,$key)
	{
		$this->db->trans_start();
		$this->db->where($key);
		$this->db->update($table, $param); 
		$query = $this->db->last_query();
		if(function_exists('debuglog'))
            debuglog($query);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
			return false;
		else
			return true;
	}

	function update_batch($table,$param,$key)
	{
		$this->db->trans_start();
		$this->db->update_batch($table, $param, $key); 
		$query = $this->db->last_query();
		if(function_exists('debuglog'))
            debuglog($query);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
			return false;
		else
			return true;
	}

	public function delete_truncate($table)
	{
		$this->db->select('*');
		$this->db->from($table);

		$qr = $this->db->get();

		$d = false;
		if($qr->num_rows()>0)
			$d = $qr->result();

		try {
			$this->db->trans_start();
            $this->db->empty_table($table);
            if(function_exists('debuglog'))
			    debugLog($this->db->last_query(),$d);
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE)
				return false;
			else
				return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function delete($table,$key)
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($key);

		$qr = $this->db->get();

		$d = false;
		if($qr->num_rows()>0)
			$d = $qr->result();

		try {
			$this->db->trans_start();
			$this->db->where($key);
            $this->db->delete($table);
            if(function_exists('debuglog'))
    			debugLog($this->db->last_query(),$d);
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE)
				return false;
			else
				return true;
		} catch (Exception $e) {
			return false;
		}
	}

    function get_menu_by_susrSgroupNama($susrSgroupNama)
	{
		$this->db->select('susrmodulNama,susrmodulNamaDisplay,susrmdgroupDisplay,susrmdgroupIcon');
		$this->db->from('s_user_group_modul');
		$this->db->join('s_user_modul_ref','sgroupmodulSusrmodulNama=susrmodulNama','left');
		$this->db->join('s_user_modul_group_ref','susrmodulSusrmdgroupNama=susrmdgroupNama','left');
		$this->db->where('sgroupmodulSgroupNama', $susrSgroupNama);
		$this->db->where('sgroupmodulSusrmodulRead','1');
		$this->db->where('susrmodulSusrmdgroupNama IS NOT NULL',null,false);
		$this->db->order_by('susrmdgroupDisplay,susrmodulUrut,susrmodulNamaDisplay');

		$qr=$this->db->get();

		if($qr->num_rows()>0)
			return $qr->result();
		else
			return false;
	}

	function get_menu_non_login()
	{
		$this->db->select('susrmodulNama,susrmodulNamaDisplay');
		$this->db->from('s_user_modul_ref');
		$this->db->where('susrmodulIsLogin','0');
		$this->db->order_by('susrmodulUrut');

		$qr=$this->db->get();

		if($qr->num_rows()>0)
			return $qr->result();
		else
			return false;
	}

	function otentifikasi_menu_by_susrSgroupNama($susrSgroupNama,$susrmodulNama)
	{
		$this->db->select('susrmodulNamaDisplay,susrmdgroupDisplay');
		$this->db->from('s_user_group_modul');
		$this->db->join('s_user_modul_ref','sgroupmodulSusrmodulNama=susrmodulNama','left');
		$this->db->join('s_user_modul_group_ref','susrmodulSusrmdgroupNama=susrmdgroupNama','left');
		$this->db->where('sgroupmodulSgroupNama', $susrSgroupNama);
		$this->db->where('sgroupmodulSusrmodulRead','1');
		$this->db->where('sgroupmodulSusrmodulNama', $susrmodulNama);
		$this->db->limit(1);
		$qr=$this->db->get();

		if($qr->num_rows()==1)
			return $qr->result();
		else
			return FALSE;
	}

	function otentifikasi_menu_by_susrNama($susrNama,$susrmodulNama)
	{
		$this->db->SELECT('SUSRMODULNAMADISPLAY,SUSRMDGROUPDISPLAY');
		$this->db->FROM('N_S_USER');
		$this->db->JOIN('N_S_USER_GROUP_MODUL','SUSRSGROUPNAMA = SGROUPMODULSGROUPNAMA','LEFT');
		$this->db->JOIN('N_S_USER_MODUL_REF','SGROUPMODULSUSRMODULNAMA=SUSRMODULNAMA','LEFT');
		$this->db->JOIN('N_S_USER_MODUL_GROUP_REF','SUSRMODULSUSRMDGROUPNAMA=SUSRMDGROUPNAMA','LEFT');
		$this->db->where('SUSRNAMA', $susrNama);
		$this->db->where('SGROUPMODULSUSRMODULNAMA', $susrmodulNama);
		$this->db->limit(1);
		$qr=$this->db->get();

		if($qr->num_rows()==1)
			return $qr->result();
		else
			return FALSE;
	}
}