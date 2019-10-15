<?php
class Model_modul extends Model_Master
{
    protected $table = 's_user_modul_ref';


    public function __construct()
    {
        parent::__construct();
    }
    function all()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('s_user_modul_group_ref', 'susrmodulSusrmdgroupNama = susrmdgroupNama', 'LEFT');
        $this->db->order_by('susrmodulSusrmdgroupNama,susrmodulUrut,susrmodulNama');
        $qr = $this->db->get();
        if ($qr->num_rows() > 0)
            return $qr->result();
        else
            return false;
    }

    function by_id($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('s_user_modul_group_ref', 'susrmodulSusrmdgroupNama = susrmdgroupNama', 'LEFT');
        $this->db->where($id);
        $qr = $this->db->get();
        if ($qr->num_rows() == 1)
            return $qr->row();
        else
            return false;
    }
}
