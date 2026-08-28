<?php
// Build URLs relative to the deployed project directory.
function app_base_url(): string
{
    // Get the document root from the server environment
    $projectRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;

    // Normalize paths to use forward slashes for consistency
    if (!$projectRoot || !$documentRoot) {
        return '';
    }

    $projectRoot = str_replace('\\', '/', $projectRoot);
    $documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');

    // Calculate the relative path from the document root to the project root
    if (strncmp($projectRoot, $documentRoot, strlen($documentRoot)) !== 0) {
        return '';
    }

    $relativePath = trim(substr($projectRoot, strlen($documentRoot)), '/');
    return $relativePath === '' ? '' : '/' . $relativePath;
}
