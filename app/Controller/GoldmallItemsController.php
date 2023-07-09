<?php
App::uses('AppController', 'Controller');
/**
 * GoldmallItems Controller
 *
 * @property GoldmallItem $GoldmallItem
 */
class GoldmallItemsController extends AppController {

	var $uses = array('GoldmallItem', 'GoldmallPlayer', 'Player');
	
	public function get() {
		$current	= date('Y-m-d H:i:s');

		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$goldmallItems	= $this->GoldmallItem->find('all', array(
				'limit'=>4, 
				'order'=>'GoldmallItem.important_point DESC', 
//				'fields'=>array('GoldmallItem.id', 'GoldmallItem.spent_gold', 'GoldmallItem.image_url', 'GoldmallItem.started', 'GoldmallItem.winner_count', 'GoldmallItem.submit_count'),
				'conditions'=>array('AND'=>
						array('`GoldmallItem`.`started` <= '=>$current
						, '`GoldmallItem`.`finished` >=' => $current
						, '`GoldmallItem`.`language_id`' => $player['Player']['language_id'])
						)
				)
			);

		foreach($goldmallItems as $k=>$goldmallItem) {
			$goldmallPlayer = $this->GoldmallPlayer->find('first', array('fields'=>array('submit_count'), 'conditions'=>array('GoldmallPlayer.submitted_player_id'=>$player['Player']['id'], 'GoldmallPlayer.goldmall_item_id'=>$goldmallItem['GoldmallItem']['id'])));

			if(empty($goldmallPlayer)) {
				$goldmallPlayer['GoldmallPlayer']['submit_count'] = 0;
			}
			
			if ($goldmallPlayer['GoldmallPlayer']['submit_count'] == 0) {
				$goldmallPlayer['GoldmallPlayer']['winning_probability'] = 0;
			} else {
				$goldmallPlayer['GoldmallPlayer']['winning_probability'] = $goldmallPlayer['GoldmallPlayer']['submit_count'] * $goldmallItems[$k]['GoldmallItem']['winner_count'] / $goldmallItems[$k]['GoldmallItem']['submit_count'] * 100;
				if($goldmallPlayer['GoldmallPlayer']['winning_probability'] >= 100)
					$goldmallPlayer['GoldmallPlayer']['winning_probability'] = 100;
				else
					$goldmallPlayer['GoldmallPlayer']['winning_probability'] = $goldmallPlayer['GoldmallPlayer']['winning_probability'];
				$goldmallPlayer['GoldmallPlayer']['winning_probability'] *= 0.9;
			}
			
			$goldmallPlayer['GoldmallPlayer']['winning_probability'] = sprintf("%3.1f", $goldmallPlayer['GoldmallPlayer']['winning_probability']);
			$goldmallItems[$k]['GoldmallItem']['current_datetime'] = date('Y-m-d H:i:s');
			
			$goldmallItems[$k] = array_merge($goldmallItems[$k], $goldmallPlayer);

			$finished	= new DateTime($goldmallItems[$k]['GoldmallItem']['finished']);
			$started	= new DateTime($goldmallItems[$k]['GoldmallItem']['started']);
			$current	= new DateTime($goldmallItems[$k]['GoldmallItem']['current_datetime']);
			
			$interval	= $finished->diff($current);
			$remainDays	= (int)$interval->format('%r%d');

			if ($remainDays > 0) {
				$finished->add(new DateInterval('P'.($remainDays+1).'D'));
				$goldmallItems[$k]['GoldmallItem']['finished']	= $finished->format('Y-m-d H:i:s');
			}
		}
		
		$this->resultRender($goldmallItems);
	}
}
