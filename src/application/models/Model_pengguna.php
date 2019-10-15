<?php
class Model_pengguna extends Model_Master
{
    protected $table = 's_user';


    public function __construct()
    {
        parent::__construct();
    }
    function all()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('s_user_group', 'susrSgroupNama = sgroupNama', 'LEFT');
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
        $this->db->join('s_user_group', 'susrSgroupNama = sgroupNama', 'LEFT');
        $this->db->where($id);
        $qr = $this->db->get();
        if ($qr->num_rows() == 1)
            return $qr->row();
        else
            return false;
    }
}
