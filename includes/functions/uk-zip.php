<?php

// UK zip codes 

// ZIP CODE RADIUS SEARCH ROUTINES
function get_zips_in_range ( $zip, $range )
 {

  global $db;
       
  // returns an array of the zip codes within $range of $zip. Returns
  // an array with keys as zip codes and values as the distance from 
  // the zipcode defined in $zip.

  $in = explode (" ", $zip);

  $zip = $in[0];
      
  $details = get_zip_point($zip);  // base zip details

  if (empty($details)) return;
      
  // This portion of the routine  calculates the minimum and maximum lat and
  // long within a given range.  This portion of the code was written
  // by Jeff Bearer (http://www.jeffbearer.com). This significanly decreases
  // the time it takes to execute a query.  My demo took 3.2 seconds in 
  // v1.0.0 and now executes in 0.4 seconds!  Greate job Jeff!
      
  // Find Max - Min Lat / Long for Radius and zero point and query
  // only zips in that range.
  $lat_range = $range/69.172;
  $lon_range = abs($range/(cos($details[0]) * 69.172));
  $min_lat = number_format($details[0] - $lat_range, "4", ".", "");
  $max_lat = number_format($details[0] + $lat_range, "4", ".", "");
  $min_lon = number_format($details[1] - $lon_range, "4", ".", "");
  $max_lon = number_format($details[1] + $lon_range, "4", ".", "");

  $return = array();    // declared here for scope
 
  $sql = 'SELECT zip, latitude, longitude FROM ' . ZIP_TABLE . '
          WHERE latitude BETWEEN ' . $min_lat . ' AND 
          ' . $max_lat . ' AND longitude BETWEEN ' . $min_lon .' AND ' . $max_lon;
             
  $r = $db->query($sql);
      
  if (!$r) {    // sql error
      
    $last_error = mysql_error();
    return;
         
   } else {
          
    while ($row = mysql_fetch_row($r)) {
   
     // loop through all 40 some thousand zip codes and determine whether
     // or not it's within the specified range.
            
     $dist = calculate_mileage($details[0],$row[1],$details[1],$row[2]);
     $dist = $dist * 1.609344;

     if ($dist <= $range) {
       $return[$row[0]] = round($dist, 2);
      }
     }
    mysql_free_result($r);
   }
      
  return $return;

 }

function get_zip_point ( $zip )
 {
   global $db;
   
   // This function pulls just the lattitude and longitude from the
   // database for a given zip code.
      
   $sql = 'SELECT latitude, longitude from ' . ZIP_TABLE . ' WHERE zip = "' . $zip . '"';
   $r = $db->query($sql);
   if (!$r) {
     $last_error = mysql_error();
     return;
    } else {
     $row = $db->fetcharray($r);
     mysql_free_result($r);
     return $row;       
    }      
 }

function calculate_mileage ($lat1, $lat2, $lon1, $lon2 )
 {
 
  // used internally, this function actually performs that calculation to
  // determine the mileage between 2 points defined by lattitude and
  // longitude coordinates.  This calculation is based on the code found
  // at http://www.cryptnet.net/fsp/zipdy/

  $lat1 = str_replace(",", ".", $lat1);
  $lat2 = str_replace(",", ".", $lat2);
  $lon1 = str_replace(",", ".", $lon1);
  $lon2 = str_replace(",", ".", $lon2);

  // Convert lattitude/longitude (degrees) to radians for calculations
  $lat1 = deg2rad($lat1);
  $lon1 = deg2rad($lon1);
  $lat2 = deg2rad($lat2);
  $lon2 = deg2rad($lon2);

  // Find the deltas
  $delta_lat = $lat2 - $lat1;
  $delta_lon = $lon2 - $lon1;

  // Find the Great Circle distance 
  $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
  $distance = 3956 * 2 * atan2(sqrt($temp),sqrt(1-$temp));

  return $distance;

 }

?>