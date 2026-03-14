<?php

if( isset( $_POST[ 'Submit' ]  ) ) {
	// Get input
	$target = trim( $_REQUEST[ 'ip' ] );

	// Strict allowlist validation (demo remediation for training app)
	$is_valid_ip = filter_var( $target, FILTER_VALIDATE_IP ) !== false;
	$is_valid_hostname = preg_match( '/^(?=.{1,253}$)(?!-)([a-zA-Z0-9-]{1,63}\.)*[a-zA-Z0-9-]{1,63}$/', $target ) === 1;

	if( !$is_valid_ip && !$is_valid_hostname ) {
		$html .= '<pre>Invalid target.</pre>';
	}
	else {
		// Escape argument to prevent command injection
		$safe_target = escapeshellarg( $target );

		// Determine OS and execute the ping command.
		if( stristr( php_uname( 's' ), 'Windows NT' ) ) {
			// Windows
			$cmd = shell_exec( 'ping ' . $safe_target );
		}
		else {
			// *nix
			$cmd = shell_exec( 'ping -c 4 ' . $safe_target );
		}

		// Feedback for the end user
		$html .= "<pre>{$cmd}</pre>";
	}
}

?>
