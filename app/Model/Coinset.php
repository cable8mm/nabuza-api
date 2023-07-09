<?php
App::uses('AppModel', 'Model');
/**
 * Coinset Model
 *
 * @property CoinsetOrder $CoinsetOrder
 */
class Coinset extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';
	var $order = 'Coinset.id ASC';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CoinsetOrder' => array(
			'className' => 'CoinsetOrder',
			'foreignKey' => 'coinset_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
