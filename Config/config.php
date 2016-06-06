<?php

use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;

/**
 * Configuration files are loaded in a specific order. First ``global.php``,
 * then ``*.global.php``. then ``local.php`` and finally ``*.local.php``. This
 * way local settings overwrite global settings. The configuration can be
 * cached. This can be done by setting ``config_cache_enabled`` to ``true``.
 * Obviously, if you use closures in your config you can't cache it.
 */

$cachedConfigFile = Configure::read('zendservicemanager.cache-file') ?: CACHE . 'zend-service-manager-cache.php';

$config = [];
if (is_file($cachedConfigFile)) {
	// Try to load the cached config
	$config = include $cachedConfigFile;
} else {
	$configDir = Configure::read('zendservicemanager.autoload-dir') ?: APP . 'Config' . DS . 'Autoload' . DS;
	// Load configuration from autoload path
	foreach (Glob::glob($configDir . '{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
		$config = ArrayUtils::merge($config, include $file);
	}
	// Cache config if enabled
	if (isset($config['config_cache_enabled']) && $config['config_cache_enabled'] === true) {
		file_put_contents($cachedConfigFile, '<?php return ' . var_export($config, true) . ';');
	}
}

// Return an ArrayObject so we can inject the config as a service
// and still use array checks like ``is_array``.
return new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
