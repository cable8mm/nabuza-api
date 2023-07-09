<?php
App::uses('AppController', 'Controller');
/**
 * Notices Controller
 *
 * @property Notice $Notice
 */
class NoticesController extends AppController {
	var $uses	= array('Notice', 'Player');
/**
 * index method
 *
 * @return void
 */

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Notice->id = $id;
		if (!$this->Notice->exists()) {
			$this->error(1000);
		}
		$this->resultRender($this->Notice->read(null, $id));
	}
	
	public function last() {
		$now	= date('Y-m-d H:i:s');
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$notices	= $this->Notice->find('all', array('fields'=>array('id','contents'), 'conditions'=>array('Notice.is_active' => 1, 'Notice.started <= ' => $now, 'Notice.finished >= '=> $now, 'Notice.language_id'=>$player['Player']['language_id'])));
		$this->resultRender($notices);
	}
	
	public function last2() {
		$now	= date('Y-m-d H:i:s');
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$notices	= $this->Notice->find('all', array('fields'=>array('id','contents'), 'limit'=>3));
		
		foreach ($notices as $k => $notice) {
			$notices[$k]['Notice']['id']	= intval($notices[$k]['Notice']['id']);
		}
		
		$this->resultRender($notices);
	}
}
