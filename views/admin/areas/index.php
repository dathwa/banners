<section class="title">
	<h4><?php echo $section; ?></h4>
</section>
<section class="item">
<?php 
$attributes= array(
    'id'=> 'f-banner'
);
echo form_open($this->config->item('admin_url').'areas/action', $attributes);
echo form_hidden('cmd', 'delete'); ?>

<?php if ( ! empty($records)): ?>
	<table class="table-list">
		<thead>
			<tr>
                <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
                <th><?php echo lang('banners_areas_name');?></th>    
                <th><?php echo lang('banners_areas_width');?></th>    
                <th><?php echo lang('banners_areas_height');?></th>    
                <th><?php echo lang('banners_areas_syntax');?></th>    
                <th><?php echo lang('banners_areas_is_default');?></th>    
           		<th><?php echo lang('banners_action');?></th> 
			</tr>
		</thead>
		<tbody>
        
		<?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo form_checkbox('action_to[]', $record->id); ?></td>
                <td><?php echo anchor($this->config->item('admin_url').'areas/edit/' . $record->id, $record->title); ?></td>		 
                <td><?php echo $record->width; ?></td>		 
                <td><?php echo $record->height; ?></td>		 
                <td><code>{{banners:page_banners area="<?php echo $record->slug; ?>"}}</code></td>	
				<td>
				<?php 
                if ($record->is_default==1) {
                    ?><img src="<?php echo base_url().APPPATH.'themes/pyrocms/img/admin/icons/16/tick-circle.png'; ?>" alt="Default" /><?
                }
                else
                {
                    echo form_radio('is_default', $record->id, FALSE, 'class="radio-banner-area"');  
                }
                    ?>
                    </td>
                <td class="buttons buttons-small">
                    <?php echo anchor($this->config->item('admin_url').'areas/edit/' . $record->id, lang('banners_edit'), array('class'=>'btn orange edit')) ?> 
                    <?php echo anchor($this->config->item('admin_url').'areas/delete/' . $record->id, lang('banners_delete'), array('class'=>'confirm btn red delete')) ?>
                </td>
            </tr>
        <?php 
        endforeach; ?>
		</tbody>
	</table>
	<div class="table_action_buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete') )); ?>
	</div>

<?php else: ?>
	<div class="blank-slate">
		<h2>You have not added any records yet.</h2>
	</div>
<?php endif;?>

<?php echo form_close(); ?>
</section>