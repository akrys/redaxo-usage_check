<?php

use FriendsOfRedaxo\addon\UsageCheck\Addon;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Actions;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Modules;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Pictures;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Templates;

$errors = [];

$type = rex_get('type', 'string', "");
switch ($type) {
	case Actions::TYPE:
	case Modules::TYPE:
	case Templates::TYPE:
	case Pictures::TYPE:
		//;
		break;
	default:
		$errors[] = rex_i18n::rawMsg('akrys_usagecheck_details_no_valid_type');
		break;
}
$id = rex_get('id', 'string', "");




$type = rex_get('type', 'string');
$id = rex_get('id', 'int');

//var_dump($type, $id);


switch ($type) {
	case Pictures::TYPE:
		$object = new Pictures();
		$template = 'modules/details/picture.php';
		$subpageTitle = rex_i18n::rawMsg('akrys_usagecheck_images_subpagetitle');
		break;
	case Modules::TYPE:
		$object = new Modules();
		$template = 'modules/details/module.php';
		$subpageTitle = rex_i18n::rawMsg('akrys_usagecheck_module_subpagetitle');
		break;
	case Actions::TYPE:
		$object = new Actions();
		$template = 'modules/details/action.php';
		$subpageTitle = rex_i18n::rawMsg('akrys_usagecheck_action_subpagetitle');
		break;
	case Templates::TYPE:
		$object = new Templates();
		$template = 'modules/details/template.php';
		$subpageTitle = rex_i18n::rawMsg('akrys_usagecheck_template_subpagetitle');
		break;
	default:
		throw new Exception('not a valid Type: '.$type);
		break;
}

$title = new rex_fragment();
$title->setVar('name', Addon::getInstance()->getName());
$title->setVar('supage_title', $subpageTitle);
$title->setVar('version', Addon::getInstance()->getVersion());
echo rex_view::title($title->parse('fragments/title.php'));

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
