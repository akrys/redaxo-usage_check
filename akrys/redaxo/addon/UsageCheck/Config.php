<?php

/**
 * Config-Datei
 * @author akrys
 */
namespace akrys\redaxo\addon\UsageCheck;

/**
 * Config-Klasse mit Konstanten für die Runtime
 * @author akrys
 */
class Config
{
	/**
	 * AddonID
	 * @var int
	 * @deprecated ID braucht es nur für Redaxo 4, daher kann auch Config::NAME genutzt werden
	 */
	const ID = 'usage_check';

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
	const VERSION = '1.0 Beta 6';

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
