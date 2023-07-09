<div class="giftTournamentRankings view">
<h2><?php  echo __('Gift Tournament Ranking'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($giftTournamentRanking['GiftTournamentRanking']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Gift Tournament'); ?></dt>
		<dd>
			<?php echo $this->Html->link($giftTournamentRanking['GiftTournament']['id'], array('controller' => 'gift_tournaments', 'action' => 'view', $giftTournamentRanking['GiftTournament']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Player'); ?></dt>
		<dd>
			<?php echo $this->Html->link($giftTournamentRanking['Player']['phone_number'], array('controller' => 'players', 'action' => 'view', $giftTournamentRanking['Player']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ranking'); ?></dt>
		<dd>
			<?php echo h($giftTournamentRanking['GiftTournamentRanking']['ranking']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($giftTournamentRanking['GiftTournamentRanking']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Gift Tournament Ranking'), array('action' => 'edit', $giftTournamentRanking['GiftTournamentRanking']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Gift Tournament Ranking'), array('action' => 'delete', $giftTournamentRanking['GiftTournamentRanking']['id']), null, __('Are you sure you want to delete # %s?', $giftTournamentRanking['GiftTournamentRanking']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Gift Tournament Rankings'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Gift Tournament Ranking'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Gift Tournaments'), array('controller' => 'gift_tournaments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Gift Tournament'), array('controller' => 'gift_tournaments', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Players'), array('controller' => 'players', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player'), array('controller' => 'players', 'action' => 'add')); ?> </li>
	</ul>
</div>
