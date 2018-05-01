<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

@set_time_limit(1800); 
ini_set('auto_detect_line_endings',true); 

// Title tag content
$title = 'Postal / ZIP Codes CSV Import';

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

   echo '<div align="left">';

   echo '<b><font color="green">CSV FILE UPLOADED SUCCESSFULLY</font></b><br /><br />';

   echo '<b><font color="green">PROCESSING...</font><br /></b>';

   $total_zips_added = 0;

   foreach ($in as $key => $value)
    {

     $value = trim($value);

     // We use ; as a delimiter
     $csv = explode (';', $value);

     // 3 level locations

     $zip = $csv[0];
     $lat = $csv[1];
     $long = $csv[2];

     $db->query('INSERT INTO ' . ZIP_TABLE . ' (zip,site_id latitude, longitude) VALUES ("' . $zip . '","'.$session->fetch('site_id').'", "' . $lat . '", "' . $long . '")');
     //echo '<b><font color="green">NEW ZIP ADDED ' . $zip . '@ ' . $lat . '/' . $long . '</font></b><br />';

	 $total_zips_added++;

    }
    
    echo 'A total of ' . $total_zips_added . ' zip codes added.<br /><br />';

     echo '</div>';
  }
 else
  {
   echo table_header ('Postal / ZIP Codes CSV Import Tool');

  echo '
  <a href="' . URL . '/admin/import.php">CSV Import</a> | 
  <a href="' . URL . '/admin/export.php">CSV Export</a> | 
  <a href="' . URL . '/admin/tools.php">3-level Locations Import</a> | 
  <a href="' . URL . '/admin/zipimport.php">ZIP Codes Radius Search Import</a> | 
  <a href="' . URL . '/admin/idx.php">IDX Import</a>
  <br /><br /><br />
  ';
  
?>

<form action = "<?php echo URL;?>/admin/zipimport.php" method="POST" enctype="multipart/form-data">

CSV File: <input type="file" name="file"><input type="submit" value="Start"><br />

</form>

<br /><br />
Notes:<br /> Use CSV text files only. Do not upload XLS or any other BINARY files. Please note that you MUST use a UTF-8 complaint text editor and file in order to process accent marks and other languages properly.<br /><br />

1. Do a complete database backup before using this tool!<br />
2. Delimit CSV values with ;<br />
3. Use comma to separate decimals in lat/long values<br />
4. The exact format for the CSV (if you need more options, please, contact PMR support to have that added):<br />

<br /><span style="font-size:10px;">ZIP Code(5 digits);Latitude;Longitude</span><br /><br />

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