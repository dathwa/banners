<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Areas extends Admin_Controller {
	
	protected $section= 'areas';
	
	var $table				=	"page_banners_areas";
	
	public function __construct()
	{
		parent::__construct();
		$this->load->config('banners_config');
		$this->load->library('form_validation');
		$this->config->load('form_validation'); // usually loaded automatically. but i need it to loop thru the rules
		$this->load->helper('html');
		$this->load->language('banners');
		$this->load->model('areas_m', 'model_m');
        
        $this->template
			->set('section', $this->section);
	}


	public function index()
	{	
		role_or_die('banners', 'view_areas');
        $data= new stdClass();
		$data->records = $this->model_m->get_all('title', 'ASC');
		$this->template
			->append_css('module::style.css')
            ->append_js('module::areas.js')
			->build('admin/areas/index', $data);
	}
	

	public function edit($id=0)
	{        
		role_or_die('banners', 'admin_areas', 'admin/banners/areas');
		$data= new stdClass(); 
		if ($_POST)
		{
			if ($this->form_validation->run('areas'))
		    {
				$update = $this->model_m->get_posted('areas');	
				if (empty($id))
				{
					$this->model_m->insert($update);
					$this->session->set_flashdata('success', "Added record OK");
				}
				else 
				{
					$this->model_m->update($id, $update);
					$this->session->set_flashdata('success', "Updated record OK");
                    
                    
                    # Until I normalise slug, update the area slug in the banners table
                    $this->load->model('banners_m');
                    $this->banners_m->update_by('banner_area', $this->input->post('old_slug'), array('banner_area' => $this->input->post('slug')));
                    // unset the session
                    $this->session->unset_userdata('area_filter');
				}
                
				// Success
				
		    	if ($this->input->post('btnAction')== 'save_exit')
                {               
                    redirect($this->config->item('admin_url').'areas', 'location');
                }
                else
                {
                    redirect(current_url(), 'location');
                }
      
		    }
			else
			{
				#$this->session->set_flashdata('error', validation_errors());
			}			
		}

        $data->form_values	= $this->model_m->get_default_form_values($id);
		$this->template
        ->append_js('module::areas.js')
		->build('admin/areas/form',$data);
	}
	
	
	
	
	public function delete($id = 0)
	{
		role_or_die('banners', 'admin_areas', 'admin/banners/areas');
		// Attention! Error of no selection not handeled yet.
		$ids = ($id) ? array($id) : $this->input->post('action_to');
		$deleted_ids = array();
		// Go through the array of slugs to delete
		foreach ($ids as $id)
		{
			$deleted_ids[] = $this->model_m->delete($id);
		}
        // unset the session
        $this->session->unset_userdata('area_filter');
		redirect($this->config->item('admin_url').'areas', 'location');
	}
	

	
	public function action()
	{
		role_or_die('banners', 'admin_areas', 'admin/banners/areas');
		// Determine the type of action
		switch($this->input->post('cmd'))
		{
			case 'delete':
				$this->delete();
			break;
			
            case 'set_default':
				$this->model_m->update_default($this->input->post('is_default'));
			break;
        
			default:
				redirect($this->config->item('admin_url').'areas');
			break;
		}
		redirect($this->config->item('admin_url').'areas');
	}
}
?>
