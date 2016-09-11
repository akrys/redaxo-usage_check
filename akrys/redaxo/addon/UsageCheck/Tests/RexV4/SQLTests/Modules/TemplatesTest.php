<?php

/**
 * Tests für Templates
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\SQLTests\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class TemplateTest
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
	 *  get Funktion für Redaxo 5
	 */
	public function testGet()
	{
		\rex_user::setAdmin(true);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion mit inaktiven für Redaxo 5
	 */
	public function testGetInactive()
	{
		\rex_user::setAdmin(true);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$api->showInactive(true);
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion mit inaktiven für Redaxo 5
	 */
	public function testGetShowAll()
	{
		\rex_user::setAdmin(true);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$api->showAll(true);
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion mit inaktiven für Redaxo 5
	 */
	public function testGetShowAllInactive()
	{
		\rex_user::setAdmin(true);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$api->showAll(true);
		$api->showInactive(true);
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion für Redaxo 5 aber ohne Admin
	 */
	public function testGetNonAdmin()
	{
		\rex_user::setAdmin(false);

		\rex_module_perm::setHasRight(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion für Redaxo 5 aber ohne Admin
	 */
	public function testGetInactiveNonAdmin()
	{
		\rex_user::setAdmin(false);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$api->showInactive(true);
		$this->assertTrue(is_array($api->getTemplates()));
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion für Redaxo 5 aber ohne Admin
	 */
	public function testGetAllNonAdmin()
	{
		\rex_user::setAdmin(false);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$api->showAll(true);
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 *  get Funktion mit inaktiven für Redaxo 5
	 */
	public function testGetShowAllInactiveNonAdmin()
	{

		\rex_user::setAdmin(false);
		\rex_user::setRight('pages', true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$api->showAll(true);
		$api->showInactive(true);
		$this->assertTrue(is_array($api->getTemplates()));
	}

	/**
	 * Test ohne Rechte
	 */
	public function testnoRight()
	{

		\rex_user::setAdmin(false);
		\rex_user::setRight('pages', false);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$this->assertFalse($api->getTemplates());
	}
}
