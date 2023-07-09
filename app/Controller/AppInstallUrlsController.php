<?php
App::uses('AppController', 'Controller');
/**
 * AppInstallUrls Controller
 *
 * @property AppInstallUrls $AppInstallUrl
 */
class AppInstallUrlsController extends AppController {
	
	var $uses = array('Notice');

	/**
	 * index method
	 *
	 * @return void
	 */
	public function beforeFilter() {
//		parent::beforeFilter();
	}
	
	public function get() {
		$this->layout	= 'html';
	}
}
