<?php

/**
 * Tests für Templates
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5\Modules;

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
		parent::setUp();
		\rex::setVersion(\rex::VERSION_5);
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
	 * Test create, wenn es Redaxo in Version 5 vorliegt
	 */
	public function testCreate()
	{

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$this->assertEquals('akrys\\redaxo\\addon\\UsageCheck\\RexV5\\Modules\\Templates', get_class($templates));
	}

	/**
	 * Funktiontest Menüparameter
	 */
	public function testGetMenuParameter()
	{
		$reflectionObject = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'getMenuParameter');
		$reflectionObject->setAccessible(true);

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$data = $reflectionObject->invokeArgs($templates, array(true, true));
		$expectedData = array(
			'showAllParam' => "",
			'showAllParamCurr' => "&showall=true",
			'showAllLinktext' => "akrys_usagecheck_template_link_show_unused",
			'showInactiveParam' => "",
			'showInactiveParamCurr' => "&showinactive=true",
			'showInactiveLinktext' => "akrys_usagecheck_template_link_show_active",
		);
		$this->assertArrayHasKey('showAllParam', $data);
		$this->assertArrayHasKey('showAllParamCurr', $data);
		$this->assertArrayHasKey('showAllLinktext', $data);
		$this->assertArrayHasKey('showInactiveParam', $data);
		$this->assertArrayHasKey('showInactiveParamCurr', $data);
		$this->assertArrayHasKey('showInactiveLinktext', $data);
		$this->assertEquals($expectedData, $reflectionObject->invokeArgs($templates, array(true, true)));
	}

	/**
	 * Funktiontest Menüparameter der Fehler verursacht
	 */
	public function testGetMenuParameterFalse()
	{
		$reflectionObject = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'getMenuParameter');
		$reflectionObject->setAccessible(true);

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$data = $reflectionObject->invokeArgs($templates, array(false, false));
		$expectedData = array(
			'showAllParam' => "&showall=true",
			'showAllParamCurr' => "",
			'showAllLinktext' => "akrys_usagecheck_template_link_show_all",
			'showInactiveParam' => "&showinactive=true",
			'showInactiveParamCurr' => "",
			'showInactiveLinktext' => "akrys_usagecheck_template_link_show_active_inactive",
		);
		$this->assertArrayHasKey('showAllParam', $data);
		$this->assertArrayHasKey('showAllParamCurr', $data);
		$this->assertArrayHasKey('showAllLinktext', $data);
		$this->assertArrayHasKey('showInactiveParam', $data);
		$this->assertArrayHasKey('showInactiveParamCurr', $data);
		$this->assertArrayHasKey('showInactiveLinktext', $data);
		$this->assertEquals($expectedData, $reflectionObject->invokeArgs($templates, array(false, false)));
	}

	/**
	 * Funktionstest addParamStatementKeywords
	 */
	public function testAddParamStatementKeywords()
	{


		$reflectionObject = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'addParamStatementKeywords');
		$reflectionObject->setAccessible(true);

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$where = 'a=2';
		$having = 'count(a) > 2';
		($reflectionObject->invokeArgs($templates, array(&$where, &$having)));

		$this->assertEquals('having count(a) > 2 ', $having);
		$this->assertEquals('where a=2 ', $where);
	}

	/**
	 *  Funktionstest addParamCriteria
	 */
	public function testAddParamCriteria()
	{


		$reflectionObject = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'addParamCriteria');
		$reflectionObject->setAccessible(true);

		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$where = '';
		$having = '';
		($reflectionObject->invokeArgs($templates, array(&$where, &$having)));

		$this->assertEquals('articles is null and templates is null', $having);
		$this->assertEquals('t.active = 1', $where);
	}

	/**
	 * Funktionstest addParamCriteria
	 */
	public function testAddParamCriteriaFalse()
	{


		$reflectionObject = new \ReflectionMethod('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'addParamCriteria');
		$reflectionObject->setAccessible(true);

		$reflectionPropertyAll = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showAll');
		$reflectionPropertyAll->setAccessible(true);

		$reflectionPropertyInactive = new \ReflectionProperty('akrys\\redaxo\\addon\\UsageCheck\\Modules\\Templates', 'showInactive');
		$reflectionPropertyInactive->setAccessible(true);


		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		$reflectionPropertyAll->setValue($templates, true);
		$reflectionPropertyInactive->setValue($templates, true);
		$where = '';
		$having = '';
		($reflectionObject->invokeArgs($templates, array(&$where, &$having)));

		$this->assertEquals('', $having);
		$this->assertEquals('', $where);
	}

	/**
	 * Funktionstest OutputMenu
	 */
	public function testOutputMenu()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
		<ul>
			<li><a href="index.php?page=usage_check/test&showinactive=true">akrys_usagecheck_template_link_show_unused</a></li>
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
	 * Funktionstest OutputMenu Admin
	 */
	public function testOutputMenuAdmin()
	{
		\rex_user::setAdmin(true);
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
<ul><li><a href="index.php?page=usage_check/test&showinactive=true">akrys_usagecheck_template_link_show_unused</a></li><li><a href="index.php?page=usage_check/test&showall=true">akrys_usagecheck_template_link_show_active</a></li></ul>
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
		<a href="index.php?page=templates&function=edit&template_id=test">&b=2</a>
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
<a href="index.php?page=templates&function=edit&template_id=test">&b=2</a>
TEXT;

		ob_start();
		$object->outputTemplateEdit(array('id' => 'test'), '&b=2', 'test');
		$text = ob_get_clean();

		$exp = str_replace(array("\r", "\n", "\t"), '', trim($expected));
		$cur = str_replace(array("\r", "\n", "\t"), '', trim($text));

		$this->assertEquals($exp, $cur);
	}

	/**
	 * Funktionstest OutputMenu
	 */
	public function testGetEditLinkt()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();

		$expected = <<<TEXT
index.php?page=templates&function=edit&template_id=test
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
		\rex_structure_perm::setHasRight(true);
		\rex_structure_perm::setHasNamedRight('test', true);
		\rex_user::setRight('structure', true);
		$expected = $object->hasArticlePerm('test');
		$this->assertTrue($expected);
	}

	/**
	 * Funktionstest hasArticlePerm ohne rechte
	 */
	public function testHasArticlePermNOPerm()
	{
		$object = \akrys\redaxo\addon\UsageCheck\Modules\Templates::create();
		\rex_structure_perm::setHasRight(false);
		\rex_structure_perm::setHasNamedRight('test', false);
		\rex_user::setRight('structure', false);
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
		$templates = \akrys\redaxo\addon\UsageCheck\Modules\Pictures::create();
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
