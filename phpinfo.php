<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated') );

// Limit phpinfo() exposure to admin only
if( dvwaCurrentUser() !== 'admin' ) {
	header( 'HTTP/1.1 403 Forbidden' );
	die( 'Forbidden' );
}

phpinfo();

?>
