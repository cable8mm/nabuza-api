<div class="giftTournamentRankings form">
<?php echo $this->Form->create('GiftTournamentRanking'); ?>
	<fieldset>
		<legend><?php echo __('Edit Gift Tournament Ranking'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('gift_tournament_id');
		echo $this->Form->input('player_id');
		echo $this->Form->input('ranking');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('GiftTournamentRanking.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('GiftTournamentRanking.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Gift Tournament Rankings'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Gift Tournaments'), array('controller' => 'gift_tournaments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Gift Tournament'), array('controller' => 'gift_tournaments', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Players'), array('controller' => 'players', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player'), array('controller' => 'players', 'action' => 'add')); ?> </li>
	</ul>
</div>
