<?php
App::uses('AppModel', 'Model');
/**
 * Notice Model
 *
 */
class Notice extends AppModel {

	public $order = 'Notice.id DESC';
	
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

}
