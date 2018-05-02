<?php

// This function checks if there is login in the database and
// checks if the password is correct if this login exists

function auth_check ($login, $password)
 {

  global $db;

  // SELECT a user with this login from the users table
  $sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE login = "' . $login . '" LIMIT 1';
  $r = $db->query ($sql);

  // If we found the record with this login
  // we proceed
  if ($db->numrows($r) > 0)
   {
    $f = $db->fetcharray($r);

    // If login and password are correct we make the
    // $logged variable TRUE
    if ($f['login'] == $login
    && $f['password'] == $password)
     $logged = TRUE;
    else
     $logged = FALSE;
   }
  else
   {
    $logged = FALSE;
   }

  return $logged;

 }

/*
if (!defined('PMRADMIN'))
	define('PMRADMIN', 'false');
*/

define('PMRUSER', true);

if (defined('PMRADMIN') && PMRADMIN == 'true' || PMRUSER == true) {

	if (!defined('LICENSE') || LICENSE == 'Your License Key Goes Here' || LICENSE == '')
		die('<h1>License Not Set</h1>Your license key has not been set in the configuration file. Please contact us at <a href="http://www.phpmyrealty.com/contact.html" target="_blank">http://www.phpMyRealty.com/contact.html</a>. Please include the following information:<ul><li>Domain name: "' . $_SERVER['SERVER_NAME'] . '"</li><li>Order ID# (either 2CheckOut.com or Plimus)</li><li>FTP/CPanel Information</li><li>phpMyRealty Version #</li></ul>Thanks!<br>The phpMyRealty Team');

	if (defined('LICENSE'))
	{
		$license2 = LICENSE;

		// Decode the license
		$sha_key_len = 40;

		$license_sha1 = substr($license2, 5, $sha_key_len);

		// License key 1
		$license_day = substr($license2, 4 + $sha_key_len + 1, 2) - 5;
		$license_month = substr($license2, 3, 2)-$license_day - 43 - 5;
		$license_year = '20' . substr($license2, -8, 2) - $license_day - 5;

		// Is this a key that will expire or a permanent license?
		$date_now = time();
		$date_license = mktime (0,0,0, $license_month, $license_day, $license_year);
		$days_left = ceil(($date_license - $date_now)/86400);

		// If the key has expired, terminate
		if ($days_left <= 0)
			die('<h1>License Expired</h1>Your license key has expired. If you were running a trial version of phpMyRealty, we hope you have enjoyed the software. For purchasing information, please visit <a href="http://www.phpmyrealty.com/purchase.html" target="_blank">http://www.phpMyRealty.com/purchase.html</a>. If you have a full license to phpMyRealty and believe this message to be in error, please contact us at <a href="http://www.phpmyrealty.com/contact.html" target="_blank">http://www.phpMyRealty.com/contact.html</a> and we will get back to you as soon as possible. Please include the following:<ul><li>Domain name: "' . $_SERVER['SERVER_NAME'] . '"</li><li>Order ID# (either 2CheckOut.com or Plimus)</li><li>FTP/CPanel Information</li><li>phpMyRealty Version #</li></ul>Thanks!<br>The phpMyRealty Team');

		// Accomodate with or without the 'www.'
		$domain = str_replace( 'www.', '', $_SERVER['SERVER_NAME'] );

		// If the key is invalid
		if ($license_sha1 != strtoupper(sha1($domain . ' DOMAIN NAME')))
			die('<h1>License Incorrect</h1>Your license key is invalid. Please contact us at <a href="http://www.phpmyrealty.com/contact.html" target="_blank">http://www.phpMyRealty.com/contact.html</a>. Please include the following information:<ul><li>Domain name: "' . $_SERVER['SERVER_NAME'] . '"</li><li>Order ID# (either 2CheckOut.com or Plimus)</li><li>FTP/CPanel Information</li><li>phpMyRealty Version #</li></ul>Thanks!<br>The phpMyRealty Team');
	}


function licenseExpiration() {

	$sha_key_len = 40;
	$license2 = LICENSE;

	$license_sha1 = substr($license2, 5, $sha_key_len);

	// License key 1
	$license_day = substr($license2, 4 + $sha_key_len + 1, 2) - 5;
	$license_month = substr($license2, 3, 2) - $license_day - 43 - 5;
	$license_year = '20' . substr($license2, -8, 2) - $license_day - 5;

	// Is this a key that will expire or a permanent license?
	$date_now = time();
	$date_license = mktime (0,0,0, $license_month, $license_day, $license_year);
	$days_left = floor(($date_license - $date_now)/86400);

	return $days_left;

}

function adminCheckIP($ip) {
 global $admin_ip;

 if (strstr($admin_ip, ';')) {
  $ip_in = explode (';', $admin_ip);

  foreach($ip_in AS $key => $value) {
   $blacked=str_replace('*', '', $value);
   $len=strlen($blacked);

  if ($ip==$blacked OR substr($ip, 0, $len)==$blacked)
   return TRUE;
  }
 }

 if (!strstr($admin_ip, ';') && $admin_ip != '') {
   $blacked=str_replace('*', '', $admin_ip);
   $len=strlen($blacked);

  if ($ip==$blacked OR substr($ip, 0, $len)==$blacked)
   return TRUE;
 }

 if ($admin_ip == '')
  return TRUE;

 return FALSE;

}

// --------------------------------------------------------------------------
// CHECKPRIVILEGE()
// This function checks if a privilege is enabled for this admin user
// and returns TRUE if 'YES' and FALSE if ''

function adminPermissionsCheck( $name, $login )
 {

 global $db;

 // Privileges check
 $sql = 'SELECT level FROM ' . ADMINS_TABLE . ' WHERE login = "' . $login . '"';
 $r = $db->query ($sql) or error ('Critical Error', mysql_error ());
 $f = $db->fetcharray($r);
 $admin_privilege = $f['level'];

	if ($admin_privilege == 'SUPERUSER')
	{
  		return TRUE;
	}
	else
	{
		$sql = 'SELECT ' . $name . ' FROM ' . PRIVILEGES_TABLE . ' WHERE privilege = "' . $admin_privilege . '" LIMIT 1';
		$r = $db->query ($sql) or error ('Critical Error', mysql_error ());
		$f = $db->fetcharray($r);

		$privilege = $f[''. $name . ''];

		if ($privilege == 'YES')
			return TRUE;
		else
			return FALSE;
	}
}

// ----------------------------------------------------------------------
// COUNTFILES()
// This function counts all image files in the folder
// for admin statistics

 function countfiles ($directory)
 {
  $handle = opendir (PATH . '/' . $directory);

  $count=0;

  while ($file = readdir($handle))
  if ($file != "." && $file != ".." && $file != "_vti_cnf" && $file != "index.html" && $file != ".htaccess") $count++;

  closedir ($handle);
  return $count/2;
 }

// ----------------------------------------------------------------------
// REMOVEUSER()
// removes user and all associated information from the database
//

function removeuser ( $id )
{

global $db;

$sql = 'DELETE FROM ' . USERS_TABLE . ' where u_id = ' . $id;
$db->query($sql) or error ('Critical Error' , 'Can\'t remove user record');
remove_image('photos', $id);

$sql = 'SELECT approved, listing_id, type FROM ' . PROPERTIES_TABLE . ' where userid = ' . $id;
$r = $db->query($sql) or error ('Critical Error' , 'Can\'t SELECT FROM listings DATABASE');

while ($f = $db->fetcharray($r))
 {

  remove_image('images', $f['id']);

  $sql = 'DELETE FROM ' . FEATURED_TABLE . ' where id = ' . $f['id'];
  $db->query($sql) or error ('Critical Error' , 'Can\'t remove user record in featured table');
 }

 $sql = 'DELETE FROM ' . PROPERTIES_TABLE . ' WHERE userid = ' . $id;
 $db->query($sql) or error ('Critical Error' , 'Can\'t remove listings record');

 $sql = 'SELECT id FROM ' . GALLERY_TABLE . ' WHERE userid = ' . $id;
 $r = $db->query($sql) or error ('Critical Error' , 'Can\'t SELECT FROM gallery DATABASE');

 while ($f = $db->fetcharray($r))
  {
   remove_image('gallery', $f['id']);
  }

 $sql = 'DELETE FROM ' . GALLERY_TABLE . ' where userid = ' . $id;
 $db->query($sql) or error ('Critical Error' , 'Can\'t remove gallery record');

}

// ----------------------------------------------------------------------
// REMOVELISTING()
// removes listing and all associated information from the database
//

function removelisting( $id )
{
	removeuserlisting( $id );
}

// ----------------------------------------------------------------------
// MYSQL BACKUP FUNCTIONS

function mysqlbackup($dbname, $structure_only, $crlf)
{

 // here we check MySQL Version
 $result=@mysql_query("SELECT VERSION() AS version");
 if ($result != FALSE && @mysql_num_rows($result) > 0) {
  $row   = @mysql_fetch_array($result);
  $match = explode('.', $row['version']);
 } else {
  $result=@mysql_query("SHOW VARIABLES LIKE \'version\'");
  if ($result != FALSE && @mysql_num_rows($result) > 0){
   $row   = @mysql_fetch_row($result);
   $match = explode('.', $row[1]);
  }
 }

 if (!isset($match) || !isset($match[0])) {
  $match[0] = 3;
 }
 if (!isset($match[1])) {
  $match[1] = 21;
 }
 if (!isset($match[2])) {
  $match[2] = 0;
 }
 if(!isset($row)) {
  $row = '3.21.0';
 }

 define('MYSQL_INT_VERSION', (int)sprintf('%d%02d%02d', $match[0], $match[1], intval($match[2])));
 define('MYSQL_STR_VERSION', $row['version']);
 unset($match);

 $sql = "# MySQL dump by phpMyRealty".$crlf;
 $sql.= "# ----------------------------".$crlf;
 $sql.= "# Server version: ".MYSQL_STR_VERSION.$crlf;

 $sql.= $crlf.$crlf.$crlf;
 out(1,$sql);
 $res=@mysql_list_tables($dbname);
 $nt=@mysql_num_rows($res);

 for ($a=0;$a<$nt;$a++) {
  $row=mysql_fetch_row($res);
  $tablename=$row[0];

  $sql=$crlf."# ----------------------------------------".$crlf."# table structure for table '$tablename' ".$crlf;
  // For MySQL < 3.23.20
  if (MYSQL_INT_VERSION >= 32321) {
   $result=mysql_query("SHOW CREATE TABLE $tablename");
   if ($result != FALSE && mysql_num_rows($result) > 0) {
    $tmpres = mysql_fetch_array($result);
    $pos           = strpos($tmpres[1], ' (');
    $tmpres[1]     = substr($tmpres[1], 0, 13)
                     . $tmpres[0]
                     . substr($tmpres[1], $pos);

    $sql .= $tmpres[1].";".$crlf.$crlf;
   }
   mysql_free_result($result);
  } else {
   $sql.="CREATE TABLE $tablename(".$crlf;
   $result=mysql_query("show fields  from $tablename",$con);

   while ($row = mysql_fetch_array($result)) {
    $sql .= "  ".$row['Field'];
    $sql .= ' ' . $row['Type'];
    if (isset($row['Default']) && $row['Default'] != '') {
     $sql .= ' DEFAULT \'' . $row['Default'] . '\'';
    }
    if ($row['Null'] != 'YES') {
     $sql .= ' NOT NULL';
    }
    if ($row['Extra'] != '') {
     $sql .= ' ' . $row['Extra'];
    }
    $sql .= ",".$crlf;
   }

   mysql_free_result($result);
   $sql = str_replace(',' . $crlf . '$', '', $sql);

   $result = mysql_query("SHOW KEYS FROM $tablename");
    while ($row = mysql_fetch_array($result)) {
     $ISkeyname    = $row['Key_name'];
     $IScomment  = (isset($row['Comment'])) ? $row['Comment'] : '';
     $ISsub_part = (isset($row['Sub_part'])) ? $row['Sub_part'] : '';
     if ($ISkeyname != 'PRIMARY' && $row['Non_unique'] == 0) {
      $ISkeyname = "UNIQUE|$kname";
     }
     if ($IScomment == 'FULLTEXT') {
      $ISkeyname = 'FULLTEXT|$kname';
     }
     if (!isset($index[$ISkeyname])) {
      $index[$ISkeyname] = array();
     }
     if ($ISsub_part > 1) {
      $index[$ISkeyname][] = $row['Column_name'] . '(' . $ISsub_part . ')';
     } else {
      $index[$ISkeyname][] = $row['Column_name'];
     }
    }
    mysql_free_result($result);

    while (list($x, $columns) = @each($index)) {
     $sql     .= ",".$crlf;
     if ($x == 'PRIMARY') {
      $sql .= '  PRIMARY KEY (';
      } else if (substr($x, 0, 6) == 'UNIQUE') {
      $sql .= '  UNIQUE ' . substr($x, 7) . ' (';
     } else if (substr($x, 0, 8) == 'FULLTEXT') {
      $sql .= '  FULLTEXT ' . substr($x, 9) . ' (';
     } else {
      $sql .= '  KEY ' . $x . ' (';
     }
     $sql     .= implode($columns, ', ') . ')';
    }
    $sql .=  $crlf.");".$crlf.$crlf;

  }
  out(1,$sql);
 if ($structure_only == FALSE) {
  // here we get table content
  $result = mysql_query("SELECT * FROM  $tablename");
  $fields_cnt   = mysql_num_fields($result);
  while ($row = mysql_fetch_row($result)) {
   $table_list     = '(';
   for ($j = 0; $j < $fields_cnt; $j++) {
    $table_list .= mysql_field_name($result, $j) . ', ';
   }
   $table_list = substr($table_list, 0, -2);
   $table_list     .= ')';

   $sql = 'INSERT INTO ' . $tablename
                                   . ' VALUES (';
   for ($j = 0; $j < $fields_cnt; $j++) {
    if (!isset($row[$j])) {
     $sql .= ' NULL, ';
    } else if ($row[$j] == '0' || $row[$j] != '') {
     $type          = mysql_field_type($result, $j);
     // a number
     if ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' ||
                        $type == 'bigint'  ||$type == 'timestamp') {
      $sql .= $row[$j] . ', ';
     }
     // a string
     else {
      $dummy  = '';
      $srcstr = $row[$j];
      for ($xx = 0; $xx < strlen($srcstr); $xx++) {
       $yy = strlen($dummy);
       if ($srcstr[$xx] == '\\')   $dummy .= '\\\\';
       if ($srcstr[$xx] == '\'')   $dummy .= '\\\'';
       if ($srcstr[$xx] == "\x00") $dummy .= '\0';
       if ($srcstr[$xx] == "\x0a") $dummy .= '\n';
       if ($srcstr[$xx] == "\x0d") $dummy .= '\r';
       if ($srcstr[$xx] == "\x1a") $dummy .= '\Z';
       if (strlen($dummy) == $yy)  $dummy .= $srcstr[$xx];
      }
      $sql .= "'" . $dummy . "', ";
     }
    } else {
     $sql .= "'', ";
    } // end if
   } // end for
   $sql = str_replace(', $', '', $sql);
   $sql .= ");".$crlf;
   out(1,$sql);

  }
  mysql_free_result($result);
  }
 }
 return;
}

