<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// EDIT TYPES & LOCATIONS

// Title tag
$title = '';

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged in we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
 	$whereClause = "";
 	$defaultClause = " site_id=0";
  if($session->fetch('role')=="SUPERUSER")
  $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_types', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  $fields = array();

  // Set the table to edit and the name of that table
  if (isset($_GET['req']))
   {

    // Edit property types
    if ($_GET['req'] == 'types')

     {
	
      $table = TYPES_TABLE;
      $name = $lang['Admin_Property_Types'];
      $req = 'types';
	 
     }
    elseif ($_GET['req'] == 'types2')

     {
	
      $table = TYPES2_TABLE;
      $name = $lang['Module_Listing_Type'];
      $req = 'types2';
      $fields[] = array(
        'label'   => $lang['Listing_Price_Type'],
        'column'  => 'class',
        'type'    => 'select',
        'classes' => array('required'),
        //'default' => 'monthly', 
        'values'  => array(
            'sale'    => $lang['Listing_Price_Type_sale'],
            'monthly' => $lang['Listing_Price_Type_monthly'],
        ),
      );
     }
    // Edit Styles
    elseif ($_GET['req'] == 'styles')
			
     {
	
      $table = STYLES_TABLE;
      $name = $lang['Admin_Styles'];
      $req = 'styles';
	
     }
    // Edit Locations (States)
    elseif ($_GET['req'] == 'locations')
			
     {
	
      $table = LOCATIONS_TABLE;
      $name = $lang['Admin_Locations'];
      $req = 'locations';
	
     }
    // Edit Additional Out Buildings
    elseif ($_GET['req'] == 'buildings')
	
     {      	
	
      $table = BUILDINGS_TABLE;
      $name = $lang['Admin_Additional_Out_Buildings'];
      $req = 'buildings';
			
     }
    // Edit Appliances Included
    elseif ($_GET['req'] == 'appliances')
	
     {
	
      $table = APPLIANCES_TABLE;
      $name = $lang['Admin_Appliances_Included'];
      $req = 'appliances';
	
     }
    // Edit Features
    elseif ($_GET['req'] == 'features')
	
     {
	
      $table = FEATURES_TABLE;
      $name = $lang['Admin_Features'];
      $req = 'features';
	
     }
    // Edit Basement Types
    elseif ($_GET['req'] == 'basement')
	
     {
	
      $table = BASEMENT_TABLE;
      $name = $lang['Admin_Basement'];
      $req = 'basement';
	
     }
    // Edit Garage Types
    elseif ($_GET['req'] == 'garage')
	
     {
	
      $table = GARAGE_TABLE;
      $name = $lang['Admin_Garage'];
      $req = 'garage';

     }
    // Edit Garage Types
    elseif ($_GET['req'] == 'status')
	
     {
	
      $table = STATUS_TABLE;
      $name = $lang['Admin_Status'];
      $req = 'status';

     }
    // If no parameters passed to the script we edit Property Types
    else
	
     {
	
      $table = TYPES_TABLE;
      $name = $lang['Admin_Property_Types'];
      $req = 'types';
  
     }

   } 

  // If no parameters passed to the script we edit Property Types
  else
	
   {
	
    $table = TYPES_TABLE;
    $name = $lang['Admin_Property_Types'];
    $req = 'types';
 
   }

  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );
  
	// Add Item
	if ($_GET['action'] == 'add')
	{
		// If adding
		if ($_POST['submit'] == true)
		{
			if ($_POST['name'] != '')
			{
				$form = array_map('safehtml', $_POST);
					
				$sql = "INSERT INTO " . $table . " (
					name, site_id, name2, name3, name4, name5, name6, name7, name8, name9, name10, name11, name12, name13, name14, name15, name16, name17, name18, name19, name20, name21, name22, name23, name24,
					name25, name26, name27, name28, name29, name30";
                if ($fields) {
                    foreach ($fields as $field) {
                        $sql .= ', '.$field['column'];
                    }
                }
				$sql .= ") VALUES (
					'" . $form['name'] . "',
					'" . $session->fetch('site_id') . "',
					'" . $form['name2'] . "',
					'" . $form['name3'] . "',
					'" . $form['name4'] . "',
					'" . $form['name5'] . "',
					'" . $form['name6'] . "',
					'" . $form['name7'] . "',
					'" . $form['name8'] . "',
					'" . $form['name9'] . "',
					'" . $form['name10'] . "',
					'" . $form['name11'] . "',
					'" . $form['name12'] . "',
					'" . $form['name13'] . "',
					'" . $form['name14'] . "',
					'" . $form['name15'] . "',
					'" . $form['name16'] . "',
					'" . $form['name17'] . "',
					'" . $form['name18'] . "',
					'" . $form['name19'] . "',
					'" . $form['name20'] . "',
					'" . $form['name21'] . "',
					'" . $form['name22'] . "',
					'" . $form['name23'] . "',
					'" . $form['name24'] . "',
					'" . $form['name25'] . "',
					'" . $form['name26'] . "',
					'" . $form['name27'] . "',
					'" . $form['name28'] . "',
					'" . $form['name29'] . "',
					'" . $form['name30'] . "'";
				if ($fields) {
                    foreach ($fields as $field) {
                        $sql .= ", '".$form[$field['column']]."'";
                    }
                }
				
				$sql .= ")";
				$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
				echo $lang['Admin_Item_Added'];
			}
			else
			{
				// Output error, one has to be filled in...
			}
		}

		echo table_header ( $name );

  		echo '
  		<form action = "' . URL . '/admin/edit.php?action=add&req=' . $_REQUEST['req'] . '" method="POST" name="form">
  		<table cellpadding="5" cellspacing="2" border="0">
		<tr>
			<td align="left" colspan="2">';
			
			// Show all available languages
			foreach ($installed_languages AS $language1 => $key1)
			{
				echo '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').show();">' . ucwords($language1) . '</a>&nbsp; ';
			}
			
			echo '<br /><br />';
			
			// Display text boxes for every language
			$num = 1;
			foreach ($installed_languages AS $language1 => $key1)
			{

				if ($num == 1)
					$display = 'normal';
				else
					$display = 'none';
				
				echo '	
				<div style="display: ' . $display . ';" id="' . $key1 . '">
				' . $lang['Admin_Item'] . ' <input type="text" size="100" maxlength="255" name="' . $key1 . '" id="' . $key1 . '" value="' . $form[$key1] . '">
				<br /><a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').hide();">Hide ' . ucwords($language1) . '</a>
				</div>
				';
				
				$num++;
			}
			
			echo '
			</td>
		</tr>
		';
			if ($fields) {
			    foreach ($fields as $field) {
			        echo '<tr align="left">
			        <td colspan="2">'.$field['label'];
			        switch ($field['type']) {
		            case 'select':
		                echo '<select name="'.$field['column'].'" ';
		                if (!empty($field['classes'])) {
		                    echo ' class="'.implode(', ', $field['classes']).'"';
		                }
		                echo '>';
		                if (!in_array('required', $field['classes'])) {
		                    echo '<option value=""></option>';
			            }
		                foreach ($field['values'] as $value => $text) {
		                    if (array_key_exists('default', $field) 
		                        && $field['default'] == $value) {
		                        $selected = ' selected="selected"';
		                    } else {
		                        $selected = '';
		                    }
		                    echo '<option value="'.htmlspecialchars($value).'"'
		                         .$selected.'>'
		                         .htmlspecialchars($text).'</option>';
		                }		                
		                break;
			        }
			        echo '
			        </td>
			        </tr>';
			    }
			}
		echo '
		<tr>
			<td align="left" colspan="2">
			<input type="submit" name="submit" value="' . $lang['Admin_Item_Submit'] . '">
			</td>
		</tr>
		</table>
		';

		echo table_footer();
	}  

	// Delete Type Entry
	if ($_GET['id'] != '' && $_GET['action'] == 'delete')
	{
		$sql = "DELETE FROM " . $table . " WHERE id = '" . intval($_GET['id']) . "' AND ".$whereClause." LIMIT 1";
		$r = $db->query( $sql ) OR error( mysql_error () );
		echo table_header ( $lang['Information'] );
		echo $lang['Admin_Item_Removed'];
		echo table_footer();
	}

	// Add Type/Location Entry
	if ($_REQUEST['action'] == 'edit' && $_REQUEST['id'] != '')
	{
		// If adding
		if ($_POST['submit'] == true)
		{
			echo table_header ( $lang['Information'] );
		
			if ($_POST['name'] != '')
			{
				$form = array_map('safehtml', $_POST);
					
				$sql = "UPDATE " . $table . " SET
					name = '" . $form['name'] . "', name2 = '" . $form['name2'] . "', name3 = '" . $form['name3'] . "', name4 = '" . $form['name4'] . "', name5 = '" . $form['name5'] . "',
					name6 = '" . $form['name6'] . "', name7 = '" . $form['name7'] . "', name8 = '" . $form['name8'] . "', name9 = '" . $form['name9'] . "', name10 = '" . $form['name10'] . "',
					name11 = '" . $form['name11'] . "', name12 = '" . $form['name12'] . "', name13 = '" . $form['name13'] . "', name14 = '" . $form['name14'] . "',
					name15 = '" . $form['name15'] . "', name16 = '" . $form['name16'] . "', name17 = '" . $form['name17'] . "', name18 = '" . $form['name18'] . "',
					name19 = '" . $form['name19'] . "', name20 = '" . $form['name20'] . "', name21 = '" . $form['name21'] . "', name22 = '" . $form['name22'] . "',
					name23 = '" . $form['name23'] . "', name24 = '" . $form['name24'] . "', name25 = '" . $form['name25'] . "', name26 = '" . $form['name26'] . "',
					name27 = '" . $form['name27'] . "', name28 = '" . $form['name28'] . "', name29 = '" . $form['name29'] . "', name30 = '" . $form['name30'] . "'";
				if ($fields) {
				    foreach ($fields as $field) {
				        $sql .= ", ".$field['column']." = '".$form[$field['column']]."'";
				    }
				}
				$sql .= "WHERE id = '" . intval($form['id']) . "' AND ".$whereClause;
				$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
				echo $lang['Admin_Item_Updated'];
			}
			else
			{
				// Output error, one has to be filled in...
			}
			echo table_footer();
		}

		echo table_header ( $name );
		
		// Grab the Item
		echo $sql = "SELECT * FROM " . $table . " WHERE id = '" . intval($_REQUEST['id']) . "' AND ".$whereClause." LIMIT 1";
		$r = $db->query( $sql ) OR error ( mysql_error() );
		$form = $db->fetcharray ( $r );
		
		// Make all fields editable
		foreach ($installed_languages AS $language1 => $key1)
		{
			$form[$key1] = unsafehtml($form[$key1]);
		}

  		echo '
  		<form action = "' . URL . '/admin/edit.php?action=edit&req=' . $_REQUEST['req'] . '" method="POST" name="form">
  		<input type="hidden" name="id" value="' . $_REQUEST['id'] . '">
  		<table cellpadding="5" cellspacing="2" border="0">
		<tr>
			<td align="left" colspan="2">';
			
			// Show all available languages
			foreach ($installed_languages AS $language1 => $key1)
			{
				echo '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').show();">' . ucwords($language1) . '</a>&nbsp; ';
			}
			
			echo '<br /><br />';
			
			// Display text boxes for every language
			$num = 1;
			foreach ($installed_languages AS $language1 => $key1)
			{

				if ($num == 1)
					$display = 'normal';
				else
					$display = 'none';
				
				echo '	
				<div style="display: ' . $display . ';" id="' . $key1 . '">
				' . $lang['Admin_Item'] . ' <input type="text" size="100" maxlength="255" name="' . $key1 . '" id="' . $key1 . '" value="' . $form[$key1] . '">
				<br /><a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').hide();">Hide ' . ucwords($language1) . '</a>
				</div>
				';
				
				$num++;
			}
			
			echo '
			</td>
		</tr>';
            if ($fields) {
                foreach ($fields as $field) {
                    echo '<tr align="left">
                    <td colspan="2">'.$field['label'];
                    switch ($field['type']) {
                    case 'select':
                        echo '<select name="'.$field['column'].'" ';
                        if (!empty($field['classes'])) {
                            echo ' class="'.implode(', ', $field['classes']).'"';
                        }
                        echo '>';
                        if (!in_array('required', $field['classes'])) {
                            echo '<option value=""></option>';
                        }
                        foreach ($field['values'] as $value => $text) {
                            if ($form[$field['column']] == $value) {
                                $selected = ' selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="'.htmlspecialchars($value).'"'
                                 .$selected.'>'
                                 .htmlspecialchars($text).'</option>';
                        }                       
                        break;
                    }
                    echo '
                    </td>
                    </tr>';
                }
            }
			
		echo '
		<tr>
			<td align="left" colspan="2">
			<input type="submit" name="submit" value="' . $lang['Admin_Item_Update'] . '">
			</td>
		</tr>
		</table>
		';

		echo table_footer();
	}  

	// Show all Types/Locations in system
	echo table_header ( $name );
	
	echo '
	<a href="' . URL . '/admin/edit_custom.php?req=custom">' . $lang['Admin_Custom_Types'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=types">' . $lang['Admin_Property_Types'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=types2">' . $lang['Module_Listing_Type'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=styles">' . $lang['Admin_Styles'] . '</a> | 
	<a href="' . URL . '/admin/locations.php">' . $lang['admin_3level'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=buildings">' . $lang['Admin_Additional_Out_Buildings'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=appliances">' . $lang['Admin_Appliances_Included'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=features">' . $lang['Admin_Features'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=basement">' . $lang['Admin_Basement'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=garage">' . $lang['Admin_Garage'] . '</a> | 
	<a href="' . URL . '/admin/edit.php?req=status">' . $lang['Admin_Status'] . '</a>
	<br /><br />
	';
	
	$sql = 'SELECT * FROM ' . $table . ' WHERE '.$whereClause.' ORDER BY name ASC';
	$r = $db->query( $sql );
	if($db->numrows( $r )>0){}
		else{
		//echo $sql = 'SELECT * FROM ' . $table . ' WHERE '.$defaultClause.' ORDER BY name ASC';
		//$r = $db->query( $sql );
	}
	echo '
	<a href="' . URL . '/admin/edit.php?action=add&req=' . $_GET['req'] . '">Add Type/Location</a> | <a href="' . URL . '/admin/edit.php?req=' . $_GET['req'] . '">Manage Types/Locations</a>
	<br /><br />
	';
	
	if ($f = $db->numrows( $r ) > 0)
	{
		
		echo '		
		<table width="100%" border="0" cellpadding="4" cellspacing="0" align="center">
		<tr>
			<td align="center"><b>ID</b></td>
			<td align="left"><b>Name</b></td>
			<td align="center"><b>Options</b></td>
		</tr>
		';
	
		while ($f = $db->fetcharray( $r ))
		{
		
			echo '
			<tr>
				<td align="center">' . $f['id'] . '</td>
				<td align="left">' . $f['name'] . '</td>
				<td align="center"><a href="' . URL . '/admin/edit.php?action=edit&id=' . $f['id'] . '&req=' . $_GET['req'] . '">Edit</a> | <a href="' . URL . '/admin/edit.php?action=delete&id=' . $f['id'] . '&req=' . $_GET['req'] . '" onClick="return confirmDelete(\'Are you sure you want to delete?\')">Delete</a></td>
			</tr>
			';
		}
		
		echo '
		</table>
		';
	}
  	else
  {
	// Error, no types/locations to edit
  }
  
  echo table_footer ();

 }
else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>