<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

if ( isset( $_REQUEST['id'] ) && preg_match( '/^[0-9]+$/', $_REQUEST['id'] ) )
{
	// Approve this account now that we've verified their email
	$sql = "
	UPDATE " . USERS_TABLE . "
	SET 
		approved = '1'
	WHERE 
		number = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
	LIMIT 1
	";
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	
	// Redirect to the log in page so they can begin
	header( 'Location: ' . URL . '/login.php' );
	exit();
}

?>