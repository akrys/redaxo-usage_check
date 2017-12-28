<?php

/**
 * Tests für Templates
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\Modules;

/**
 * Description of BlogRSSTest
 *
 * @author akrys
 */
class TemplateTest
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{

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
	 * Test create, wenn es keine gültige Redaxoversion gibt
	 */
	public function testCreateNoValidVersion()
	{
		$this->expectException('akrys\\redaxo\\addon\\UsageCheck\\Exception\\FunctionNotCallableException');
		\rex::setVersion(\rex::VERSION_INVALID);
		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$this->assertEquals(-1, $templates);
	}
}