function define_crlf() {
 global $HTTP_USER_AGENT;
 $ucrlf = "\n";
 if (strstr($HTTP_USER_AGENT, 'Win')) {
  $ucrlf = "\r\n";
 }
 else if (strstr($HTTP_USER_AGENT, 'Mac')) {
  $ucrlf = "\r";
 }
 else {
  $ucrlf = "\n";
 }
 return $ucrlf;
}

//print the result
function out($fptr,$s)   {
 echo $s;
}

// This function checks if there is login for this admin
// in the database and checks if the password is correct
// if this login exists

function adminAuth ($login, $password) {

 global $db;
 global $_SERVER;

 $user_ip = $_SERVER['REMOTE_ADDR'];
 // If there is more than one IP
 // get the first one from the
 // comma separated list
 if ( strstr($user_ip, ', ') ) {
  $ips = explode(', ', $user_ip);
  $user_ip = $ips[0];
 }

 if (!adminCheckIP($user_ip)) {
  die('<h1>Access Denied for ' . $user_ip . '</h1><br />You must enable this IP in your configuration file if you are the administrator of this site');
 }

 // SELECT admin with this login from the admin table
 $sql = 'SELECT * FROM ' . ADMINS_TABLE . ' WHERE login = "' . $login . '" LIMIT 1';
 $r = $db->query ($sql);

 // If we found the record with this login
 // we proceed
 if ($db->numrows($r) > 0) {
  $f = $db->fetcharray($r);
  // If login and password are correct we make the
  // $logged variable TRUE
  if ($f['login'] == $login && $f['password'] == $password && adminCheckIP($user_ip))
   $logged = TRUE;
  else
   $logged = FALSE;
 }
 else
  $logged = FALSE;
 return $logged;
}

}
?>