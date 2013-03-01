<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Firesale_m extends MY_Model
{
	protected $_table				=	"firesale_categories";
	
	public function __construct()
	{
		parent::__construct();
	}
	

   
    /*
     * now supporting firesale pages
     */
    function get_firesale_links() {
        
        $query= $this->db->select('*', FALSE)
    		
            ->get('firesale_categories');
        
        $items= $query->result();
        return $items;
    }
    
    /*
     * now supporting firesale pages
     */
    function get_category_id($slug) {
        
        $query= $this->db->select('*', FALSE)
    		->where(array('slug'=> $slug))
            ->get('firesale_categories');
        
        $items= $query->result();
        foreach($items as $i):
			return $i->id;
		endforeach;
        return 0;
    }
    
    
    
}