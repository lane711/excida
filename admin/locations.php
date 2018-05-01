<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['admin_3level'];

include PATH . '/admin/template/header.php';

// If logged we can start the page output
if ( adminAuth( $session->fetch( 'adminlogin' ), $session->fetch( 'adminpassword' ) ) )
{
	include PATH . '/admin/navigation.php';
	$whereClause = "";
  	if($session->fetch('role')=="SUPERUSER")
  	$whereClause=" AND site_id=".$session->fetch('site_id');
  	$whereClause;
	// Make sure this administrator have access to this script
	adminPermissionsCheck( 'manage_types', $session->fetch( 'adminlogin' ) ) or error( 'Critical Error', 'Incorrect privileges' );
	
	echo table_header ( $lang['admin_3level'] );
	
	echo '
	<a href="' . URL . '/admin/edit_custom.php?req=custom">' . $lang['Admin_Custom_Types'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=types">' . $lang['Admin_Property_Types'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=types2">' . $lang['Module_Listing_Type'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=styles">' . $lang['Admin_Styles'] . '</a> | 
	<a href="' . URL . '/admin/locations.php">' . $lang['admin_3level'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=buildings">' . $lang['Admin_Additional_Out_Buildings'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=appliances">' . $lang['Admin_Appliances_Included'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=features">' . $lang['Admin_Features'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=basement">' . $lang['Admin_Basement'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=garage">' . $lang['Admin_Garage'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=status">' . $lang['Admin_Status'] . '</a>
	<br /><br />
	<b>Tip:</b> Adding a lot of locations? Try our <a href="tools.php">location import tool</a>.
	<br /><br />
	<a href="locations.php?action=add">Add Location</a> | <a href="locations.php">Manage Locations</a>
	<br /><br />
	';
	
	// Add form
	if ( $_REQUEST['action'] == 'add' )
	{
		if ( $_POST['submit'] == true )
		{
			if ( $_POST['location_name'] != '' )
			{			
				$sql = "
				INSERT INTO " . LOCATIONS_TABLE . "
				(
					location_name,
					site_id,
					location_parent
				)
				VALUES
				(
					'" . $db->makeSafe( $_POST['location_name'] ) . "',
					'" . $session->fetch('site_id') . "',
					'" . $db->makeSafe( $_POST['location_parent'] ) . "'
				)
				";
				$q = $db->query( $sql );
				
				echo "<b>Success!</b> The location has been added.<br /><br />";
			}
		}
	
		echo '
  		<form action="locations.php?action=add" method="post" name="form">
  		<table cellpadding="4" cellspacing="0" border="0" align="left" width="100%">
		<tr>
			<td align="right" style="width:10%"">Location Name</td>
			<td align="left"><input type="text" name="location_name" value="' . $_REQUEST['location_name'] . '"></td>
		</tr>
		<tr>
			<td align="right">Location Parent</td>
			<td align="left">
			<select name="location_parent">
				<option value="0">None</option>
				';
				
				$sql = "
				SELECT location_id, location_name
				FROM " . LOCATIONS_TABLE . "
				WHERE location_parent = '0'".$whereClause."
				ORDER BY location_name ASC
				";
				$q = $db->query( $sql );
				if ( $db->numrows( $q ) > 0 )
				{
					while ( $f = $db->fetchassoc( $q ) )
					{
						$sel = ( $f['location_id'] == $_REQUEST['location_id'] ) ? ' selected' : '';
						echo '<option value="' . $f['location_id'] . '"' . $sel . '>' . $f['location_name'] . '</option>';
						
						// Grab all locations under this
						$sql = "
						SELECT location_id, location_name
						FROM " . LOCATIONS_TABLE . "
						WHERE location_parent = '" . $f['location_id'] . "' ".$whereClause."
						ORDER BY location_name ASC
						";
						$q2 = $db->query( $sql );
						if ( $db->numrows( $q2 ) > 0 )
						{
							while ( $f2 = $db->fetchassoc( $q2 ) )
							{
								$sel = ( $f2['location_id'] == $_REQUEST['location_id'] ) ? ' selected' : '';
								echo '<option value="' . $f2['location_id'] . '"' . $sel . '>- ' . $f2['location_name'] . '</option>';
								
								// Grab all locations under this
								$sql = "
								SELECT location_id, location_name
								FROM " . LOCATIONS_TABLE . "
								WHERE location_parent = '" . $f2['location_id'] . "' ".$whereClause."
								ORDER BY location_name ASC
								";
								$q3 = $db->query( $sql );
								if ( $db->numrows( $q3 ) > 0 )
								{
									while ( $f3 = $db->fetchassoc( $q3 ) )
									{
										$sel = ( $f3['location_id'] == $_REQUEST['location_id'] ) ? ' selected' : '';
										echo '<option value="' . $f3['location_id'] . '"' . $sel . '>-- ' . $f3['location_name'] . '</option>';
									}
								}
							}
						}
					}
				}
				
				echo '
			</select>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;</td>
			<td align="left"><input type="submit" name="submit" value="submit"></td>
		</tr>
		</table>
		</form>
		<br /><br />
		';
	}
	
	// Edit location
	if ( $_REQUEST['action'] == 'edit' && $_REQUEST['location_id'] != '' )
	{
		if ( $_POST['submit'] == true )
		{
			if ( $_POST['location_name'] != '' )
			{
				// Make sure this name/location_parent doesn't already exist
				$sql = "
				SELECT location_id
				FROM " . LOCATIONS_TABLE . "
				WHERE
					location_parent = '" . $db->makeSafe( $_POST['location_parent'] ) . "'
					AND location_name = " . $db->makeSafe( $_POST['location_name'] ) . "'
				";
				$q = $db->query( $sql );
				if ( $db->numrows( $q ) == 0 )
				{			
					$sql = "
					UPDATE " . LOCATIONS_TABLE . "
					SET 
						location_name = '" . $db->makeSafe( $_POST['location_name'] ) . "',
						location_parent = '" . $db->makeSafe( $_POST['location_parent'] ) . "'
					WHERE
						location_id = '" . $db->makeSafe( $_POST['location_id'] ) . "'
					";
					$q = $db->query( $sql );
					
					echo "<b>Success!</b> The location has been updated.<br /><br />";
				}
				else
				{
					echo "<b>Error:</b> This location/parent combination already exists!<br /><br />";
				}
			}
		}
		
		$sql = "
		SELECT 
			location_id, location_parent, location_name
		FROM " . LOCATIONS_TABLE . "
		WHERE location_id = '" . $db->makeSafe( $_REQUEST['location_id'] ) . "'
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			$f = $db->fetcharray( $q );
			
			if ( $_POST )
			{
				$form = $_POST;
			}
			else
			{
				$form = $f;
			}

			echo '
	  		<form action="locations.php?action=edit" method="post" name="form">
	  		<input type="hidden" name="location_id" value="' . $_REQUEST['location_id'] . '">
	  		<table cellpadding="4" cellspacing="0" border="0" align="left" width="100%">
			<tr>
				<td align="right" style="width:10%"">Location Name</td>
				<td align="left"><input type="text" name="location_name" value="' . stripslashes( $form['location_name'] ) . '"></td>
			</tr>
			<tr>
				<td align="right">Location Parent</td>
				<td align="left">
				<select name="location_parent">
					<option value="0">None</option>
					';
					
					$sql = "
					SELECT location_id, location_name
					FROM " . LOCATIONS_TABLE . "
					WHERE location_parent = '0'
					ORDER BY location_name ASC
					";
					$q = $db->query( $sql );
					if ( $db->numrows( $q ) > 0 )
					{
						while ( $f = $db->fetchassoc( $q ) )
						{
							$sel = ( $f['location_id'] == $form['location_parent'] ) ? ' selected' : '';
							echo '<option value="' . $f['location_id'] . '"' . $sel . '>' . $f['location_name'] . '</option>';
							
							// Grab all locations under this
							$sql = "
							SELECT location_id, location_name
							FROM " . LOCATIONS_TABLE . "
							WHERE location_parent = '" . $f['location_id'] . "'
							ORDER BY location_name ASC
							";
							$q2 = $db->query( $sql );
							if ( $db->numrows( $q2 ) > 0 )
							{
								while ( $f2 = $db->fetchassoc( $q2 ) )
								{
									$sel = ( $f2['location_id'] == $form['location_parent'] ) ? ' selected' : '';
									echo '<option value="' . $f2['location_id'] . '"' . $sel . '>- ' . $f2['location_name'] . '</option>';
									
									// Grab all locations under this
									$sql = "
									SELECT location_id, location_name
									FROM " . LOCATIONS_TABLE . "
									WHERE location_parent = '" . $f2['location_id'] . "'
									ORDER BY location_name ASC
									";
									$q3 = $db->query( $sql );
									if ( $db->numrows( $q3 ) > 0 )
									{
										while ( $f3 = $db->fetchassoc( $q3 ) )
										{
											$sel = ( $f3['location_id'] == $form['location_parent'] ) ? ' selected' : '';
											echo '<option value="' . $f3['location_id'] . '"' . $sel . '>-- ' . $f3['location_name'] . '</option>';
										}
									}
								}
							}
						}
					}
					
					echo '
				</select>
				</td>
			</tr>
			<tr>
				<td align="right">&nbsp;</td>
				<td align="left"><input type="submit" name="submit" value="Update"></td>
			</tr>
			</table>
			</form>
			<br /><br />
			';
		}
		else
		{
			echo "<b>Error:</b> The location ID could not be found in the system. Please try again.<br /><br />";
		}
	}
	
	// Delete
	if ( $_REQUEST['action'] == 'delete' && $_REQUEST['location_id'] != '' )
	{
		// Get all 2nd level locations that are under this requested location, if any
		$sql = "SELECT location_id FROM " . LOCATIONS_TABLE . " WHERE location_id = '" . $db->makeSafe( $_REQUEST['location_id'] ) . "'";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			while ( $f = $db->fetchassoc( $q ) )
			{
				// Delete all locations under this one
				$sql = "DELETE FROM " . LOCATIONS_TABLE . " WHERE location_parent = '" . $db->makeSafe( $f['location_id'] ) . "'";
				$q2 = $db->query( $sql );
			}
		}
	
		// Delete any 2nd level locations under the requested location
		$sql = "DELETE FROM " . LOCATIONS_TABLE . " WHERE location_parent = '" . $db->makeSafe( $_REQUEST['location_id'] ) . "'";
		$q2 = $db->query( $sql );
		
		// Delete the requested location
		$sql = "DELETE FROM " . LOCATIONS_TABLE . " WHERE location_id = '" . $db->makeSafe( $_REQUEST['location_id'] ) . "'";
		$q = $db->query( $sql );
	}
	
	// All parents or a specific parent's locations
	if ( $_REQUEST['location_parent'] != '' )
	{
		$whereSQL = " location_parent = '" . $db->makeSafe( $_REQUEST['location_parent'] ) . "' ";
	}
	else
	{
		$whereSQL = " location_parent = '0' ";
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

	$sql = "
	SELECT *
	FROM " . LOCATIONS_TABLE . "
	WHERE 
		" . $whereSQL . " ".$whereClause."
	ORDER BY location_name ASC
	LIMIT " . $limit . "
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		echo '
		<table width="100%" border="0" cellpadding="4" cellspacing="0" align="left">
		<tr>
			<td align="left"><b>Location ID</b></td>
			<td align="left"><b>Location Name</b></td>
			<td align="left"><b>Location Parent</b></td>
			<td align="center"><b>Options</b></td>
		</tr>
		';
		
		while ( $f = $db->fetcharray( $q ) )
		{	
			// If location parent isn't blank
			if ( $f['location_parent'] != 0 )
			{
				$location_parent = get_location_name( $f['location_parent'] );
			}
			else
			{
				$location_parent = '-';
			}
		
			echo '
			<tr>
				<td align="left">' . $f['location_id'] . '</td>
				<td align="left"><a href="' . URL . '/admin/locations.php?location_parent=' . $f['location_id'] . '">' . $f['location_name'] . '</a></td>
				<td align="left">' . $location_parent . '</td>
				<td align="center"><a href="' . URL . '/admin/locations.php?action=edit&location_id=' . $f['location_id'] . '">Edit</a> | <a href="' . URL . '/admin/locations.php?action=delete&location_id=' . $f['location_id'] . '" onClick="return confirm(\'Are you sure you want to delete?\')">Delete</a></td>
			</tr>
			';
		}
		
		echo '
		</table>
		';
		
		// Pagination
		$sql = "
		SELECT COUNT(*) AS total_results
		FROM " . LOCATIONS_TABLE . "
		WHERE 
			" . $whereSQL . " ".$whereClause."
		";
		$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		$f = $db->fetcharray( $q );
		$total_results = $f['total_results'];
		
		$custom['pagination'] = pagination( URL . '/admin/locations.php', $_REQUEST['page'], $total_results, $conf['search_results'] );

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
		echo '<b>Error:</b> No locations were found. How about <a href="locations.php?action=add">adding</a> some?<br /><br />';
	}
	echo table_footer();
}
else
{
	header( 'Location: index.php' );
	exit();
}

include PATH . '/admin/template/footer.php';

?>