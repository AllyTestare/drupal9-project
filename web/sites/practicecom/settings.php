<?php

// phpcs:ignoreFile

if (file_exists($app_root . '/' . $site_path . '/../default/settings.php')) {
  include $app_root . '/' . $site_path . '/../default/settings.php';
}

$settings['file_public_path'] = 'sites/practicecom/files';
$settings['file_private_path'] = 'sites/practicecom/private';
$settings['config_sync_directory'] = '../config/practicecom/sync';
