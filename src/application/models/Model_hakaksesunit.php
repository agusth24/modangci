<?php
class Model_hakaksesunit extends Model_Master
{
    protected $table = 's_user_group_unit';


    public function __construct()
    {
        parent::__construct();
    }

    function by_id($id)
    {
        $this->db->select('*');
        $this->db->from('s_unit');
        $this->db->join($this->table, "sgroupunitUnitId = unitId AND sgroupunitSgroupNama = '$id'", 'LEFT');
        $this->db->join('s_user_group', 'sgroupunitSgroupNama = sgroupNama', 'LEFT');
        $qr = $this->db->get();
        if ($qr->num_rows() > 0)
            return $qr->result();
        else
            return false;
    }
}
