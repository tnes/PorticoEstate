<?php
	//include common logic for all templates
	include("common.php");
?>

<div class="identifier-header">
	<h1><img src="<?php echo ACTIVITYCALENDAR_TEMPLATE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('arena') ?></h1>
	<div>
		<label><?php echo lang('name'); ?></label>
		 <?php if($arena->get_arena_name()){ echo $arena->get_arena_name(); } else { echo lang('no_value'); }?>
	</div>
</div>
<div class="yui-content">
	<div id="details">
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($arena->get_id()){ echo $arena->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt>
					<?php if($arena->get_arena_name() || $editable) { ?>
					<label for="name"><?php echo lang('name') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="arena_name" id="arena_name" value="<?php echo $arena->get_arena_name() ?>" />
					<?php
					}
					else
					{
						echo $arena->get_arena_name();
					}
					?>
				</dd>
				<dt>
					<?php if($arena->get_internal_arena_id() || $editable) { ?>
					<label for="internal_arena_id"><?php echo lang('internal_arena_id') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="internal_arena_id" id="internal_arena_id" value="<?php echo $arena->get_internal_arena_id() ?>" />
					<?php
					}
					else
					{
						echo $arena->get_internal_arena_id();
					}
					?>
				</dd>
				<dt>
					<?php if($arena->get_address() || $editable) { ?>
					<label for="address"><?php echo lang('address') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="address" id="address" value="<?php echo $arena->get_address() ?>" />
					<?php
					}
					else
					{
						echo $arena->get_address();
					}
					?>
				</dd>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_arena" value="' . lang('save') . '"/>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>

