<?php
App::uses('AppController', 'Controller');
/**
 * Notices Controller
 *
 * @property Notice $Notice
 */
class InvitationMessagesController extends AppController {
	var $uses	= array('InvitationMessage', 'Player');
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
	public function get() {
		
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		
		$invitationMessage	= $this->InvitationMessage->find('first', array('fields'=>array('id', 'kakaotalk_message', 'sms_message'), 'conditions'=>array('InvitationMessage.Language_id'=> $player['Player']['language_id']), 'order'=>array('InvitationMessage.id DESC')));
		$this->resultRender($invitationMessage);
//		$this->set('notices', $notices);
	}
}
