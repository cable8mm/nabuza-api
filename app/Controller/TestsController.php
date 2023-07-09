<?php
App::uses('AppController', 'Controller');
/**
 * Tests Controller
 *
 * @property Test $Test
 */
class TestsController extends AppController {
	
	var $devServerKey = "AIzaSyCsU2tOfhpJrEU8aQjhWh1pmrTuqJD7RT0";
public function sendGCM() {
                $this->validateQuery('msg', 'reg_id', 'msg_type','title');
                $msg    = $this->request->query['msg'];
                $regId  = $this->request->query['reg_id'];
                $type   = $this->request->query['msg_type'];

                Controller::loadModel('Player');
                $player = $this->Player->find('first',
                                array('conditions'=>array('Player.notificationid'=>$regId)
                                        , 'fields'=>array('Player.is_invitation_notification','Player.is_gift_notification','Player.is_changing_order_notification')));
                $begin = 0;
                if(count($player) < 1) $this->error(1100);
                if($type == 1) {
                        if(!$player['Player']['is_invitation_notification']) $begin = 1;
                } elseif($type == 2) {
                        if(!$player['Player']['is_gift_notification']) $begin = 1;
                } else {
                        if(!$player['Player']['is_changing_order_notification']) $begin = 1;
                }

                if($begin) {
                        $data = array(
                                        'registration_ids' => array($regId),
                                        'data' => array('msg' => $msg)
                                     );

                        $headers = array(
                                        "Content-Type:application/json",
                                        "Authorization:key=".$this->devServerKey
                                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $result = curl_exec($ch);

//                        print_r($result);
                        curl_close($ch);
                }
              $this->resultRender();
        }

	public function sendGCM_() {
//		$this->validateQuery('msg', 'reg_id', 'msg_type');
		$this->validateQuery('reg_id', 'msg_type');
		
		$type 	= $this->request->query['msg_type'];

//		$msg	= $this->request->query['msg'];
		
		$regId	= $this->request->query['reg_id'];
		$begin = 0;

		Controller::loadModel('Player');
		
		$sentPlayer = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$player = $this->Player->find('first',
				array('conditions'=>array('Player.notificationid'=>$regId)
						, 'fields'=>array('Player.is_invitation_notification','Player.is_gift_notification','Player.is_changing_order_notification', 'language_id', 'nickname')));
		
		
		switch($type) {
			case 1:	// 1. 님이 보낸 초대장이 도착하였습니다
				$msg	= ($player['Player']['language_id'] == 1)? $sentPlayer['Player']['nickname']."님이 보낸 초대장이 도착하였습니다." : 'You got a card from '.$sentPlayer['Player']['nickname'];
				$title	= ($player['Player']['language_id'] == 1)? '초대장 도착' : 'Card arrived';
				break;
			case 2:	//  2. 님이 보낸 선물이 도착하였습니다
				$msg	= ($player['Player']['language_id'] == 1)? $sentPlayer['Player']['nickname']."님이 보낸 선물이 도착하였습니다." : 'You got a gift from '.$sentPlayer['Player']['nickname'];
				$title	= ($player['Player']['language_id'] == 1)? '선물 도착' : 'Gift arrived';
				break;
			case 3:	// 3. 님이 주간최고 기록을 갱신하였습니다
				$msg	= ($player['Player']['language_id'] == 1)? $sentPlayer['Player']['nickname']."님이 주간최고 기록을 갱신하였습니다." : $sentPlayer['Player']['nickname'].' got a new weekly high score';
				$title	= ($player['Player']['language_id'] == 1)? '축하해주세요~' : 'Please congratulate me~';
				break;
		}
		
		
		
		if(count($player) < 1) $this->error(1100);
		if($type == 1) {
			if(!$player['Player']['is_invitation_notification']) $begin = 1;
		} elseif($type == 2) {
			if(!$player['Player']['is_gift_notification']) $begin = 1;
		} else {
			if(!$player['Player']['is_changing_order_notification']) $begin = 1;
		}
			
//		$this->resultRender();	

		if($begin) {
			if(empty($title)) {
			$data = array(
					'registration_ids' => array($regId),
					'data' => array('msg' => $msg)
				     );
			} else {
                        $data = array(
                                        'registration_ids' => array($regId),
                                        'data' => array('msg' => $msg, 'title'=>$title)
                                     );
			}
			$headers = array(
					"Content-Type:application/json",
					"Authorization:key=".$this->devServerKey
					);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			$result = curl_exec($ch);

//			print_r($result);
			curl_close($ch);
		}
		$this->resultRender();
	}

	public function sendGCM_P() {
		$this->validateQuery('msg', 'reg_id');
		$msg	= $this->request->query['msg'];
		$regId	= $this->request->query['reg_id'];
		
		$data = array(
				'registration_ids' => array($regId),
				'data' => array('msg' => $msg)
		);
		
		$headers = array(
				"Content-Type:application/json",
				"Authorization:key=".$this->devServerKey
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		
		print_r($result);
		curl_close($ch);
		
		$this->resultRender();
	}
	
	public function sendAPNS() {
		$this->validateQuery('msg', 'reg_id', 'badge');
		$msg	= $this->request->query['msg'];
		$regId	= $this->request->query['reg_id'];
		$badge	= $this->request->query['badge'];
		$payload['aps'] = array('alert' => $msg, 'badge' => (int)$badge, 'sound' => 'default');
		$payload = json_encode($payload);

		$deviceToken	= $regId;
		
		$apnsHost = 'gateway.sandbox.push.apple.com';
		$apnsPort = 2195;
		$apnsCert = 'apns-dev.pem';
		
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		
		$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
		fwrite($apns, $apnsMessage);
		@socket_close($apns);
		fclose($apns);
		
		$this->resultRender();
	}
}
