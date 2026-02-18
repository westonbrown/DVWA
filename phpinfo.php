<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated') );

// SECURITY: Restrict phpinfo() output to admins only.
if (dvwaCurrentUser() !== 'admin') {
	http_response_code(403);
	die('Forbidden');
}

phpinfo();

?>
