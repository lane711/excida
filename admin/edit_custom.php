<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = '';

include PATH . '/admin/template/header.php';

// If logged we can start the page output
if ( adminAuth( $session->fetch( 'adminlogin' ), $session->fetch( 'adminpassword' ) ) )
{
	include PATH . '/admin/navigation.php';
  $whereClause = "";
  if($session->fetch('role')=="SUPERUSER")
  $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
	// Make sure this administrator have access to this script
	adminPermissionsCheck('manage_types', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  // Set the table to edit and the name of that table
  if (isset($_GET['req']))
   {

    // Edit property types
    if ($_GET['req'] == 'fields')

     {

      $table = FIELDS_TABLE;
      $name = $lang['Admin_Custom_Types'];
      $req = 'fields';

     }
    elseif ($_GET['req'] == 'values')

     {

      $table = VALUES_TABLE;
      $name = $lang['Admin_Custom_Values'];
      $req = 'values';

     }
    else
    {

      $table = FIELDS_TABLE;
      $name = $lang['Admin_Custom_Types'];
      $req = 'fields';

    }
   }

  // If no parameters passed to the script we edit Property Types
  else

   {

      $table = FIELDS_TABLE;
      $name = $lang['Admin_Custom_Fields'];
      $req = 'fields';

   }

  // If the form is submitted we save the data
  if (isset($_POST['submit_item']) && $_POST['submit_item'] == $lang['Admin_Item_Submit'] . ' Field')
   {

    echo table_header ( $lang['Information'] );

    $form = array_map ( 'safehtml', $_POST );

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['item']) || strlen($form['item']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Item'] . '</span><br />'; $count_error++;}

     // Figure out what to name this field
	 $count = "SELECT * FROM " . FIELDS_TABLE . " WHERE ".$whereClause."";
	 $num = $db->query($count) OR error( 'Critical Error', $count );
	 $number = $db->numrows($num);

	 if ($number < 10) {
	 	$number = $number + 1;
	 } else {
	 	echo "Field Limit Reached - <span class=\"warning\">You cannot have more than 10 custom fields.</span><br>";
	 	$count_error++;
	 }

    if ($count_error == 0)

     {

      // Update the database
      $sql = 'INSERT INTO ' . $table . '

      (field, site_id, type, name, name2, name3, name4, name5, name6)
      VALUES
      ("custom' . $number . '", "' . $session->fetch('site_id'). '", "' . $form['type'] . '", "' . $form['item'] . '", "' . $form['item2'] . '", "' . $form['item3'] . '",
	"' . $form['item4'] . '", "' . $form['item5'] . '", "' . $form['item6'] . '")';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Item_Added'];

     }

    echo table_footer ( );

   }

  // If the form was updated we save the data
  if (isset($_POST['update_item']) && $_POST['update_item'] == $lang['Admin_Item_Update'] . ' Field')

   {

    echo table_header ( $lang['Information'] );

    $form = array_map ( 'safehtml', $_POST );

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['item']) || strlen($form['item']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Item'] . '</span><br />'; $count_error++;}

    if (empty($form['id']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">ID</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Update the record in the database
      $sql = 'UPDATE ' . $table . '
    	      SET type = "' . $form['type'] . '", name = "' . $form['item'] . '", name2 = "' . $form['item2'] . '", name3 = "' . $form['item3'] . '",
	      name4 = "' . $form['item4'] . '", name5 = "' . $form['item5'] . '", name6 = "' . $form['item6'] . '"
 	      WHERE id = "' . $form['id'] . '" AND '.$whereClause;

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Item_Updated'];

     }

    echo table_footer ();

   }


  // If the ite was removed we clean the data
  if (isset($_POST['remove_item']) && $_POST['remove_item'] == $lang['Admin_Item_Remove'] . ' Field')
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form = array_map ( 'safehtml', $_POST );

    $count_error = 0;

    if (empty($form['id']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">ID</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Delete the record from the database
      $sql = 'DELETE FROM ' . $table . '
  	      WHERE id = "' . $form['id'] . '"'.$whereClause;

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Item_Removed'];

     }

    echo table_footer();

   }

  // If the form is submitted we save the data
  if (isset($_POST['submit_option']) && $_POST['submit_option'] == 'Add Option')
   {

    echo table_header ( $lang['Information'] );

    $form = array_map ( 'safehtml', $_POST );

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['item']) || strlen($form['item']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Item'] . '</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Update the database
      $sql = 'INSERT INTO ' . VALUES_TABLE . '

      (f_id, site_id, name, name2, name3, name4, name5, name6)
      VALUES
      ("' . $form['id'] . '", "' . $session->fetch('site_id'). '", "' . $form['item'] . '", "' . $form['item2'] . '", "' . $form['item3'] . '",
	"' . $form['item4'] . '", "' . $form['item5'] . '", "' . $form['item6'] . '")';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Item_Added'];

     }

    echo table_footer ( );

   }


  // If the ite was removed we clean the data
  if (isset($_POST['remove_option']) && $_POST['remove_option'] == 'Remove Option')
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form = array_map ( 'safehtml', $_POST );

    $count_error = 0;

    if (empty($form['id']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">ID</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Delete the record from the database
      $sql = 'DELETE FROM ' . VALUES_TABLE . '
  	      WHERE id = "' . $form['id'] . '"'.$whereClause;

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Item_Removed'];

     }

    echo table_footer();

   }

  // Fetching the data from the database
  $sql = 'SELECT * FROM ' . $table .' WHERE '.$whereClause;
  $r = $db->query( $sql );
  $num = $db->numrows($r);

  // Generating the form
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

  echo '<form action = "' . URL . '/admin/edit_custom.php?req=' . $req . '" method="POST"> ';

  echo ' <table cellpadding="5" cellspacing="0" border="0">';

  while ($f = $db->fetcharray( $r )) {
   echo ' <tr><td width="30" align="middle" align="center" ><input type="radio" name="id" value="' . $f['id'] . '"></td><td align="left" valign="top"> <span class="warning">' . $f['name'] . '</span> </td></tr>';

   // Child values
   $sql2 = 'SELECT * FROM ' . VALUES_TABLE . ' WHERE f_id = "' . $f['id'] . '"'.$whereClause;
   $r2 = $db->query($sql2);
   if ($db->numrows($r2) > 0) {
   	while($f2 = $db->fetcharray( $r2 )) {
   		echo '<tr><td colspan="2">
   		<table><tr><td align="left" style="padding-left: 25px;">-- <input type="radio" name="id" value="' . $f2['id'] . '"></td><td align="left" valign="top"> <span class="warning">' . $f2['name'] . '</span> </td></tr></table>
   		</td></tr>';
   	}
   }

   }

  echo '
	<tr><td colspan="2" align="left" valign="top"><br /><br />
	 Default (English): <input type="text" name="item" value="" size="45" maxlength="50"><br />
	 French: <input type="text" name="item2" value="" size="45" maxlength="50"><br />
	 German: <input type="text" name="item3" value="" size="45" maxlength="50"><br />
	 Italian: <input type="text" name="item4" value="" size="45" maxlength="50"><br />
	 Russian: <input type="text" name="item5" value="" size="45" maxlength="50"><br />
	 Spanish: <input type="text" name="item6" value="" size="45" maxlength="50"><br />
	 Type of Field: <select name="type"><option value="input" selected>Input (Text)</option><option value="select">Select (Drop Down Menu)</option></select><br><br>
';

	if ($num < 10)
		echo 'Custom Field Options: <input type="submit" name="submit_item" value="' . $lang['Admin_Item_Submit'] . ' Field">';

	echo '
 	 <input type="submit" name="update_item" value="' . $lang['Admin_Item_Update'] . ' Field">
     <input type="submit" name="remove_item" value="' . $lang['Admin_Item_Remove'] . ' Field">
     <br><br>
     Custom Value Options: <input type="submit" name="submit_option" value="Add Option">
     <input type="submit" name="remove_option" value="Remove Option">
	</td></tr>

       ';

  echo ' </table>
      </form>';

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