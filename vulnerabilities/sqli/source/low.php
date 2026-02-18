<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
	// Get input
	$id = $_REQUEST[ 'id' ];
	$id_display = $id;

	// Optional safety switch for demos: set DVWA_SAFE_MODE=true to use parameterized queries.
	// NOTE: DVWA is intentionally vulnerable by default; this flag helps prevent accidental exposure.
	$safe_mode = ( isset($_DVWA['safe_mode']) && $_DVWA['safe_mode'] );

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Check database
			if( $safe_mode ) {
				$id_int = (int) $id;
				$id_display = $id_int;

				$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?;");
				if( $stmt === false ) {
					die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );
				}
				mysqli_stmt_bind_param($stmt, "i", $id_int);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
			} else {
				$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
				$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );
			}

			// Get results
			while( $row = mysqli_fetch_assoc( $result ) ) {
				// Get values
				$first = $row["first_name"];
				$last  = $row["last_name"];

				// Feedback for end user
				$html .= "<pre>ID: {$id_display}<br />First name: {$first}<br />Surname: {$last}</pre>";
			}

			if( $safe_mode && isset($stmt) ) {
				mysqli_stmt_close($stmt);
			}
			mysqli_close($GLOBALS["___mysqli_ston"]);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			#$sqlite_db_connection = new SQLite3($_DVWA['SQLITE_DB']);
			#$sqlite_db_connection->enableExceptions(true);

			if( $safe_mode ) {
				$id_int = (int) $id;
				$id_display = $id_int;

				$stmt = $sqlite_db_connection->prepare("SELECT first_name, last_name FROM users WHERE user_id = :id;");
				$stmt->bindValue(':id', $id_int, SQLITE3_INTEGER);
				try {
					$results = $stmt->execute();
				} catch (Exception $e) {
					echo 'Caught exception: ' . $e->getMessage();
					exit();
				}
			} else {
				$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
				#print $query;
				try {
					$results = $sqlite_db_connection->query($query);
				} catch (Exception $e) {
					echo 'Caught exception: ' . $e->getMessage();
					exit();
				}
			}

			if ($results) {
				while ($row = $results->fetchArray()) {
					// Get values
					$first = $row["first_name"];
					$last  = $row["last_name"];

					// Feedback for end user
					$html .= "<pre>ID: {$id_display}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				echo "Error in fetch ".$sqlite_db->lastErrorMsg();
			}
			break;
	}
}

?>
