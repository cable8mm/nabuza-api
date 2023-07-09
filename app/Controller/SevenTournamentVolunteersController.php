<?php
App::uses('AppController', 'Controller');
/**
 * SevenTournamentVolunteers Controller
 *
 * @property SevenTournamentVolunteer $GiftTournamentVolunteer
 */
class SevenTournamentVolunteersController extends AppController {

	var $uses	= array('SevenTournamentVolunteer', 'Player');
	var $components	= array('PlayerLevel');
	
	public function submit() {
		$this->validateQuery('highscore');

		// player_id 구하자
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$highscore = $this->request->query['highscore'];

		/* 이전 데이터 삭제 */
		$q = "DELETE FROM seven_tournament_volunteers WHERE player_id =".$player['Player']['id'];
		$userDel = $this->SevenTournamentVolunteer->query($q);
		
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['highscore']	= $highscore;
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['player_id']	= $player['Player']['id'];
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['own_gold']	= $player['Player']['own_gold'];

		$playerLevelTerms	= $this->PlayerLevel->getTerms($player['Player']['own_gold']);
		
		$playerGoldLevelStep	= $this->PlayerLevel->getGoldLevelStep($player['Player']['own_gold']);	// 플래이어 레벨(초, 중, 고)
		
//		1. 각 레벨 별로 사용자 등수 제한 (예를 들어 초보방에 3등으로 제한을 하면, 플레이어는 아무리 잘해도 3등 이상을 할 수 없음 - 같은 레벨의 이전 플레이어 정보를 가져와서 1, 2등으로 설정한다.)
//		2. 레벨 별 등수 제한 확율 조정 (예를 들어 중수방 등수 제한 확율을 90%로 설정하면, 10%에 대한 랭킹 요청에 대해서는 등수 제한 기능이 해제된 상태로 앱에 전달한다.)

		Controller::loadModel('GameInfo');
		
		switch ($playerGoldLevelStep) {
			case 0:
				$levelMaxRanking	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 3)));
				$levelLimitConstant	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 6)));
				break;
			case 1:
				$levelMaxRanking	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 4)));
				$levelLimitConstant	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 7)));
				break;
			case 2:
				$levelMaxRanking	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 5)));
				$levelLimitConstant	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 8)));
				break;
		}
		
		$mustLowVolunteer	= 0;
		
		if ($levelLimitConstant['GameInfo']['number'] != 0) {
			$rand	= rand ( 1 , 100 );
			if ($rand < $levelLimitConstant['GameInfo']['number']) {	// 2. 레벨별 등수 제한 확율 조정
				$mustLowVolunteer	= $levelMaxRanking['GameInfo']['number'] - 1;
			}
		}
		
		if ($this->SevenTournamentVolunteer->save($sevenTournamentVolunteer)) {	// 점수 저장 완료
			$sevenTournamentVolunteerId	= $this->SevenTournamentVolunteer->id;
			$mustLowVolunteers	= array();
			// $mustLowVolunteer 만큼의 나보다 점수가 높은 플래이어가 반드시 있어야 한다.
			// $mustLowVolunteers 들은 랜덤하게 나와야 한다.
			if ($mustLowVolunteer != 0) {
				$mustLowVolunteers	= $this->SevenTournamentVolunteer->find('all'
					, array(
						'conditions'=>
							array(
								'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
								'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1],
								'SevenTournamentVolunteer.highscore >= '=> $highscore,
								'SevenTournamentVolunteer.player_id NOT' => $player['Player']['id']
							)
						, 'order'=>'rand()'
						, 'limit'=>$mustLowVolunteer
						)
					);
				
				$mustLowVolunteerIds	= array();

 				foreach ($mustLowVolunteers as $v) {
 					$mustLowVolunteerIds[]	= $v['SevenTournamentVolunteer']['id'];
 				}

				$sevenTournamentVolunteers	= $this->SevenTournamentVolunteer->find('all'
						, array(
								'conditions'=>
								array(
												'SevenTournamentVolunteer.id < ' => $this->SevenTournamentVolunteer->id,
												'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
												'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1]
												,'SevenTournamentVolunteer.id NOT' => $mustLowVolunteerIds
												,'SevenTournamentVolunteer.player_id NOT' => $player['Player']['id']
								)
								, 'order'=>'SevenTournamentVolunteer.id DESC'
								, 'limit'=>6-count($mustLowVolunteers)
						)
				);	// 레벨에 맞는 플래이어 6명을 뽑는다.
// 				$sevenTournamentVolunteers	= $this->SevenTournamentVolunteer->find('all'
// 						, array(
// 								'conditions'=>
// 								array(
// 										'AND'=>array(
// 												'SevenTournamentVolunteer.id < ' => $this->SevenTournamentVolunteer->id,
// 												'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
// 												'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1]
// 										),
// 										'NOT'=>array(
// 												'SevenTournamentVolunteer.id' => $mustLowVolunteerIds
// 										)
											
// 								)
// 								, 'order'=>'SevenTournamentVolunteer.id DESC'
// 								, 'limit'=>6-$mustLowVolunteer
// 						)
// 				);	// 레벨에 맞는 플래이어 6명을 뽑는다.
			} else {
				$sevenTournamentVolunteers	= $this->SevenTournamentVolunteer->find('all'
						, array(
								'conditions'=>
								array(
												'SevenTournamentVolunteer.id < ' => $this->SevenTournamentVolunteer->id,
												'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
												'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1]
												,'SevenTournamentVolunteer.player_id NOT' => $player['Player']['id']
								)
								, 'order'=>'SevenTournamentVolunteer.id DESC'
								, 'limit'=>6-$mustLowVolunteer
						)
				);	// 레벨에 맞는 플래이어 6명을 뽑는다.
			}

			
			if (count($mustLowVolunteers) != 0) {
				$sevenTournamentVolunteers	= array_merge($sevenTournamentVolunteers, $mustLowVolunteers);
			}
			
			$result = array();
			if(count($sevenTournamentVolunteers) > 0)
			{
				for($i = 0 ; $i < count($sevenTournamentVolunteers) ; $i++)
				{
					$playerInfo = $this->Player->find('first', array('conditions'=>array('Player.id'=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['player_id']),'fields'=>array('Player.nickname','Player.own_gold')));
					if (empty($playerInfo['Player']['nickname'])) {
						$playerInfo['Player']['nickname']	= "없음";
					}
					if (empty($playerInfo['Player']['own_gold'])) {
						$playerInfo['Player']['own_gold']	= 0;
					}
					$resultOne = array("SevenTournamentVolunteer"=>array("id"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['id']
								,"player_id"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['player_id']
								,"nickname"=>$playerInfo['Player']['nickname']
								,"own_gold"=>$playerInfo['Player']['own_gold']
								,"highscore"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['highscore']
								,"created"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['created']
								));	// 나의 정보를 가져온다.
					array_push($result,$resultOne);	// 위의 6명의 플래이어 정보를 얻어서 배열로 만든다.
				}
			}
			
			$rankingOrder	= 1;
			$sameRank		= 0;

			foreach ($result as $volunteer) {
				if ($volunteer['SevenTournamentVolunteer']['highscore'] == $highscore) {
					$sameRank++;
				}
				if ($volunteer['SevenTournamentVolunteer']['highscore'] > $highscore) {
					$rankingOrder	= $rankingOrder + $sameRank + 1;
					$sameRank = 0;
				}
			}
			
			$combineResult['ranking_infos']['ranking']	= $rankingOrder;
			$rewardJadeCount	= 0;
			$rewardGoldCount	= 0;
			
			$updatePlayer['Player']['own_gold']	= $player['Player']['own_gold'];
			$updatePlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'];
				
			switch ($rankingOrder) {
				case 1:
					$reward	= $this->GameInfo->read(null, 9);
					$rewardJadeCount	= $reward['GameInfo']['number'];
					$this->Player->id	= $player['Player']['id'];
					$updatePlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'] + $rewardJadeCount;
					$this->Player->save($updatePlayer);
					break;
				case 2:
					$reward	= $this->GameInfo->read(null, 10);
					$rewardGoldCount	= $reward['GameInfo']['number'];
					$this->Player->id	= $player['Player']['id'];
					$updatePlayer['Player']['own_gold']	= $player['Player']['own_gold'] + $rewardGoldCount;
					$this->Player->save($updatePlayer);
					break;
				case 3:
					$reward	= $this->GameInfo->read(null, 11);
					$rewardGoldCount	= $reward['GameInfo']['number'];
					$this->Player->id	= $player['Player']['id'];
					$updatePlayer['Player']['own_gold']	= $player['Player']['own_gold'] + $rewardGoldCount;
					$this->Player->save($updatePlayer);
					break;
			}

			$combineResult['ranking_infos']['reward_jade_count']	= $rewardJadeCount;
			$combineResult['ranking_infos']['reward_gold_count']	= $rewardGoldCount;

			$combineResult['player']['own_jade_count']	= $updatePlayer['Player']['own_jade_count'];
			$combineResult['player']['own_gold_count']	= $updatePlayer['Player']['own_gold'];
				
			$combineResult['rankings']	= $result;
			
			// rankingOrder update(ranking 값)
			$this->SevenTournamentVolunteer->id	= $sevenTournamentVolunteerId;
			$this->SevenTournamentVolunteer->saveField('ranking', $rankingOrder);
			
			$this->resultRender($combineResult);
		} else {
			$this->error(9000);
		}
	}
	
	public function submit2() {
		$this->validateQuery('highscore');
	
		// player_id 구하자
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$highscore = $this->request->query['highscore'];
	
		/* 이전 데이터 삭제 */
		$q = "DELETE FROM seven_tournament_volunteers WHERE player_id =".$player['Player']['id'];
		$userDel = $this->SevenTournamentVolunteer->query($q);
	
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['highscore']	= $highscore;
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['player_id']	= $player['Player']['id'];
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['nickname']	= $player['Player']['nickname'];
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['own_gold']	= $player['Player']['own_gold'];
		$sevenTournamentVolunteer['SevenTournamentVolunteer']['created']	= $player['Player']['created'];
		
		
		$playerLevelTerms	= $this->PlayerLevel->getTerms($player['Player']['own_gold']);
	
		$playerGoldLevelStep	= $this->PlayerLevel->getGoldLevelStep($player['Player']['own_gold']);	// 플래이어 레벨(초, 중, 고)
	
		//		1. 각 레벨 별로 사용자 등수 제한 (예를 들어 초보방에 3등으로 제한을 하면, 플레이어는 아무리 잘해도 3등 이상을 할 수 없음 - 같은 레벨의 이전 플레이어 정보를 가져와서 1, 2등으로 설정한다.)
		//		2. 레벨 별 등수 제한 확율 조정 (예를 들어 중수방 등수 제한 확율을 90%로 설정하면, 10%에 대한 랭킹 요청에 대해서는 등수 제한 기능이 해제된 상태로 앱에 전달한다.)
	
		Controller::loadModel('GameInfo');
	
		switch ($playerGoldLevelStep) {
			case 0:
				$levelMaxRanking	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 3)));
				$levelLimitConstant	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 6)));
				break;
			case 1:
				$levelMaxRanking	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 4)));
				$levelLimitConstant	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 7)));
				break;
			case 2:
				$levelMaxRanking	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 5)));
				$levelLimitConstant	= $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id' => 8)));
				break;
		}
	
		$mustLowVolunteer	= 0;
	
		if ($levelLimitConstant['GameInfo']['number'] != 0) {
			$rand	= rand ( 1 , 100 );
			if ($rand < $levelLimitConstant['GameInfo']['number']) {	// 2. 레벨별 등수 제한 확율 조정
				$mustLowVolunteer	= $levelMaxRanking['GameInfo']['number'] - 1;
			}
		}
	
		if ($this->SevenTournamentVolunteer->save($sevenTournamentVolunteer)) {	// 점수 저장 완료
			$sevenTournamentVolunteer['SevenTournamentVolunteer']['id']	= $this->SevenTournamentVolunteer->id;
			$sevenTournamentVolunteerId	= $this->SevenTournamentVolunteer->id;
			$mustLowVolunteers	= array();
			// $mustLowVolunteer 만큼의 나보다 점수가 높은 플래이어가 반드시 있어야 한다.
			// $mustLowVolunteers 들은 랜덤하게 나와야 한다.
			if ($mustLowVolunteer != 0) {
				$mustLowVolunteers	= $this->SevenTournamentVolunteer->find('all'
						, array(
								'conditions'=>
								array(
										'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
										'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1],
										'SevenTournamentVolunteer.highscore >= '=> $highscore,
										'SevenTournamentVolunteer.player_id NOT' => $player['Player']['id']
								)
								, 'order'=>'rand()'
								, 'limit'=>$mustLowVolunteer
						)
				);
	
				$mustLowVolunteerIds	= array();
	
				foreach ($mustLowVolunteers as $v) {
					$mustLowVolunteerIds[]	= $v['SevenTournamentVolunteer']['id'];
				}
	
				$sevenTournamentVolunteers	= $this->SevenTournamentVolunteer->find('all'
						, array(
								'conditions'=>
								array(
										'SevenTournamentVolunteer.id < ' => $this->SevenTournamentVolunteer->id,
										'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
										'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1]
										,'SevenTournamentVolunteer.id NOT' => $mustLowVolunteerIds
										,'SevenTournamentVolunteer.player_id NOT' => $player['Player']['id']
								)
								, 'order'=>'SevenTournamentVolunteer.id DESC'
								, 'limit'=>6-count($mustLowVolunteers)
						)
				);	// 레벨에 맞는 플래이어 6명을 뽑는다.
						// 				$sevenTournamentVolunteers	= $this->SevenTournamentVolunteer->find('all'
						// 						, array(
						// 								'conditions'=>
						// 								array(
						// 										'AND'=>array(
						// 												'SevenTournamentVolunteer.id < ' => $this->SevenTournamentVolunteer->id,
						// 												'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
						// 												'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1]
						// 										),
						// 										'NOT'=>array(
						// 												'SevenTournamentVolunteer.id' => $mustLowVolunteerIds
						// 										)
							
						// 								)
						// 								, 'order'=>'SevenTournamentVolunteer.id DESC'
						// 								, 'limit'=>6-$mustLowVolunteer
						// 						)
						// 				);	// 레벨에 맞는 플래이어 6명을 뽑는다.
			} else {
				$sevenTournamentVolunteers	= $this->SevenTournamentVolunteer->find('all'
						, array(
								'conditions'=>
								array(
										'SevenTournamentVolunteer.id < ' => $this->SevenTournamentVolunteer->id,
										'SevenTournamentVolunteer.own_gold >= ' => $playerLevelTerms[0],
										'SevenTournamentVolunteer.own_gold <= ' => $playerLevelTerms[1]
										,'SevenTournamentVolunteer.player_id NOT' => $player['Player']['id']
								)
								, 'order'=>'SevenTournamentVolunteer.id DESC'
								, 'limit'=>6-$mustLowVolunteer
						)
				);	// 레벨에 맞는 플래이어 6명을 뽑는다.
			}
	
				
			if (count($mustLowVolunteers) != 0) {
				$sevenTournamentVolunteers	= array_merge($sevenTournamentVolunteers, $mustLowVolunteers);
			}
			
			$sevenTournamentVolunteers[]	= $sevenTournamentVolunteer;

			$result = array();
			if(count($sevenTournamentVolunteers) > 0)
			{
				for($i = 0 ; $i < count($sevenTournamentVolunteers) ; $i++)
				{
					$playerInfo = $this->Player->find('first', array('conditions'=>array('Player.id'=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['player_id']),'fields'=>array('Player.nickname','Player.own_gold')));
					if (empty($playerInfo['Player']['nickname'])) {
						$playerInfo['Player']['nickname']	= "없음";
					}
					if (empty($playerInfo['Player']['own_gold'])) {
						$playerInfo['Player']['own_gold']	= 0;
					}
					$resultOne = array("SevenTournamentVolunteer"=>array("id"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['id']
						,"player_id"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['player_id']
						,"nickname"=>$playerInfo['Player']['nickname']
						,"own_gold"=>$playerInfo['Player']['own_gold']
						,"highscore"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['highscore']
						,"created"=>$sevenTournamentVolunteers[$i]['SevenTournamentVolunteer']['created']
								));	// 나의 정보를 가져온다.
						array_push($result,$resultOne);	// 위의 6명의 플래이어 정보를 얻어서 배열로 만든다.
					}
				}
					
				$rankingOrder	= 1;
				$sameRank		= 0;
	
				foreach ($result as $volunteer) {
				if ($volunteer['SevenTournamentVolunteer']['highscore'] == $highscore) {
						$sameRank++;
				}
				if ($volunteer['SevenTournamentVolunteer']['highscore'] > $highscore) {
					$rankingOrder	= $rankingOrder + $sameRank + 1;
						$sameRank = 0;
				}
				}
					
				$combineResult['ranking_infos']['ranking']	= $rankingOrder;
			$rewardJadeCount	= 0;
				$rewardGoldCount	= 0;
					
				$updatePlayer['Player']['own_gold']	= $player['Player']['own_gold'];
				$updatePlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'];
	
						switch ($rankingOrder) {
				case 1:
				$reward	= $this->GameInfo->read(null, 9);
				$rewardJadeCount	= $reward['GameInfo']['number'];
				$this->Player->id	= $player['Player']['id'];
				$updatePlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'] + $rewardJadeCount;
					$this->Player->save($updatePlayer);
				break;
				case 2:
				$reward	= $this->GameInfo->read(null, 10);
				$rewardGoldCount	= $reward['GameInfo']['number'];
				$this->Player->id	= $player['Player']['id'];
				$updatePlayer['Player']['own_gold']	= $player['Player']['own_gold'] + $rewardGoldCount;
				$this->Player->save($updatePlayer);
				break;
				case 3:
				$reward	= $this->GameInfo->read(null, 11);
				$rewardGoldCount	= $reward['GameInfo']['number'];
				$this->Player->id	= $player['Player']['id'];
						$updatePlayer['Player']['own_gold']	= $player['Player']['own_gold'] + $rewardGoldCount;
								$this->Player->save($updatePlayer);
								break;
				}
	
				$combineResult['ranking_infos']['reward_jade_count']	= $rewardJadeCount;
			$combineResult['ranking_infos']['reward_gold_count']	= $rewardGoldCount;
	
			$combineResult['Player']['own_jade_count']	= intval($updatePlayer['Player']['own_jade_count']);
			$combineResult['Player']['own_gold_count']	= intval($updatePlayer['Player']['own_gold']);
	
			$combineResult['rankings']	= $result;
		
			// rankingOrder update(ranking 값)
			$this->SevenTournamentVolunteer->id	= $sevenTournamentVolunteerId;
			$this->SevenTournamentVolunteer->saveField('ranking', $rankingOrder);
			
			$combineResult['ranking_infos']['reward_jade_count']	= intval($combineResult['ranking_infos']['reward_jade_count']);
			$combineResult['ranking_infos']['reward_gold_count']	= intval($combineResult['ranking_infos']['reward_gold_count']);
			$combineResult['Player']['own_gold_count']	= intval($combineResult['Player']['own_gold_count']);
				
			if (count($combineResult['rankings']) != 0) {
				foreach($combineResult['rankings'] as $k=>$ranking) {
					$combineResult['rankings'][$k]['SevenTournamentVolunteer']['id']	= intval($ranking['SevenTournamentVolunteer']['id']);
					$combineResult['rankings'][$k]['SevenTournamentVolunteer']['player_id']	= intval($ranking['SevenTournamentVolunteer']['player_id']);
					$combineResult['rankings'][$k]['SevenTournamentVolunteer']['own_gold']	= intval($ranking['SevenTournamentVolunteer']['own_gold']);
					$combineResult['rankings'][$k]['SevenTournamentVolunteer']['highscore']	= intval($ranking['SevenTournamentVolunteer']['highscore']);
				}
			}
			
			// 결과를 소팅하고, isMe 플래그를 셋 한다.
			usort($combineResult['rankings'], 'compare_highscore');
			$sameRank = 0; $lastHighscore	= 0; $rank = 0;
			foreach ($combineResult['rankings'] as $k=>$ranking) {

				if ($ranking['SevenTournamentVolunteer']['highscore'] == $lastHighscore) {
					$sameRank++;
				} else {
					$rank	= $rank + $sameRank + 1;
					$sameRank = 0;
				}
				$combineResult['rankings'][$k]['SevenTournamentVolunteer']['rank'] = $rank;
				
				$lastHighscore	= $ranking['SevenTournamentVolunteer']['highscore'];
				
				if ($ranking['SevenTournamentVolunteer']['player_id'] == $player['Player']['id']) {
					$combineResult['rankings'][$k]['SevenTournamentVolunteer']['isMe'] = true;
				} else {
					$combineResult['rankings'][$k]['SevenTournamentVolunteer']['isMe'] = false;
				}
				
			}
						
			$this->resultRender($combineResult);
			} else {
				$this->error(9000);
			}
		}
	
	public function notify_ranking() {
		$this->validateQuery('ranking', 'get_gold', 'get_jade');
		$getGold = $this->request->query['get_gold'];
		$getJade = $this->request->query['get_jade'];
		$ranking	= $this->request->query['ranking'];
		
		// player_id 구하자
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$this->SevenTournamentVolunteer->updateAll(array('SevenTournamentVolunteer.ranking'=>$ranking), array('SevenTournamentVolunteer.player_id'=>$player['Player']['id']));

		$player['Player']['own_gold'] += $getGold;
		$player['Player']['own_jade_count'] += $getJade;
		
		if ($this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}
}

function compare_highscore($b, $a)
{
	return strnatcmp($a['SevenTournamentVolunteer']['highscore'], $b['SevenTournamentVolunteer']['highscore']);
}
