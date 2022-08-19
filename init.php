<?php
define('DHL_API_DIR', __DIR__ . '/');
require_once(DHL_API_DIR . 'vendor/autoloadManager/autoloadManager.php');

// Load adequate configuration file based on the APPLICATION_ENVIRONMENT 
if ($applicationEnvironment = getenv('APPLICATION_ENVIRONMENT'))
{
    $configFilename = 'config-' . $applicationEnvironment . '.php';
}
else
{
    $configFilename = 'config.php';
}

//Allow to place the config file anywhere in project
if (! defined('DHL_CONF_API_DIR')) {
    define('DHL_CONF_API_DIR', __DIR__ . '/');
}

$config = require(DHL_CONF_API_DIR . 'conf/' . $configFilename);
$scanOption = isset($config['autoloader']['scanOption']) ? $config['autoloader']['scanOption'] : autoloadManager::SCAN_ONCE;
$autoloadDir = isset($config['autoloader']['dir']) ? $config['autoloader']['dir'] : sys_get_temp_dir() . '/dhl-api-autoload.php';

$autoloadManager = new AutoloadManager($autoloadDir, $scanOption);
$autoloadManager->addFolder(DHL_API_DIR . 'vendor');
$autoloadManager->addFolder(DHL_API_DIR . 'DHL');
$autoloadManager->register();
