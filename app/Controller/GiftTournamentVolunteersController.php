<?php
App::uses('AppController', 'Controller');
/**
 * GiftTournamentVolunteers Controller
 *
 * @property GiftTournamentVolunteer $GiftTournamentVolunteer
 */
class GiftTournamentVolunteersController extends AppController {

	
	public function submit() {
		$this->validateQuery('highscore', 'tournament_id');

		Controller::loadModel('GiftTournament');
		
		// 현재 진행형인 토너먼트인지?
		if (!$this->GiftTournament->hasAny(array('? BETWEEN started AND finished' => date('Y-m-d H:i:s')))) {
			$this->error(3000);
		}
		
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$highscore	= $this->request->query['highscore'];
		$tournamentId	= $this->request->query['tournament_id'];
		
		$giftTournamentVolunteer['GiftTournamentVolunteer']['highscore']	= $highscore;
		$giftTournamentVolunteer['GiftTournamentVolunteer']['player_id']	= $player['Player']['id'];
		$giftTournamentVolunteer['GiftTournamentVolunteer']['gift_tournament_id']	= $tournamentId;

		$player['Player']['gift_tournament_id'] = $tournamentId;
		
		if ($this->GiftTournamentVolunteer->save($giftTournamentVolunteer) && $this->Player->save($player)) {
			$this->resultRender();
		} else {
			$this->error(9000);
		}
	}

	public function get_entry_info() {
		$this->validateQuery('tournament_id');

		Controller::loadModel('GiftTournament');

		$tournamentId   = $this->request->query['tournament_id'];
		$entriesCnt = $this->GiftTournamentVolunteer->query("SELECT COUNT(*) as count FROM gift_tournament_volunteers WHERE gift_tournament_id = $tournamentId;");
		if($entriesCnt[0][0]['count'] > 0) {
			// player_id 구하자
			Controller::loadModel('Player');
			$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
			// 나의 응모 회수
			$entries_count = 0;
			$entries_count = $this->GiftTournamentVolunteer->find('count', array(
						'conditions'=>array(
							'GiftTournamentVolunteer.player_id'=>$player['Player']['id'],
							'GiftTournamentVolunteer.gift_tournament_id'=>$tournamentId)));
			// 최고 점수
			$total_highscore = $this->GiftTournamentVolunteer->query("SELECT player_id, highscore FROM gift_tournament_volunteers WHERE gift_tournament_id = $tournamentId ORDER BY highscore DESC, id limit 1;");
			// 현재 1등 참가자 정보
			$champion = $this->Player->find('first', array('conditions'=>array('Player.id'=>$total_highscore[0]['gift_tournament_volunteers']['player_id'])));
			// 참가자 전체 평균 점수
			$total_average = $this->GiftTournamentVolunteer->query("SELECT AVG(highscore) as average FROM gift_tournament_volunteers WHERE gift_tournament_id = $tournamentId;");
			$result = array("GiftTournamentVolunteer"=>array(
						"total_average"=>$total_average[0][0]['average'],
						"own_entries_count"=>$entries_count,
						"total_highscore"=>$total_highscore[0]['gift_tournament_volunteers']['highscore'],
						"nickname"=>$champion['Player']['nickname'],
						"own_gold"=>$champion['Player']['own_gold']
						));
		} else {
			$result = "";
		}
		$this->resultRender($result);
	}

