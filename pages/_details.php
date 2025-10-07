<?php

use FriendsOfRedaxo\UsageCheck\Addon;
use FriendsOfRedaxo\UsageCheck\Enum\ModuleType;
use FriendsOfRedaxo\UsageCheck\Modules\Actions;
use FriendsOfRedaxo\UsageCheck\Modules\Modules;
use FriendsOfRedaxo\UsageCheck\Modules\Pictures;
use FriendsOfRedaxo\UsageCheck\Modules\Templates;

$errors = [];

$type = rex_get('type', 'string', "");
switch ($type) {
	case Actions::TYPE->value:
	case Modules::TYPE->value:
	case Templates::TYPE->value:
	case Pictures::TYPE->value:
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

$enumType = ModuleType::tryFrom($type);

if (!$enumType) {
	throw new Exception('not a valid Type: '.$type);
}
$object = $enumType->getObject();
$template = $enumType->getTemplate();
$subpageTitle = $enumType->getSubpageTitle();

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
