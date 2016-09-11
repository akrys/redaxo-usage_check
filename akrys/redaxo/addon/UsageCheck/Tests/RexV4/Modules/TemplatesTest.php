<?php

/**
 * Tests für Templates
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4\Modules;

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
	 * Test create, wenn es Redaxo in Version 4 vorliegt
	 */
	public function testCreate()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV4\\Modules\\Templates', get_class($templates));
	}

	/**
	 * Funktionstest OutputMenu
	 */
	public function testOutputMenu()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
		<ul>
			<li><a href="index.php?page=usage_check&subpage=test&showinactive=true">akrys_usagecheck_template_link_show_unused</a></li>
		</ul>
TEXT;

		ob_start();
		$object->outputMenu('test', '&b=2', 'test');
		$text = ob_get_clean();

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($text));

		$this->assertEquals($exp, $cur);
	}

	/**
	 * Funktionstest OutputMenu asl Admin
	 */
	public function testOutputMenuAdmin()
	{
		\rex_user::setAdmin(true);
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
<ul><li><a href="index.php?page=usage_check&subpage=test&showinactive=true">akrys_usagecheck_template_link_show_unused</a></li><li><a href="index.php?page=usage_check&subpage=test&showall=true">akrys_usagecheck_template_link_show_active</a></li></ul>
TEXT;

		ob_start();
		$object->outputMenu('test', '&b=2', 'test');
		$text = ob_get_clean();

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($text));

		$this->assertEquals($exp, $cur);
	}

	/**
	 * Funktionstest OutputMenu
	 */
	public function testOutputTemplateEdit()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
<a href="index.php?page=template&subpage=&function=edit&template_id=test">&b=2</a>
TEXT;

		ob_start();
		$object->outputTemplateEdit(array('id' => 'test'), '&b=2', 'test');
		$text = ob_get_clean();

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($text));

		$this->assertEquals($exp, $cur);
	}

	/**
	 * Funktionstest OutputMenu asl Admin
	 */
	public function testOutputTemplateEditAdmin()
	{
		\rex_user::setAdmin(true);
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
<a href="index.php?page=template&subpage=&function=edit&template_id=test">&b=2</a>
TEXT;

		ob_start();
		$object->outputTemplateEdit(array('id' => 'test'), '&b=2', 'test');
		$text = ob_get_clean();

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($text));

		$this->assertEquals($exp, $cur);
	}

	/**
	 * Funktionstest getEditLink
	 */
	public function testGetEditLink()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
index.php?page=template&subpage=&function=edit&template_id=test
TEXT;

		$text = $object->getEditLink('test');

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($text));

		$this->assertEquals($exp, $cur);
	}

	/**
	 * Funktionstest hasArticlePerm mit rechten
	 */
	public function testHasArticlePerm()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
//		\rex_structure_perm::setHasRight(true);
		\rex_user::setRight('test', true);
		$expected = $object->hasArticlePerm('test');
		$this->assertTrue($expected);
	}

	/**
	 * Funktionstest hasArticlePerm ohne rechte
	 */
	public function testHasArticlePermNOPerm()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
//		\rex_structure_perm::setHasRight(false);
		\rex_user::setRight('test', false);
		$expected = $object->hasArticlePerm('test');
		$this->assertFalse($expected);
	}

	// <editor-fold defaultstate="collapsed" desc="ShowAll Parameter">
	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean True wird übergeben
	 */
	public function testShowAllTrue()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->showAll(true);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall boolean False wird übergeben
	 */
	public function testShowAllFalse()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->showAll(false);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Pictures', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 1 wird übergeben
	 */
	public function testShowAll1()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->showAll(1);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall int 0 wird übergeben
	 */
	public function testShowAll0()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->showAll(0);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall irgendein string wird übergeben
	 */
	public function testShowAllString()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->showAll('adsfasdfasdadsf');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowAll
	 * Testfall ein Leerstring wird übergeben
	 */
	public function testShowAllStringFalse()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->showAll('');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showAll');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($templates));
	}

	// </editor-fold>
	// <editor-fold defaultstate="collapsed" desc="Show Inactive Parameter">
	/**
	 * Tests für den Parameter ShowInactive
	 * Testfall boolean True wird übergeben
	 */
	public function testShowInactiveTrue()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->ShowInactive(true);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowInactive
	 * Testfall boolean False wird übergeben
	 */
	public function testShowInactiveFalse()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->ShowInactive(false);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowInactive
	 * Testfall int 1 wird übergeben
	 */
	public function testShowInactive1()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->ShowInactive(1);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowInactive
	 * Testfall int 0 wird übergeben
	 */
	public function testShowInactive0()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->ShowInactive(0);
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowInactive
	 * Testfall irgendein string wird übergeben
	 */
	public function testShowInactiveString()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->ShowInactive('adsfasdfasdadsf');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$property->setAccessible(true);
		$this->assertEquals(true, $property->getValue($templates));
	}

	/**
	 * Tests für den Parameter ShowInactive
	 * Testfall ein Leerstring wird übergeben
	 */
	public function testShowInactiveStringFalse()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$templates->ShowInactive('');
		$property = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$property->setAccessible(true);
		$this->assertEquals(false, $property->getValue($templates));
	}
	// </editor-fold>
}
