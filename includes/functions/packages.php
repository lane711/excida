<?php

function package_check( $user_id, $type = 'seller' )
{
	global $db, $conf;
	
	$package = array();

	// Look up this user's package, if any
	if ( $type == 'seller' )
	{
		$sql = "
		SELECT " . PACKAGES_AGENT_TABLE . ".*
		FROM " . USERS_TABLE . "
		LEFT JOIN " . PACKAGES_AGENT_TABLE . " ON " . PACKAGES_AGENT_TABLE . ".id = " . USERS_TABLE . ".package
		WHERE " . USERS_TABLE . ".u_id = '" . $db->makeSafe( $user_id ) . "'
		";
		$q = $db->query( $sql );
		$f = $db->fetcharray( $q );			
		if ( $f['id'] != NULL && $f['id'] != 0 )
		{
			// Set their paid package
			$package = $f;
		}
		else
		{
			// Just use defaults for the free account
			$package['listings'] = $conf['free_listings'];
			$package['gallery'] = $conf['free_gallery'];
			$package['mainimage'] = $conf['free_mainimage'];
			$package['photo'] = $conf['free_photo'];
			$package['phone'] = $conf['free_phone'];
			$package['address'] = $conf['free_address'];
		}
	}
	elseif ( $type == 'listing' )
	{
		// Nothing yet.
	}

	return $package;
}

// --------------------------------------------------------------------------
// GENERATE_PACKAGES_LIST()
// This function generates <option> tags for all the items
// taken from the packages database and makes one SELECTED if there were
// any errors and we are returned back to the form

function generate_packages_list ( $selected = '' )
 {

  global $db, $conf;

  $sql = 'SELECT * FROM ' . PACKAGES_TABLE . ' ORDER BY name';
  $r = $db->query ($sql) or error ('Critical Error', mysql_error () );

  $output = '';

  while ($f = $db->fetcharray ($r) )

   {
    if ($f['id'] == $selected)
     $output.= '<option value="'. $f['id'] . '" SELECTED>' . $f['name'] . ' (' . $conf['paypal_currency'] . $f['price'] . ' / ' . $f['days'] . ' days.)</option>';
    else
     $output.= '<option value="'. $f['id'] . '">' . $f['name'] . ' (' . $conf['paypal_currency'] . $f['price'] . ' / ' . $f['days'] . ' days.)</option>';
   }

  return $output;

 }


// --------------------------------------------------------------------------
// GENERATE_AGENTS_PACKAGES_LIST()
// This function generates <option> tags for all the items
// taken from the packages database and makes one SELECTED if there were
// any errors and we are returned back to the form

function generate_agents_packages_list ( $selected = '' )

 {

  global $db, $conf;

  $sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE . ' ORDER BY name';
  $r = $db->query ($sql) or error ('Critical Error', mysql_error () );

  $output = '';

  while ($f = $db->fetcharray ($r) )
   {

    if ($f['id'] == $selected)
     $output.= '<option value="'. $f['id'] . '" SELECTED>' . $f['name'] . ' (' . $conf['paypal_currency'] . $f['price'] . ' / ' . $f['days'] . ' days.)</option>';
    else
     $output.= '<option value="'. $f['id'] . '">' . $f['name'] . ' (' . $conf['paypal_currency'] . $f['price'] . ' / ' . $f['days'] . ' days.)</option>';
   }

  return $output;

 }


// --------------------------------------------------------------------------
// UPDATE_PACKAGE

function update_package ( $id, $package )

 {

  global $db;

  if ($package != 0)
   {

    $sql = 'SELECT * FROM ' . PACKAGES_TABLE . ' WHERE id = ' . $package . ' LIMIT 1';
    $r = $db->query ($sql) or error ('Critical Error', mysql_error () );
    $f = $db->fetcharray($r);

    $days = $f['days'];
    $start_date = date('Y-m-d');

    // Calculate the end date
    // Current timestamp + days converted into the timestamp => date
    $end_date = date ( 'Y-m-d', (time() + ($days * 24 * 60 * 60)) );
 
    // Check if this listing had featured dates set before 
    $sql = 'SELECT * FROM ' . FEATURED_TABLE . ' WHERE id = ' . $id . '';
    $r_featured = $db->query($sql) or error ('Critical Error', mysql_error ());
              
    if ($db->numrows($r_featured) == 0)
     {
      // Adding the data into the featured table 
      $sql = 'INSERT INTO ' . FEATURED_TABLE . ' (id, start_date, end_date, package) VALUES (' . $id . ', "' . $start_date . '", "' . $end_date . '", "' . $package . '") ';
      $db->query($sql) or error ('Critical Error', mysql_error ());
     }
    else
     {
      // Updating the data in the featured table 
      $sql = 'UPDATE ' . FEATURED_TABLE . ' SET start_date = "' . $start_date . '", end_date = "' . $end_date . '", package = "' . $package . '" WHERE id = ' . $id . ' ';
      $db->query($sql) or error ('Critical Error', mysql_error ());
 
      // Updating the properties table 
      $sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET featured = "B", approved = "1" WHERE listing_id = ' . $id . ' LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
     }
        
    // If listing needs to be  featured since today
    // we convert it
    if ($start_date <= date('Y-m-d') && $end_date > date('Y-m-d'))
     {
      // Updating the featured table 
      $sql = 'UPDATE ' . FEATURED_TABLE . ' SET featured = 1 WHERE id = ' . $id . ' LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
   
      // Updating the properties table 
      $sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET featured = "A", approved = "1"  WHERE listing_id = ' . $id . ' LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
     }
   }

  else
   {

    // Check if this listing had featured dates set before 
    $sql = 'SELECT * FROM ' . FEATURED_TABLE . ' WHERE id = ' . $id . '';
    $r_featured = $db->query($sql) or error ('Critical Error', mysql_error ());
              
    if ($db->numrows($r_featured) > 0)
     {
      // Updating the data in the featured table 
      $sql = 'DELETE FROM ' . FEATURED_TABLE . ' WHERE id = ' . $id . ' LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
 
      // Updating the users table 
      $sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET featured = "B", approved = "1" WHERE listing_id = ' . $id . ' LIMIT 1';

      $db->query($sql) or error ('Critical Error', mysql_error ());
     }

   }
 }


