<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Plugin_Banners extends Plugin
{
    function __construct()
    {
		$this->load->config('banners/banners_config');
		$this->load->model('banners/banners_m');
		#$this->default = site_url($this->config->item('banners_upload_path').'banner-default.png');
        $this->load->helper(array('image_thumb'));
    }
    
    
    /***************************************************************************
     * 
     *  @usage:
     *  {{ pyro:banners:page_banners area="top" mode="random" limit="1" default="banner-default.png" }}
     *    {{ image_link }}
     *    <h1>{{ title }}</h1>
     *    <p>{{ alt }}</p> 
     *    <p>{{ /pyro:banners:page_banners }}</p> 
     * 
     ***************************************************************************/
    function page_banners()
    {
        $banners= NULL;
        $mode = $this->attribute("mode" ,'default'); // default|random
        $area = $this->attribute("area" ,'default');
        $limit = $this->attribute("limit", NULL);   
        $page_uri= uri_string() ? uri_string(): 'home';      
        
       
        $module= $this->attribute("module", 'page');   
        
        $where= array(
                'page_banners.status'=>1, 
                "banner_area"=>$area,
                "page_banners_link.type"=> $module,
            );
        
        if ($module== 'page')
        {
            if ($page_uri== 'home')
            {
                $where['pages.is_home']= 1;
            }
            else 
            {
                $where['pages.uri']= $page_uri;
            }
            
        }else
        {
            $where['page_banners_link.from_id']= $this->get_module_item_id($module, end($this->uri->segments));
        }
        
        if ($mode== 'random')
        {
            $order= array("title"=>"RANDOM");
        }
        else
        {
            $order= NULL;
        }
        $query = $this->banners_m->get_banners($where, $limit, $order);
       
        #$query= $this->pyrocache->model('banners_m', 'get_banners', array($where, $limit), 30);

		if ($query)
		{                        
            $banners= $query->result();
			foreach($banners as $banner)
			{
                if ($banner->link_img)
                {
                    $banner->alt= empty($banner->alt)? $banner->title: $banner->alt;
                    $dims= array('w'=>$banner->width, 'h'=>$banner->height);
                    $img= image_thumb($this->config->item('banners_upload_path').$banner->link_img, $dims);
                    // strip html and convert quoptes, etc.
                    //$img_alt= htmlspecialchars(strip_tags($banner->alt));
                    $image_properties = array(
                        'src' => $img,
                        'alt' => $banner->title,
                        'class' => 'pbanner page_banner_'.$area
                    );
					
                    if (!empty($banner->width))
                    {
                        $image_properties['width']= $banner->width;
                    }
                    if (!empty($banner->height))
                    {
                        $image_properties['height']= $banner->height;
                    }
					
                    $banner->image= img($image_properties);                        
                    switch($banner->banner_link_type)
                    {
                        case '0':  // not set
                        case '1':  // no link
                            $banner->link= '';
                            $banner->title_link= $banner->title;
                            $banner->image_link= $banner->image;
                        break;
                        case '2': // internal link
                            $banner->link= $banner->banner_link;
                            $banner->title_link= anchor($banner->banner_link, $banner->title, 'target="'.$banner->banner_target.'"');
                            $banner->image_link= anchor($banner->banner_link, $banner->image, 'target="'.$banner->banner_target.'"');
                        break;
                        case '3': // external link
                            $banner->link= $banner->banner_link;
                            $banner->title_link= '<a href="'.$banner->banner_link.'" target="'.$banner->banner_target.'">'.$banner->title.'</a>';
                            $banner->image_link= '<a href="'.$banner->banner_link.'" target="'.$banner->banner_target.'">'.$banner->image.'</a>';
                        break;
                    }
                }
			} // endforeach;        
		}
        
		return $banners;
    }
    
    private function get_module_item_id($module, $uri)
    {
        $id= 0;
        $module= strtolower($module);
        switch ($module)
        {
            case 'firesale':
                // now supporting firesale module
                $this->load->model('firesale_m', 'custom_m');
                $id = $this->custom_m->get_category_id($uri);
            break;
        
            case 'polls':
                $this->load->model($module.'/'.$module.'_m', 'custom_m');
                $id = $this->custom_m->get_category_id($uri);
            break;
                
            case 'locations':
                $this->load->model($module.'/'.$module.'_m', 'custom_m');
                #$id = $this->custom_m->get_banner_links();  
            break;
        
            default:
                // module is not supported
        }
        return $id;    
    }
}

/* End of file plugin.php */