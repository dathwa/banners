<section class="title">
	<h4>Add/Edit <?php echo $this->module_details['name']; ?></h4>
</section>
<section class="item">
<?php 
echo form_open_multipart($this->uri->uri_string(), 'id="fbanners" class="crud"');
?>
<div class="tabs">
    <ul class="tab-menu">
		
		<li><a href="#banner-pages"><span><?php echo lang('banners:pages');?></span></a></li>
        <?php
        if (!empty($modules_new))
        {
            foreach ($modules_new as $module) {
                printf('<li><a href="#banner-%s"><span>Associated %s</span></a></li>', $module, ucwords($module));
            }
        }
        ?>
        <li><a href="#banner-options"><span><?php echo lang('banners:options');?></span></a></li>
	</ul>
    
    <div class="form_inputs" id="banner-pages">
        <ul>
            <li><label><?php echo lang('banners_pages'); ?> <span>*</span></label>
                <div style="float:left;width:300px;">
            <?php
                foreach($pages as $page)
                {

                    if (isset($_POST['page_links']) && is_array($_POST['page_links']))
                    {
                        $checked = (in_array($page->id, $_POST['page_links'])) ? TRUE : FALSE;
                    }
                    else
                    {
                        $checked = ($this->model_m->check_link($page->id, $id, 'page')==1) ? TRUE : FALSE;
                    }
                    echo form_checkbox('page_links[]', $page->id, $checked).' '.$page->title.'<br />';
                } // endforeach;
            ?>
                </div>
            </li>
        </ul>
    </div>
    
    <?php
    
    if (!empty($modules_new))
    {
        foreach ($modules_new as $module) 
        {
            
        ?>
    <div class="form_inputs" id="banner-<?php echo $module; ?>">
        <ul>
            <li><label>Associated <?php echo ucwords($module); ?></label>
                <div style="float:left;width:300px;">
            <?php
            $c_links= $custom_modules[$module];
                foreach($c_links as $link)
                {

                    if (isset($_POST[$module.'_links']) && is_array($_POST[$module.'_links']))
                    {
                        $checked = (in_array($link->id, $_POST[$module.'_links'])) ? TRUE : FALSE;
                    }
                    else
                    {
                        $checked = ($this->model_m->check_link($link->id, $id, $module)==1) ? TRUE : FALSE;
                    }
                    echo form_checkbox($module.'_links[]', $link->id, $checked).' '.$link->title.'<br />';
                } // endforeach;
            ?>
                </div>
            </li>
        </ul>
    </div>
    <?
        }
    }
    ?>
    
    <?php $this->load->view('admin/form_options'); ?>
    
    <div class="buttons align-right padding-top">	    	
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save_exit', 'save', 'cancel') )); ?>
    </div>
    
</div>  
<?php echo form_close(); ?>
</section>