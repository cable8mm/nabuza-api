<div class="accessTokens view">
<h2><?php  echo __('Access Token'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($accessToken['AccessToken']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Token'); ?></dt>
		<dd>
			<?php echo h($accessToken['AccessToken']['token']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Consumer Id'); ?></dt>
		<dd>
			<?php echo h($accessToken['AccessToken']['consumer_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($accessToken['AccessToken']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Access Token'), array('action' => 'edit', $accessToken['AccessToken']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Access Token'), array('action' => 'delete', $accessToken['AccessToken']['id']), null, __('Are you sure you want to delete # %s?', $accessToken['AccessToken']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Access Tokens'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Access Token'), array('action' => 'add')); ?> </li>
	</ul>
</div>
