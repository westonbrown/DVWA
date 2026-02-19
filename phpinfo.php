<?php

/**
 * Security hardening: phpinfo() leaks sensitive environment/configuration.
 * Gate this endpoint behind an explicit opt-in environment variable.
 */

define( 'DVWA_WEB_PAGE_TO_ROOT', '' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated') );

// Default: disabled
$enabled = getenv('DVWA_ENABLE_PHPINFO') ?: '';
if ($enabled !== '1') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

phpinfo();

?>
