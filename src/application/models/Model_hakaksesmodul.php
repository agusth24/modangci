<?php
class Model_hakaksesmodul extends Model_Master
{
    protected $table = 's_user_group_modul';


    public function __construct()
    {
        parent::__construct();
    }

    function by_id($id)
    {
        $this->db->select('*');
        $this->db->from('s_user_modul_ref');
        $this->db->join($this->table, "sgroupmodulSusrmodulNama = susrmodulNama AND sgroupmodulSgroupNama='$id'", 'LEFT');
        $this->db->join('s_user_group', 'sgroupmodulSgroupNama = sgroupNama', 'LEFT');
        $this->db->join('s_user_modul_group_ref', 'susrmdgroupNama = susrmodulSusrmdgroupNama', 'LEFT');
        $qr = $this->db->get();
        if ($qr->num_rows() > 0)
            return $qr->result();
        else
            return false;
    }
}
