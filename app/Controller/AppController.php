<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	var $helpers	= array('Xml');
	var $layout	= 'json';
	var $accessToken;
	var $type;
	
	public function beforeFilter() {
		parent::beforeFilter();

		if(isset($this->params->query['type']) && $this->params->query['type'] == 'json') {
			$this->type	= 'json';
			Configure::write('debug', 0);
		} else {
			$this->type = 'xml';
		}
		
		if ($this->request->params['controller'] == 'access_tokens' && $this->request->params['action'] == 'get') return;

        if($this->request->is('post'))
		{
            $token = @$this->request->data['token'];
		} else {
			$token = @$this->request->query['token'];
		}
		
		if(empty($token)) {
			$this->error(1);
		}
		
		Controller::loadModel('AccessToken');
		$this->accessToken      = $this->AccessToken->find('first', array('conditions' => array('AccessToken.token' => $token)));
		
		if(empty($this->accessToken)) {
			$this->error(1);
		}

	}
	
	public function validateQuery() {	// array
		$queries	= func_get_args();
		foreach($queries as $query) {
			if (!isset($this->request->query[$query])) {
				$this->error(2);
			}
		}
	}
	
	private $errorMessage	= array(
			0 => 'OK',
			1 => 'INVALID_TOKEN',
			2 => 'INVALID_PARAMETERS',
			3 => 'INVALID_PHONE_NUMBER',
			4 => 'INVALID_AUTH_NUMBER',
			10 => 'INVALID_CONSUMER',
			100 => 'VALIDATION_ERROR',
			1000 => 'INVALIDE_KEY',
			1100 => 'NO_PLAYER',
			1200 => 'NEED_MORE_INFO',
			3000 => 'INVALID_TOURNAMENT_ID',
			4000 => 'INVALID_COUPON',
			4500 => 'NEED_MORE_GOLD',
			4501 => 'NOT_PLAYER_GOLDMALL_ITEM',
			4502 => 'NOT_VALID_SUBMIT_TIME',
			4503 => 'NEED_MORE_JADE',
			5000 => 'NOT_LANGUAGE_PACK_EXISTED',
			5100 => 'NOT_LANGUAGE_ID_EXISTED',
			9000 => 'NOT_INSERTED',
			9100 => 'NOT_UPDATED'
			);
	
	public function error($code=0) {
	
		$result['code']	= $code;
		$result['message']	= $this->errorMessage[$code];
		$result['result']	= array();
		
		$this->set('result', $result);
		if ($this->type == 'json') {
			$this->render('/Pages/json');
		} else {
			$this->render('/Pages/xml');
		}
		
		$this->response->send();
		die();
	}
	
	public function resultRender($result = array()) {
		$output['code']	= 0;
		$output['message']	= $this->errorMessage[0];
		$output['result']	= $result;

		$this->set('result', $output);

		if ($this->type == 'json') {
			$this->render('/Pages/json');
		} else {
			$this->render('/Pages/xml');
		}
		
		$this->response->send();
		$this->_stop();
	}
}
