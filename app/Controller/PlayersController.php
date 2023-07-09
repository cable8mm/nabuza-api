<?php
App::uses('AppController', 'Controller');
/**
 * Players Controller
 *
 * @property Player $Player
 */
class PlayersController extends AppController {

	public function set_language() {
		$this->validateQuery('language_id');
		$languageId      = $this->request->query['language_id'];
		
		if ($languageId != 1 && $languageId != 2) {
			$this->error(5100);
		}
		
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		if ($this->Player->updateAll(array('language_id'=>$languageId), array('Player.id'=>$player['Player']['id']))) {
			$this->resultRender();
		} else {
			$this->error(9100);
		}
	}
	
	public function start_game() {
		$this->validateQuery('spent_gold', 'invitation_count', 'remaining_time');
		$spentGold      = $this->request->query['spent_gold'];
		$spentJade	= empty($this->request->query['spent_jade_count'])? 0 : $this->request->query['spent_jade_count'];
		$invitation_count = $this->request->query['invitation_count'];
		$remaining_time = $this->request->query['remaining_time'];

		$newPlayer	= array();
		
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$newPlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'] - $spentJade;
		$newPlayer['Player']['own_gold']   = $player['Player']['own_gold'] - $spentGold;
		$newPlayer['Player']['invitation_count']   = $invitation_count;
		$newPlayer['Player']['start_game'] = date('Y-m-d H:i:s');
		$newPlayer['Player']['remaining_time'] = $remaining_time;
		
		if ($newPlayer['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}

		if ($newPlayer['Player']['own_gold'] < 0) {
			$this->error(4500);
		}

		$this->Player->id	 = $player['Player']['id'];
		
		if ($this->Player->save($newPlayer)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}

	public function start_game2() {
		$this->validateQuery('spent_gold', 'spent_jade_count', 'last_invitation_count_updated', 'invitation_count');
		$spentGold      = $this->request->query['spent_gold'];
		$spentJade		= $this->request->query['spent_jade_count'];
		$lastInvitationCountUpdated = $this->request->query['last_invitation_count_updated'];
		$invitationCount = $this->request->query['invitation_count'];
		
		$newPlayer	= array();
	
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$newPlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'] - $spentJade;
		$newPlayer['Player']['own_gold']   = $player['Player']['own_gold'] - $spentGold;
		$newPlayer['Player']['invitation_count']   = $invitationCount;
		$newPlayer['Player']['start_game'] = date('Y-m-d H:i:s');
		$newPlayer['Player']['last_invitation_count_updated'] = $lastInvitationCountUpdated;
		$newPlayer['Player']['invitation_count'] = $invitationCount;
		
		if ($newPlayer['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}
	
		if ($newPlayer['Player']['own_gold'] < 0) {
			$this->error(4500);
		}
	
		$this->Player->id	 = $player['Player']['id'];
	
		if ($this->Player->save($newPlayer)) {
			$resultPlayer['Player']['invitation_count']	= intval($newPlayer['Player']['invitation_count']);
			$resultPlayer['Player']['own_jade_count']	= intval($newPlayer['Player']['own_jade_count']);
			$resultPlayer['Player']['own_gold']	= intval($newPlayer['Player']['own_gold']);
				
			$this->resultRender($resultPlayer);
		} else {
			$this->error(9000);
		}
	}
	
	public function finished_game() {
		$this->validateQuery('weekly_highscore', 'gold_count', 'score_type','level');
		$score_type = $this->request->query['score_type'];
		$weekly_highscore = $this->request->query['weekly_highscore'];
		$goldCnt = $this->request->query['gold_count'];
		$level = $this->request->query['level'];

		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		/* 레벨 조회 */
		if($player['Player']['last_level'] < $level) $player['Player']['last_level'] = $level;

		$player['Player']['own_gold']   += $goldCnt;
		if($score_type == 1)
		{
			$player['Player']['weekly_highscore'] = $weekly_highscore;
			if($weekly_highscore > $player['Player']['highscore'])
				$player['Player']['highscore'] = $weekly_highscore;
		}

		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}

	public function finished_game2() {
		$this->validateQuery('highscore');
		$highscore = $this->request->query['weekly_highscore'];
	
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		/* 레벨 조회 */
//		if($player['Player']['last_level'] < $level) $player['Player']['last_level'] = $level;
	
//		$player['Player']['own_gold']   += $goldCnt;
		if ($player['Player']['weekly_highscore'] < $highscore)
				$player['Player']['weekly_highscore'] = $highscore;
		if($highscore > $player['Player']['highscore'])
			$player['Player']['highscore'] = $weekly_highscore;
	
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}
	
	public function register_invitation_letter() {
		$this->validateQuery('invitation_count');
		$invitaionCnt = $this->request->query['invitation_count'];

		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$player['Player']['invitation_count'] = $invitaionCnt;

		if ($this->Player->save($player)) {
			$this->resultRender(true);
		} else {
			$this->error(9000);
		}
	}

	public function recommendation() {
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$recommendCnt = $player['Player']['recommendation_count'] + 1;
        //        $recommendCnt = 1;
	

	if(($recommendCnt) < 78) {
		$player['Player']['invitation_count'] += 5;
	}

		$player['Player']['recommendation_count'] += 1;

		if ($this->Player->save($player)) {
			$result = array('recommendation_count'=>$recommendCnt);
			$this->resultRender($result);
		} else {
			$this->error(9000);
		}
	}

	public function recommendation2() {
		$this->validateQuery('buddy_id');
		$buddyId = $this->request->query['buddy_id'];
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$buddy	= $this->Player->read(null, $buddyId);
		
		if ($buddy) {
			$this->resultRender($buddy);
		} else {
			$this->error(1100);
		}
	}
	
	public function mail() {
                $player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
print_r($this->accessToken);
echo "TEST\n";
                        $datetime       = date("Y:m:d H:i:s");
echo "닉네임 : ".$player['Player']['nickname']."\n전화번호 : ".$player['Player']['phone_number']."\nappid : ".$player['Player']['appid']."\n탈퇴를 요청한 시간 : ".$datetime;
echo "\n".'[나부자-탈퇴요청] '.$player['Player']['nickname'].'님이 나부자 탈퇴 신청을 보내셨습니다.';
//exit;                        
App::uses('CakeEmail', 'Network/Email');
                        $email = new CakeEmail();
                        $email->from(array('cable8mm@anytale.com' => '나부자 심심이'))
                        ->to('cable8mm@gmail.com')
                        ->subject('[나부자-탈퇴요청] '.$player['Player']['nickname'].'님이 나부자 탈퇴 신청을 보내셨습니다.')
                        ->send("닉네임 : ".$player['Player']['nickname']."\n전화번호 : ".$player['Player']['phone_number']."\nappid : ".$player['Player']['appid']."\n탈퇴를 요청한 시간 : ".$datetime);
exit;                        
$this->resultRender();
	}

	public function is_closing() {
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		if(empty($player['Player']['is_closing'])) $player['Player']['status_closing_id'] = 1;
		
                if($this->Player->save($player)) {
                        // $this->resultRender();
			$datetime       = date("Y:m:d H:i:s");
			App::uses('CakeEmail', 'Network/Email');
                        $email = new CakeEmail();
                        $email->from(array('cable8mm@anytale.com' => '나부자 Mailer'))
                        ->to('nabuza@nenora.com')
                        ->subject('[나부자-탈퇴요청] '.$player['Player']['nickname'].'님이 나부자 탈퇴 신청을 보내셨습니다.')
                        ->send("닉네임 : ".$player['Player']['nickname']."\n전화번호 : ".$player['Player']['phone_number']."\nappid : ".$player['Player']['appid']."\n탈퇴를 요청한 시간 : ".$datetime);

			$this->resultRender();
                } else {
                        $this->error(9000);
                }
	}

	public function is_join() {
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$result = array('gift_tournament_id'=>$player['Player']['gift_tournament_id']);
		$this->resultRender($result);
	}

	public function is_confirm() {
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		Controller::loadModel('GameInfo');
		$gameNumber = $this->GameInfo->find('first');

		$player['Player']['is_check_highscore'] = 1;
		$player['Player']['game_number'] = $gameNumber['GameInfo']['number'];

		if($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}

	public function write_review() {
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$reviewCnt = $player['Player']['review_count'];

		if($reviewCnt < 1) {
			$player['Player']['review_count'] = 1;
			$player['Player']['invitation_count'] += 5;
			if($this->Player->save($player)) {
				$this->resultRender(true);
			} else {
				$this->error(9000);
			}
		}
		$this->resultRender(false);
	}

	public function get_medal() {
		$this->validateQuery('ranking');
		$ranking = $this->request->query['ranking'];

		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		if($ranking == 1) $player['Player']['gold_medal_count'] += 1;
		if($ranking == 2) $player['Player']['silver_medal_count'] += 1;
		if($ranking == 3) $player['Player']['bronze_medal_count'] += 1;

		if($ranking < 4) {
			if($this->Player->save($player)) {
				$this->resultRender(true);
			} else {
				$this->error(9000);
			}
		}
		$this->resultRender(false);
	}

	public function get_logininfo() {
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$loginId = $player['Player']['loginid'];
		$password = $player['Player']['password'];

		if($loginId == "") $loginId = "None";
		if($password == "") $password = "None";

		$result = array('loginid'=>$loginId,'password'=>$password);
		$this->resultRender($result);
	}


/**
 * index method
 *
 * @return void
 */
	public function index() {
		if($this->request->is('post'))
			$phoneNumbers	= $this->request->data['phone_numbers'];
		else
			$phoneNumbers	= $this->request->query['phone_numbers'];

		if (empty($phoneNumbers)) {
			$this->error(2);
		}

		$phoneNumbers	= preg_replace('/,,/', ',', $phoneNumbers);
		$phoneNumbers	= preg_replace('/^,/', '', $phoneNumbers);
		$phoneNumbers	= preg_replace('/,$/', '', $phoneNumbers);
		
		$phoneNumbersArray	= explode(',', $phoneNumbers);

		$players	= $this->Player->find('all'
				, array('conditions'=>
					array('Player.phone_number' => $phoneNumbersArray)
					, 'fields' => 
					array('Player.id', 'Player.nickname', 'Player.highscore', 'Player.weekly_highscore', 'Player.phone_number'
						, 'Player.own_jade_count', 'Player.own_gold', 'Player.notificationid'
						, 'Player.gold_medal_count', 'Player.silver_medal_count', 'Player.bronze_medal_count', 'Player.is_check_highscore' , 'Player.last_level' , 'Player.lastweek_highscore'
					     )
				       )
				);
		
		Controller::loadModel('LogRequestPhoneNumber');
		$logRequestPhoneNumber['LogRequestPhoneNumber']['appid']	= $this->accessToken['AccessToken']['appid'];
		$logRequestPhoneNumber['LogRequestPhoneNumber']['phone_number']	= $phoneNumbers;
		$this->LogRequestPhoneNumber->save($logRequestPhoneNumber);
		
		$this->resultRender($players);
	}

	public function is_login() {
		if($this->Player->hasAny(array('Player.appid' => $this->accessToken['AccessToken']['appid']))) {
			$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
			$phone_number = $player['Player']['phone_number'];
			$start_game = $player['Player']['start_game'];
			$current = date('Y-m-d H:i:s');
			$result = array('last_login'=>$player['Player']['modified'], 'login'=>$current, 'start_game'=>$start_game);

			if($this->Player->updateAll(array('Player.modified'=>"'$current'") ,array('Player.phone_number'=>$phone_number))) {
				$this->resultRender($result);
			} else {
				$this->error(9000);
			}

		} else {
			$this->resultRender(false);
		}
	}

	public function is_login_2() {
		if($this->Player->hasAny(array('Player.appid' => $this->accessToken['AccessToken']['appid']))) {
			$player = $this->Player->find('first', 
					array(
							'fields'=>array('Player.phone_number', 'Player.start_game')
							, 'conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])
							)
						)
					;
			$result = $player;
			$result['current']	= date('Y-m-d H:i:s');
			$result['is_login']	= true;
			
			if($this->Player->updateAll(array('Player.modified'=>"'$current'") ,array('Player.phone_number'=>$phone_number))) {
				$this->resultRender($result);
			} else {
				$this->error(9000);
			}
	
		} else {
			$result['is_login']	= false;
			$this->resultRender($result);
		}
	}
	
	public function is_registed_phone_number() {
		$this->validateQuery('phone_number');
		$phoneNumber	= $this->request->query['phone_number'];
		
		if($this->Player->hasAny(array('Player.phone_number' => $phoneNumber))) {
			$this->resultRender(true);
		} else {
			$this->resultRender(false);
		}
	}
	
	public function is_registed_password() {
		$this->validateQuery('phone_number', 'password');
		$phoneNumber	= $this->request->query['phone_number'];
		$password	= $this->request->query['password'];
		
		if($this->Player->hasAny(array('Player.phone_number' => $phoneNumber, 'password' => $password))) {
			$this->resultRender(true);
		} else {
			$this->resultRender(false);
		}
	}
	
	public function is_redisted_loginid() {
		$this->validateQuery('loginid');
		
		$loginid	= $this->request->query['loginid'];
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));

		if($this->Player->hasAny(array('Player.loginid' => $loginid))) {
			$this->resultRender(false);
		} else {
			$player['Player']['loginid'] = $loginid;
			if($this->Player->save($player)) {
				$this->resultRender(true);
			} else {
				$this->resultRender(9000);
			}
		}
	}
	
	public function is_registed_password_id() {
		$this->validateQuery('loginid', 'password');
		$loginid	= $this->request->query['loginid'];
		$password	= $this->request->query['password'];
	
		if($this->Player->hasAny(array('Player.loginid' => $loginid, 'password' => $password))) {
			$this->resultRender(true);
		} else {
			$this->resultRender(false);
		}
	}
	
	public function regist_phone_number() {
		$this->validateQuery('phone_number', 'nickname', 'auth_number');
		$phoneNumber	= $this->request->query['phone_number'];
		$nickname	= $this->request->query['nickname'];
		$authNumber	= $this->request->query['auth_number'];

		// auth_number validate
		Controller::loadModel('ActivatedPhoneNumber');
		if (!$this->ActivatedPhoneNumber->hasAny(array('ActivatedPhoneNumber.auth_number' => $authNumber))) {
			$this->error(4);
		}
		
		$player['Player']['phone_number']	= $phoneNumber;
		$player['Player']['nickname']	= $nickname;
		$player['Player']['appid']	= $this->accessToken['AccessToken']['appid'];

// 		if (!empty($this->request->query['market_id'])) {
// 			$marketId	= $this->request->query['market_id'];
// 		} else {
// 			$marketId	= 1;
// 		}
		
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
		
		
		// appid를 비교한 후 같은게 있으면 업데이트하고, 같은게 없으면 인서트 한다.
// 		if ($this->Player->hasAny(array('Player.appid' => $this->accessToken['AccessToken']['appid']))) {
// 			// update
// 			$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
// 			$player['Player']['phone_number']	= $phoneNumber;
// 			if ($this->Player->save($player)) {
// 				$this->resultRender();
// 			} else {
// 				$this->error(9000);
// 			}
// 		} else {
// 			$player['Player']['phone_number']	= $phoneNumber;
// 			$player['Player']['appid']	= $this->accessToken['AccessToken']['appid'];
// 			pr($player);
// 			if ($this->Player->save($player)) {
// 				$this->resultRender();
// 			} else {
// 				$this->error(9000);
// 			}
// 			// insert
// 		}
	}
	
	public function regist_phone_number_woa() {
		$this->validateQuery('phone_number');
		$phoneNumber	= $this->request->query['phone_number'];
	
		// appid를 비교한 후 같은게 있으면 업데이트하고, 같은게 없으면 인서트 한다.
		if ($this->Player->hasAny(array('Player.appid' => $this->accessToken['AccessToken']['appid']))) {
			// update
			$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
			$player['Player']['phone_number']	= $phoneNumber;
			if ($this->Player->save($player)) {
				$this->resultRender();
			} else {
				$this->error(9000);
			}
		} else {
			$player['Player']['phone_number']	= $phoneNumber;
			$player['Player']['appid']	= $this->accessToken['AccessToken']['appid'];

			if ($this->Player->save($player)) {
				$this->resultRender();
			} else {
				$this->error(9000);
			}
			// insert
		}
	}
	
	// 아이폰 전용
	// appid 를 받고 이름을 리턴한다.
	public function regist() {
		// appid를 비교한 후 같은게 있으면 업데이트하고, 같은게 없으면 인서트 한다.
		if ($this->Player->hasAny(array('Player.appid' => $this->accessToken['AccessToken']['appid']))) {

			$nicknamePlayer	= $this->Player->find('first'
					, array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])
							, 'fields'=>array('Player.last_invitation_count_updated', 'Player.nickname', 'Player.id', 'Player.own_jade_count', 'Player.own_gold', 'Player.invitation_count', 'Player.last_level')));
			
			$nicknamePlayer['Player']['id']	= intval($nicknamePlayer['Player']['id']);
			$nicknamePlayer['Player']['own_jade_count']	= intval($nicknamePlayer['Player']['own_jade_count']);
			$nicknamePlayer['Player']['own_gold']	= intval($nicknamePlayer['Player']['own_gold']);
			$nicknamePlayer['Player']['last_level']	= intval($nicknamePlayer['Player']['last_level']);

			// invitation count 를 업데이트 한다.
			// 지금 갯수가 5개가 안 될 경우 마지막 게임 시간(start_game)과 현재 시간의 차가 10분 마다 1개의 invitation count가 올라간다.
			// 단 최대는 5개
			$addedInvitationCount	= 0;
			$remainSeconds	= 0;
// 			if($nicknamePlayer['Player']['invitation_count'] < 5) {
// 				$diff	= time()-strtotime($nicknamePlayer['Player']['last_invitation_count_updated']);
// 				if ($diff > 0) {
// 					$addedInvitationCount	= $diff / 600;	// 600 = 60초 * 10분
// 					$remainSeconds	= $diff % 600;
// 				}
// 			}
			
//			$invitationCount	= $nicknamePlayer['Player']['invitation_count']+$addedInvitationCount < 5? $nicknamePlayer['Player']['invitation_count']+$addedInvitationCount : 5;
			
// 			$this->Player->id	= $nicknamePlayer['Player']['id'];
// 			$playerForInvitation	= array('invitation_count'=>$invitationCount, 'last_invitation_count_updated'=>date("Y-m-d H:i:s"));
// 			$this->Player->save($playerForInvitation);
			
			$nicknamePlayer['Player']['invitation_count']	= intval($nicknamePlayer['Player']['invitation_count']);
//			$nicknamePlayer['Player']['remain_seconds']	= intval($remainSeconds);
			
			$result	= array('is_regist'=>false, 'player_info'=>$nicknamePlayer);
				
			$this->resultRender($result);
		} else {
			$player['Player']['appid']	= $this->accessToken['AccessToken']['appid'];
			$player['Player']['os']	= "i";	// 이 API는 아이폰 전용임
		
			if ($this->Player->save($player)) {
				$nicknamePlayer	= $this->Player->find('first'
						, array('conditions'=>array('Player.id'=>$this->Player->id)
								, 'fields'=>array('Player.nickname', 'Player.id', 'Player.own_jade_count', 'Player.own_gold', 'Player.invitation_count', 'Player.last_level')));
				
				$nicknamePlayer['Player']['nickname']	= 'Player#'.$this->Player->id;
				$nicknamePlayer['Player']['id']	= $this->Player->id;
				$nicknamePlayer['Player']['own_jade_count']	= intval($nicknamePlayer['Player']['own_jade_count']);
				$nicknamePlayer['Player']['own_gold']	= intval($nicknamePlayer['Player']['own_gold']);
				$nicknamePlayer['Player']['invitation_count']	= intval($nicknamePlayer['Player']['invitation_count']);
				$nicknamePlayer['Player']['last_level']	= intval($nicknamePlayer['Player']['last_level']);
				
				$result	= array('is_regist'=>true, 'player_info'=>$nicknamePlayer);
				
				$this->Player->save($nicknamePlayer);
				$this->resultRender($result);
			} else {
				$this->error(9000);
			}
			// insert
		}
	}
	
	public function login_as_phone_number() {
//		$this->validateQuery('phone_number', 'password', 'reg_id', 'os');
		$this->validateQuery('phone_number', 'reg_id', 'os');
		$phoneNumber	= $this->request->query['phone_number'];
//		$password	= $this->request->query['password'];
		$regId	= $this->request->query['reg_id'];
		$os	= $this->request->query['os'];
		
		$player	= $this->Player->find('first', array('conditions'=>array('Player.phone_number'=>$phoneNumber)));
		$player['Player']['notificationid']	= $regId;
		$player['Player']['appid']	= $this->accessToken['AccessToken']['appid'];
//		$player['Player']['password']	= $password;
		$player['Player']['os']	= $os;
		$player['Player']['modified'] = date('Y-m-d H:i:s');
		
		if ($this->Player->save($player)) {
			$result	= $player['Player']['modified'];
			$this->resultRender($result);
		} else {
			$this->error(9000);
		}
	}
	
	public function login_as_id() {
		$this->validateQuery('loginid', 'password', 'reg_id', 'os', 'phone_number');
		$phoneNumber	= $this->request->query['phone_number'];
		$loginid	= $this->request->query['loginid'];
		$password	= $this->request->query['password'];
		$regId	= $this->request->query['reg_id'];
		$os	= $this->request->query['os'];

		$player	= $this->Player->find('first', array('conditions'=>array('Player.loginid'=>$loginid, 'Player.password'=>$password, 'Player.phone_number'=>$phoneNumber)));
		$player['Player']['notificationid']	= $regId;
		$player['Player']['appid']	= $this->accessToken['AccessToken']['appid'];
		$player['Player']['os']	= $os;
		$player['Player']['modified']   = date('Y-m-d H:i:s');
		
		if ($this->Player->save($player)) {
			$result	= $player['Player']['modified'];
			$this->resultRender($result);
		} else {
			$this->error(9000);
		}
	}
	
	public function regist_nickname() {
		$this->validateQuery('nickname');
		
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		
		if (!empty($this->request->query['market_id'])) {
			$marketId	= $this->request->query['market_id'];
			$player['Player']['market_id']	= $this->request->query['market_id'];
		}
		
		$player['Player']['nickname']	= $this->request->query['nickname'];
		
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}
	
	public function regist_password() {
		$this->validateQuery('password');
		
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		
		$player['Player']['password']	= $this->request->query['password'];
		
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}
	
	public function set_invitation_notification() {
		$this->validateQuery('set');
		
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		
		$player['Player']['is_invitation_notification']	= $this->request->query['set'];
		
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}

	public function set_gift_notification() {
		$this->validateQuery('set');
	
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
	
		$player['Player']['is_gift_notification']	= $this->request->query['set'];
	
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}

	public function set_notifications() {
		$this->validateQuery('set');

                $player = $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));

