<?php

// Getting the latest visitors counter and timestamp from the cron table
$sql = 'SELECT * FROM ' . CRON_TABLE . ' LIMIT 1';
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
if ( $db->numrows( $q ) > 0 )
{
	$f = $db->fetcharray( $q );
	$last_run = date( 'Y-m-d', $f['time'] );
}
else
{
	// First time the cron job script has been executed
	$last_run = 0;
	
	// Update the count for the future
	$sql = "
	INSERT INTO " . CRON_TABLE . "
	(
		counter,
		time	
	)
	VALUES
	(
		1,
		'" . time() . "'
	)
	";
	$q = $db->query( $sql );
}

// The cron should only run once per day
// If last_run is 0, it has never run before
// If today's date is greater than $last_run, it's a new day
if ( date( 'Y-m-d' ) > $last_run || $last_run == 0 )
{
	// Update timestamp in cron table
	$sql = "
	UPDATE " . CRON_TABLE . "
	SET
		counter = counter + 1,
		time = '" . time() . "'
	";
	$q = $db->query( $sql );

	// LISTINGS

    // Expire or delete the free listings
    if ( $conf['free_listings_expire'] > 0 && is_numeric( $conf['free_listings_expire'] ) )
    {
        $sql = "
        SELECT listing_id, type 
        FROM " . PROPERTIES_TABLE . "
        WHERE 
        	approved = 1
        	AND DATE_ADD( date_approved, INTERVAL " . $conf['free_listings_expire'] . " DAY ) < NOW()
        ";
        $q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $q ) > 0 )
		{
	        while ( $f = $db->fetcharray( $q ) )
	        {
	            // If set to delete
	            if ( $conf['expired_listings'] == '2' )
	            {
	                removeuserlisting( $f['listing_id'] );
	            }
	            else
	            {
	                // Set to 'expired' which is approved = 2 
	                $sql = "
	                UPDATE " . PROPERTIES_TABLE . "
	                SET 
	                	date_approved = NULL,
	                	approved = '2'
	                WHERE 
	                	id = '" . $f['listing_id'] . "'
	                LIMIT 1
	                ";
	                $q2 = $db->query( $sql );
	            }
	        }
        }
     }

	// Featured listings
	$sql = "SELECT * FROM " . FEATURED_TABLE;
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $q ) > 0 )
	{
		while ( $f = $db->fetcharray( $q ) )
		{
			// Should this listing be made featured?
			if ( $f['start_date'] <= date( 'Y-m-d' ) && $f['featured'] != 1 )
			{
				// Updating the featured table 
				$sql = 'UPDATE ' . FEATURED_TABLE . ' SET featured = 1 WHERE id = ' . $f['listing_id'] . ' LIMIT 1';
				$db->query( $sql ) or error( 'Critical Error', mysql_error() );
				
				// Updating the users table 
				$sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET featured = "A" WHERE listing_id = ' . $f['listing_id'] . ' LIMIT 1';
				$db->query( $sql ) or error( 'Critical Error', mysql_error() );		
			}
	
			// Listings that should no longer be featured (e.g., expired)
			if ( $f['end_date'] <= date( 'Y-m-d' ) && $f['featured'] == 1 )
			{
				// Updating the featured table 
				$sql = 'DELETE FROM ' . FEATURED_TABLE . ' WHERE id = ' . $f['listing_id'] . ' LIMIT 1';
				$db->query( $sql ) or error( 'Critical Error', mysql_error() );
				
				// Updating the users table 
				$sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET featured = "B" WHERE listing_id = ' . $f['listing_id'] . ' LIMIT 1';
				$db->query( $sql ) or error( 'Critical Error', mysql_error() );
			}				
		}
	}

	// SELLERS
	
	// Update seller packages
	$sql = "SELECT * FROM " . FEATURED_AGENTS_TABLE;
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $q ) > 0 )
	{
		while ( $f = $db->fetcharray( $q ) )
		{
			// Does a seller's featured package need to end?
			if ( $f['end_date'] <= date( 'Y-m-d' ) && $f['featured'] == 1 )
			{
				// Updating the featured table 
				$sql = 'DELETE FROM ' . FEATURED_AGENTS_TABLE . ' WHERE id = ' . $f['id'] . ' LIMIT 1';
				$db->query( $sql ) or error( 'Critical Error', mysql_error () );
				
				// Updating the users table 
				$sql = 'UPDATE ' . USERS_TABLE . ' SET package = "" WHERE u_id = ' . $f['id'] . ' LIMIT 1';
				$db->query( $sql ) or error( 'Critical Error', mysql_error () );
				
				// Is the seller somehow over their free listing limit?
				// If so, delete or expire listings based on preference starting with most recent
				// This will not affect paid sellers since $f['end_date'] will not fail
				$sql = "
				SELECT * 
				FROM " . PROPERTIES_TABLE . "
				WHERE 
					userid = '" . $f['id'] . "'
				ORDER BY id DESC
				";
				$rl = $db->query( $sql );
				$num_listings = $db->numrows( $rl );
				
				if ( $num_listings > $conf['free_listings'] )
				{
					$over_limit = $num_listings - $conf['free_listings'];
					$count = 1;
					
					while ( $fl = $db->fetcharray( $rl ) )
					{
						// Loop through every listing until we've gone through the total necessary to delete/expire
						if ( $count <= $over_limit )
						{
							// If set to delete
							if ( $conf['expired_listings'] == '2' )
							{
								removeuserlisting( $fl['listing_id'] );
							}
							else
							{
								// Set to 'expired' which is approved = 2 
								$sql = "
								UPDATE " . PROPERTIES_TABLE . " 
								SET 
									date_approved = NULL,
									approved = '2'
								WHERE 
									listing_id = '" . $fl['listing_id'] . "' 
								LIMIT 1
								";
								$ul = $db->query( $sql );
							}
						}
						$count++;
					}
				}
			}
		}
	}
	
	// Expiration alerts for listings
	$sql = "
	SELECT * 
	FROM " . FEATURED_TABLE . "
	WHERE 
		featured = 1 
		AND end_date BETWEEN '" . date( 'Y-m-d' ) . "' AND '" . date( 'Y-m-d', strtotime( 'now +' . $conf['expire_notice'] . ' days' ) ) . "'
	";
	$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $r ) > 0 )
	{
		while ( $f = $db->fetcharray( $r ) )
		{
			$sql = 'SELECT * FROM ' . PROPERTIES_TABLE . ' WHERE listing_id = ' . $f['id'];
			$re = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
			$fe = $db->fetcharray($re);
			
			$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE u_id = ' . $fe['userid'];
			$ru = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
			$fu = $db->fetcharray($ru);
			
			$mailout_listing_expiration = $lang['Listing_Expiration_Email'];
			
			$mailout_listing_expiration = str_replace( '{name}', $fu['first_name'] . ' ' . $fu['last_name'] , $mailout_listing_expiration );
			$mailout_listing_expiration = str_replace( '{listing}', $fe['title'], $mailout_listing_expiration );
			$mailout_listing_expiration = str_replace( '{date}', printdate( $f['end_date'] ), $mailout_listing_expiration );
			$mailout_listing_expiration = str_replace( '{website}', $conf['website_name'], $mailout_listing_expiration );
			
			send_mailing( 
				$conf['general_e_mail'], 
				$conf['general_e_mail_name'], 
				$fu['email'], 
				$lang['Listing_Expiration_Subject'], 
				$mailout_listing_expiration 
			);
		}
	}

	// Expiration alerts for seller accounts
	$sql = "
	SELECT * 
	FROM " . FEATURED_AGENTS_TABLE . "
	WHERE 
		featured = 1 
		AND end_date BETWEEN '" . date( 'Y-m-d' ) . "' AND '" . date( 'Y-m-d', strtotime( 'now +' . $conf['expire_notice'] . ' days' ) ) . "'
	";
	$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $r ) > 0 )
	{
		while ( $f = $db->fetcharray( $r ) )
		{	
			$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE u_id = ' . $f['id'];
			$ru = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
			$fu = $db->fetcharray( $ru );
			
			$mailout_agent_expiration = $lang['Agent_Expiration_Email'];
			
			$mailout_agent_expiration = str_replace( '{name}', $fu['first_name'] . ' ' . $fu['last_name'] , $mailout_agent_expiration );
			$mailout_agent_expiration = str_replace( '{date}', printdate( $f['end_date'] ), $mailout_agent_expiration );
			$mailout_agent_expiration = str_replace( '{website}', $conf['website_name'], $mailout_agent_expiration );
			
			send_mailing( 
				$conf['general_e_mail'], 
				$conf['general_e_mail_name'], 
				$fu['email'], 
				$lang['Agent_Expiration_Subject'], 
				$mailout_agent_expiration 
			);	
		}
	}
}

?>