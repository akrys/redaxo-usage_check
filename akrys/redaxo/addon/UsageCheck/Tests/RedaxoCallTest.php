<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-08-05
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests;

/**
 * Description of RedaxoCallTest
 *
 * @author akrys
 */
class RedaxoCallTest
	extends \PHPUnit\Framework\TestCase
{

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetApiInvalid()
	{
		$this->expectException('akrys\\redaxo\\addon\\UsageCheck\\Exception\\InvalidVersionException');
		\rex::setVersion(\rex::VERSION_INVALID);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertFalse(is_object($api));
	}

	/**
	 * Funktionstest getApiInvalid für Redaxo5
	 */
	public function testGetApiRex5()
	{
		\rex::setVersion(\rex::VERSION_5);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertTrue(is_object($api));
		$this->assertEquals(get_class($api), 'akrys\\redaxo\\addon\\UsageCheck\\RexV5\\RedaxoCallAPI');
	}

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetTaggedError()
	{
		\rex::setVersion(\rex::VERSION_5);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTaggedErrorMsg('test');
		$this->assertFalse(is_object($api));
	}

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetTaggedInformation()
	{
		\rex::setVersion(\rex::VERSION_5);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getTaggedInfoMsg('test');
		$this->assertFalse(is_object($api));
	}
}
