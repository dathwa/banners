<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
 * @dodo: make it also page orientated. select which banner one wants associated with a page. evert time i add a page i need to go into each banner and associate it.
 * @dodo: use caching
 * @dodo: add is_slider/is_marquee
 * @dodo: this plugin can be hooked into other modules to show banners on a modules sub-pagess - document it...
 * @dodo: flag as warning if $config['index_page'] <> '' - 'index.php' seems to break it
 */
class Module_Banners extends Module {

	public $version = '1.886';
				
	var $tables = array(
		'page_banners'=> 'page_banners',
        'page_banners_areas'=> 'page_banners_areas',
		'page_banners_link'=> 'page_banners_link'
	);

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Banners',
			),
			'description' => array(
				'en' => 'Manage images and button links on your pages.',
			),
			'author' => 'dathwa@gmail.com',
			'frontend' => FALSE,
			'backend' => TRUE,
			'skip_xss' => true,
			'menu' => 'content',
			'roles' => array('admin_banners', 'admin_areas', 'view_areas'),
			'sections' => array(
			    'banners' => array(
				    'name' => 'banners_banners',
				    'uri' => 'admin/banners',
				    'shortcuts' => array(
						array(
							'name' => 'banners_create',
							'uri' => 'admin/banners/edit',
							'class' => 'add'
						),
					),
				),
				'areas' => array(
				    'name' => 'banners_areas',
				    'uri' => 'admin/banners/areas',
				    'shortcuts' => array(
						array(
						    'name' => 'banners_areas_create',
						    'uri' => 'admin/banners/areas/edit',
						    'class' => 'add'
						),
					),
			    ),
			),			
		);
	}

	public function install()
	{
        if(! $this->uninstall())
		{
			return FALSE;
		}
		$tables= array(
			"CREATE TABLE ".$this->db->dbprefix('page_banners')." (
				`id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) DEFAULT NULL,
                `link_img` varchar(255) DEFAULT NULL,
                `status` int(4) DEFAULT '0',
                `update_time` bigint(20) DEFAULT NULL,
                `banner_link_type` int(4) DEFAULT '0',
                `banner_link` varchar(255) DEFAULT NULL,
                `banner_target` varchar(255) DEFAULT NULL,
                `banner_area` varchar(255) DEFAULT NULL,
                `alt` varchar(255) DEFAULT NULL,
                `the_order` int(4) DEFAULT '0',
                `clicks` bigint(20) DEFAULT '0',
                PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			",
			"CREATE TABLE ".$this->db->dbprefix('page_banners_link')." (
				`id` int(11) NOT NULL AUTO_INCREMENT,
                `from_id` bigint(15) DEFAULT NULL,
                `to_id` bigint(15) DEFAULT NULL,
                `type` varchar(255) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			",
			"CREATE TABLE ".$this->db->dbprefix('page_banners_areas')." (
				`id` int(11) NOT NULL AUTO_INCREMENT,
                `slug` varchar(255) NOT NULL,
                `title` varchar(255) DEFAULT NULL,
                `width` int(11) NOT NULL,
                `height` int(11) NOT NULL,
                `is_default` tinyint(4) DEFAULT '0',
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);
        
		$ok= TRUE;
        foreach ($tables as $table) {
			if (! $this->db->query($table))
			{
				$ok= FALSE;
			}
		}
        
        $sql= "INSERT INTO ".$this->db->dbprefix('settings')." (`slug`, `title`, `description`, `type`, `default`, `value`, `options`, `is_required`, `is_gui`, `module`, `order`) VALUES 
				('banners_modules', 'Modules to use in the links', 'Comma separated list of modules to include for links', 'text', '', '', '', '0', '1', 'page_banners', '1')
				;";
        $this->db->query($sql);
        
        
		if ($ok)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
        
	}

	public function uninstall()
	{		
		$result = array();
		foreach ($this->tables as $table) {
			$result[] = ($this->dbforge->drop_table($table)) ? TRUE : FALSE;
		}
		
        $result[]= $this->db->query("DELETE FROM `".$this->db->dbprefix('settings')."` WHERE slug='banners_modules';"); 
        
		if (!in_array(FALSE, $result))
		{
			return TRUE;
		}
	}

	public function upgrade($old_version)
	{
		
		if ($old_version < '1.6')
		{
			#add link 
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` CHANGE `name` `title` varchar(255)
			");
			
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` ADD COLUMN `width` int(11) default NULL AFTER title
			");
			
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` ADD COLUMN `height` int(11) default NULL AFTER width
			");
			
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."`.`default_page_banners_areas` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
			");
	
		
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` DROP PRIMARY KEY
			");
			
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` ADD COLUMN `id` INT NOT NULL FIRST, ADD PRIMARY KEY (`id`)
			");
			
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."`.`default_page_banners_areas` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
			");
	
#?????????????
            #add link 
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` ADD COLUMN `is_default` TINYINT DEFAULT 0 NULL AFTER `height`;
			");
            
            $this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` ADD COLUMN `ststus` TINYINT DEFAULT 1 AFTER `link_img`;
			");
            
			
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."`.`default_page_banners_areas` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
			");
		}

		if ($old_version < '1.72')
		{
			#add link 
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` CHANGE `width` `width` INT(11) DEFAULT 0 NULL, CHANGE `height` `height` INT(11) DEFAULT 0 NULL;
			");
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners')."` ADD COLUMN `banner_area_id` INT DEFAULT 0 NULL AFTER `banner_target`;
			");
			
			$this->db->query("
				DELETE FROM `".$this->db->dbprefix('page_banners')."` WHERE status < 0;
			");
			
		}

 #????????
		if ($old_version < '1.73')
		{
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners')."` ADD COLUMN `banner_link_type` INT DEFAULT 0 NULL AFTER `update_time`;
			");
		}
 
#?????????
        if ($old_version < '1.74')
		{
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners')."` DROP COLUMN `clicks`
			");
		}
        
        if ($old_version < '1.75')
		{
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners_areas')."` CHANGE `is_default` `is_default` TINYINT(4) DEFAULT 0 NOT NULL
			");
		}
 #??????       
        if ($old_version < '1.76')
		{
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners')."` CHANGE `alt` `alt` TEXT
			");
		}
        
        if ($old_version < '1.76')
		{
			$this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners')."` CHANGE `alt` `alt` TEXT
			");
		}
        
        if ($old_version < '1.86')
            $this->db->query("
				ALTER TABLE `".$this->db->dbprefix('page_banners')."` ADD COLUMN `the_order` INT(4) DEFAULT 0 NULL AFTER `alt`;
			"); 
        
        if ($old_version < '1.87')
            $this->db->query("
				UPDATE `".$this->db->dbprefix('settings')."` SET `is_required` = 0 WHERE slug='banners_modules';
			"); 
        
        if ($old_version < '1.88')
            $this->db->query("
				UPDATE `".$this->db->dbprefix('settings')."` SET `module` = 'banners' WHERE module='page_banners';
			"); 
        
		return TRUE;
	}

	private function settings_sql()
	{
		/*
		return "
			INSERT INTO ".$this->db->dbprefix('page_banners')." (`slug`, `title`, `description`, `type`, `default`, `value`, `options`, `is_required`, `is_gui`, `module`, `order`) VALUES 
				('pb_default_area', 'People per page', 'How many people should we show per page?', 'text', '25', '25', '', '1', '1', 'people', '1')
				;
		";
		*/
	}
	public function help()
	{
		// Return a string containing help info
		// You could include a file and return it here.
		return "No documentation has been added for this module.<br/>Contact the module developer for assistance.";
	}
}
/* End of file details.php */