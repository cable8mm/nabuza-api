<div class="giftTournamentRankings index">
	<h2><?php echo __('Gift Tournament Rankings'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('gift_tournament_id'); ?></th>
			<th><?php echo $this->Paginator->sort('player_id'); ?></th>
			<th><?php echo $this->Paginator->sort('ranking'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($giftTournamentRankings as $giftTournamentRanking): ?>
	<tr>
		<td><?php echo h($giftTournamentRanking['GiftTournamentRanking']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($giftTournamentRanking['GiftTournament']['id'], array('controller' => 'gift_tournaments', 'action' => 'view', $giftTournamentRanking['GiftTournament']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($giftTournamentRanking['Player']['phone_number'], array('controller' => 'players', 'action' => 'view', $giftTournamentRanking['Player']['id'])); ?>
		</td>
		<td><?php echo h($giftTournamentRanking['GiftTournamentRanking']['ranking']); ?>&nbsp;</td>
		<td><?php echo h($giftTournamentRanking['GiftTournamentRanking']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $giftTournamentRanking['GiftTournamentRanking']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $giftTournamentRanking['GiftTournamentRanking']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $giftTournamentRanking['GiftTournamentRanking']['id']), null, __('Are you sure you want to delete # %s?', $giftTournamentRanking['GiftTournamentRanking']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Gift Tournament Ranking'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Gift Tournaments'), array('controller' => 'gift_tournaments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Gift Tournament'), array('controller' => 'gift_tournaments', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Players'), array('controller' => 'players', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player'), array('controller' => 'players', 'action' => 'add')); ?> </li>
	</ul>
</div>
