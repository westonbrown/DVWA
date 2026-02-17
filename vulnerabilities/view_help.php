<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ] = 'Help' . $page[ 'title_separator' ].$page[ 'title' ];

$help = "<p>Not Found</p>";

if (
	array_key_exists( 'id', $_GET ) &&
	array_key_exists( 'security', $_GET ) &&
	array_key_exists( 'locale', $_GET )
) {
	$id       = $_GET[ 'id' ];
	$security = $_GET[ 'security' ];
	$locale   = $_GET[ 'locale' ];

	// Strict allowlists to prevent path traversal / arbitrary file read
	$allowed_security = array( 'low', 'medium', 'high', 'impossible' );
	if( !in_array( $security, $allowed_security, true ) ) {
		$security = 'impossible';
	}

	if( preg_match( '/^[a-z0-9_]+$/', $id ) === 1 ) {
		$baseHelpDir = realpath( DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help" );
		$baseVulnDir = realpath( DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}" );

		// Ensure the directory exists and is within the intended vulnerabilities directory
		if( $baseHelpDir !== false && $baseVulnDir !== false && strpos( $baseHelpDir, $baseVulnDir ) === 0 ) {
			// Locale allowlist: 2-5 letters/dashes only (e.g. en, zh, pt-BR)
			if( preg_match( '/^[a-z]{2,5}(-[A-Z]{2})?$/', $locale ) !== 1 ) {
				$locale = 'en';
			}

			$helpFile = ($locale === 'en')
				? ( $baseHelpDir . DIRECTORY_SEPARATOR . 'help.php' )
				: ( $baseHelpDir . DIRECTORY_SEPARATOR . "help.{$locale}.php" );

			$resolved = realpath( $helpFile );
			if( $resolved !== false && strpos( $resolved, $baseHelpDir ) === 0 && is_file( $resolved ) ) {
				ob_start();
				include $resolved;
				$help = ob_get_contents();
				ob_end_clean();
			}
		}
	}
}

$page[ 'body' ] .= "
<script src='/vulnerabilities/help.js'></script>
<link rel='stylesheet' type='text/css' href='/vulnerabilities/help.css' />

<div class=\"body_padded\">
	{$help}
</div>\n";

dvwaHelpHtmlEcho( $page );

?>
