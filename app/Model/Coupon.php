<?php
App::uses('AppModel', 'Model');
/**
 * Coupon Model
 *
 */
class Coupon extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'serial';

	public $belongsTo = array(
			'CouponIssue' => array(
					'className' => 'CouponIssue',
					'foreignKey' => 'coupon_issue_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);
}
