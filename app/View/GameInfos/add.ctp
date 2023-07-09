<div class="gameInfos form">
<?php echo $this->Form->create('GameInfo'); ?>
	<fieldset>
		<legend><?php echo __('Add Game Info'); ?></legend>
	<?php
		echo $this->Form->input('number');
		echo $this->Form->input('title');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Game Infos'), array('action' => 'index')); ?></li>
	</ul>
</div>
