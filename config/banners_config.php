<?php
// Configuration settings and variables for the Banners module
$config['banners_upload_path'] 	= UPLOAD_PATH.'banners/';
$config['link_types']= array('1'=>'none', '2'=>'internal', '3'=>'external');
$config['status_options']= array('1'=>'Enabled', '0'=>'Disabled');

$config['banners_thumb_dims']= array(
	'small'=> array('w'=>80, 'h'=>80)
	);

$config['banner_thumbs_config'] = array(
        'upload_path'=> UPLOAD_PATH.'banners/',
		'allowed_types'=> 'jpg|png',
		'max_size'=> '81920',
		'max_filename'=> '490'
        );
        
$config['admin_url'] 	= 'admin/banners/';