<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {
	
	protected $section= 'banners';
	
	public function __construct()
	{
		parent::__construct();		
		role_or_die('banners', 'admin_banners');
		
		$this->load->config('banners_config');
		$this->load->helper(array('html', 'image_thumb'));
		$this->load->language('banners');
		$this->load->library('form_validation');
		$this->config->load('form_validation'); // usually loaded automatically. but i need it to loop thru the rules
		
		$this->load->model('banners_m', 'model_m');
		$this->load->model('firesale_m', 'firesale_m');
		$this->load->model('page_banners_link_m');
		$this->load->model('areas_m');
		$this->load->model('pages_m');		  
	}

    
    
	public function index()
	{
		$data= new stdClass();
		$this->model_m->module_check();
        $data->messages['notice']= $this->model_m->check_for_upgrade();
        
        switch($this->input->post('cmd'))
		{
			case 'set_filter_area':
				$this->session->set_userdata(array('area_filter'=> $this->input->post('banner_area')));
                redirect($this->uri->uri_string());
			break;
		}

        if (!$this->session->userdata('area_filter'))
        {
			$query= $this->areas_m->get_by(array('is_default'=> 1));	
			if ($query)
			{
				$tmp= $query;
				$this->session->set_userdata(array('area_filter'=> $tmp->slug));
			}
			else
			{
				$this->session->set_flashdata('error', 'Please set a default area');
				redirect($this->config->item('admin_url').'areas', 'location');
			}
        }
        $where= array("banner_area"=> $this->session->userdata('area_filter'));
        $limit= NULL;
        $order= NULL;
		$group_by= 'page_banners.id';
		$data->records = $this->model_m->get_banners($where, $limit, $order, $group_by)->result();	
		
        $data->banner_areas = $this->areas_m->get_banner_areas();
		
		$this->template
        	->append_js('module::banners.js')
			->append_css('module::style.css')
			->build('admin/index', $data);
	}
	
	
    /**
	 * Create method, creates a new category via ajax
	 * 
	 * @return void
	 */
	public function ajax_get_associated()
	{
        $id= $this->input->post('id');
        if (empty($id))
        {
            $id= 0;
        }
        $where= array('page_banners.id'=> $id, 'page_banners_link.type'=> 'page');
 
        $msg= array();
        $order= array("pages.order"=>"ASC");     
        $query = $this->model_m->get_banners($where, NULL, $order);
        #$query= $this->pyrocache->model('banners_m', 'get_banners', array($where, $limit), 30);

		if ($query)
		{                        
            $banners= $query->result();
			foreach($banners as $banner)
            {
                $msg[]= 'page: '.$banner->uri; 
            }
        }
		
        $tmp= trim($this->settings->banners_modules);
        $modules= empty($tmp) ? array(): explode(',',$tmp);
        
        foreach ($modules as $module) 
        { 
            $where= array('page_banners.id'=> $id, 'page_banners_link.type'=> $module);
            $query= $this->model_m->get_custom_banner_data($where, $module);
            if ($query)
            {                        
                $result= $query->result();
                foreach($result as $row)
                {
                    $msg[]= $module.': '.$row->title; 	
                }
            }
        }
                    
        echo json_encode($msg);
	}
    
    
    
	public function edit($id=0)
	{	
        $config_validation= $this->config->item('banners');

        $tmp= trim($this->settings->banners_modules);
        $modules= empty($tmp) ? NULL: explode(',',$tmp);
        
        $this->form_validation->set_rules(array_merge($config_validation, array(
			'slug' => array(
				'field' => 'upload_img',
				'label' => 'lang:banners_image',
				'rules' => 'callback__image_check['.$id.']'
			),
		)));
        
		$data= new stdClass();
		if ($_POST)
		{
			if ($this->form_validation->run('banners'))
		    {
				$update = $this->model_m->get_posted('banners');
                
				$update['update_time'] = now();
			
                if (! empty($_FILES['upload_img']['name']))
                {
                    // presume it passed all the error checks
                    $this->load->library('upload', $this->config->item('banner_thumbs_config'));
                    $this->upload->do_upload('upload_img');
                    $file_data = $this->upload->data();
                    $update['link_img'] = $file_data['file_name'];
                }
                else 
                {
                    unset($update['link_img']);
                }
                
                unset($update['upload_img']);
                
                
				$page_links= $this->input->post('page_links');
				unset($update['page_links[]']);
				// Success
				if (empty($id))
				{
					$id= $this->model_m->insert($update);
					$this->session->set_flashdata('success', "Added record OK");
				}
				else 
				{
					$this->model_m->update($id, $update);			
					// temporarily remove existing links for pages for this id
                    $this->page_banners_link_m->delete_by(array('to_id' => $id, 'type'=>'page'));
                    
                    // temporarily remove existing links for modules for this id
                    // custom modules
                    if (!empty($modules))
                    {
                        foreach ($modules as $module) 
                        {
                            #$this->load->model($module.'/'.$module.'_m', 'custom_m');
                            $this->page_banners_link_m->delete_by(array('to_id' => $id, 'type'=>$module));
                        }
                    }

					$this->session->set_flashdata('success', "Updated record OK");  
				}
				
				if($page_links)
				{
					foreach($page_links as $banner_id)
                    {
						$this->page_banners_link_m->insert(array('from_id'=>$banner_id, 'to_id'=> $id, 'type'=>'page'));
                    }
				}					
				
                // custom modules
                if (!empty($modules))
                {
                    foreach ($modules as $module) 
                    {
                        $custom_links= $this->input->post($module.'_links');
                        if($custom_links)
                        {
                            foreach($custom_links as $banner_id)
                            {
                                $this->page_banners_link_m->insert(array('from_id'=>$banner_id, 'to_id'=> $id, 'type'=> $module));
                            }
                        }	
                    }
                }
                    
				$this->session->set_userdata(array('area_filter'=> $this->input->post('banner_area')));
                    
				if ($this->input->post('btnAction')== 'save_exit')
                {               
                    redirect($this->config->item('admin_url'), 'location');
                }
                else
                {
                    redirect(current_url(), 'location');
                }				
		    }			
		}
   
		$data->form_values= $this->model_m->get_default_form_values($id);
		
		$data->id= $id;
        $data->banner_areas =       $this->areas_m->get_banner_areas();
        
        $data->custom_modules= $this->get_custom_modules($modules);
        $data->modules_new= $modules;
        
		$data->drop_down_pages =    $this->pages_m->get_drop_down_pages();
		$data->pages =              $this->pages_m->get_pages();
		$this->template
        	->append_js('module::banners.js')
			->append_metadata( $this->load->view('fragments/wysiwyg', $this->data, TRUE) )
			->build('admin/form',$data);
	}
	
		
	public function delete($id = 0)
	{
		
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		$deleted_ids = array();
		// Go through the array of slugs to delete
		foreach ($ids as $id)
		{
			$delete_details = $this->model_m->get('default', array('id'=>$id));
			foreach ($delete_details as $record):
				$this->delete_images($record->link_img);				
			endforeach;
			
			$deleted_ids[] = $this->model_m->delete($id);
			#$deleted_ids[] = $this->model_m->update('default', array('id'=>$id), array('status'=>-1));
		}
		
		// Some pages have been deleted
		if(!empty($deleted_ids))
		{
			// Only deleting one page
			if( count($deleted_ids) == 1 )
			{
				$this->session->set_flashdata('success', "Successfully deleted record");
			}			
			else // Deleting multiple pages
			{
				$this->session->set_flashdata('success', "Successfully deleted records");
			}
			
			$this->delete_image_thumbs();
		}
			
		else // For some reason, none of them were deleted
		{
			$this->session->set_flashdata('notice', "There was an error and no records were deleted");
		}
		redirect($this->config->item('admin_url'), 'location');
	}
	
	private function delete_images($img)
	{
		$this->load->helper('file');
		if (!empty($img))
		{
			unlink($this->config->item('banners_upload_path').$img);
		}
	}
	
	private function delete_image_thumbs()
	{
		$this->load->helper('file');
		if (is_dir($this->config->item('banners_upload_path').'thumbs'))
		{
			delete_files($this->config->item('banners_upload_path').'thumbs'); 
		}
	}
	
	public function action()
	{
		// Determine the type of action
		switch($this->input->post('btnAction'))
		{
			case 'activate':
				$this->activate();
			break;
			case 'deactivate':
				$this->deactivate();
			break;
			case 'delete':
				$this->delete();
			break;
			default:
				redirect($this->config->item('admin_url'), 'location');
			break;
		}
		redirect($this->config->item('admin_url'), 'location');
	}
	
	public function activate($id=0)
	{
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		$activated_ids = array();
		foreach ($ids as $id) {
			$activated_ids = $this->model_m->update($id, array('status'=>1));
		}
		
		if (!empty($activated_ids))
		{
			if( count($activated_ids) == 1 )
			{
				$this->session->set_flashdata('success', "Successfully activated record");
			}			
			else 
			{
				$this->session->set_flashdata('success', "Successfully activated records");
			}
		}
		else // For some reason, none of them were deleted
		{
			$this->session->set_flashdata('notice', "There was an error and no records were activated");
		}
		
	}
	
	public function deactivate($id=0)
	{
	
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		$deactivated_ids = array();
		foreach ($ids as $id) {
			$deactivated_ids[] = $this->model_m->update($id, array('status'=>0));
		}

		if (!empty($deactivated_ids))
		{
			if( count($deactivated_ids) == 1 )
			{
				$this->session->set_flashdata('success', "Successfully deactivated record");
			}			
			else 
			{
				$this->session->set_flashdata('success', "Successfully deactivated records");
			}
		}
		else // For some reason, none of them were deleted
		{
			$this->session->set_flashdata('notice', "There was an error and no records were deactivated");
		}
		
	}
    

    
    public function _link_type_check($str)
	{
        $result= TRUE;
        switch ( $str)
        {
            case '0': // none
                #$this->form_validation->set_message('link_type_check', 'The  field is required.'.$str);
                $this->form_validation->set_message('_link_type_check', lang('banners:link_type_check'));
                $result= FALSE;
            break;
         
            case '1': // none
                $_POST['banner_link']= '';
                #unset($_POST['banner_link_internal']);
            break;
             
            case '2': // banner_link_internal
                 if ($this->input->post('banner_link_internal'))
                 {
                    $_POST['banner_link']= $this->input->post('banner_link_internal');
                    #unset($_POST['banner_link_internal']);
                 }
                 else
                 {
                     #$this->form_validation->set_message('banner_link_type_check', 'The %s field is required');
                     $this->form_validation->set_message('_link_type_check', 'The '.lang('banners_internal_link').' field is required');
                     $result= FALSE;
                 }
             break;
             
             case '3': // external
                 if ($this->input->post('banner_link'))
                 {
                     #unset($_POST['banner_link_internal']);
                 }
                 else
                 {
                     $this->form_validation->set_message('_link_type_check', 'The '.lang('banners_link').' field is required');
                     $result= FALSE;
                 }
             break;
         }
         
         return $result;
	}
    
    function _image_check($str, $id= NULL)
	{
        // $str is empty cos its a file!
		if ($id> 0 && empty($_FILES['upload_img']['name']))
        {
            // this is an edit and no file uploaded
            return TRUE;
        }
        else
        {
            $this->load->library('upload', $this->config->item('banner_thumbs_config')); 
            if ($this->upload->do_upload('upload_img'))
            {
                /*
                $file_data = $this->upload->data();
                if ($file_data['file_name']!='')
                {
                    $update['link_img'] = $file_data['file_name'];
                }
                else
                {
                    unset($update['link_img']);
                }
                 * 
                 */
                return TRUE;
            }
            else 
            {
                #$this->form_validation->set_message('_image_check', '%s error!'.$this->upload->display_errors());
                $this->form_validation->set_message('_image_check', $this->upload->display_errors());
                return FALSE;
            }
        }
    }
    
    private function get_custom_modules($modules)
    {
        $custom_modules= array();
        if (!empty($modules))
        {
            foreach ($modules as $module) 
            {
                switch ($module)
                {
                    case 'firesale':
                        // now supporting firesale module
                        $custom_modules[$module] = $this->firesale_m->get_firesale_links();
                    break;
                
                    default:
                        $this->load->model($module.'/'.$module.'_m', 'custom_m');
                        $custom_modules[$module] = $this->custom_m->get_banner_links();
                }
            }
        }
        return $custom_modules;
    }
   
    /**
	 * Ajax helper to update the sort/display order
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function update_order()
	{
		$data = $this->input->post('order');
		if(is_array($data))
		{
			$order = 1;
			foreach($data as $id)
			{
				$this->model_m->update_order($id, $order);
				$order++;
			}
		}
	}
}