<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pages_m extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
    
    function get_drop_down_pages()
	{
        $select= array('uri', 'title');
        $pages= $this->get_pages($select);
        $data= array();
        $data['']= 'select';
		foreach($pages as $page):
			$data[$page->uri]= $page->title;
		endforeach;
		return $data;
	}
    
    
    function get_pages($select= NULL)
	{
        if (isset($select))
        {
            $this->db->select($select);
        }
        else
        {
            $this->db->select(array('id', 'title'));
        }
        $where= array('status'=> 'live', 'slug <>'=>'404');
        $this->db->where($where);
		
		$this->db->order_by('title', 'ASC');
		return $this->db->get($this->_table)->result();
	}
}