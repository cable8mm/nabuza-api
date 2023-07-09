<?php
App::uses('AppController', 'Controller');
/**
 * GameInfos Controller
 *
 * @property GameInfo $GameInfo
 */
class GameInfosController extends AppController {

	public function coinset_term() {
		$coinsetTerm        = $this->GameInfo->find('first', array('conditions' => array('GameInfo.id'=>2)));
		$this->resultRender($coinsetTerm['GameInfo']);
	}
}
