<?php

define( 'PMR', 'true' );

include '../config.php';
include PATH . '/defaults.php';

function dropdown()
{
	global $db, $conf, $lang;
	
	if ( $_REQUEST['location1'] != '' )
	{
		$sql = "
		SELECT location_id, location_name
		FROM " . LOCATIONS_TABLE . "
		WHERE location_parent = '" . $db->makeSafe( $_REQUEST['location1'] ) . "'
		ORDER BY location_name ASC
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			echo '<option value="">' . $lang['Select'] . '</option>';
			while( $f = $db->fetcharray( $q ) )
			{
				echo '<option value="' . $f['location_id'] . '">' . $f['location_name'] . '</option>';
			}
		}
		else
		{
			echo 'empty';
		}
	}
	elseif ( $_REQUEST['location2'] != '' )
	{
		$sql = "
		SELECT location_id, location_name
		FROM " . LOCATIONS_TABLE . "
		WHERE location_parent = '" . $db->makeSafe( $_REQUEST['location2'] ) . "'
		ORDER BY location_name ASC
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			echo '<option value="">' . $lang['Select'] . '</option>';
			while( $f = $db->fetcharray( $q ) )
			{
				echo '<option value="' . $f['location_id'] . '">' . $f['location_name'] . '</option>';
			}
		}
		else
		{
			echo 'empty';
		}	
	}
	else
	{
		echo 'empty';
	}
}

function bulk_upload()
{
	global $db;

	// Default settings
	$output_dir = MEDIA_PATH . '/gallery';
	
	if ( isset( $_FILES["myfile"] ) )
	{
		$ret = array();
	
		$error = $_FILES["myfile"]["error"];

		// Check package limit for this customer
		if ( $_SESSION['admin'] == false )
		{
			$package = package_check( $_SESSION['user_id'], 'seller' );
		
			// Total gallery images that currently exist for this listing ID or temp ID
			if ( $_SESSION['listing_id'] != '' )
			{
				$total_images = num_gallery_images_check( $_SESSION['user_id'], $_SESSION['listing_id'] );
			}
			else
			{
				$total_images = num_gallery_images_check( $_SESSION['user_id'], $_SESSION['image_session'], true );
			}
		
			$admin_bypass = false;
		}
		else
		{
			$admin_bypass = true;
		}
		
		if ( $total_images < $package['gallery'] || $admin_bypass == true )
		{
			$file_data = explode( '.', $_FILES['myfile']['name'] );
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
				
				$path = $output_dir . '/' . $image_name;
	
				if ( move_uploaded_file( $_FILES['myfile']['tmp_name'], $path ) )
				{
					$ret[$image_name] = $output_dir . $image_name;
					
					// If the listing_id is known (e.g., we're editing a listing)
					if ( $_SESSION['listing_id'] != '' )
					{
				  		$sql = "
				  		INSERT INTO " . GALLERY_TABLE . "
				  		(
				  			userid,
				  			listingid,
				  			image_name,
				  			date_added,
				  			ip_added
				  		)
				  		VALUES
				  		(
				  			'" . $db->makeSafe( $_SESSION['user_id'] ) . "',
				  			'" . $db->makeSafe( $_SESSION['listing_id'] ) . "',
				  			'" . $db->makeSafe( $image_name ) . "',
				  			'" . date( 'Y-m-d' ) . "',
				  			'" . $db->makeSafe( $_SERVER['REMOTE_ADDR'] ) . "'
				  		)
				  		";
				  		$q = $db->query( $sql );
					}
					// We're using a temporary listing ID (e.g., adding a new listing)
					else
					{
				  		$sql = "
				  		INSERT INTO " . GALLERY_TABLE . "
				  		(
				  			userid,
				  			temp_id,
				  			image_name,
				  			date_added,
				  			ip_added
				  		)
				  		VALUES
				  		(
				  			'" . $db->makeSafe( $_SESSION['user_id'] ) . "',
				  			'" . $db->makeSafe( $_SESSION['image_session'] ) . "',
				  			'" . $db->makeSafe( $image_name ) . "',
				  			'" . date( 'Y-m-d' ) . "',
				  			'" . $db->makeSafe( $_SERVER['REMOTE_ADDR'] ) . "'
				  		)
				  		";
				  		$q = $db->query( $sql );
			  		}
				}
				else
				{
					// Couldn't upload the image
					// Dont' do anything
				}
			}
		}
	}
	echo json_encode( $ret );
}

switch( $_REQUEST['action'] )
{
	case 'dropdown':
		dropdown();
	break;

	case 'bulk_upload':
		bulk_upload();
	break;
}

?>