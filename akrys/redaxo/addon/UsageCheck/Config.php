<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace akrys\redaxo\addon\UsageCheck;

class Config
{
	/**
	 * AddonID
	 * @todo Addon registrieren um eine echte ID zu bekommen
	 * @var int
	 */
	const ID = '000000'; //noch kein offizielles Addon

	/**
	 * Technischer Name des Addons
	 * @var string
	 */
	const NAME = 'usage_check';

	/**
	 * Ausgabename des Addons
	 * @var string
	 */
	const NAME_OUT = 'Usage Check';

	/**
	 * Version des Addons
	 *
	 * @var string
	 */
	const VERSION = '1.0 Beta 1';

	/**
	 * release state
	 * @var int
	 */
	const RELEASE_STATE = 0;

	/**
	 * Status: LIVE-Version
	 * @var int
	 */
	const RELEASE_STATE_LIVE = 1;

	/**
	 * Status: DEV-Version
	 * @var int
	 */
	const RELEASE_STATE_DEV = 0;

}