<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

@set_time_limit(1800); 
ini_set('auto_detect_line_endings',true); 

// Title tag content
$title = 'Location Import';

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
 	$whereClause = "";
    if($session->fetch('role')=="SUPERUSER")
      $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
 // Navigation menu
 include ( PATH . '/admin/navigation.php' );

 // Make sure this administrator have access to this script
 adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

 if (isset($_FILES['file']['tmp_name'])) 

  {

   $in = file ($_FILES['file']['tmp_name']) or die ('<font color="red">CSV file can not be found</font> Exiting..');

   $output .= '<div align="left">';

	$total1 = 0;
	$total2 = 0;
	$total3 = 0;
	
	foreach ( $in AS $key => $value )
	{
		$value = trim( $value );
		
		// We use ; as a delimiter
		$csv = explode( ';', $value );
		
		// 3 level locations
		$location1 = $csv[0];
		$location2 = $csv[1];
		$location3 = $csv[2];
		
		// First level
		$sql = "
		SELECT location_id
		FROM " . LOCATIONS_TABLE . "
		WHERE
			location_name = '" . $db->makeSafe( $location1 ) . "'
			AND location_parent = '0' AND ".$whereClause."
		";
		$q = $db->query( $sql ) or die( mysql_error() );
		if ($db->numrows($q ) > 0 )
		{
			// Grab the ID for any children locations (level 2)
			$f = $db->fetcharray( $q );
			$location_1_id = $f['location_id'];
		}
		else
		{
			// Add it as a new location
			$sql = "
			INSERT INTO " . LOCATIONS_TABLE . "
			(
				location_name,site_id
			)
			VALUES
			(
				'" . $db->makeSafe( $location1 ) . "','".$session->fetch('site_id')."'
			)
			";
			$q = $db->query( $sql ) or die( mysql_error() );
			$location_1_id = $db->getLastID();
			
			$total1++;
		}
		
		// Second level
		if ( $location2 != '' )
		{
			$sql = "
			SELECT location_id
			FROM " . LOCATIONS_TABLE . "
			WHERE 
				location_name = '" . $db->makeSafe( $location2 ) . "'
				AND location_parent = '" . $location_1_id . "' AND ".$whereClause."
			";
			$q = $db->query( $sql );
			if ($db->numrows($q ) > 0 )
			{
				// Grab the ID for any children locations (level 2)
				$f = $db->fetcharray( $q );
				$location_2_id = $f['location_id'];
			}
			else
			{
				// Add it as a new location
				$sql = "
				INSERT INTO " . LOCATIONS_TABLE . "
				(
					location_name,
					site_id,
					location_parent
				)
				VALUES
				(
					'" . $db->makeSafe( $location2 ) . "',
					'".$session->fetch('site_id')."',
					'" . $location_1_id . "'
				)
				";
				$q = $db->query( $sql ) or die( mysql_error() );
				$location_2_id = $db->getLastID();
				
				$total2++;
			}
			
			// Third level
			if ( $location3 != '' )
			{
				$sql = "
				SELECT location_id
				FROM " . LOCATIONS_TABLE . "
				WHERE 
					location_name = '" . $db->makeSafe( $location3 ) . "'
					AND location_parent = '" . $location_2_id . "' AND ".$whereClause."
				";
				$q = $db->query( $sql );
				if ($db->numrows($q ) == 0 )
				{
					// Add it as a new location
					$sql = "
					INSERT INTO " . LOCATIONS_TABLE . "
					(
						location_name,
						site_id,
						location_parent
					)
					VALUES
					(
						'" . $db->makeSafe( $location3 ) . "',
						'".$session->fetch('site_id')."',
						'" . $location_2_id . "'
					)
					";
					$q = $db->query( $sql ) or die( mysql_error() );
					
					$total3++;
				}
			}
		}
	}

	$output .= "Totals added:<blockquote> " . number_format( $total1 ) . " first level locations<br />" . number_format( $total2 ) . " second level locations<br />" . number_format( $total3 ) . " third level locations</blockquote>";

     $output .= '</div>';
     
     echo table_header ('3-level Locations CSV Import Tool');
     
     echo $output;
     
     echo table_footer();

  }

 else

  {

   echo table_header ('Location CSV Import Tool');
   
  echo '
  <a href="' . URL . '/admin/import.php">CSV Import</a> | 
  <a href="' . URL . '/admin/export.php">CSV Export</a> | 
  <a href="' . URL . '/admin/tools.php">3-level Locations Import</a> | 
  <a href="' . URL . '/admin/zipimport.php">ZIP Codes Radius Search Import</a> | 
  <a href="' . URL . '/admin/idx.php">IDX Import</a>
  <br /><br /><br />
  ';

?>

<form action = "<?php echo URL;?>/admin/tools.php" method="POST" enctype="multipart/form-data">

CSV File: <input type="file" name="file"><input type="submit" value="Start"><br />

</form>

<br /><br />

This tool allows you to import locations that users can select from when creating their listings and accounts. 

<br /><br />

<b>Note:</b> Use a CSV text file only. Do not upload XLS or any other BINARY files. Please note that you MUST use a UTF-8 compliant text editor and file in order to process accent marks and other languages properly.

<br /><br />

The following format should be used: Location1;Location2;Location3

<br /><br />

For example, you could do the following to achieve a Country > State > Town/City hierarchy:

<br /><br />

United States;Massachusetts;Boston<br />
Untied States;Massachusetts;Springfield

<br /><br />

Alternatively, you can enter just one or two levels. You do not need to use all three. You may also use them in different ways, for example:

<br /><br />

Massachusetts;Boston;South Boston

<br /><br />

This would give you a State > City/Town > Neighborhood hierarchy. It is flexible and you can import as few or up to three levels.

<?php

   echo table_footer ();

  }

 }

else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>