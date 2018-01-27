<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-08-09
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5;

/**
 * Description of PermissionRex4Test
 *
 * @author akrys
 */
class PermissionRex5Test
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Aufsetzen der Simulation
	 */
	public function setUp()
	{
		parent::setUp();
		\rex::setVersion(\rex::VERSION_5);
	}

	/**
	 * Funktionstest der Funktion map()
	 */
	public function testMap()
	{
		$permission = \akrys\redaxo\addon\UsageCheck\Permission::getVersion();
		$function = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Permission', 'mapPerm');
		$function->setAccessible(true);

		$this->assertEquals('mediapool', $function->invokeArgs($permission, array('perm' => \akrys\redaxo\addon\UsageCheck\RexV5\Permission::PERM_MEDIAPOOL)));
		$this->assertEquals('media', $function->invokeArgs($permission, array('perm' => \akrys\redaxo\addon\UsageCheck\RexV5\Permission::PERM_MEDIA)));
		$this->assertEquals('modules', $function->invokeArgs($permission, array('perm' => \akrys\redaxo\addon\UsageCheck\RexV5\Permission::PERM_MODUL)));
		$this->assertEquals('structure', $function->invokeArgs($permission, array('perm' => \akrys\redaxo\addon\UsageCheck\RexV5\Permission::PERM_STRUCTURE)));
		$this->assertEquals('template', $function->invokeArgs($permission, array('perm' => \akrys\redaxo\addon\UsageCheck\RexV5\Permission::PERM_TEMPLATE)));
		$this->assertEquals('asdfg', $function->invokeArgs($permission, array('perm' => 'asdfg')));
	}
}
