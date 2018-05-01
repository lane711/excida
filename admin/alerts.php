<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php' ;
include PATH . '/defaults.php';

$title = $lang['Alert'];

include PATH . '/admin/template/header.php';

// If logged we can start the page output
if ( adminAuth( $session->fetch( 'adminlogin' ), $session->fetch( 'adminpassword' ) ) )
{
	// Include navigation panel
	$session->set('navigation', '');
	include ( PATH . '/admin/navigation.php' );
	
	// Generating the configuration form
	echo table_header ( $lang['Alert'] );
	
	// Delete alert
	if ( $_REQUEST['action'] == 'delete' && $_REQUEST['alert_id'] != '' )
	{
		$db->query('DELETE FROM ' . ALERTS_TABLE . ' WHERE alert_id = ' . $_GET['alert_id']);
		echo 'The alert has been removed.<br /><br />';
	}
	
	// Approve pending alert
	if ( $_REQUEST['action'] == 'approve' && $_REQUEST['alert_id'] != '' )
	{
		$db->query('UPDATE ' . ALERTS_TABLE . ' SET approved = 1 WHERE alert_id = ' . $_GET['alert_id']);
		echo 'The alert has been approved.<br /><br />';
	}
	
	// Limit & Pagination
	$page = ( $_REQUEST['page'] != '' ) ? (int)$_REQUEST['page'] : 1;
	
	if ( $page == 1 )
	{
		$limit = '0, ' . $conf['search_results'];
	}
	else
	{
		$prev_page = $page - 1;
		$limit = $prev_page * $conf['search_results'] . ', ' . $conf['search_results'];
	}
	
	// Fetching the configuration data from the database
	$sql = "
	SELECT * 
	FROM " . ALERTS_TABLE . "
	LIMIT " . $limit;
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		echo '
		<table cellpadding="7" cellspacing="0" border="0" width="100%">
		<tr>
			<td><b>Date</b></td>
			<td><b>Email</b></td>
			<td><b>Status</b></td>
			<td><b>Options</b></td>
		</tr>
		';
	
		while ( $f = $db->fetcharray( $q ) )
		{    
			if ($f['approved'] == 1)
			{
				$status = '<span style="color:green">Active</span>';
			}
			else
			{
				$status = '<a href="' . URL . '/admin/alerts.php?action=approve&alert_id=' . $f['alert_id'] . '" style="color:red">Pending</a>';
			}
			
			echo '
			<tr>
				<td>' . $f['date'] . '</td>
				<td>' . $f['email'] . '</td>
				<td>' . $status . '</td>
				<td><a href="' . URL . '/admin/alerts.php?action=delete&alert_id=' . $f['alert_id'] . '">Remove</td>
			</tr>
			';
		}
		echo '</table>';
		
		// Pagination
		$sql = "
		SELECT COUNT(*) AS total_results
		FROM " . ALERTS_TABLE;
		$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		$f = $db->fetcharray( $q );
		$total_results = $f['total_results'];
		
		$custom['pagination'] = pagination( URL . '/admin/alerts.php', $_REQUEST['page'], $total_results, $conf['search_results'] );

        if ( is_array( $custom['pagination'] ) )
        {	
        	$num = 1;
        	echo '<br clear="both"><br clear="both">';
	        foreach ( $custom['pagination'] AS $page )
	        {				        	
	        	if ( $_REQUEST['page'] == $page['page'] || ( $_REQUEST['page'] == '' && $num == 1 ) )
	        	{
	        		$bold = 'bold';
	        	}
	        	else
	        	{
	        		$bold = 'normal';
	        	}
	        	
	        	echo '<a href="' . $page['url'] . '" style="font-weight:' . $bold . '">' . $page['page'] . '</a>&nbsp;&nbsp;';
	        
				$num++;
	        }
        }
	}
	else
	{
		echo 'There aren\'t any alerts in the system yet.<br /><br />';	
	}

	echo table_footer ();
}
else
{
	header( 'Location: ' . URL . '/admin/index.php' );
	exit();
}

include PATH . '/admin/template/footer.php';

?>