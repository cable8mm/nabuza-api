<div class="gameInfos index">
	<h2><?php echo __('Game Infos'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('number'); ?></th>
			<th><?php echo $this->Paginator->sort('title'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($gameInfos as $gameInfo): ?>
	<tr>
		<td><?php echo h($gameInfo['GameInfo']['id']); ?>&nbsp;</td>
		<td><?php echo h($gameInfo['GameInfo']['number']); ?>&nbsp;</td>
		<td><?php echo h($gameInfo['GameInfo']['title']); ?>&nbsp;</td>
		<td><?php echo h($gameInfo['GameInfo']['modified']); ?>&nbsp;</td>
		<td><?php echo h($gameInfo['GameInfo']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $gameInfo['GameInfo']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $gameInfo['GameInfo']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $gameInfo['GameInfo']['id']), null, __('Are you sure you want to delete # %s?', $gameInfo['GameInfo']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Game Info'), array('action' => 'add')); ?></li>
	</ul>
</div>
