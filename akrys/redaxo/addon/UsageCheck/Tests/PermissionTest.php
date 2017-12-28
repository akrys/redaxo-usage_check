<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-07-30
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests;

/**
 * Description of ErrorTest
 *
 * @author akrys
 */
class PermissionTest
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Alles zurücksetzen
	 */
	protected function tearDown()
	{
		parent::tearDown();
		$permissionClass = new \ReflectionClass('akrys\\redaxo\\addon\\UsageCheck\\Permission');
		$property = $permissionClass->getProperty('api');
		$property->setAccessible(true);
		$property->setValue(null);
	}

	// <editor-fold defaultstate="collapsed" desc="create function">
	/**
	 * Test create, wenn es keine gültige Redaxoversion gibt
	 */
	public function testCreateNoValidVersion()
	{
		$this->expectException('\\akrys\\redaxo\\addon\\UsageCheck\\Exception\\InvalidVersionException');
		\rex::setVersion(\rex::VERSION_INVALID);
		$object = \akrys\redaxo\addon\UsageCheck\Permission::getVersion();
		$this->assertEquals(-1, $object);
	}

	/**
	 * Test create, wenn es Redaxo in Version 5 vorliegt
	 */
	public function testCreate5()
	{
		\rex::setVersion(\rex::VERSION_5);
		$object = \akrys\redaxo\addon\UsageCheck\Permission::getVersion();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Permission', get_class($object));
	}
	// </editor-fold>
}
