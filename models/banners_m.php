<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Banners_m extends MY_Model
{
	protected $_table				=	"page_banners";
	
	public function __construct()
	{
		parent::__construct();
	}
	

    function get_banners($where= NULL, $limit= NULL, $order= NULL, $group_by= NULL)
    {
    	if (!isset($where))
    	{
    		$where= array(
            "banner_area"=> $this->session->userdata('area_filter'));
    	}
        
        if (isset($limit))
    	{
    		$this->db->limit($limit);
    	}
        
		$select= array(
            $this->_table.".*",
            'page_banners_areas.title AS banner_area_name',
            'page_banners_areas.width',
            'page_banners_areas.height',
            'pages.title AS page_title',
            "page_banners_link.type AS link_type",
            'pages.uri',
        );
		$join= array(
			'page_banners_areas'=> $this->_table.".banner_area= page_banners_areas.slug",
			'page_banners_link'=> $this->_table.".id= page_banners_link.to_id",
			'pages'=> "pages.id= page_banners_link.from_id"
		);
        
        if (isset($group_by))
        {
            $this->db->group_by($group_by);
        }
        
        if (empty($order))
        {
            $order= array("the_order"=>"ASC", "page_title"=>"ASC");
        }
				
        foreach ($where as $field => $value) {
			$this->db->where($field, $value);
		}

        foreach ($order as $field => $value) {
            $this->db->order_by($field, $value);
        }

		$this->db->select($select);

		foreach ($join as $join_table => $value) {
			$this->db->join($join_table, $value, 'left');
		}

		$query = $this->db->get($this->_table);
		#$query = $this->get();
		
		return $query;
    }
    
    
    function get_custom_banner_data($where, $module)
    {
       $module= strtolower($module);
       switch($module)
       {
           case 'firesale':
               $select= array("firesale_categories.slug AS title");
               $order= array("firesale_categories.slug"=>"ASC");
               $join= array(
                    'page_banners_areas'=> $this->_table.".banner_area= page_banners_areas.slug",
                    'page_banners_link'=> $this->_table.".id= page_banners_link.to_id",
                    'firesale_categories'=> "firesale_categories.id= page_banners_link.from_id"
                );
            break;
        
            case 'polls':
               $select= array("poll_categories.slug AS title");
               $order= array("poll_categories.slug"=>"ASC");
               $join= array(
                    'page_banners_areas'=> $this->_table.".banner_area= page_banners_areas.slug",
                    'page_banners_link'=> $this->_table.".id= page_banners_link.to_id",
                    'poll_categories'=> "poll_categories.id= page_banners_link.from_id"
                );
            break;
        
            default:
                return NULL;
       }
      	
        foreach ($where as $field => $value) {
			$this->db->where($field, $value);
		}

        foreach ($order as $field => $value) {
            $this->db->order_by($field, $value);
        }

		foreach ($join as $join_table => $value) {
			$this->db->join($join_table, $value, 'left');
		}

        $this->db->select($select);
		$query = $this->db->get($this->_table);
	
		return $query;
    }
    
    
	function get_default_form_values($id)
	{
		$config= $this->config->item('banners');
		$config_skip= array('upload_img', 'page_links[]', 'banner_link_internal');
		$form_values= new stdClass();
		if ($_POST)
		{
            foreach ($config as $rule)
            {
                if (!in_array($rule['field'], $config_skip))
                {
                    $form_values->{$rule['field']} = $this->input->post($rule['field']); // suppress img-elements, username and password errors
                }
                $form_values->banner_link_internal= $this->input->post('banner_link_internal');
                
            }
            
        }
        else 
        {
            $where= array($this->table_name().'.id'=> $id);
            $query = $this->get_banners($where);
            if ($query->num_rows()== 0)
            {
                // set up defaults
                foreach ($config as $rule)
                {
                    if (!in_array($rule['field'], $config_skip))
                    {
                        $form_values->{$rule['field']} = '';
                    }
                    
                
                        $form_values->banner_link_type= '0';
                        $form_values->banner_link_internal= '';
                   
                }
            }
            else 
            {
                $records= $query->result();
                // CAREFUL - this can be MORE than 1 record!!!
                #$row= $query->result()->row();
                #$row= $query->result();
                foreach ($records as $row)
                {
                    foreach ($config as $rule)
                    {
                        if (!in_array($rule['field'], $config_skip))
                        {
                            $form_values->{$rule['field']} = $row->{$rule['field']}; // suppress img-elements, username and password errors
                        }


                    }
                    switch ( $form_values->banner_link_type)
                    {
                        case '0': // not set
                            $form_values->banner_link_internal= '';
                        break;

                        case '1': // none
                            $form_values->banner_link_internal= '';
                        break;

                        case '2': // banner_link_internal
                            $form_values->banner_link_internal= $form_values->banner_link;
                        break;

                        case '3': // external
                            $form_values->banner_link_internal= '';
                        break;

                        default: 
                            $form_values->banner_link_internal= '';
                    }
                    break;
                }
            }
        }
        return $form_values; 
	}
    
    
    
	function check_link($link_from=0, $link_to=0, $link_type='page')
	{
		$where= array(
			'from_id'=> $link_from,
			'to_id'=> $link_to,
			'type'=> $link_type
		);
		
		$this->db->where($where);
		$this->db->from('page_banners_link');
		return $this->db->count_all_results();	
	}
	
	function get_images()
	{
		$select= array('link_img', 'title');
		$where= array('status >'=> '0');
		$order= array('title'=>'ASC');
		return $this->get_drop_down('page_banners', $where, $order, $select);
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
			$data['slug'] = url_title($data[$title_to_slug], 'dash', TRUE);	
		}
        switch ($this->input->post('banner_link_type'))
        {
            case '0': // not sett - show all options
            case '1':
                $data['banner_link']= '';
            break;
            
            case '2':
                $data['banner_link']= $this->input->post('banner_link_internal');
            break;
                
            case '3':
               //
            break;   
        }
        unset($data['banner_link_internal']);
            
        
		return $data;
	}
	
    
    public function update_order($id, $order)
    {
       return $this->update($id, array("{$this->_table}.the_order" => $order), TRUE);
    }
    
	/**
	 * Create a new site's folder set
	 *
	 * return TRUE on success or array of failed folders
	 *
	 * @param	string	$new_ref	The new site ref
	 * @return	boolean
	 */
	function _make_folder($location, $new_ref= 'default')
	{
		$this->load->helper('file');
		#$unwritable = array();
		
	
		//check perms and log the failures
		if ( ! is_really_writable($location))
		{
			return FALSE; #$unwritable[] = $location.'/'.$new_ref.'/index.html';
		}
		// it's writable, time to create
		else
		{
			#if ( ! is_dir($location.'/'.$new_ref))
			if ( ! is_dir($new_ref))
			{
				#@mkdir($location.'/'.$new_ref, 0777, TRUE);
				@mkdir($new_ref, 0777, TRUE);
				#if (write_file($location.'/'.$new_ref.'/index.html', ''))
				if (write_file($new_ref.'/index.html', ''))
				{
					return TRUE;
				}else
				{
					return FALSE;
				}
			}
			return TRUE;
		}
	
		#return (count($unwritable) > 0) ? FALSE : TRUE;
	}
    
    function module_check()
    {
		$dirs= array(
			UPLOAD_PATH.'banners',
			UPLOAD_PATH.'banners/thumbs',
			);
		
		foreach($dirs as $dir)
		{
			if(! is_dir($dir))
			{
				#$location= 'uploads/'.SITE_REF;
				if ($this->_make_folder(UPLOAD_PATH, $dir))
				{
					$this->template->set('messages', array('success' => 'New folders created OK'));
				}
				else
				{
					$this->template->set('messages', array('error' => 'FAILED to create folders. Please create manually. &quot;'.$dir.'&quot;'));//$msg.= 'Please create the folder: &quot;'.$dir.'&quot;<br/>';
				}
			}
		
		}		
    }
    
    
    public function check_for_upgrade()
    {
        $msg= ''; 
        $this->load->model('modules/module_m', 'core_model');
        $info = $this->core_model->get($this->module);
        if ($info['current_version']<> $info['version'])
        {    
            $func= 'console';
            $msg= 'This module requires upgrading. Current version: '.$info['current_version'].', Latest version: '.$info['version'];
            if (function_exists($func)) 
            {
                $func($msg);
            }   
        }
        return $msg;
    }
}