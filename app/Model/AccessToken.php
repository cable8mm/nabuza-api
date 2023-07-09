<?php
App::uses('AppModel', 'Model');
/**
 * AccessToken Model
 *
 * @property Consumer $Consumer
 */
class AccessToken extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'token';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $validate	= array(
			'token' => 'isUnique'
			);
}
