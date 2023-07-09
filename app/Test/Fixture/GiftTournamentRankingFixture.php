<?php
/**
 * GiftTournamentRankingFixture
 *
 */
class GiftTournamentRankingFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ROW ID'),
		'gift_tournament_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '토너먼트 ID'),
		'player_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '플레이어 ID'),
		'ranking' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '토너먼트 순위'),
		'created' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '생성시간'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'gift_tournament_id' => 1,
			'player_id' => 1,
			'ranking' => 1,
			'created' => 1358731850
		),
	);

}
