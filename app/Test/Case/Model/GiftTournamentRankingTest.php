<?php
App::uses('GiftTournamentRanking', 'Model');

/**
 * GiftTournamentRanking Test Case
 *
 */
class GiftTournamentRankingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.gift_tournament_ranking',
		'app.gift_tournament',
		'app.player'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->GiftTournamentRanking = ClassRegistry::init('GiftTournamentRanking');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->GiftTournamentRanking);

		parent::tearDown();
	}

}
