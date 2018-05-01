<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
  $whereClause = "";
    if($session->fetch('role')=="SUPERUSER")
      $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
  $mime_type = ('USER_BROWSER_AGENT' == 'IE' || 'USER_BROWSER_AGENT' == 'OPERA')
  ? 'application/octetstream'
  : 'application/octet-stream';


  $now = gmdate('D, d M Y H:i:s') . ' GMT';
  $filename = $dbi['sql_dbname'] . '-' . printdate(date('y-m-d'));
  $ext = "csv";
  $crlf = define_crlf();

  header('Content-Type: ' . $mime_type);
  header('Expires: ' . $now);

  if ('USER_BROWSER_AGENT' == 'IE') 
   {
    header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
   }
  else
   {
    header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
    header('Pragma: no-cache');
   }



$sql = 'SELECT * FROM ' . PROPERTIES_TABLE .' WHERE '.$whereClause;

$r = $db->query ( $sql );

  while ($f = $db->fetcharray( $r ))

   {


$cat = explode ("#", $f['location2']);

$r11 = $db->query('SELECT category FROM ' . LOCATION1_TABLE . ' WHERE selector = "' . $cat[0] . '"');
$f11 = $db->fetcharray($r11);

$r22 = $db->query('SELECT subcategory FROM ' . LOCATION2_TABLE . ' WHERE catsubsel = "' . $cat[1] . '"');
$f22 = $db->fetcharray($r22);

$r33 = $db->query('SELECT subsubcategory FROM ' . LOCATION3_TABLE . ' WHERE catsubsubsel = "' . $cat[2] . '"');
$f33 = $db->fetcharray($r33);

echo 

$f['id'] . ';' .
$f['userid'] . ';' .
$f['approved'] . ';' .
$f['featured'] . ';' .
$f['mls'] . ';' .
getnamebyid(TYPES_TABLE, $f['type']) . ';' .
getnamebyid(TYPES2_TABLE, $f['type2']) . ';' .
getnamebyid(STYLES_TABLE, $f['style']) . ';' .
$f['title'] . ';' .
$f['description'] . ';' .
$f['size'] . ';' .
$f['dimensions'] . ';' .
$f['bedrooms'] . ';' .
$f['bathrooms'] . ';' .
$f['half_bathrooms'] . ';' .
getnamebyid(GARAGE_TABLE, $f['garage']) . ';' .
$f['garage_cars'] . ';' .
getnamebyid(BASEMENT_TABLE, $f['basement']) . ';' .
$f11['category'] . ' ' . $f22['subcategory'] . ' ' . $f33['subsubcategory'] . ';' .
$f['zip'] . ';' .
$f['address1'] . ';' .
$f['address2'] . ';' .
$f['price'] . ';' .
$f['directions'] . ';' .
$f['year_built'] . ';' .
show_multiple ( BUILDINGS_TABLE, $f['buildings'] ) . ';' .
show_multiple ( APPLIANCES_TABLE, $f['appliances'] ) . ';' .
show_multiple ( FEATURES_TABLE, $f['features'] ) . ';' .
$f['date_added'] . ';' .
$f['date_updated'] . ';' .
$f['date_upgraded'] . ';' .
$f['ip_added'] . ';' .
$f['ip_updated'] . ';' .
$f['ip_upgraded'] . ';' .
$f['hits'] . ';' .
$f['video'] . "\n";
  }

 }

else
{
	header( 'Location: index.php' );
	exit();
}

?>