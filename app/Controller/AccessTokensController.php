<?php
App::uses('AppController', 'Controller');
/**
 * AccessTokens Controller
 *
 * @property AccessToken $AccessToken
 */
class AccessTokensController extends AppController {
	
	/*
	 * 토큰을 새로 가지고 온다.
	 */
	public function get_from_phone_number() {
		$this->validateQuery('consumer_key', 'signature', 'app_key', 'phone_number');
		
		Controller::loadModel('Consumer');
		if (
				!$this->Consumer->hasAny(array(
						'Consumer.consumer_key' => $this->request->query['consumer_key']
						, 'Consumer.signature' => $this->request->query['signature']
				))
		) {
			$this->error(10);
		}
		 
		$phoneNumber	= $this->request->query['phone_number'];
		
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions' => array('Player.phone_number' => $phoneNumber)));
		
		if (empty($player)) {
			$this->error(10);
		}
		
		$accessToken['AccessToken']['token']	= String::uuid();
		$accessToken['AccessToken']['created']	= date('Y-m-d h:i:s');
		$accessToken['AccessToken']['player_id']	= $player['Player']['id'];
		
		$this->AccessToken->create();
		if ($this->AccessToken->save($accessToken)) {
			$result	= array();
			$this->resultRender($accessToken['AccessToken']['token']);
		} else {
			$this->error(9000);
		}
	}
	
	public function get_from_email() {
		$this->validateQuery('consumer_key', 'signature', 'app_key', 'email', 'password');
	
		Controller::loadModel('Consumer');
		if (
				!$this->Consumer->hasAny(array(
						'Consumer.consumer_key' => $this->request->query['consumer_key']
						, 'Consumer.signature' => $this->request->query['signature']
				))
		) {
			$this->error(10);
		}
	
		$email	= $this->request->query['email'];
		$password	= $this->request->query['password'];

		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions' => array('Player.email' => $email, 'Player.password' => $password)));
	
		if (empty($player)) {
			$this->error(10);
		}
	
		$accessToken['AccessToken']['token']	= String::uuid();
		$accessToken['AccessToken']['created']	= date('Y-m-d h:i:s');
		$accessToken['AccessToken']['player_id']	= $player['Player']['id'];
	
		$this->AccessToken->create();
		if ($this->AccessToken->save($accessToken)) {
			$result	= array();
			$this->resultRender($accessToken['AccessToken']['token']);
		} else {
			$this->error(9000);
		}
	}
	
	public function get() {
		
		$this->validateQuery('consumer_key', 'signature', 'appid');
		
		$appid	= $this->request->query['appid'];
		
		Controller::loadModel('Consumer');
		if (
				!$this->Consumer->hasAny(array(
					'Consumer.consumer_key' => $this->request->query['consumer_key']
					, 'Consumer.signature' => $this->request->query['signature']
				))
		) {
			$this->error(10);
		}
		
		$prevAccessToken	= $this->AccessToken->find('first', array('conditions'=>array('AccessToken.appid'=>$appid)));

		if (empty($prevAccessToken)) {
			$accessToken['AccessToken']['token']	= String::uuid();
			$accessToken['AccessToken']['appid']	= $appid;
			$accessToken['AccessToken']['created']	= date('Y-m-d h:i:s');
			
			$this->AccessToken->create();

			if ($this->AccessToken->save($accessToken)) {
				$result	= array();
				$this->resultRender($accessToken['AccessToken']['token']);
			} else {
				$this->error(9000);
			}
		} else {
			$accessToken['AccessToken']['token']	= String::uuid();
			$sql	= '
				UPDATE access_tokens AS AccessToken SET
					AccessToken.token = "'.$accessToken['AccessToken']['token'].'"
					WHERE AccessToken.appid = "'.$appid.'"
					';
			$result	= $this->AccessToken->query($sql);

			$result	= array();
			$this->resultRender($accessToken['AccessToken']['token']);
			
// 			if ($this->AccessToken->query($sql)) {
// 				$result	= array();
// 				$this->resultRender($accessToken['AccessToken']['token']);
// 			} else {
// 				$this->error(9000);
// 			}
		}
		
	}
	
	public function login_phone_number() {
		$this->validateQuery('phone_number');
		
		$phoneNumber	= $this->request->query['phone_number'];
		
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions' => array('Player.phone_number' => $phoneNumber)));
		
		
	}
}
