<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2016-08-05
 * @package       new_package
 * @subpackage    new_subpackage
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Tests\RexV5;

/**
 * Description of RedaxoCallTest
 *
 * @author akrys
 */
class RedaxoCallRex5Test
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
	 * Funktionstest: getTaggedMsg
	 */
	public function testGetTaggedMsg()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$result = $api->getTaggedMsg('aa');
		$this->assertEquals(['setVar' => [['text', 'aa']]], $result);
	}

	/**
	 * Funktionstest: getTable
	 */
	public function testGetTable()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('rex_aa', $api->getTable('aa'));
	}

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetTaggedError()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getErrorMsg('aa');
		$this->assertFalse(is_object($api));
	}

	/**
	 * Funktionstest getApiInvalid
	 */
	public function testGetTaggedInformation()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI()->getInfoMsg('aa');
		$this->assertFalse(is_object($api));
	}

	/**
	 * Funktion getRexTitle
	 */
	public function testGetRexTitle()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('aa', $api->getRexTitle('aa'));
	}

	/**
	 * Funktion panelOut
	 */
	public function testPanelOut()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$out = <<<TEXT

<div class="panel panel-default">
	<header class="panel-heading"><div class="panel-title">title</div></header>
	<div class="panel-body">
		text
	</div>
</div>

TEXT;

		$result = ($api->getPanelOut('title', 'text'));
		$this->assertArrayHasKey('setVar', $result);
		$this->assertEquals(2, count($result['setVar']));
		$this->assertEquals('heading', $result['setVar'][0][0]);
		$this->assertEquals('title', $result['setVar'][0][1]);

		$this->assertEquals('body', $result['setVar'][1][0]);
		$this->assertEquals('text', $result['setVar'][1][1]);
	}

	/**
	 * Funktion getTableClass
	 */
	public function testGetTableClass()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('table table-striped', $api->getTableClass());
	}

	/**
	 * Funktionstest: getArticleMetaUrl
	 */
	public function testGetArticleMetaUrl()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('index.php?page=content/metainfo&article_id=1&clang=1&ctype=1', $api->getArticleMetaUrl(1, 1));
	}

	/**
	 * Funktionstest: getYFormEditUrl
	 */
	public function testGetYFormEditUrl()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('index.php?page=yform/manager/data_edit&table_name=table&data_id=1&func=edit', $api->getYFormEditUrl('table', 1));
	}

	/**
	 * Funktion geti18n
	 */
	public function testGetI18N()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		$this->assertEquals('aa', $api->getI18N('aa'));
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
	 * Medien-Kategorierechtesimulation prüfen (als Admin)
	 */
	public function testHasCategoryPerm()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_media_perm::setHasNamedRight(1, false);
		$this->assertFalse($api->hasCategoryPerm(1));
	}

	/**
	 * Medien-Kategorierechtesimulation prüfen (ohne rechte)
	 */
	public function testHasMediaCategoryPerm()
	{
		\rex_user::setAdmin(false);
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
		\rex_media_perm::setHasNamedRight(1, false);
		$this->assertFalse($api->hasMediaCategoryPerm(1));
	}

	/**
	 * Tabellen-Rechtesimulation prüfen genereller aufruf
	 */
	public function testHasTablePerm()
	{
		$api = \akrys\redaxo\addon\UsageCheck\RedaxoCall::getAPI();
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
		$this->assertArrayHasKey('name', $data[0]);
		$this->assertArrayHasKey('name', $data[1]);
	}
}
