<div class="gameInfos view">
<h2><?php  echo __('Game Info'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($gameInfo['GameInfo']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Number'); ?></dt>
		<dd>
			<?php echo h($gameInfo['GameInfo']['number']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Title'); ?></dt>
		<dd>
			<?php echo h($gameInfo['GameInfo']['title']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($gameInfo['GameInfo']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($gameInfo['GameInfo']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Game Info'), array('action' => 'edit', $gameInfo['GameInfo']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Game Info'), array('action' => 'delete', $gameInfo['GameInfo']['id']), null, __('Are you sure you want to delete # %s?', $gameInfo['GameInfo']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Game Infos'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Game Info'), array('action' => 'add')); ?> </li>
	</ul>
</div>
