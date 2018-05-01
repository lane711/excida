<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / ADD ADMINISTRATOR

// Title tag
$title = $lang['Admin_Edit_Administrators'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
 {
  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );
  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_administrators', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  // If the form is submitted we save the data
  if (isset($_POST['submit_admin']) && $_POST['submit_admin'] == $lang['Admin_Submit'])
   {

    echo table_header ( $lang['Information'] );

    $form = array_map ( 'safehtml', $_POST );

    // Make login and password lower case
    $login = strtolower ($form['login']);
    $password = $form['password'];

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['login']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Login'] . '</span><br />'; $count_error++;}

    if (empty($form['password']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Password'] . '</span><br />'; $count_error++;}

    if (preg_match('/[^A-Za-z0-9]+$/', $login))
     { echo $lang['Login_Incorrect'] . '<br />'; $count_error++;}

    // Check if login is already exist
    $sql = 'SELECT login FROM ' . ADMINS_TABLE . ' WHERE login = "' . $login . '"';
    $r = $db->query($sql) or error ('Critical Error', mysql_error () );

    if ($db->fetcharray($r) > 0 )
     { echo $lang['Login_Used'] . '<br />'; $count_error++;}

    if ($count_error == 0)
     {
      // Add the new admin record into the database
      $sql = 'INSERT INTO ' . ADMINS_TABLE . '

              (login, password, level)
              VALUES 
              ("' . $login . '", "' . md5($password) . '", "' . $form['level'] . '")';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );
 
      echo $lang['Admin_Added'];

     }

    echo table_footer();

   }

  // If the form was updated we save the data
  if (isset($_POST['update_admin']) && $_POST['update_admin'] == $lang['Admin_Update'])

   {

    echo table_header ( $lang['Information'] );

    $form = array_map ( 'safehtml', $_POST );

    // Make login and password lower case
    $login = strtolower ($_POST['login']);
    $password = $form['password'];

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['login']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Login'] . '</span><br />'; $count_error++;}

    if (empty($form['password']))
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Password'] . '</span><br />'; $count_error++;}

    if (preg_match('/[^A-Za-z0-9]+$/', $form['password']))
     { echo $lang['Login_Incorrect'] . '<br />'; $count_error++;}

    if (empty($form['old_login']))
     { echo $lang['Field_Empty'] . ' - <span class="warning"> old_login (form corrupted) </span><br />'; $count_error++;}

    // Check if password was not changed
    $sql = 'SELECT login, password, level FROM ' . ADMINS_TABLE . ' WHERE login = "' . $form['old_login'] . '"';
    $r = $db->query($sql) or error ('Critical Error', mysql_error () );
    $f = $db->fetcharray($r);

    if ($password == $f['password'])
     $password = $f['password'];
    else
     $password = md5( $password );

    if ($count_error == 0)

     {

      if (!isset($form['level']) and ($f['level'] == 'SUPERUSER'))
      $form['level'] = 'SUPERUSER';
 
      // Update the admin record in the database
      $sql = 'UPDATE ' . ADMINS_TABLE . ' SET
              login = "' . $login . '",
              password = "' . $password . '",
              level = "' . $form['level'] . '"
              WHERE login = "' . $form['old_login'] . '"';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );
 
      // if login/password was changed and this is our account
      // we update session login and password
 
      if ($f['login'] == $session->fetch('adminlogin'))
       {

        if ($form['login'] != $f['login'])
	 $session->set('adminlogin', $login);
        if ($form['password'] != $f['password'])
	 $session->set('adminpassword', $password);
       }

      echo $lang['Admin_Updated'];

     }

    echo table_footer ();

   }

  // If the admin was  removed we clean the data
  if (isset($_POST['remove_admin']) && $_POST['remove_admin'] == $lang['Admin_Remove'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form = array_map ( 'safehtml', $_POST );

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['old_login']))
     { echo $lang['Field_Empty'] . ' - <span class="warning"> old_login (form corrupted) </span><br />'; $count_error++;}

    if ($form['old_login'] == $session->fetch('adminlogin'))
     { echo $lang['Admin_Do_Not_Remove_Your_Own_Record'] . '<br />'; $count_error++;}

    // Check if this is a SUPERUSER
    $sql = 'SELECT level FROM ' . ADMINS_TABLE . ' WHERE login = "' . $form['old_login'] . '" AND level = "SUPERUSER"';
    $r = $db->query($sql) or error ('Critical Error', mysql_error () );
  
    if ($db->fetcharray($r) > 0 )
     { echo $lang['Admin_Do_Not_Remove_Superuser'] . '<br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Update the privileges record in the database
      $sql = 'DELETE FROM ' . ADMINS_TABLE . ' 
              WHERE login = "' . $form['old_login'] . '"';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );
 
      echo $lang['Admin_Removed'];

     }

    echo table_footer ();

   }

  // Fetching the administrators data from the database
  $sql = 'SELECT * FROM ' . ADMINS_TABLE;
  $r = $db->query( $sql );

  // Generating the form
  echo table_header ( $lang['Admin_Edit_Administrators'] );

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

  while ($f = $db->fetcharray( $r ))

   {

    if ($f['level'] == 'SUPERUSER')
     $table_name = 'Super User';
    else
     $table_name = 'Administrator';

    echo table_header ( $table_name );

    echo '<table cellpadding="5" cellspacing="0" border="0">';
    echo ' <form action = "' . URL . '/admin/editadmins.php" method="POST"> ';

    echo ' <input type="hidden" name="old_login" value="' . $f['login'] . '">';
    echo ' <td align="left" valign="top"> ' . $lang['Admin_Login'] . ': <br /><input type = "text" name = "login" value = "' . $f['login'] . '"></td>';
    echo ' <td align="left" valign="top"> ' . $lang['Admin_Password'] . ': <br /><input type = "password" name = "password" value = "' . $f['password'] . '"></td>';

    if ($f['level'] == 'SUPERUSER')

     {

      echo ' <td align="left" valign="top"> ' . $lang['Admin_Privileges_Name'] . ': SUPERUSER <input type="hidden" name="level" value="SUPERUSER"></td>';

     }

    else

     {

      echo ' <td align="left" valign="top"> ' . $lang['Admin_Privileges_Name'] . ': <br /><select name = "level">';

      $sql = 'SELECT * FROM ' . PRIVILEGES_TABLE;
      $r_privileges = $db->query($sql);

      while ($f_privileges = $db->fetcharray($r_privileges))
       {
	if ($f_privileges['privilege'] == $f['level']) $selected = 'SELECTED'; else $selected = '';
	echo '<option value="' . $f_privileges['privilege'] . '" ' . $selected . '>' . $f_privileges['privilege'] . '</option>';
       }

      echo ' </select></td>';

     }

    // Update / Remove Buttons
    echo ' <td align="left" valign="top"> <input type="submit" name="update_admin" value="' . $lang['Admin_Update'] . '"> ';
    echo ' <input type="submit" name="remove_admin" value="' . $lang['Admin_Remove'] . '"></td></tr>';
    echo ' </form>
          </table>';

    echo table_footer ();

   }

  echo table_header ( $lang['Admin_Submit'] );

  echo '<table cellpadding="5" cellspacing="0" border="0">';
  echo ' <form action = "' . URL . '/admin/editadmins.php" method="POST"> ';
  echo ' <td align="left" valign="top"> ' . $lang['Admin_Login'] . ': <br /><input type = "text" name = "login" value = "' . $f['login'] . '"></td>';
  echo ' <td align="left" valign="top"> ' . $lang['Admin_Password'] . ': <br /> <input type = "password" name = "password" value = "' . $f['password'] . '"></td>';

  $sql = 'SELECT * FROM ' . PRIVILEGES_TABLE;
  $r_privileges = $db->query($sql);

  echo ' <td align="left" align="top"> ' . $lang['Admin_Privileges_Name'] . ': <br /><select name = "level">';

  while ($f_privileges = $db->fetcharray($r_privileges))
   {
    echo '<option value="' . $f_privileges['privilege'] . '">' . $f_privileges['privilege'] . '</option>';
   }

  echo ' </select></td>';

  // Submit Button
  echo ' <td align="left" valign="top"> <input type="submit" name="submit_admin" value="' . $lang['Admin_Submit'] . '"></td></tr>';

  echo ' </form>
        </table>
       '; 

  echo table_footer ();

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