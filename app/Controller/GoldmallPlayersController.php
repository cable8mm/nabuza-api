<?php
App::uses('AppController', 'Controller');
/**
 * GoldmallPlayers Controller
 *
 * @property GoldmallPlayer $GoldmallPlayer
 */
class GoldmallPlayersController extends AppController {
	
	var $uses	= array('GoldmallPlayer', 'GoldmallItem', 'Player');

	public function submit() {
		
		$this->validateQuery('goldmall_item_id');
		$goldmallItemId      = $this->request->query['goldmall_item_id'];
		$goldmallItem	= $this->GoldmallItem->find('first', array('conditions'=>array('GoldmallItem.id'=>$goldmallItemId)));
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$currentTime	= strtotime("now");
		$finished		= strtotime($goldmallItem['GoldmallItem']['finished']);
		
		
		if ($player['Player']['own_gold'] < $goldmallItem['GoldmallItem']['spent_gold']) {
			$this->error(4500);
		}
		
		$currentTime	= strtotime("now");
		$finished		= strtotime($goldmallItem['GoldmallItem']['finished']);
		
		if ($currentTime > $finished) {
			$this->error(4502);
		}
		
		
		$goldmallPlayer	= $this->GoldmallPlayer->find('first', array('conditions'=>array('submitted_player_id'=>$player['Player']['id'], 'goldmall_item_id'=>$goldmallItemId)));
		
		$goldmallPlayer['GoldmallPlayer']['goldmall_item_id']	= $goldmallItemId;
		$goldmallPlayer['GoldmallPlayer']['submitted_player_id']	= $player['Player']['id'];
		$goldmallPlayer['GoldmallPlayer']['submit_count']	= empty($goldmallPlayer['GoldmallPlayer']['submit_count'])? 1 : $goldmallPlayer['GoldmallPlayer']['submit_count'] + 1;
		$goldmallPlayer['GoldmallPlayer']['modified'] = date('Y-m-d H:i:s');
		
		$goldmallItem	= $this->GoldmallItem->read(null, $goldmallPlayer['GoldmallPlayer']['goldmall_item_id']);
		
		if($this->GoldmallPlayer->save($goldmallPlayer)) {
			$this->GoldmallItem->updateAll(array('GoldmallItem.submit_count'=>'GoldmallItem.submit_count + 1'), array('GoldmallItem.id'=>$goldmallItemId));
			
			$this->Player->updateAll(array('Player.own_gold'=>'Player.own_gold - '.$goldmallItem['GoldmallItem']['spent_gold']), array('Player.id'=>$player['Player']['id']));
			$result['Player']['own_gold'] = $player['Player']['own_gold'] - $goldmallItem['GoldmallItem']['spent_gold'];
			$result['GoldmallPlayer']['submit_count'] = $goldmallPlayer['GoldmallPlayer']['submit_count'];
			
			if ($result['GoldmallPlayer']['submit_count'] == 0) {
				$result['GoldmallPlayer']['winning_probability'] = 0;
			} else {
				$result['GoldmallPlayer']['winning_probability'] = $result['GoldmallPlayer']['submit_count'] * $goldmallItem['GoldmallItem']['winner_count'] / ($goldmallItem['GoldmallItem']['submit_count']+1) * 100;

				if($result['GoldmallPlayer']['winning_probability'] >= 100)
					$result['GoldmallPlayer']['winning_probability'] = 100;
				else
					$result['GoldmallPlayer']['winning_probability'] = (int)$result['GoldmallPlayer']['winning_probability'];
				$result['GoldmallPlayer']['winning_probability'] *= 0.9;
			}

			$result['GoldmallPlayer']['winning_probability'] = sprintf("%3.1f", $result['GoldmallPlayer']['winning_probability']);
			$result['GoldmallPlayer']['goldmall_item_id'] = $goldmallPlayer['GoldmallPlayer']['goldmall_item_id'];
// 당첨확률, 응모횟수
			$this->resultRender($result);
		} else {
			$this->error(9000);
		}
	}

	public function inquiry() {
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$this->GoldmallPlayer->bindModel(array('belongsTo'=>array('GoldmallItem'=>array('className'=>'GoldmallItem'))));
		
		$result = $this->GoldmallPlayer->find('all', array('order'=>array('GoldmallItem.is_request_delivery_info ASC'), 'conditions'=>array('GoldmallPlayer.is_winner'=>1, 'GoldmallPlayer.is_winning_confirm'=>0, 'GoldmallPlayer.submitted_player_id'=>$player['Player']['id'])));
		
		$this->resultRender($result);
	}
	
	public function winning_confirm() {
		$this->validateQuery('goldmall_item_id', 'invitation_count');
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$goldmallItemId      = $this->request->query['goldmall_item_id'];
		$invitationCount      = $this->request->query['invitation_count'];
		
		$goldmallItemCount	= $this->GoldmallPlayer->find('count', array('conditions'=>array('GoldmallPlayer.is_winning_confirm'=>0, 'GoldmallPlayer.goldmall_item_id'=>$goldmallItemId, 'GoldmallPlayer.submitted_player_id'=>$player['Player']['id'])));
		
		if ($goldmallItemCount > 0) {
			// 성공 처리
			// is_winning_confirm 필드를 1로...
			$this->GoldmallPlayer->updateAll(array('GoldmallPlayer.is_winning_confirm'=>1),array('GoldmallPlayer.goldmall_item_id'=>$goldmallItemId, 'GoldmallPlayer.submitted_player_id'=>$player['Player']['id']));

			// 상품 award가 비취나 골드일 경우 더하기 함
			$goldmallItem	= $this->GoldmallItem->read(null, $goldmallItemId);
			$player['Player']['own_jade_count'] = $player['Player']['own_jade_count'] + $goldmallItem['GoldmallItem']['award_jade_count'];
			$player['Player']['own_gold'] = $player['Player']['own_gold'] + $goldmallItem['GoldmallItem']['award_gold_count'];
			$player['Player']['invitation_count'] = $invitationCount + $goldmallItem['GoldmallItem']['award_invitation_count'];
				
			$this->Player->save($player);
			$result['Player']['own_jade_count']	= $player['Player']['own_jade_count'];
			$result['Player']['own_gold']	= $player['Player']['own_gold'];
			$result['Player']['invitaion_count']	= $player['Player']['invitation_count'];
				
			$this->resultRender($result);
		} else {
			$this->error(4501);
		}
	}
}
