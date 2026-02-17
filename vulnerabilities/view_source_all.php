<?php

define('DVWA_WEB_PAGE_TO_ROOT', '../');
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup(array('authenticated'));

$page = dvwaPageNewGrab();
$page['title'] = 'Source' . $page['title_separator'] . $page['title'];

if( array_key_exists( 'id', $_GET ) ) {
	$id = $_GET['id'];

	// Prevent traversal/arbitrary file reads
	if( preg_match( '/^[a-z0-9_]+$/', $id ) !== 1 ) {
		$page['body'] = "<p>Not found</p>";
		dvwaSourceHtmlEcho($page);
		exit;
	}

	$baseDir = realpath( __DIR__ . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'source' );
	if( $baseDir === false ) {
		$page['body'] = "<p>Not found</p>";
		dvwaSourceHtmlEcho($page);
		exit;
	}

	function dvwa_safe_read_source( $baseDir, $filename ) {
		$candidate = $baseDir . DIRECTORY_SEPARATOR . $filename;
		$resolved  = realpath( $candidate );
		if( $resolved === false || strpos( $resolved, $baseDir ) !== 0 || !is_file( $resolved ) ) {
			return '';
		}
		return @file_get_contents( $resolved );
	}

	$lowsrc  = dvwa_safe_read_source( $baseDir, 'low.php' );
	$lowsrc  = str_replace(array('$html .='), array('echo'), $lowsrc);
	$lowsrc  = highlight_string($lowsrc, true);

	$medsrc  = dvwa_safe_read_source( $baseDir, 'medium.php' );
	$medsrc  = str_replace(array('$html .='), array('echo'), $medsrc);
	$medsrc  = highlight_string($medsrc, true);

	$highsrc = dvwa_safe_read_source( $baseDir, 'high.php' );
	$highsrc = str_replace(array('$html .='), array('echo'), $highsrc);
	$highsrc = highlight_string($highsrc, true);

	$impsrc  = dvwa_safe_read_source( $baseDir, 'impossible.php' );
	$impsrc  = str_replace(array('$html .='), array('echo'), $impsrc);
	$impsrc  = highlight_string($impsrc, true);

	switch ($id) {
		case "javascript":
			$vuln = 'JavaScript';
			break;
		case "fi":
			$vuln = 'File Inclusion';
			break;
		case "brute":
			$vuln = 'Brute Force';
			break;
		case "csrf":
			$vuln = 'CSRF';
			break;
		case "exec":
			$vuln = 'Command Injection';
			break;
		case "sqli":
			$vuln = 'SQL Injection';
			break;
		case "sqli_blind":
			$vuln = 'SQL Injection (Blind)';
			break;
		case "upload":
			$vuln = 'File Upload';
			break;
		case "xss_r":
			$vuln = 'Reflected XSS';
			break;
		case "xss_s":
			$vuln = 'Stored XSS';
			break;
		case "weak_id":
			$vuln = 'Weak Session IDs';
			break;
		case "authbypass":
			$vuln = 'Authorisation Bypass';
			break;
		case "open_redirect":
			$vuln = 'Open HTTP Redirect';
			break;
		case "bac":
			$vuln = 'Vulnerability: Broken Access Control';
			break;
		default:
			$vuln = "Unknown Vulnerability";
	}

	$page['body'] .= "
	<div class=\"body_padded\">
		<h1>{$vuln}</h1>
		<br />

		<h3>Impossible {$vuln} Source</h3>
		<table width='100%' bgcolor='white' style=\"border:2px #C0C0C0 solid\">
			<tr>
				<td><div id=\"code\">{$impsrc}</div></td>
			</tr>
		</table>
		<br />

		<h3>High {$vuln} Source</h3>
		<table width='100%' bgcolor='white' style=\"border:2px #C0C0C0 solid\">
			<tr>
				<td><div id=\"code\">{$highsrc}</div></td>
			</tr>
		</table>
		<br />

		<h3>Medium {$vuln} Source</h3>
		<table width='100%' bgcolor='white' style=\"border:2px #C0C0C0 solid\">
			<tr>
				<td><div id=\"code\">{$medsrc}</div></td>
			</tr>
		</table>
		<br />

		<h3>Low {$vuln} Source</h3>
		<table width='100%' bgcolor='white' style=\"border:2px #C0C0C0 solid\">
			<tr>
				<td><div id=\"code\">{$lowsrc}</div></td>
			</tr>
		</table>
		<br /> <br />

		<form>
			<input type=\"button\" value=\"<-- Back\" onclick=\"history.go(-1);return true;\">
		</form>

	</div>\n";
}
else {
	$page['body'] = "<p>Not found</p>";
}

dvwaSourceHtmlEcho($page);

?>
