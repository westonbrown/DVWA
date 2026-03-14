<?php

if( isset( $_POST[ 'Submit' ]  ) ) {
    // Get input
    $target = $_REQUEST[ 'ip' ] ?? '';

    // Validate as an IP address before command execution
    if( filter_var( $target, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 ) === false ) {
        $html .= '<pre>Invalid IP address.</pre>';
    }
    else {
        $safe_target = escapeshellarg( $target );

        // Determine OS and execute the ping command safely.
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
