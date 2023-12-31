<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPEnum.php to edit this template
 */
namespace FriendsOfRedaxo\addon\UsageCheck\Enum;

use FriendsOfRedaxo\addon\UsageCheck\Lib\BaseModule;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Actions;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Modules;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Pictures;
use FriendsOfRedaxo\addon\UsageCheck\Modules\Templates;
use rex_i18n;

/**
 *
 * @author akrys
 */
enum ModuleType: string
{
	case ACTIONS = 'actions';
	case PICTURES = 'media';
	case MODULES = 'modules';
	case TEMPLATES = 'templates';

	public function getObject(): BaseModule
	{
		return match ($this) {
			ModuleType::ACTIONS => new Actions(),
			ModuleType::PICTURES => new Pictures(),
			ModuleType::MODULES => new Modules(),
			ModuleType::TEMPLATES => new Templates(),
		};
	}

	public function getTemplate(): string
	{
		return match ($this) {
			ModuleType::ACTIONS => 'modules/details/action.php',
			ModuleType::PICTURES => 'modules/details/picture.php',
			ModuleType::MODULES => 'modules/details/module.php',
			ModuleType::TEMPLATES => 'modules/details/template.php',
		};
	}

	public function getSubpageTitle(): string
	{
		return match ($this) {
			ModuleType::ACTIONS => rex_i18n::rawMsg('akrys_usagecheck_action_subpagetitle'),
			ModuleType::PICTURES => rex_i18n::rawMsg('akrys_usagecheck_images_subpagetitle'),
			ModuleType::MODULES => rex_i18n::rawMsg('akrys_usagecheck_module_subpagetitle'),
			ModuleType::TEMPLATES => rex_i18n::rawMsg('akrys_usagecheck_template_subpagetitle'),
		};
	}
}
