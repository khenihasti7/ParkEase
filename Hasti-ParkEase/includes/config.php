<?php
// Dynamically determine the app base URL from the project folder under the web server document root.
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$projectRoot = realpath(dirname(__DIR__));
$baseUrl = '/';
if ($documentRoot && $projectRoot && str_starts_with($projectRoot, $documentRoot)) {
    $baseUrl = str_replace('\\', '/', substr($projectRoot, strlen($documentRoot)));
    $baseUrl = '/' . trim($baseUrl, '/') . '/';
}
define('BASE_URL', $baseUrl);
?>