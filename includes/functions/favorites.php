<?php

// ----------------------------------------------------------------------------
// favoriteListingsAdd($id)
//
// adds the listing into the Favorite Listings cookie
//
// $id - listing ID
//

function favoriteListingsAdd($id) {

 global $conf;

 // Check if the cookie exists and fetch the data
 if (isset($_COOKIE['favoriteListings']) && strstr($_COOKIE['favoriteListings'], ';'))
  $list = explode (";", $_COOKIE['favoriteListings']);
 elseif (isset($_COOKIE['favoriteListings']) && !strstr($_COOKIE['favoriteListings'], ';'))
  $list = $_COOKIE['favoriteListings'];
 else
  $list = '';

 if (is_array($list)) {
  // If the id is not in the array we add it into the beginning
  if(!in_array($id, $list)) 
   array_unshift ($list, $id);

  // Create a new array
  $cookie = implode (";", $list);
 } 
 elseif (!is_array($list) && $list != '') {
  $cookie = $list . ';' . $id;
 }
 else
  $cookie = $id;

 // Clean up cookie
 $_c = explode(';', $cookie);
 foreach ($_c as $k => $v) {
     if (!is_numeric($v)) {
         unset($_c[$k]);
     }
 }
 $cookie = implode(';', $_c);

 // Generate/reset cookie
 setcookie ( "favoriteListings", "", time() - 3600, "/");
 setcookie ( "favoriteListings", $cookie, time()+2592000, "/");
 $_COOKIE['favoriteListings'] = $cookie;

}


// ----------------------------------------------------------------------------
// favoriteListingsRemove($id)
//
// removes the listing from the Favorite Listings cookie
//
// $id - listing ID
//

function favoriteListingsRemove($id) {

 $favorites_list = array();

 if (isset( $_COOKIE['favoriteListings'] ) && strstr($_COOKIE['favoriteListings'], ';')) {

  $favorites_list = explode (";", $_COOKIE['favoriteListings']);
  $key = array_search ($id, $favorites_list);
  unset ($favorites_list[$key]);

  $cookie = implode (";", $favorites_list);

  setcookie ( "favoriteListings", "", time() - 3600, "/");
  setcookie ( "favoriteListings", $cookie, time()+2592000, "/");
  $_COOKIE['favoriteListings'] = $cookie;

 }
 elseif (isset( $_COOKIE['favoriteListings'] ) && !strstr($_COOKIE['favoriteListings'], ';')) {
  setcookie ( "favoriteListings", "", time() - 3600, "/");
 }
}

// ----------------------------------------------------------------------------
// favoriteListingsCheck($id)
//
// checks if the listing is in the Favorite Listings cookie
//
// $id - listing ID
//

function favoriteListingsCheck($id) {

 $favorites_list = array();

 if (isset($_COOKIE['favoriteListings']) && strstr($_COOKIE['favoriteListings'], ';')) {
  $favorites_list = explode (";", $_COOKIE['favoriteListings']);
  if(in_array ($id, $favorites_list))
   return TRUE;
  else
   return FALSE;
 }
 elseif (isset( $_COOKIE['favoriteListings'] ) && !strstr($_COOKIE['favoriteListings'], ';')) {
  if ($_COOKIE['favoriteListings'] == $id)
   return TRUE;
  else
   return FALSE;
 }
 else  
  return FALSE;
}

// ----------------------------------------------------------------------------
// favoriteListingsClean()
//
// cleans the Favorite Listings cookie of the removed listings
//

function favoriteListingsClean() {

 global $db;

 $favorites_list = array();

 if (isset($_COOKIE['favoriteListings']) && strstr($_COOKIE['favoriteListings'], ';')) {
  $favorites_list = explode (";", $_COOKIE['favoriteListings']);

  foreach ($favorites_list as $key => $value) {
   $sql = 'SELECT id FROM ' . PROPERTIES_TABLE  . ' WHERE approved = 1 AND id = "' . intval($value) . '" LIMIT 1';
   $r = $db->query ( $sql );
   if ($db->numrows($r) == 0)
    favoriteListingsRemove($value);
  }
 }
 elseif (isset($_COOKIE['favoriteListings']) && !strstr($_COOKIE['favoriteListings'], ';')) {
  $sql = 'SELECT id FROM ' . PROPERTIES_TABLE  . ' WHERE approved = 1 AND id = "' . intval($_COOKIE['favoriteListings']) . '" LIMIT 1';
  $r = $db->query ( $sql );
  if ($db->numrows($r) == 0)
   setcookie ( "favoriteListings", "", time() - 3600, "/");
 }
 
}