// --------------------------------------------------------------------------
// UPDATE_AGENTS_PACKAGE

function update_agents_package ( $id, $package )

 {

  global $db;
  
  	// Approve all listings, the system will automatically remove them later if they aren't valid
  	$sql = "UPDATE " . PROPERTIES_TABLE . " SET approved = '1' WHERE userid = '" . $id . "' LIMIT 1";
  	$r = $db->query ( $sql ) or error ('Critical Error', mysql_error () );

    // Grab the specifics of the package they purchased
    $sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE . ' WHERE id = "' . $package . '" LIMIT 1';
    $r = $db->query ($sql) or error ('Critical Error', mysql_error () );
    $f = $db->fetcharray($r);

    $days = $f['days'];
    $start_date = date('Y-m-d');

    // Calculate the end date
    // Current timestamp + days converted into the timestamp => date
    $end_date = date ( 'Y-m-d', (time() + ($days * 24 * 60 * 60)) );
 
    // Check if this user had featured dates set before 
    $sql = 'SELECT * FROM ' . FEATURED_AGENTS_TABLE . ' WHERE id = ' . $id . '';
    $r_featured = $db->query($sql) or error ('Critical Error', mysql_error ());
              
    if ($db->numrows($r_featured) == 0)
     {
      // Adding the data into the featured table 
      $sql = 'INSERT INTO ' . FEATURED_AGENTS_TABLE . ' (id, start_date, end_date, package) VALUES (' . $id . ', "' . $start_date . '", "' . $end_date . '", "' . $package . '") ';
      $db->query($sql) or error ('Critical Error', mysql_error ());
     }
    else
     {
      // Updating the data in the featured table 
      $sql = 'UPDATE ' . FEATURED_AGENTS_TABLE . ' SET start_date = "' . $start_date . '", end_date = "' . $end_date . '", package = "' . $package . '" WHERE id = "' . $id . '" ';
      $db->query($sql) or error ('Critical Error', mysql_error ());
 
      // Updating the users table 
      $sql = 'UPDATE ' . USERS_TABLE . ' SET package = "' . $package . '" AND approved = "1" WHERE u_id = "' . $id . '" LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
     }
        
    // If listing needs to be  featured since today
    // we convert it
    if ($start_date <= date('Y-m-d') && $end_date > date('Y-m-d'))
     {
      // Updating the featured table 
      $sql = 'UPDATE ' . FEATURED_AGENTS_TABLE . ' SET featured = 1 WHERE id = "' . $id . '" LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
   
      // Updating the users table 
      $sql = 'UPDATE ' . USERS_TABLE . ' SET package = "' . $package . '", approved = "1" WHERE u_id = "' . $id . '" LIMIT 1';
      $db->query($sql) or error ('Critical Error', mysql_error ());
     }

  // Check the number of current listings and photo gallery images for each
  // listing, if there are more listings or photos we just remove the most recent until they are in good standing
  // If there is less listings than we just assigned we leave them.

  $sql = 'SELECT * FROM ' . PROPERTIES_TABLE . ' WHERE userid = ' . $id . ' ORDER BY listing_id DESC';
  $rl = $db->query($sql);
  $listings_number = $db->numrows($rl);

  // If there are more listings than we allow we remove all of them for
  // the user to resubmit all the listings (the problem is that we do not
  // know what listings to remove or add.
 
	if ($listings_number > $f['listings'])
	{
		$count = 1;
		while ($fl = $db->fetcharray($rl))
   		{
   			// Only remove most recent offenders
   			if ($count <= $f['listings'])
   			{
		      	// If set to delete
		      	if ($conf['expired_listings'] == '2')
		      		removeuserlisting($fl['listing_id']);
		      	else
		      	{
		      		// Set to 'expired' which is approved = 2 
		      		$sql = "UPDATE " . PROPERTIES_TABLE . " SET approved = '2' WHERE listing_id = '" . $fl['listing_id'] . "' LIMIT 1";
		      		$ul = $db->query($sql);
		      	}
	      	}
	      	$count++;
   		}
	}

  // Check the number of gallery images for each listing, if there is more than
  // we allow remove the most recent offenders only
  $sql = 'SELECT * FROM ' . PROPERTIES_TABLE . ' WHERE userid = ' . $id . ' ORDER BY listing_id DESC';
  $rl = $db->query($sql);
  
  	$count = 1;
    while ($fl = $db->fetcharray($rl))
     {
      $sql = 'SELECT * FROM ' . GALLERY_TABLE . ' WHERE listingid = ' . $fl['listing_id'];
      $rg = $db->query($sql);
      $gallery_number = $db->numrows($rg);
     
       if ($gallery_number > $f['gallery'])
        {
         while ($fg = $db->fetcharray($rg))
         {
         
         	// Only remove most recent offenders
         	if ($count <= $f['listings'])
         	{
	         	// If set to delete
	         	if ($conf['expired_listings'] == '2')
	         	{
	          		removelistingimage('gallery', $fg['id']);
	
	        		$sql = 'DELETE FROM ' . GALLERY_TABLE . ' WHERE listingid = ' . $fl['listing_id'];
	         		$db->query($sql);
	        	}
			}
			        	
        	$count++;
         }
       }
     }
   }

?>