<?php

/**
 * Tests für Medien
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\SQLTests\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class PicturesTest
	extends \PHPUnit_Framework_TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		\rex::setVersion(\rex::VERSION_4);
		$GLOBALS['REX']['DB'][1]['NAME'] = 'redaxo_4_6_1';
		$GLOBALS['REX']['DB'][2]['NAME'] = '';
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		//
	}

	/**
	 * Funktionstest: get
	 */
	public function testGet()
	{
		\rex_addon::setAvailable('xform', true);
		\rex_plugin::setAvailable('xform', 'manager', true);

		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}


	/**
	 * Funktionstest: get
	 */
	public function testGetRex45()
	{
		$GLOBALS['REX']['DB'][1]['NAME'] = 'redaxo_4_5_0';
		\rex_addon::setAvailable('xform', true);
		\rex_plugin::setAvailable('xform', 'manager', true);

		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}

	/**
	 * Funktionstest get ohne Admin
	 */
	public function testGetNonAdmin()
	{
		\rex_addon::setAvailable('xform', true);
		\rex_plugin::setAvailable('xform', 'manager', true);
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertFalse($api->getPictures());
	}

	/**
	 * Funktionstest ohne Table Manager
	 */
	public function testGetWithXFormNoManager()
	{
		\rex_addon::setAvailable('xform', true);
		\rex_plugin::setAvailable('xform', 'manager', false);

		\rex_user::setAdmin(true);
		\rex_structure_perm::setHasRight(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}

	/**
	 * Funktionstest ohne YForm
	 */
	public function testGetNoYFrom()
	{
		\rex_addon::setAvailable('xform', false);
		\rex_plugin::setAvailable('xform', 'manager', false);

		\rex_user::setAdmin(true);
		\rex_structure_perm::setHasRight(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}
}
