<?php
App::uses('AppModel', 'Model');
/**
 * Player Model
 *
 */
class Player extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'phone_number';

/**
 * Validation rules
 *
 * @var array
 */
	
// 	public $validate	= array(
// 			'phone_number' => array(
// 					'rule' => 'isUnique',
// 					'allowEmpty' => false
// 				),
// 	);
	
	public function afterSave($created) {
		App::import('Vendor', 'AnyTale/Classification');
		$classification	= new Classification();
		if(!empty($this->data['Player']['last_level'])) {
			if ($this->data['Player']['last_level'] < $classification->getGoldLevel($this->data['Player']['own_gold'])) {
				$this->data['Player']['last_level']	= $classification->getGoldLevel($this->data['Player']['own_gold']);
				$this->save($this->data, array('callbacks'=>false));
			}
		}
	}
}
