<?php

/**
 * Tests für Medien
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\SQLTests\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class PicturesTest
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		\rex::setVersion(\rex::VERSION_5);
		\rex_addon::setAvailable('yform', true);
		\rex_plugin::setAvailable('yform', 'manager', true);
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
	 * Funktionstest mit YForm aber ohne Table Manager
	 */
	public function testGetWithYFormNoManager()
	{
		\rex_addon::setAvailable('yform', true);
		\rex_plugin::setAvailable('yform', 'manager', false);

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
		\rex_addon::setAvailable('yform', false);
		\rex_plugin::setAvailable('yform', 'manager', false);

		\rex_user::setAdmin(true);
		\rex_structure_perm::setHasRight(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}

	/**
	 * Test der Funktion get mit Adminrechten
	 */
	public function testGet()
	{
		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue(is_array($api->getPictures()));
	}

	/**
	 * Test der Funktion get ohne Adminrechten
	 */
	public function testGetNonAdmin()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertFalse($api->getPictures());
	}

	/**
	 * Test für die YFORM 2.0 anpassung
	 *
	 * Test, wenn das Feld multiple enthalten ist
	 *
	 */
	public function testHasMultipleTrue()
	{
		$function = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Modules\\Pictures', 'hasMultiple');
		$function->setAccessible(true);

		$params = array(
			'yformFieldTable' => \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('yform_field'),
			'dbs' => null,
		);

		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertTrue($function->invokeArgs($api, $params));
	}

	/**
	 * Test für die YFORM 2.0 anpassung
	 *
	 * Test, wenn das Feld multiple nicht enthalten ist
	 *
	 */
	public function testHasMultipleFalse()
	{
		$function = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Modules\\Pictures', 'hasMultiple');
		$function->setAccessible(true);

		$params = array(
			'yformFieldTable' => \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTable('yform_field'),
			//In Redaxo 5.1 kann YFORM 2 nicht laufen
			'dbs' => array(array('name' => 'redaxo_5_1'), array('name' => '')),
		);

		$api = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertFalse($function->invokeArgs($api, $params));
	}
}
