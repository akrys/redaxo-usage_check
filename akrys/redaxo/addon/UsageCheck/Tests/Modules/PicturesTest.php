<?php

/**
 * Tests für Medien
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\Modules;

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
		$this->setExpectedException('akrys\\redaxo\\addon\\UsageCheck\\Exception\\FunctionNotCallableException');
		\rex::setVersion(\rex::VERSION_INVALID);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertEquals(-1, $pictures);
	}

	/**
	 * Test create, wenn es Redaxo in Version 4 vorliegt
	 */
	public function testCreate4()
	{
		\rex::setVersion(\rex::VERSION_4);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV4\\Modules\\Pictures', get_class($pictures));
	}

	/**
	 * Test create, wenn es Redaxo in Version 5 vorliegt
	 */
	public function testCreate5()
	{
		\rex::setVersion(\rex::VERSION_5);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Modules\\Pictures', get_class($pictures));
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
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$pictures->showAll(true);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($pictures));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean False wird übergeben
	 */
	public function testShowAllFalse()
	{
		\rex::setVersion(\rex::VERSION_5);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$pictures->showAll(false);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($pictures));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 1 wird übergeben
	 */
	public function testShowAll1()
	{
		\rex::setVersion(\rex::VERSION_5);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$pictures->showAll(1);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($pictures));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 0 wird übergeben
	 */
	public function testShowAll0()
	{
		\rex::setVersion(\rex::VERSION_5);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$pictures->showAll(0);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($pictures));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall irgendein string wird übergeben
	 */
	public function testShowAllString()
	{
		\rex::setVersion(\rex::VERSION_5);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$pictures->showAll('adsfasdfasdadsf');
		$property = new \ReflectionProperty('\\akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($pictures));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall ein Leerstring wird übergeben
	 */
	public function testShowAllStringFalse()
	{
		\rex::setVersion(\rex::VERSION_5);
		$pictures = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();

		$pictures->showAll('');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($pictures));
	}
	// </editor-fold>
	// <editor-fold defaultstate="collapsed" desc="Größen">

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall Bytes
	 */
	public function testGetSizeOutByte()
	{
		$size = 1;
		$item = array('filesize' => $size);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 B', 'B nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall kB
	 */
	public function testGetSizeOutKB()
	{
		$size = (1024) + 1;
		$item = array('filesize' => $size);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 kB', 'KB nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall MB
	 */
	public function testGetSizeOutMB()
	{
		$size = (1024 * 1024) + 1;
		$item = array('filesize' => $size);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 MB', 'MB nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall GB
	 */
	public function testGetSizeOutGB()
	{
		$size = (1024 * 1024 * 1024) + 1;
		$item = array('filesize' => $size + 1);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 GB', 'GB nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall TB
	 */
	public function testGetSizeOutTB()
	{
		$size = (1024 * 1024 * 1024 * 1024) + 1;
		$item = array('filesize' => $size + 1);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 TB', 'TB nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall EB
	 */
	public function testGetSizeOutEB()
	{
		$size = (1024 * 1024 * 1024 * 1024 * 1024) + 1;
		$item = array('filesize' => $size + 1);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 EB', 'EB nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall PB
	 */
	public function testGetSizeOutPB()
	{
		$size = (1024 * 1024 * 1024 * 1024 * 1024 * 1024) + 1024;
		$item = array('filesize' => $size + 1);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 PB', 'PB nicht richtig');
	}

	/**
	 * Test für Medien-Dateigrößen
	 * TestFall nicht mehr unterstützte Größe
	 */
	public function testGetSizeOutTooMuch()
	{
		$size = (1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024) + (1024 * 1024);
		$item = array('filesize' => $size + 1);
		$object = new \akrys\redaxo\addon\UsageCheck\RexV5\Modules\Pictures();
		$sizeOut = $object->getSizeOut($item);
		$this->assertEquals($sizeOut, '1 ????', '�berlauf nicht richtig');
	}

	// </editor-fold>
}
