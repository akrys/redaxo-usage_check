<?php

/*
 * Generelle uninstall-Operationen
 */
require_once __DIR__.'/FriendsOfRedaxo/addon/UsageCheck/Config.php';

/** @phpstan-ignore-next-line */
spl_autoload_register(['FriendsOfRedaxo\\addon\\UsageCheck\\Config', 'autoload'], true, true);
