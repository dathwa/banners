<section class="title">
	<h4><?php echo lang('banners_banners'); ?></h4>
</section>
<section class="item">
<?php 
echo form_open($this->uri->uri_string(), 'class="crud"');
echo form_hidden('cmd', 'set_filter_area');
?>
    <ul>
        <li>
            <label for="banner_area">Banner Area Filter</label>
            <?php echo form_dropdown('banner_area', $banner_areas, $this->session->userdata('area_filter'), 'id="banner_area" onchange="this.form.submit()"'); ?>
        </li>
    </ul>
<?php 
echo form_close(); 
?>

<?php
echo form_open($this->config->item('admin_url').'action'); ?>

<?php if ( ! empty($records)): ?>
	<table class="table-list banner-list">
		<thead>
			<tr>
                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                <th><?php echo lang('banners_status');?></th>
                <th><?php echo lang('banners_title');?></th>
                <th><?php echo lang('banners_link_general');?></th>
                <th><?php echo lang('banners_alt');?></th>
                <th><?php echo lang('banners_pages');?></th>
                <th><?php echo lang('banners_photo');?></th>
                <th><?php echo lang('banners_actions');?></th>
			</tr>
		</thead>
		<tbody class="ui-sortable">
		<?php 
        foreach ($records as $record)
        {   
            ?>
            <tr>
                <td class="action-to"><?php echo form_checkbox('action_to[]', $record->id); ?></td>
                <td class="status<?php echo $record->status; ?>">&nbsp;</td>
                <td><?php echo anchor($this->config->item('admin_url').'edit/' . $record->id, $record->title); ?></td>
                <td>
                <?php
                switch($record->banner_link_type)
                {
                    case '0':
                        $tpl= ' ? ';
                    break;
                    case '1':
                        $tpl= ' ';
                    break;
                    case '2':
                        #print anchor(site_url($record->banner_link));
                        #$tpl= anchor(site_url('%'));
                        $tpl= '<a href="'.base_url().'%s">'.$record->banner_link.'</a>';// $record->banner_link;anchor(site_url('%'));
                    break;
                    case '3':
                        $tpl= '<a href="%s">'.$record->banner_link.'</a>';// $record->banner_link;
                    break;
                    default:
                        die(lang('banners:missing_link_type_option'));
                }
                printf($tpl, $record->banner_link);
                ?></td>
                <td><?php echo character_limiter($record->alt, 20); ?></td>
                <td id="td_<?php echo $record->id; ?>">
                    <script type="text/javascript">
                        $(document).ready(function() {
                            get_page_data(<?php echo $record->id ?>);
                        });
                    </script>
                </td>
                <td><?php 
                    if (!empty($record->link_img))
                    {			
                        $tmp= $this->config->item('banners_thumb_dims');
                        echo img(image_thumb($this->config->item('banners_upload_path').$record->link_img, $tmp['small']));
                    }
                    ?>
                </td>
                <td class="buttons buttons-small">
                    <?php echo anchor($this->config->item('admin_url').'edit/' . $record->id, "edit", array('class'=>'btn orange edit')) ?> 
                    <?php echo anchor($this->config->item('admin_url').'delete/' . $record->id, "delete", array('class'=>'confirm btn red delete')) ?>
                </td>
                <?php
               
                    ?>
                </tr>
        <?php 
            
        } //  endforeach; 
        ?>
		</tbody>
	</table>

<div class="table_action_buttons">
	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('activate', 'deactivate', 'delete') )); ?>	
</div>
<?php else: ?>
	<div class="no_data"><?php echo lang('banners_nothing_found');?></div><!--.no_data-->
<?php endif;?>

<?php echo form_close(); ?>
</section>