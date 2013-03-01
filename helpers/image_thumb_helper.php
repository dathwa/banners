<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * usage: <?=image_thumb('assets/images/picture-1/picture-1.jpg', 50, 50)?>
 * @url http://jrtashjian.com/2009/02/image-thumbnail-creation-caching-with-codeigniter/
 * @args $dims= array('w'=>240, 'h'=>180)
 * 
 * @dodo: return error as image
 */


function image_thumb($source_image, $dims= NULL, $config= NULL)
{
    // Get the CodeIgniter super object
    $CI =& get_instance();

    $error_pic= APPPATH.'themes/pyrocms/img/admin/icons/16/exclamation.png';
    if (empty($config))
    {
    	$config= array(
    		'image_library'=> 'gd2',
    		'quality'=> '90',
    		'maintain_ratio'=> TRUE,
    		'master_dim'=> 'width'
    	);
    }
    
    
    if (empty($dims))
    {
    	log_message('error', __FUNCTION__.': missing args');
		$image_thumb= $error_pic;
    }
    else 
    {
        // Path to image thumbnail
        $path_info = pathinfo($source_image);
        if (empty($path_info['extension']))
        {
            $image_thumb= $error_pic;
        }
        else
        {
            //
            $image_thumb = $path_info['dirname'].'/thumbs/'.$path_info['filename'].'_'.$dims['w'].'_'.$dims['h'].'.'.$path_info['extension'];
            if(! file_exists($image_thumb))
            {
                // LOAD LIBRARY
                $CI->load->library('image_lib');

                // CONFIGURE IMAGE LIBRARY
                $config['source_image']     = $source_image;
                $config['new_image']        = $image_thumb;
                $config['width']            = $dims['w'];
                $config['height']           = $dims['h'];
				// @dodo: this is too random!
                #$config['master_dim']= $config['width']> $config['height'] ? 'height': 'width';

                $CI->image_lib->initialize($config);

                if ( ! $CI->image_lib->resize())
                {
                    log_message('error', __FUNCTION__.': '.'$image_thumb: '.$image_thumb.' - '.$CI->image_lib->display_errors());
                    $image_thumb= $error_pic;
                }
                //$CI->image_lib->watermark();
                $CI->image_lib->clear();
            }
        }
    }
    return site_url($image_thumb);
}