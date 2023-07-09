<?php
App::uses('AppController', 'Controller');
/**
 * Messages Controller
 *
 * @property Message $Message
 */
class MessagesController extends AppController {

	public function use_invitation() {
		$this->validateQuery('id');
		$messageId      = $this->request->query['id'];
		Controller::loadModel('Player');

		$player = $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		$message = $this->Message->find('first',array('conditions'=>array('Message.id'=>$messageId)));

		if(!$this->Message->hasAny(array('Message.id' => $messageId, 'Message.is_used'=>1))) {
			$this->resultRender(false);
		}

		if($message['Message']['type'] == 1) {
			$player['Player']['invitation_count'] += 1;
		} else {
			$player['Player']['own_jade_count'] += $message['Message']['t_count'];
		}
		if ($this->Message->updateAll(array('Message.is_used'=>0), 
					array('Message.received_player_id'=>$player['Player']['id'], 'Message.id'=>$messageId)) &&
				$this->Player->save($player)) {
			$this->resultRender(true);
		} else {
			$this->error(9000);
		}
		
	}
	
	public function use_invitation2() {
		$this->validateQuery('id');
		$messageId      = $this->request->query['id'];
		Controller::loadModel('Player');
	
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		$message = $this->Message->find('first',array('conditions'=>array('Message.id'=>$messageId)));
	
		if(!$this->Message->hasAny(array('Message.id' => $messageId, 'Message.is_used'=>1))) {
			$this->resultRender(false);
		}
	
		if($message['Message']['type'] == 1) {
			$player['Player']['invitation_count'] += 1;
		} else {
			$player['Player']['own_jade_count'] += $message['Message']['t_count'];
		}
		if ($this->Message->updateAll(array('Message.is_used'=>0),
				array('Message.received_player_id'=>$player['Player']['id'], 'Message.id'=>$messageId)) &&
				$this->Player->save($player)) {
			$this->resultRender(true);
		} else {
			$this->error(9000);
		}
	
	}
	
	public function send_invitation() {
		$this->validateQuery('phone_number');
		$phoneNumber	= $this->request->query['phone_number'];
		
		Controller::loadModel('Player');
		
		$sendPlayer	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		$receivedPlayer	= $this->Player->find('first', array('conditions'=>array('Player.phone_number'=>$phoneNumber)));
		
		if (empty($receivedPlayer)) {
			$this->resultRender(false);
		}
		
		$message['Message']['sent_player_id']	= $sendPlayer['Player']['id'];
		$message['Message']['received_player_id']	= $receivedPlayer['Player']['id'];
		
		if ($this->Message->save($message)) {
			if ($this->Player->save($sendPlayer)) {
				$result	= date('Y-m-d H:i:s');
				$this->resultRender($result);
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(9000);
		}
	}

	public function send_invitation2() {
		$this->validateQuery('buddy_id');
		$buddyId = $this->request->query['buddy_id'];
	
		Controller::loadModel('Player');
	
		$sendPlayer	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		$receivedPlayer	= $this->Player->find('first', array('conditions'=>array('Player.id'=>$buddyId)));
	
		if (empty($receivedPlayer)) {
			$this->resultRender(false);
		}
	
		$message['Message']['sent_player_id']	= $sendPlayer['Player']['id'];
		$message['Message']['received_player_id']	= $receivedPlayer['Player']['id'];
	
		if ($this->Message->save($message)) {
			if ($this->Player->save($sendPlayer)) {
				$result	= date('Y-m-d H:i:s');
				$this->resultRender($result);
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(9000);
		}
	}
	
	public function get() {
		$page	= empty($this->request->query['page'])? 1 : $this->request->query['page'];
		$limit	= empty($this->request->query['limit'])? 20 : $this->request->query['limit'];
		$start	= ($page - 1) * $limit;
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));

		$messages	= $this->Message->find('all', array(
				'conditions'=>array('Message.received_player_id' => $player['Player']['id'], 'Message.is_used' => 1)
				, 'order' => 'Message.type'
				, 'limit' => $start.','.$limit
				));

		$new_messages = array();
		for($i = 0 ; $i < count($messages) ; $i++)
		{
			$sPlayer = $this->Player->find('first', array('conditions'=>array('Player.id'=>$messages[$i]['Message']['sent_player_id'])));
			if($sPlayer != null)
			{
				$message = array("Message"=>array("id"=>$messages[$i]['Message']['id']
									,"sent_player_id"=>$messages[$i]['Message']['sent_player_id']
									,"nickname"=>$sPlayer['Player']['nickname']
									,"own_gold"=>$sPlayer['Player']['own_gold']    
									,"received_player_id"=>$messages[$i]['Message']['received_player_id']
									,"type"=>$messages[$i]['Message']['type']
									,"is_used"=>$messages[$i]['Message']['is_used']
									,"modified"=>$messages[$i]['Message']['modified']
									,"created"=>$messages[$i]['Message']['created']
						));
				array_push($new_messages,$message);
			}
		}

		$this->resultRender($new_messages);
	}
	
	public function get2() {
		$page	= empty($this->request->query['page'])? 1 : $this->request->query['page'];
		$limit	= empty($this->request->query['limit'])? 50 : $this->request->query['limit'];
		$start	= ($page - 1) * $limit;
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
	
		$messages	= $this->Message->find('all', array(
				'conditions'=>array('Message.received_player_id' => $player['Player']['id'], 'Message.is_used' => 1),
				'order' => 'Message.type'
				, 'limit' => $start.','.$limit
		));
	
		$new_messages = array();
		for($i = 0 ; $i < count($messages) ; $i++)
		{
			$sPlayer = $this->Player->find('first', array('conditions'=>array('Player.id'=>$messages[$i]['Message']['sent_player_id'])));
			if($sPlayer != null)
			{
				$message = array("Message"=>array("id"=>$messages[$i]['Message']['id']
						,"sent_player_id"=>$messages[$i]['Message']['sent_player_id']
											,"nickname"=>$sPlayer['Player']['nickname']
						,"own_gold"=>$sPlayer['Player']['own_gold']
						,"received_player_id"=>$messages[$i]['Message']['received_player_id']
						,"type"=>$messages[$i]['Message']['type']
						,"is_used"=>$messages[$i]['Message']['is_used']
						,"modified"=>$messages[$i]['Message']['modified']
						,"created"=>$messages[$i]['Message']['created']
				));
				array_push($new_messages,$message);
			}
		}
	
		foreach ($new_messages as $k => $v) {
			$new_messages[$k]['Message']['id']	= intval($new_messages[$k]['Message']['id']);
			$new_messages[$k]['Message']['sent_player_id']	= intval($new_messages[$k]['Message']['sent_player_id']);
			$new_messages[$k]['Message']['own_gold']	= intval($new_messages[$k]['Message']['own_gold']);
			$new_messages[$k]['Message']['received_player_id']	= intval($new_messages[$k]['Message']['received_player_id']);
			$new_messages[$k]['Message']['type']	= intval($new_messages[$k]['Message']['type']);
			$new_messages[$k]['Message']['is_used']	= $new_messages[$k]['Message']['is_used']? true : false;
		}
		
		$this->resultRender($new_messages);
	}
}
