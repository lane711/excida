<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / BAN EMAILS

// Title tag value
$title = $lang['Admin_Ban_e_mails'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {

  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );

  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_types', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  // The table name to work with
  $table = BANS_TABLE;

  // The name of the banned item
  $name = $lang['Admin_Ban_e_mails'];

  // If the form is submitted we save the data
  if (isset($_POST['submit_item']) && $_POST['submit_item'] == $lang['Admin_Item_Submit'])
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
      $sql = 'INSERT INTO ' . $table . '
      (name)
      VALUES 
      ("' . $form['item'] . '")';
 
      $db->query( $sql ) or error ('Critical Error', mysql_error () );
 
      echo $lang['Admin_Item_Added'];
 
     }
 
    echo table_footer ( );
 
   }
 
  // If the form was updated we save the data
  if (isset($_POST['update_item']) && $_POST['update_item'] == $lang['Admin_Item_Update'])
 
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
              SET name = "' . $form['item'] . '"
              WHERE id = "' . $form['id'] . '"';
 
      $db->query( $sql ) or error ('Critical Error', mysql_error () );
 
      echo $lang['Admin_Item_Updated'];

     }

    echo table_footer ();

   }
 
  // If the item was removed we clean the data
  if (isset($_POST['remove_item']) && $_POST['remove_item'] == $lang['Admin_Item_Remove'])
 
   {
 
    echo table_header ( $lang['Information'] );
 
    $form = array_map ( 'safehtml', $_POST );
 
    $count_error = 0;
 
    if (empty($form['id']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">ID</span><br />'; $count_error++;}
 
    if ($count_error == 0)
 
     {
 
      // Delete the record from the database
      $sql = 'DELETE FROM ' . $table . ' 
    	     WHERE id = "' . $form['id'] . '"';
 
      $db->query( $sql ) or error ('Critical Error', mysql_error () );
 
      echo $lang['Admin_Item_Removed'];
 
     }
 
    echo table_footer();
 
   }
 
  // Fetching the data from the database
  $sql = 'SELECT * FROM ' . $table;
  $r = $db->query( $sql );
 
  // Generating the form
  echo table_header ( $name );
  
 	echo '
	<a href="' . URL . '/admin/addusers.php">' . $lang['Admin_Add_Users'] . '</a> | 
	<a href="' . URL . '/admin/users.php">' . $lang['Admin_Edit_Users'] . '</a> | 
	<a href="' . URL . '/admin/users.php?realtor_approved=YES">' . $lang['Admin_Approve_New_Users'] . '</a> | 
	<a href="' . URL . '/admin/users.php?realtor_approved=YES&realtor_updated_days=5">' . $lang['Admin_Approve_Updated_Users'] . '</a> | 
	<a href="' . URL . '/admin/editadmins.php">' . $lang['Admin_Edit_Administrators'] . '</a> | 
	<a href="' . URL . '/admin/privileges.php">' . $lang['Admin_Edit_Privileges'] . '</a> | 
	<a href="' . URL . '/admin/banemails.php">' . $lang['Admin_Ban_e_mails'] . '</a>
	<br /><br /><br />
	';
 
  echo '<form action = "' . URL . '/admin/banemails.php" method="POST"> ';
 
  echo ' <table cellpadding="5" cellspacing="0" border="0">';
 
  while ($f = $db->fetcharray( $r ))
   {
    echo ' <tr><td width="30" align="middle" align="center" ><input type="radio" name="id" value="' . $f['id'] . '"></td><td align="left" valign="top"> <span class="warning">' . $f['name'] . '</span> </td></tr>';
   }
 
  echo '  </table>';
 
  echo ' <table cellpadding="5" cellspacing="0" border="0">';
 
  echo ' 
 	<tr><td align="left" valign="top"><br /><br />
  	 <input type="text" name="item" value="" size="45" maxlength="50">
 	 <input type="submit" name="submit_item" value="' . $lang['Admin_Item_Submit'] . '">
         <input type="submit" name="update_item" value="' . $lang['Admin_Item_Update'] . '">
         <input type="submit" name="remove_item" value="' . $lang['Admin_Item_Remove'] . '">
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