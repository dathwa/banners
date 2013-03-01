<section class="title">
	<h4>Add/Edit <?php echo $section; ?></h4>
</section>
<section class="item">
<?php 
echo $form_values->old_slug;
echo form_open($this->uri->uri_string(), 'class="crud"'); 
echo form_hidden('old_slug', set_value('old_slug', $form_values->old_slug));
?>
<div class="form_inputs">
<ul>
        <li>
            <label for="title"><?php echo lang('banners_areas_name').' <span>*</span>';?></label>
            <?php echo form_input('title', set_value('title', $form_values->title), 'maxlength="255" class="text" id="title"'); ?>
        </li>
		
		<li>
            <label for="slug"><?php echo lang('banners_areas_slug').' <span>*</span>';?></label>
            <?php echo form_input('slug', set_value('slug', $form_values->slug), 'readonly="readonly" maxlength="255" class="text" id="slug"'); ?>
        </li>
		
		<li>
            <label for="width"><?php echo lang('banners_areas_width');?></label>
            <?php echo form_input('width', set_value('width', $form_values->width), 'maxlength="255" class="text" id="width"'); ?> <em><?php echo lang('banners_areas_ignore_dimension')?></em>
        </li>
		
		<li>
            <label for="height"><?php echo lang('banners_areas_height');?></label>
            <?php echo form_input('height', set_value('height', $form_values->height), 'maxlength="255" class="text" id="height"'); ?> <em><?php echo lang('banners_areas_ignore_dimension')?></em>
        </li>        
</ul>
</div>
<div class="buttons">    	
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save_exit', 'save', 'cancel') )); ?>
</div>
<?php echo form_close(); ?>
</section>