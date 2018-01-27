<?php

/**
 * Tests für Module
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class ModulesTest
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		//
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		//
	}

	// <editor-fold defaultstate="collapsed" desc="create function">
	/**
	 * Test create, wenn es keine gültige Redaxoversion gibt
	 */
	public function testCreateNoValidVersion()
	{
		$this->expectException('akrys\\redaxo\\addon\\UsageCheck\\Exception\\FunctionNotCallableException');
		\rex::setVersion(\rex::VERSION_INVALID);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();
		$this->assertEquals(-1, $modules);
	}

	// </editor-fold>
	// <editor-fold defaultstate="collapsed" desc="ShowAll Parameter">
	/**
	 * Test create, wenn es Redaxo in Version 5 vorliegt
	 */
	public function testCreate5()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Modules\\Modules', get_class($modules));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean True wird übergeben
	 */
	public function testShowAllTrue()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$modules->showAll(true);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Modules', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($modules));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean False wird übergeben
	 */
	public function testShowAllFalse()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$modules->showAll(false);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Modules', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($modules));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 1 wird übergeben
	 */
	public function testShowAll1()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$modules->showAll(1);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Modules', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($modules));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 0 wird übergeben
	 */
	public function testShowAll0()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$modules->showAll(0);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Modules', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($modules));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall irgendein string wird übergeben
	 */
	public function testShowAllString()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$modules->showAll('adsfasdfasdadsf');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Modules', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($modules));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall ein Leerstring wird übergeben
	 */
	public function testShowAllStringFalse()
	{
		\rex::setVersion(\rex::VERSION_5);
		$modules = \akrys\redaxo\addon\UsageCheck\Modules\Modules::create();

		$modules->showAll('');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Modules', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($modules));
	}
	// </editor-fold>
}
