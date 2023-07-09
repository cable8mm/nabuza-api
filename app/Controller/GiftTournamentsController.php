<?php
App::uses('AppController', 'Controller');
/**
 * GiftTournaments Controller
 *
 * @property GiftTournament $GiftTournament
 */
class GiftTournamentsController extends AppController {

	public function getRank() {
		$this->resultRender();
	}
	
/**
 * index method
 *
 * @return void
 */
	public function get() {
		$this->GiftTournament->recursive = 0;
		$tournament	= $this->GiftTournament->find('first', array('order' => 'GiftTournament.id DESC'));
		$this->resultRender($tournament);
	}

	public function tournament_status() {
		$this->validateQuery('tournament_id');
		$tournamentId   = $this->request->query['tournament_id'];
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
			$result = array('tournament_status'=>$type);
			$this->resultRender($result);
		} else {
			$this->error(3000);
		}
	}

}
