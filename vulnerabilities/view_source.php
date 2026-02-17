<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ] .= 'Source' . $page[ 'title_separator' ].$page[ 'title' ];

if( array_key_exists( 'id', $_GET ) && array_key_exists( 'security', $_GET ) ) {
	$id       = $_GET[ 'id' ];
	$security = $_GET[ 'security' ];

	// Validate inputs to prevent traversal/arbitrary file read
	$allowed_security = array( 'low', 'medium', 'high', 'impossible' );
	if( !in_array( $security, $allowed_security, true ) ) {
		$security = 'impossible';
	}

	if( preg_match( '/^[a-z0-9_]+$/', $id ) !== 1 ) {
		$page['body'] = "<p>Not found</p>";
		dvwaSourceHtmlEcho( $page );
		exit;
	}

	switch( $id ) {
		case "fi" :
			$vuln = 'File Inclusion';
			break;
		case "brute" :
			$vuln = 'Brute Force';
			break;
		case "csrf" :
			$vuln = 'CSRF';
			break;
		case "exec" :
			$vuln = 'Command Injection';
			break;
		case "sqli" :
			$vuln = 'SQL Injection';
			break;
		case "sqli_blind" :
			$vuln = 'SQL Injection (Blind)';
			break;
		case "upload" :
			$vuln = 'File Upload';
			break;
		case "xss_r" :
			$vuln = 'Reflected XSS';
			break;
		case "xss_s" :
			$vuln = 'Stored XSS';
			break;
		case "weak_id" :
			$vuln = 'Weak Session IDs';
			break;
		case "javascript" :
			$vuln = 'JavaScript';
			break;
		case "authbypass" :
			$vuln = 'Authorisation Bypass';
			break;
		case "open_redirect" :
			$vuln = 'Open HTTP Redirect';
			break;
		case "bac":
			$vuln = 'Vulnerability: Broken Access Control';
			break;
		default:
			$vuln = "Unknown Vulnerability";
	}

	$baseDir = realpath( DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/source" );
	if( $baseDir === false ) {
		$page['body'] = "<p>Not found</p>";
		dvwaSourceHtmlEcho( $page );
		exit;
	}

	$phpCandidate = $baseDir . DIRECTORY_SEPARATOR . "{$security}.php";
	$phpResolved  = realpath( $phpCandidate );
	$source       = '';
	if( $phpResolved !== false && strpos( $phpResolved, $baseDir ) === 0 && is_file( $phpResolved ) ) {
		$source = @file_get_contents( $phpResolved );
	}
	$source = str_replace( array( '$html .=' ), array( 'echo' ), $source );

	$js_html = "";
	$jsCandidate = $baseDir . DIRECTORY_SEPARATOR . "{$security}.js";
	$jsResolved  = realpath( $jsCandidate );
	if( $jsResolved !== false && strpos( $jsResolved, $baseDir ) === 0 && is_file( $jsResolved ) ) {
		$js_source = @file_get_contents( $jsResolved );
		$js_html = "
		<h2>vulnerabilities/{$id}/source/{$security}.js</h2>
		<div id=\"code\">
			<table width='100%' bgcolor='white' style=\"border:2px #C0C0C0 solid\">
				<tr>
					<td><div id=\"code\">" . highlight_string( $js_source, true ) . "</div></td>
				</tr>
			</table>
		</div>
		";
	}

	$page[ 'body' ] .= "
	<div class=\"body_padded\">
		<h1>{$vuln} Source</h1>

		<h2>vulnerabilities/{$id}/source/{$security}.php</h2>
		<div id=\"code\">
			<table width='100%' bgcolor='white' style=\"border:2px #C0C0C0 solid\">
				<tr>
					<td><div id=\"code\">" . highlight_string( $source, true ) . "</div></td>
				</tr>
			</table>
		</div>
		{$js_html}
		<br /> <br />

		<form>
			<input type=\"button\" value=\"Compare All Levels\" onclick=\"window.location.href='view_source_all.php?id=$id'\">
		</form>
	</div>\n";
}
else {
	$page['body'] = "<p>Not found</p>";
}

dvwaSourceHtmlEcho( $page );

?>