	public function result() {
		$this->validateQuery('tournament_id');
		$tournamentId   = $this->request->query['tournament_id'];
		Controller::loadModel('GiftTournament');
		// 현재 토너먼트 정보 읽기
		$gift = $this->GiftTournament->find('first', array('conditions'=>array('GiftTournament.id' => $tournamentId)));
		if(count($gift) > 0) {
			$start = $gift['GiftTournament']['started'];
			$finish = $gift['GiftTournament']['finished'];
			$today = date('Y-m-d H:i:s');
			/* 토너먼트 상태 체크*/
			if($start > $today) $type = 0; // 토너먼트 시작전
			else if($finish < $today) $type = 2; // 토너먼트 완료
			else $type = 1; // 토너먼트 진행중

			if($type == 2) {
				// player_id 구하자
				Controller::loadModel('Player');
				$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
				Controller::loadModel('GiftTournamentRankings');
				$isRanking = $this->GiftTournamentRankings->find('count');
				if($isRanking < 1) {
					/* 1등 선정 */
					$rankAllData = array();
					$no1 = $this->GiftTournamentVolunteer->find('first', array(
								'fields' => array('GiftTournamentVolunteer.player_id', 
									'GiftTournamentVolunteer.highscore' ),
								'conditions' => array('GiftTournamentVolunteer.gift_tournament_id'=>$tournamentId),
								'order' => array('GiftTournamentVolunteer.highscore DESC','GiftTournamentVolunteer.id')
								));
					$rankData = array("GiftTournamentRankings"=>array("gift_tournament_id"=>$tournamentId
								,"player_id"=>$no1['GiftTournamentVolunteer']['player_id']
								,"ranking"=>1
								,"created"=>date('Y-m-d H:i:s')
								));
					array_push($rankAllData,$rankData);
					/* 2등 추첨 */
					$q = "SELECT player_id, COUNT(*) AS COUNT FROM gift_tournament_volunteers WHERE player_id <> ".$no1['GiftTournamentVolunteer']['player_id']." AND gift_tournament_id = $tournamentId GROUP BY player_id ORDER BY COUNT DESC, RAND() LIMIT 1;";
					$no2 = $this->GiftTournamentVolunteer->query($q);
					$sub = ""; $len = count($no2);
					for($i = 0 ; $i < $len ; $i++)
					{
						$sub .= $no2[$i]['gift_tournament_volunteers']['player_id'].",";
						$rankData = array("GiftTournamentRankings"=>array("gift_tournament_id"=>$tournamentId
									,"player_id"=>$no2[$i]['gift_tournament_volunteers']['player_id']
									,"ranking"=>2
									,"created"=>date('Y-m-d H:i:s')
									));
						array_push($rankAllData,$rankData);
					}
					/* 3등 추첨 */
					$sub .= $no1['GiftTournamentVolunteer']['player_id'];
					$q = "SELECT player_id, highscore FROM gift_tournament_volunteers WHERE gift_tournament_id = $tournamentId AND player_id NOT IN (".$sub.") AND highscore > (SELECT AVG(highscore) FROM gift_tournament_volunteers) ORDER BY RAND() LIMIT 1;";
					$no3 = $this->GiftTournamentVolunteer->query($q);
					$len = count($no3);
					for($i = 0 ; $i < $len ; $i++)
					{
						$rankData = array("GiftTournamentRankings"=>array("gift_tournament_id"=>$tournamentId
									,"player_id"=>$no3[$i]['gift_tournament_volunteers']['player_id']
									,"ranking"=>3
									,"created"=>date('Y-m-d H:i:s')
									));
						array_push($rankAllData,$rankData);
					}
					/* 랭킹 정보 저장 */
					for($i = 0 ; $i < count($rankAllData) ; $i++) {
						$this->GiftTournamentRankings->save($rankAllData[$i]);
						$this->GiftTournamentRankings->create();
					}
					// 본인 랭킹
					$myRanking = $this->GiftTournamentRankings->find('first', 
							array('conditions'=>array('GiftTournamentRankings.player_id'=>$player['Player']['id'])));
					if(count($myRanking) > 0) {
						$result = array('GiftTournamentVolunteer'=>array("type"=>$type,"ranking"=>$myRanking['GiftTournamentRankings']['ranking']));
					} else {
						$result = array('GiftTournamentVolunteer'=>array("type"=>$type,"ranking"=>0));
					}
					$this->resultRender($result);

				} else {
					// 이미 랭킹이 있을때
					$myRanking = $this->GiftTournamentRankings->find('first', 
							array('conditions'=>array('GiftTournamentRankings.player_id'=>$player['Player']['id'])));
					if(count($myRanking) > 0) {
						$result = array('GiftTournamentVolunteer'=>array("type"=>$type,"ranking"=>$myRanking['GiftTournamentRankings']['ranking']));
					} else {
						$result = array('GiftTournamentVolunteer'=>array("type"=>$type,"ranking"=>0));
					}
					$this->resultRender($result);
				}
			} else {
				// type 설정 0, 1, 2
				$result = array('GiftTournamentVolunteer'=>array("type"=>$type,"ranking"=>0));
				$this->resultRender($result);
			}
		} else {
			$this->error(3000);
		}

	}

}
