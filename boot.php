<?php

require_once __DIR__.'/lib/Config.php'; // remove in v4

spl_autoload_register(['FriendsOfRedaxo\\UsageCheck\\Config', 'autoload'], true, true); // remove in v4

class_alias(FriendsOfRedaxo\UsageCheck\Exception\CloneException::class, 'FriendsOfRedaxo\\addon\\UsageCheck\\Exception\\CloneException');// remove in v4
class_alias(FriendsOfRedaxo\UsageCheck\Exception\FunctionNotCallableException::class, 'FriendsOfRedaxo\\addon\\UsageCheck\\Exception\\FunctionNotCallableException');// remove in v4
class_alias(FriendsOfRedaxo\UsageCheck\Exception\InvalidParameterException::class, 'FriendsOfRedaxo\\addon\\UsageCheck\\Exception\\InvalidParameterException');// remove in v4
class_alias(FriendsOfRedaxo\UsageCheck\Exception\InvalidVersionException::class, 'FriendsOfRedaxo\\addon\\UsageCheck\\Exception\\InvalidVersionException');// remove in v4
class_alias(FriendsOfRedaxo\UsageCheck\Exception\MediaNotFoundException::class, 'FriendsOfRedaxo\\addon\\UsageCheck\\Exception\\MediaNotFoundException');// remove in v4

// Namespace-Test old namespace
//$x = new \FriendsOfRedaxo\addon\UsageCheck\Modules\Templates;

$dir = realpath(__DIR__);
if ($dir !== false) {
	rex_fragment::addDirectory($dir);
}

if (rex::isBackend()) {
	/** @var \rex_addon $this */
	rex_view::addCssFile($this->getAssetsUrl('css/style.css'));

	if (filemtime($this->getPath('_build/scss/style.scss')) > filemtime($this->getPath('assets/css/style.css'))) {
		$compiler = new rex_scss_compiler();
		$compiler->setRootDir($this->getPath());
		$compiler->setScssFile($this->getPath('_build/scss/style.scss'));
		$compiler->setCssFile($this->getPath('assets/css/style.css'));
		$compiler->compile();
		rex_file::copy($this->getPath('assets/css/style.css'), $this->getAssetsPath('css/style.css'));
	}
}
