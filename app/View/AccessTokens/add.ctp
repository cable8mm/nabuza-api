<div class="accessTokens form">
<?php echo $this->Form->create('AccessToken'); ?>
	<fieldset>
		<legend><?php echo __('Add Access Token'); ?></legend>
	<?php
		echo $this->Form->input('token');
		echo $this->Form->input('consumer_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Access Tokens'), array('action' => 'index')); ?></li>
	</ul>
</div>
