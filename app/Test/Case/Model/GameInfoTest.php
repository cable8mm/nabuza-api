<?php
App::uses('GameInfo', 'Model');

/**
 * GameInfo Test Case
 *
 */
class GameInfoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.game_info'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->GameInfo = ClassRegistry::init('GameInfo');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->GameInfo);

		parent::tearDown();
	}

}
