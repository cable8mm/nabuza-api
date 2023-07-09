<?php
App::uses('AppController', 'Controller');
/**
 * Languages Controller
 *
 * @property Language $Language
 */
class LanguagesController extends AppController {

	public function last() {
		$this->validateQuery('country');
		$country      = $this->request->query['country'];
		$language	= $this->Language->find('first', array('fields'=>array('iso', 'language_pack_url'), 'conditions'=>array('country_code'=>$country, 'language_pack_url <> ' => '')));
		
		if (!$language) {
			$country	= 'EN';
			$language	= $this->Language->find('first', array('fields'=>array('iso', 'language_pack_url'), 'conditions'=>array('country_code'=>'', 'language_pack_url <> ' => '')));
		}
		
		if ($language) {
			$this->resultRender($language['Language']);
		} else {
			$this->error(5000);
		}
	}
}
