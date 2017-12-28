<?php

/**
 * Tests für Actions
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class ActionsTest
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
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();
		$this->assertEquals(-1, $actions);
	}

	// </editor-fold>
	// <editor-fold defaultstate="collapsed" desc="ShowAll Parameter">
	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean True wird übergeben
	 */
	public function testShowAllTrue()
	{
		\rex::setVersion(\rex::VERSION_5);
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$actions->showAll(true);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Actions', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($actions));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean False wird übergeben
	 */
	public function testShowAllFalse()
	{
		\rex::setVersion(\rex::VERSION_5);
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$actions->showAll(false);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Actions', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($actions));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 1 wird übergeben
	 */
	public function testShowAll1()
	{
		\rex::setVersion(\rex::VERSION_5);
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$actions->showAll(1);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Actions', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($actions));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 0 wird übergeben
	 */
	public function testShowAll0()
	{
		\rex::setVersion(\rex::VERSION_5);
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$actions->showAll(0);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Actions', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($actions));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall irgendein string wird übergeben
	 */
	public function testShowAllString()
	{
		\rex::setVersion(\rex::VERSION_5);
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$actions->showAll('adsfasdfasdadsf');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Actions', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($actions));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall ein Leerstring wird übergeben
	 */
	public function testShowAllStringFalse()
	{
		\rex::setVersion(\rex::VERSION_5);
		$actions = \akrys\redaxo\addon\UsageCheck\Modules\Actions::create();

		$actions->showAll('');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Actions', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($actions));
	}
	// </editor-fold>
}
