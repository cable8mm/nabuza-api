<?php
App::uses('AppController', 'Controller');
/**
 * SpentJades Controller
 *
 * @property SpentJade $SpentJade
 */
class SpentJadesController extends AppController {

	public function buy_invitation() {
		$this->validateQuery('spend_count', 'buyed_count');
		$method	= 1;	// 초대
		$spendCount	= $this->request->query['spend_count'];
		$buyedCount	= $this->request->query['buyed_count'];
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$spentJade['SpentJade']['player_id'] = $player['Player']['id'];
		$spentJade['SpentJade']['method'] = $method;
		$spentJade['SpentJade']['spent_count'] = $spendCount;
		$spentJade['SpentJade']['buyed_count'] = $buyedCount;
		
		$player['Player']['own_jade_count']	-= $spendCount;
		$player['Player']['invitation_count']	+= $buyedCount;
		
		if ($player['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}
		
		if ($this->SpentJade->save($spentJade)) {
			if ($this->Player->save($player)) {
				$this->resultRender(true);
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(9000);
		}
	}

	public function buy_invitation2() {
		$this->validateQuery('spend_count', 'buyed_count');	
		$method	= 1;	// 초대
		$spendCount	= $this->request->query['spend_count'];
		$buyedCount	= $this->request->query['buyed_count'];
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$this->Player->id	= $player['Player']['id'];
		
		$spentJade['SpentJade']['player_id'] = $player['Player']['id'];
		$spentJade['SpentJade']['method'] = $method;
		$spentJade['SpentJade']['spent_count'] = $spendCount;
		$spentJade['SpentJade']['buyed_count'] = $buyedCount;
	
		$updatePlayer['Player']['own_jade_count']	= $player['Player']['own_jade_count'] - $spendCount;
		$updatePlayer['Player']['invitation_count']	= $player['Player']['invitation_count'] + $buyedCount;
		
		if ($updatePlayer['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}

		if ($this->SpentJade->save($spentJade)) {
			if ($this->Player->save($updatePlayer)) {
				$updatedPlayer	= $this->Player->find('first', array('fields'=>array('Player.own_jade_count'), 'conditions'=>array('Player.id' => $this->Player->id)));
				$updatedPlayer['Player']['own_jade_count']	= intval($updatedPlayer['Player']['own_jade_count']);
				$updatedPlayer['SpentJade']['buyed_count']	= intval($buyedCount);
				$this->resultRender($updatedPlayer);
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(9000);
		}
	}
	
	public function buy_gold() {
		$this->validateQuery('spend_count', 'buyed_count');
		$method	= 2;	// 초대
		$spendCount	= $this->request->query['spend_count'];
		$buyedCount	= $this->request->query['buyed_count'];
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('fields'=>array('Player.own_jade_count', 'Player.own_gold', 'Player.id'), 'conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$spentJade['SpentJade']['player_id'] = $player['Player']['id'];
		$spentJade['SpentJade']['method'] = $method;
		$spentJade['SpentJade']['spent_count'] = $spendCount;
		$spentJade['SpentJade']['buyed_count'] = $buyedCount;
		
		$player['Player']['own_jade_count']	-= $spendCount;
		$player['Player']['own_gold']	+= $buyedCount;

		if ($player['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}
		
		if ($player['Player']['own_gold'] < 0) {
			$this->error(4500);
		}
		
		if ($this->SpentJade->save($spentJade)) {
			if ($this->Player->save($player)) {
				$this->resultRender(true);
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(9000);
		}
	}
	
	public function buy_gold2() {
		$this->validateQuery('spend_count', 'buyed_count');
		$method	= 2;	// 초대
		$spendCount	= $this->request->query['spend_count'];
		$buyedCount	= $this->request->query['buyed_count'];
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('fields'=>array('Player.own_jade_count', 'Player.own_gold', 'Player.id'), 'conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
	
		$spentJade['SpentJade']['player_id'] = $player['Player']['id'];
		$spentJade['SpentJade']['method'] = $method;
		$spentJade['SpentJade']['spent_count'] = $spendCount;
		$spentJade['SpentJade']['buyed_count'] = $buyedCount;
	
		$player['Player']['own_jade_count']	-= $spendCount;
		$player['Player']['own_gold']	+= $buyedCount;
	
		if ($player['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}
	
		if ($player['Player']['own_gold'] < 0) {
			$this->error(4500);
		}
	
		if ($this->SpentJade->save($spentJade)) {
			if ($this->Player->save($player)) {
				$player['Player']['id']	= intval($player['Player']['id']);
				$this->resultRender($player);
			} else {
				$this->error(9000);
			}
		} else {
			$this->error(9000);
		}
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->SpentJade->recursive = 0;
		$this->set('spentJades', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->SpentJade->id = $id;
		if (!$this->SpentJade->exists()) {
			throw new NotFoundException(__('Invalid spent jade'));
		}
		$this->set('spentJade', $this->SpentJade->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SpentJade->create();
			if ($this->SpentJade->save($this->request->data)) {
				$this->flash(__('Spentjade saved.'), array('action' => 'index'));
			} else {
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
		$this->SpentJade->id = $id;
		if (!$this->SpentJade->exists()) {
			throw new NotFoundException(__('Invalid spent jade'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SpentJade->save($this->request->data)) {
				$this->flash(__('The spent jade has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->SpentJade->read(null, $id);
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
		$this->SpentJade->id = $id;
		if (!$this->SpentJade->exists()) {
			throw new NotFoundException(__('Invalid spent jade'));
		}
		if ($this->SpentJade->delete()) {
			$this->flash(__('Spent jade deleted'), array('action' => 'index'));
		}
		$this->flash(__('Spent jade was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
