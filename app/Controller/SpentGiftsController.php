<?php
App::uses('AppController', 'Controller');
/**
 * SpentGifts Controller
 *
 * @property SpentGift $SpentGift
 */
class SpentGiftsController extends AppController {

	public function send() {
		$this->validateQuery('received_player_id', 'spent_count');
		$receivedPlayerId	= $this->request->query['received_player_id'];
		$spentCount	= $this->request->query['spent_count'];
		
		Controller::loadModel('Player');
		$spentPlayer	= $this->Player->find('first', array('conditions'=>array('Player.appid'=>$this->accessToken['AccessToken']['appid'])));
		$receivedPlayer	=  $this->Player->find('first', array('conditions'=>array('Player.id'=>$receivedPlayerId)));
		$spentGift['SpentGift']['it']	= 1;	// 비취
		$spentGift['SpentGift']['spent_player_id']	= $spentPlayer['Player']['id'];	// 비취
		$spentGift['SpentGift']['received_player_id']	= $receivedPlayerId;	// 비취
		$spentGift['SpentGift']['spent_count']	= $spentCount;	// 비취
		
		$spentPlayer['Player']['own_jade_count']	-= $spentCount;

		if ($spentPlayer['Player']['own_jade_count'] < 0) {
			$this->error(4503);
		}
		
		Controller::loadModel('Message');
		$data = array('Message'=>array('sent_player_id'=>$spentPlayer['Player']['id']
					,'received_player_id'=>$receivedPlayerId
					, 'type'=>0
					, 't_count'=>$spentCount
					));

		if ($this->SpentGift->save($spentGift) && $this->Player->save($spentPlayer) && $this->Message->save($data)) {
			$this->resultRender(true);
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
		$this->SpentGift->recursive = 0;
		$this->set('spentGifts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->SpentGift->id = $id;
		if (!$this->SpentGift->exists()) {
			throw new NotFoundException(__('Invalid spent gift'));
		}
		$this->set('spentGift', $this->SpentGift->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SpentGift->create();
			if ($this->SpentGift->save($this->request->data)) {
				$this->flash(__('Spentgift saved.'), array('action' => 'index'));
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
		$this->SpentGift->id = $id;
		if (!$this->SpentGift->exists()) {
			throw new NotFoundException(__('Invalid spent gift'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SpentGift->save($this->request->data)) {
				$this->flash(__('The spent gift has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->SpentGift->read(null, $id);
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
		$this->SpentGift->id = $id;
		if (!$this->SpentGift->exists()) {
			throw new NotFoundException(__('Invalid spent gift'));
		}
		if ($this->SpentGift->delete()) {
			$this->flash(__('Spent gift deleted'), array('action' => 'index'));
		}
		$this->flash(__('Spent gift was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
