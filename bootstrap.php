<?php
// === Project Root Path ===
define('PROJECT_ROOT', __DIR__);

// === Base URL ===
// $scriptName = dirname($_SERVER['SCRIPT_NAME']);
// $baseUrl = rtrim($scriptName, '/');
// define('BASE_URL', $baseUrl === '' ? '/' : $baseUrl . '/');

// === Load config ===
require_once PROJECT_ROOT . '/includes/config.php';