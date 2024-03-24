<?php

/**
 * Generelle Configuration
 */
require_once __DIR__.'/FriendsOfRedaxo/addon/UsageCheck/Config.php';

/** @phpstan-ignore-next-line */
spl_autoload_register(['FriendsOfRedaxo\\addon\\UsageCheck\\Config', 'autoload'], true, true);

rex_fragment::addDirectory(realpath(__DIR__));

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
