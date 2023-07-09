<?php
App::uses('AppController', 'Controller');
/**
 * Events Controller
 *
 * @property Event $Event
 */
class EventsController extends AppController {
	var $uses	= array('Event', 'Player');
	
	public function get() {
		$now	= date('Y-m-d H:i:s');
		
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$events	= $this->Event->find('all', array('fields'=>array('link_url'), 'conditions'=>array('Event.is_active' => 1, 'Event.language_id'=>$player['Player']['language_id'])));
		$this->resultRender($events);
	}
	
}
