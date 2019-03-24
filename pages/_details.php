<?php

use \akrys\redaxo\addon\UsageCheck\Config;

$errors = [];

$type = rex_get('type', 'string', "");
switch ($type) {
	case \akrys\redaxo\addon\UsageCheck\Modules\Actions::TYPE:
	case \akrys\redaxo\addon\UsageCheck\Modules\Modules::TYPE:
	case \akrys\redaxo\addon\UsageCheck\Modules\Templates::TYPE:
	case \akrys\redaxo\addon\UsageCheck\Modules\Pictures::TYPE:
		//;
		break;
	default:
		$errors[] = \rex_i18n::rawMsg('akrys_usagecheck_details_no_valid_type');
		break;
}
$id = rex_get('id', 'string', "");



$title = new \rex_fragment();
$title->setVar('name', Config::NAME_OUT);
$title->setVar('supage_title', \rex_i18n::rawMsg('akrys_usagecheck_template_subpagetitle'));
$title->setVar('version', Config::VERSION);
echo \rex_view::title($title->parse('fragments/title.php'));

$type = rex_get('type', 'string');
$id = rex_get('id', 'int');

//var_dump($type, $id);


switch ($type) {
	case akrys\redaxo\addon\UsageCheck\Modules\Pictures::TYPE:
		$object = new akrys\redaxo\addon\UsageCheck\Modules\Pictures();
		$template = 'modules/details/picture.php';
		break;
	case akrys\redaxo\addon\UsageCheck\Modules\Modules::TYPE:
		$object = new akrys\redaxo\addon\UsageCheck\Modules\Modules();
		$template = 'modules/details/module.php';
		break;
	case akrys\redaxo\addon\UsageCheck\Modules\Actions::TYPE:
		$object = new akrys\redaxo\addon\UsageCheck\Modules\Actions();
		$template = 'modules/details/action.php';
		break;
	case akrys\redaxo\addon\UsageCheck\Modules\Templates::TYPE:
		$object = new akrys\redaxo\addon\UsageCheck\Modules\Templates();
		$template = 'modules/details/template.php';
		break;
	default:
		throw new \Exception('not a valid Type: '.$type);
		break;
}

$object->setRexSql(rex_sql::factory());
$data = $object->getDetails($id);
if (count($data) <= 0) {
	$errors[] = rex_i18n::rawMsg('akrys_usagecheck_details_no_data');
}

$params = [
	'data' => $data,
	'errors' => $errors,
];

$fragment = new rex_fragment($params);
echo $fragment->parse($template);
