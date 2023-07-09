<?php
App::uses('AppController', 'Controller');
/**
 * Versions Controller
 *
 * @property Version $Version
 */
class VersionsController extends AppController {

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function current() {
		$version        = $this->Version->find('first', array('order' => 'Version.id DESC', 'fields' => array('Version.version')));
		Controller::loadModel('GameInfo');
		$gameNumber = $this->GameInfo->find('first', array('conditions'=>array('GameInfo.id'=>1)));
		$result = array('version'=>$version['Version']['version'],'game_number'=>$gameNumber['GameInfo']['number']);
		$this->resultRender($result);
	}
}
