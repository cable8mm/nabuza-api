<?php
App::uses('AppModel', 'Model');
/**
 * GameInfo Model
 *
 */
class GameInfo extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'game_info';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'number' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
}