// ----------------------------------------------------------------------------
// visitedListingsAdd($id)
//
// adds the listing into the Recently Visited Listings cookie
//
// $id - listing ID
//

function visitedListingsAdd($id) {

 global $conf;

 // Check if the cookie exists and fetch the data
 if (isset($_COOKIE['visitedListings']) && strstr($_COOKIE['visitedListings'], ';'))
  $list = explode (";", $_COOKIE['visitedListings']);
 elseif (isset($_COOKIE['visitedListings']) && !strstr($_COOKIE['visitedListings'], ';'))
  $list = $_COOKIE['visitedListings'];
 else
  $list = '';

 if (is_array($list)) {
  // If the id is not in the array we add it into the beginning
  if(!in_array($id, $list)) 
   array_unshift ($list, $id);

  // If the array has more than $conf['most_visited_limit']
  //  listings now we cut the oldest one off the list
  if (count($list) > $conf['most_visited_limit']) 
   array_pop($list);

  // Create a new array
  $cookie = implode (";", $list);
 } 
 elseif (!is_array($list) && $list != '') {
  $cookie = $list . ';' . $id;
 }
 else
  $cookie = $id;

 // Generate/reset cookie
 setcookie ( "visitedListings", "", time() - 3600, "/");
 setcookie ( "visitedListings", $cookie, time()+2592000, "/");

}

// ----------------------------------------------------------------------------
// visitedListingsRemove($id)
//
// removes the listing from the Visited Listings cookie
//
// $id - listing ID
//

function visitedListingsRemove($id) {

 $visited_list = array();

 if (isset( $_COOKIE['visitedListings'] ) && strstr($_COOKIE['visitedListings'], ';')) {

  $visited_list = explode (";", $_COOKIE['visitedListings']);
  $key = array_search ($id, $visited_list);
  unset ($visited_list[$key]);

  $cookie = implode (";", $visited_list);

  setcookie ( "visitedListings", "", time() - 3600, "/");
  setcookie ( "visitedListings", $cookie, time()+2592000, "/");
  $_COOKIE['visitedListings'] = $cookie;

 }
 elseif (isset( $_COOKIE['visitedListings'] ) && !strstr($_COOKIE['visitedListings'], ';')) {
  setcookie ( "visitedListings", "", time() - 3600, "/");
 }
}

// ----------------------------------------------------------------------------
// visitedListingsClean()
//
// cleans the Visited Listings cookie of the removed listings
//

function visitedListingsClean() {

 global $db;

 $visited_list = array();

 if (isset($_COOKIE['visitedListings']) && strstr($_COOKIE['visitedListings'], ';')) {
  $visited_list = explode (";", $_COOKIE['visitedListings']);

  foreach ($visited_list as $key => $value) {
   $sql = 'SELECT id FROM ' . PROPERTIES_TABLE  . ' WHERE approved = 1 AND id = "' . intval($value) . '" LIMIT 1';
   $r = $db->query ( $sql );
   if ($db->numrows($r) == 0)
    visitedListingsRemove($value);
  }
 }
 elseif (isset($_COOKIE['visitedListings']) && !strstr($_COOKIE['visitedListings'], ';')) {
  $sql = 'SELECT id FROM ' . PROPERTIES_TABLE  . ' WHERE approved = 1 AND id = "' . intval($_COOKIE['visitedListings']) . '" LIMIT 1';
  $r = $db->query ( $sql );
  if ($db->numrows($r) == 0)
   setcookie ( "visitedListings", "", time() - 3600, "/");
 }
}

?>