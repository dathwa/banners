<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Areas_m extends MY_Model
{
	protected $_table				=	"page_banners_areas";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_all($col, $dir)
    {
        $this->db->order_by($col, $dir);
        return $this->db->get($this->_table)->result();
        #return parent:$this->db->get($this->_table)->result();
    }
    
	
    function get_banner_areas()
	{
		$data= array();
        
        $select= array('slug', 'title');
		$order= array('title'=>'ASC');
        if (isset($order))
        {
            foreach ($order as $field => $value) {
                $this->db->order_by($field, $value);
            }
        }

		$this->db->select(array('slug', 'title'));
        $query = $this->db->get($this->_table);
		$pages= $query->result();
       
		foreach($pages as $page):
			$data[$page->{$select[0]}]= $page->{$select[1]};
		endforeach;
		return $data;
	}
    
    
    
    
	public function get_posted($item, $title_to_slug= 'title')
	{
		$data = array();	
		$config_validation= $this->config->item($item);
		// Loop through each rule
        // Set the values for the form inputs
		foreach ($config_validation as $rule)
		{
            $data[$rule['field']] = $this->input->post($rule['field']);
		}	
		if (isset($data['slug']))
		{
			$data['slug'] = url_title($data[$title_to_slug],'dash', TRUE);	
		}
		#$data['clicks'] = 0;
		return $data;
	}
	
	/*
	 * ensure there is only ever 1 default area
	 */
	function update_default($id=0)
	{
		$update = array('is_default'=> 0);		
        $this->model_m->update_all($update);
        $update = array('is_default'=> 1);		
        $this->model_m->update($id, $update);
        $this->session->set_flashdata('success', "Successfully updated default record");
	}
	
	function get_default_form_values($id)
	{
        $config= $this->config->item('areas');
		$config_skip= array('');
		$form_values= new stdClass();
		
        $row = $this->get($id);
        if (empty($row))
        {
         	// set up defaults
            foreach ($config as $rule)
            {
                if (!in_array($rule['field'], $config_skip))
                {
                    $form_values->{$rule['field']} = '';
                }
                $form_values->width = '0';
                $form_values->height = '0';
                $form_values->old_slug = '';
            }
        }
        else 
        {
        	foreach ($config as $rule)
            {
                if (!in_array($rule['field'], $config_skip))
                {
                    $form_values->{$rule['field']} = $row->{$rule['field']}; // suppress img-elements, username and password errors
                }
                $form_values->old_slug = $row->slug;
            }
        }
        return $form_values; 
	}
}