<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
$config = array(
	'banners' => array(
		array(
			'field' => 'title',
			'label'	=> "lang:banners_title",
			'rules'	=> 'trim|required|max_length[255]'
		),
		array(
			'field' => 'banner_area',
			'label'	=> "lang:banners_area",
			'rules'	=> 'trim|required'
		),
		array(
			'field' => 'page_links[]',
			'label'	=> "lang:banners_pages",
			'rules'	=> ''
		),
		
        array(
			'field' => 'link_img',
			'label' => 'lang:banners_image',
			'rules' => 'trim'
		),
        array(
			'field' => 'status',
			'label' => 'lang:banners_status',
			'rules' => 'required'
		),
		array(
			'field' => 'banner_link_type',
			'label' => 'lang:banners_link_type',
			'rules' => 'callback__link_type_check'
		),
		array(
			'field' => 'banner_link', // banner_link_internal goes here
			'label'	=> "lang:banners_link",
			'rules'	=> 'trim|max_length[255]' // prep_url removed so i can add file refs
		),
		array(
			'field' => 'banner_link_internal', // not actually in the db
			'label'	=> "lang:banners_internal_link",
			'rules'	=> ''
		),
        array(
			'field' => 'banner_target', 
			'label'	=> "banners_target:target",
			'rules'	=> 'required'
		),
        array(
			'field' => 'alt',
			'label'	=> "lang:banners_alt",
			'rules'	=> 'trim'
		),
    ),
	'areas' => array(
		array(
			'field' => 'title',
			'label'	=> "lang:banners_areas_name",
			'rules'	=> 'trim|required|max_length[255]'
		),
		array(
			'field' => 'slug',
			'label'	=> "lang:banners_areas_slug",
			'rules'	=> 'trim|required|max_length[255]'
		),
		array(
			'field' => 'width',
			'label'	=> "lang:banners_areas_width",
			'rules'	=> 'trim|numeric|required'
		),
		array(
			'field' => 'height',
			'label'	=> "lang:banners_areas_height",
			'rules'	=> 'trim|numeric|required'
		),	
     )
);
?>