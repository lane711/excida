<?php

function upload_image( $dir, $user_id, $uploaded_image )
{
	global $db;
	
	$return = false;

	// If image exist we start
	if ( $uploaded_image['tmp_name'] != '' )
	{	
		$file_data = explode( '.', $uploaded_image['name'] );
		$file_ext = strtolower( end( $file_data ) );
		
		// Allowed file types
		$valid_ext = array(
			'jpeg', 'jpg', 'png', 'gif'
		);
		
		if ( in_array( $file_ext, $valid_ext ) === false )
		{
			$return = false;		
		}
		else
		{
			$rand_name = md5( rand( 11111, 99999 ) );
			$image_name = $rand_name . '.' . $file_ext;
			
			$path = MEDIA_PATH . '/' . $dir . '/' . $image_name;

			if ( move_uploaded_file( $uploaded_image['tmp_name'], $path ) )
			{
				// Update DB with this new name
				$sql = "
				UPDATE " . USERS_TABLE . "
				SET	image = '" . $image_name . "'
				WHERE u_id = '" . $db->makeSafe( $user_id ) . "'
				";
				$q = $db->query( $sql );
			
				$return = true;
			}
			else
			{
				$return = false;
			}
		}
	}

	return $return;
}

function show_image( $dir, $image_name, $width = '', $height = '', $type = 1 )
{
	global $conf;
	
	// Return the URL to the image
	$output = URL . '/includes/functions/resize.php?src=' . MEDIA_URL . '/' . $dir . '/' . $image_name . '&w=' . $width . '&h=' . $height . '&zc=' . $type;
	
	return $output;
}

function get_images( $dir, $listing_id, $width = '', $height = '', $type = 1, $limit = '' )
{
	global $conf, $db;
	
	if ( $limit != '' )
	{
		$limit = "LIMIT " . $limit;
	}
	
	$images = array();
	
	if ( $dir == 'gallery' )
	{
		$sql = "
		SELECT 
			id, image_name
		FROM " . GALLERY_TABLE . "
		WHERE
			listingid = '" . $db->makeSafe( $listing_id ) . "'
		ORDER BY id DESC
		" . $limit . "
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			while ( $f = $db->fetcharray( $q ) )
			{
				$images[] = show_image( $dir, $f['image_name'], $width, $height, $type );
			}
		}
		else
		{
			$images[] = show_image( $dir, 'error.png', $width, $height, $type );
		}
	}
	elseif ( $dir == 'photos' )
	{
		$sql = "
		SELECT 
			image
		FROM " . USERS_TABLE . "
		WHERE
			u_id = '" . $db->makeSafe( $listing_id ) . "'
			AND image != ''
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			$f = $db->fetcharray( $q );
			
			$images[] = show_image( $dir, $f['image'], $width, $height, $type );
		}
		else
		{
			$images[] = show_image( $dir, 'error.png', $width, $height, $type );
		}
	}
	elseif ( $dir == 'hidden' )
	{
		$dir = 'gallery';
		$images[] = show_image( $dir, 'error.png', $width, $height, $type );
	}
	
	return $images;
}

function remove_image( $type, $image_name )
{
	global $db;

	// Delete the image
	if ( file_exists ( MEDIA_PATH . '/' . $type . '/' . $image_name ) )
	{
		@unlink( MEDIA_PATH . '/' . $type . '/' . $image_name );
	}
}

?>