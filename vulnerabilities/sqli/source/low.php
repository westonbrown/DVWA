<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
    // Get input
    $id = $_REQUEST[ 'id' ] ?? '';

    switch ($_DVWA['SQLI_DB']) {
        case MYSQL:
            // Use prepared statement to prevent SQL injection
            $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], 'SELECT first_name, last_name FROM users WHERE user_id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while( $row = mysqli_fetch_assoc( $result ) ) {
                    $first = $row['first_name'];
                    $last  = $row['last_name'];
                    $safe_id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
                    $html .= "<pre>ID: {$safe_id}<br />First name: {$first}<br />Surname: {$last}</pre>";
                }

                mysqli_stmt_close($stmt);
            }
            break;

        case SQLITE:
            global $sqlite_db_connection;

            $stmt = $sqlite_db_connection->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id');
            $stmt->bindValue(':id', $id, SQLITE3_TEXT);
            $results = $stmt->execute();

            if ($results) {
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    $first = $row['first_name'];
                    $last  = $row['last_name'];
                    $safe_id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
                    $html .= "<pre>ID: {$safe_id}<br />First name: {$first}<br />Surname: {$last}</pre>";
                }
            }
            break;
    }
}

?>
