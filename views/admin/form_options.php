<div class="form_inputs" id="banner-options">
    <ul>
        <li>
            <label for="title"><?php echo lang('banners_title').' <span>*</span>';?></label>
            <div class="input"><?php echo form_input('title', set_value('title', $form_values->title), 'maxlength="255" class="text" id="title"'); ?></div>
        </li><li>
            <label for="banner_area"><?php echo lang('banners_area').' <span>*</span>'; ?></label>
            <div class="input"><?php echo form_dropdown('banner_area', $banner_areas, set_value('banner_area', $this->session->userdata('area_filter')), 'id="banner_area"'); ?></div>
        </li><li>
                <label for="upload_img">
                <?php echo lang('banners_image'); 
                if (empty($form_values->link_img))
                {
                    print ' <span>*</span>';
                }
                ?></label>
                <div class="input"><input type="file" name="upload_img" id="upload_img" /></div>
            </li>
            <?php
            if (!empty($form_values->link_img))
            {	
            ?>
            <li>
                <label><?php echo lang('banners_current_image'); ?></label>
                <?php  
                echo form_hidden('link_img', $form_values->link_img);
                $tmp= $this->config->item('banners_thumb_dims');        
                echo anchor($this->config->item('banners_upload_path').$form_values->link_img, img(image_thumb($this->config->item('banners_upload_path').$form_values->link_img, $tmp['small'])));
                ?>
                <br/>
            </li>
            <?php
            }
            ?>

            <li>
                <label for="alt" style="width:100px;"><?php echo lang('banners_alt'); ?></label>
                <div class="input"><?php
				$atts= array(
					'name'=> 'alt', 
					'value'=> set_value('alt', $form_values->alt), 
					'rows'=> 3,
					'class'=> 'wysiwyg-simple',	
					'id'=> 'alt');
				echo form_textarea($atts); ?></div>
            </li>
            <li>
                <label for="banner_link_type"><?php echo lang('banners_link_type').' <span>*</span>';?></label>
                <div class="input"><?php
                $attributes= 'id="banner_link_type"';
                echo form_dropdown('banner_link_type', $this->config->item('link_types'), set_value('banner_link_type', $form_values->banner_link_type), $attributes);
                ?></div>
            </li>
            <?php
            switch (set_value('banner_link_type', $form_values->banner_link_type))
            {
                case '0': // not set
                    $display_internal= 'none';
                    $display_external= 'none';
                    $display_target= 'none';
                break;
                case '1':
                    $display_internal= 'none';
                    $display_external= 'none';
                    $display_target= 'none';
                break;
                case '2':
                    $display_internal= 'block';
                    $display_external= 'none';
                    $display_target= 'none';
                break;
                case '3':
                    $display_internal= 'none';
                    $display_external= 'block';
                    $display_target= 'block';
                break;
                default:
                    die(lang('banners:missing_link_type_option'));
            }
            # uncomment these 2 if updating from old
            #$display_internal= 'block';
            #$display_external= 'block';
            ?>
            <li id="li-banner-1" style="display:<?php echo $display_internal; ?>">
                <label for="banner_link_internal"><?php echo lang('banners_internal_link').' <span>*</span>'; ?></label>
                <div class="input"><?php 
                #echo $form_values->banner_link.'<br/>';
                #echo $form_values->banner_link_internal.'<br/>';
                $attributes= 'id="banner_link_internal"';
                echo form_dropdown('banner_link_internal', $drop_down_pages, set_value('banner_link_internal', $form_values->banner_link_internal), $attributes); ?>

                <em> a link on THIS site</em></div>
            </li>

            <li id="li-banner-2" style="display:<?php echo $display_external; ?>">
                <label for="banner_link"><?php echo lang('banners_link').' <span>*</span>'; ?></label>
                <div class="input"><?php echo form_input('banner_link', set_value('banner_link', $form_values->banner_link), 'maxlength="255" class="text" id="banner_link"'); ?>
                <em> a full url, e.g. http://www.example.com</em></div>
            </li>

            <li id="li-banner-3" style="display:<?php echo $display_target; ?>">
                <label for="banner_target"><?php echo lang('banners_target').' <span>*</span>'; ?></label>
                <div class="input"><?php echo form_dropdown('banner_target', array("_self"=>"Current Window", "_blank"=>"New Window","_parent"=>"Parent"), $form_values->banner_target, 'id="banner_target"'); ?></div>
            </li>
            
            <li>
                <label for="status"><?php echo lang('banners_status').' <span>*</span>'; ?></label>
                <div class="input"><?php echo form_dropdown('status', $this->config->item('status_options'), set_value('status', $form_values->status), 'id="status"'); ?></div>
            </li>
        </ul>
		
</div>