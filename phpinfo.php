<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated' ) );

// phpinfo() leaks sensitive configuration details; restrict to admin user.
if ( function_exists( 'dvwaCurrentUser' ) && dvwaCurrentUser() !== 'admin' ) {
    header( 'HTTP/1.1 403 Forbidden' );
    echo 'Forbidden';
    exit;
}

phpinfo();

?>
