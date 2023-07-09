<div class="accessTokens form">
<?php echo $this->Form->create('AccessToken'); ?>
	<fieldset>
		<legend><?php echo __('Edit Access Token'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('token');
		echo $this->Form->input('consumer_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('AccessToken.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('AccessToken.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Access Tokens'), array('action' => 'index')); ?></li>
	</ul>
</div>