                $player['Player']['is_gift_notification']       = $this->request->query['set'];
		$player['Player']['is_changing_order_notification']     = $this->request->query['set'];
		$player['Player']['is_invitation_notification'] = $this->request->query['set'];                

		if ($this->Player->save($player)) {
                        $this->resultRender();
                } else {
                        $this->error(9000);
                }

	}
	
	public function set_changing_order_notification() {
		$this->validateQuery('set');
	
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
	
		$player['Player']['is_changing_order_notification']	= $this->request->query['set'];
	
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}
	
	public function remove() {
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		$this->Player->id = $player['Player']['id'];		
		if (!$this->Player->exists()) {
			$this->error(1100);
		}
		if ($this->Player->delete()) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}
	
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view() {
		$query	= $this->request->query;
		$players	= $this->Player->find('first', array('conditions' => array('Player.phone_number' => $query['phone_number']), 'fields' => array('id', 'status_closing_id', 'nickname', 'highscore', 'own_jade_count', 'own_gold', 'gold_medal_count', 'silver_medal_count', 'bronze_medal_count', 'invitation_count', 'remaining_time' , 'is_check_highscore' , 'password')));

		$this->resultRender($players);
	}
	
	public function view2() {
		$query	= $this->request->query;
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));

		$players	= $this->Player->find('all', 
				array('conditions' =>array('Player.id < ' => $player['Player']['id'])
				,'fields' => array(
									'id', 
									'status_closing_id', 
									'nickname', 
									'highscore',
									'weekly_highscore',
									'own_jade_count', 
									'own_gold', 
									'gold_medal_count', 
									'silver_medal_count', 
									'bronze_medal_count', 
									'invitation_count', 
									'remaining_time' , 
									'is_check_highscore',
									'is_invitation_notification',
									'password'
						)
					, 'limit' => 10
				)
			);
		$rank = 0;
		foreach ($players as $k => $v) {
			$players[$k]['Player']['id']	= intval($v['Player']['id']);
			$players[$k]['Player']['status_closing_id']	= intval($v['Player']['status_closing_id']);
			$players[$k]['Player']['highscore']	= intval($v['Player']['highscore']);
			$players[$k]['Player']['weekly_highscore']	= intval($v['Player']['weekly_highscore']);
			$players[$k]['Player']['own_jade_count']	= intval($v['Player']['own_jade_count']);
			$players[$k]['Player']['own_gold']	= intval($v['Player']['own_gold']);
			$players[$k]['Player']['gold_medal_count']	= intval($v['Player']['gold_medal_count']);
			$players[$k]['Player']['silver_medal_count']	= intval($v['Player']['silver_medal_count']);
			$players[$k]['Player']['bronze_medal_count']	= intval($v['Player']['bronze_medal_count']);
			$players[$k]['Player']['invitation_count']	= intval($v['Player']['invitation_count']);
			$players[$k]['Player']['remaining_time']	= intval($v['Player']['remaining_time']);
			$players[$k]['Player']['is_check_highscore']	= $v['Player']['is_check_highscore']? true : false;
			$players[$k]['Player']['is_invitation_notification']	= $v['Player']['is_invitation_notification']? true : false;
			$players[$k]['Player']['rank']	= ++$rank;
		}
		
		$this->resultRender($players);
	}
	
	public function get_buddy() {
		$this->validateQuery('buddy_key');
		$buddyKey	= $this->request->query['buddy_key'];

		$buddy	= $this->Player->find('first', array('conditions'=>array('Player.id'=>$buddyKey)));

		$buddy['Player']['highscore']	= intval($buddy['Player']['highscore']);
		$buddy['Player']['own_gold']	= intval($buddy['Player']['own_gold']);
		$buddy['Player']['gold_medal_count']	= intval($buddy['Player']['gold_medal_count']);
		$buddy['Player']['silver_medal_count']	= intval($buddy['Player']['silver_medal_count']);
		$buddy['Player']['bronze_medal_count']	= intval($buddy['Player']['bronze_medal_count']);
		$buddy['Player']['weekly_highscore']	= intval($buddy['Player']['weekly_highscore']);

		$this->resultRender($buddy);
	}
	
	public function regist_finished() {
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		
		if (!empty($player['Player']['phone_number']) && !empty($play['Player']['nickname'])) {	// 플래이어 등록 시 확인해야 될 최소 것들
			$player['Player']['is_active']	= 1;
			if ($this->Player->save($player)) {
				$this->resultRender();
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(1200);
		}
		
	}
/**
 * add method
 *
 * @return void
 */
	public function add() {
		$data['Player']	= $this->request->query;
		if ($this->request->is('get')) {
			$this->Player->create();
			if ($this->Player->save($data)) {
				$this->resultRender();
			} else {
				if (empty($this->Player->validationErrors))
					$this->error(9000);
				else
					$this->error(100);
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Player->id = $id;
		if (!$this->Player->exists()) {
			throw new NotFoundException(__('Invalid player'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Player->save($this->request->data)) {
				$this->flash(__('The player has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->Player->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Player->id = $id;
		if (!$this->Player->exists()) {
			throw new NotFoundException(__('Invalid player'));
		}
		if ($this->Player->delete()) {
			$this->flash(__('Player deleted'), array('action' => 'index'));
		}
		$this->flash(__('Player was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
