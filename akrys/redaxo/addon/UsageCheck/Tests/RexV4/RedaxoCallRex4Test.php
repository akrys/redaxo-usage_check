<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-08-05
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV4;

/**
 * Description of RedaxoCallTest
 *
 * @author akrys
 */
class RedaxoCallRex4Test
	extends \PHPUnit_Framework_TestCase
{

	/**
	 * Aufsetzen der Simulation
	 */
	public function setUp()
	{
		parent::setUp();
		\rex::setVersion(\rex::VERSION_4);
	}

	/**
	 * Funktionstest: getTaggedMsg
	 */
	public function testGetTaggedMsg()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('<p><span>test</span></p>', $api->getTaggedMsg('test'));
	}

	/**
	 * Funktionstest: getTable
	 */
	public function testGetTable()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('rex_test', $api->getTable('test'));
	}

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetTaggedError()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getErrorMsg('test');
		$this->assertFalse(is_object($api));
	}

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetTaggedInformation()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getInfoMsg('test');
		$this->assertFalse(is_object($api));
	}

	/**
	 * Funktion getRexTitle
	 */
	public function testGetRexTitle()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('test', $api->getRexTitle('test'));
	}

	/**
	 * Funktion panelOut
	 */
	public function testPanelOut()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();

		$out = <<<TEXT

<div class="rex-addon-output">
	<h2 class="rex-hl2">title</h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">
			text
		</p>
	</div>
</div>

TEXT;
		$this->assertEquals($out, $api->getPanelOut('title', 'text'));
	}

	/**
	 * Funktion getTableClass
	 */
	public function testGetTableClass()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('rex-table', $api->getTableClass());
	}

	/**
	 * Funktionstest: getArticleMetaUrl
	 */
	public function testGetArticleMetaUrl()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('index.php?page=content&article_id=1&mode=meta&clang=1&ctype=1', $api->getArticleMetaUrl(1, 1));
	}

	/**
	 * Funktionstest: getXFormEditUrl
	 */
	public function testGetXFormEditUrl()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('index.php?page=xform&subpage=manager&tripage=data_edit&table_name=table&rex_xform_search=0&data_id=1&func=edit&start=', $api->getXFormEditUrl('table', 1));
	}

	/**
	 * Funktion geti18n
	 */
	public function testGetI18N()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('test', $api->getI18N('test'));
	}

	/**
	 * Funktion getSQL
	 */
	public function testGetSQL()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('rex_sql', get_class($api->getSQL()));
	}

	/**
	 * Funktion isAdmin
	 */
	public function testIsNotAdmin()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertFalse($api->isAdmin());
	}

	/**
	 * Funktion isAdmin
	 */
	public function testIsAdmin()
	{
		\rex_user::setAdmin(true);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertTrue($api->isAdmin());
	}

	/**
	 * Kategorierechtesimulation prüfen (Admin)
	 */
	public function testHasCategoryPerm()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_user::setRight(1, true);
		$this->assertTrue($api->hasCategoryPerm(1));
	}

	/**
	 * Medien-Kategorierechtesimulation prüfen (als nicht-Admin)
	 */
	public function testHasMediaCategoryPermNoAdmin()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_user::setAdmin(false);
		\rex_user::setRight('media[0]', false);
		\rex_user::setRight('media[1]', true);
		$this->assertTrue($api->hasMediaCategoryPerm(1));
	}

	/**
	 * Medien-Kategorierechtesimulation prüfen (als Admin)
	 */
	public function testHasMediaCategoryPermAdmin()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_user::setAdmin(true);
		$this->assertTrue($api->hasMediaCategoryPerm(1));
	}

	/**
	 * Medien-Kategorierechtesimulation prüfen (ohne rechte)
	 */
	public function testHasMediaCategoryPermNoRights()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_user::setAdmin(false);
		\rex_user::setRight('media[0]', false);
		\rex_user::setRight('media[1]', false);
		$this->assertFalse($api->hasMediaCategoryPerm(1));
	}

	/**
	 * Tabellen-Rechtesimulation prüfen genereller aufruf
	 */
	public function testHasTablePerm()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_user::setRight('xform[]', true);
		\rex_user::setRight('xform[table:1]', false);
		$this->assertFalse($api->hasTablePerm(1));
	}

	/**
	 * Sprachensimulation testen
	 */
	public function testGetLang()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertTrue($api->getLang());
	}

	/**
	 * Sprachensimulation testen
	 */
	public function testGetDB()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$data = $api->getDB();

		$this->asserttrue(is_array($data));
		$this->assertEquals(2, count($data));
		$this->assertArrayHasKey('NAME', $data[1]);
		$this->assertArrayHasKey('NAME', $data[2]);
	}
}
