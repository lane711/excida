<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / MAILER

// Title tag
$title = $lang['Admin_Mailer'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
  // Include navigation panel
  $session->set('navigation', '');
  include ( PATH . '/admin/navigation.php' );

  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');
  $whereClause = "";
    if($session->fetch('role')=="SUPERUSER")
      $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
  // If the Submit button was pressed we start this routine
  if (isset($_POST['submit_mailer']) 
  && $_POST['submit_mailer'] == $lang['Admin_Mailer_Submit'] && $_POST['subject'] != '' && $_POST['message'] != '')
   {

    $form = array();

    // safehtml() all the POST variables
    // to insert into the database or
    // print the form again if errors
    // found
	$form = $_POST;

    $recipients = array();

    if (isset($_POST['agent']) && is_array($_POST['agent'])) 
     {      

      foreach($_POST['agent'] as $arrayid => $id)
       {
	
	if ($id != '' ) 
         {

          $sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE u_id = ' . $id .' AND '.$whereClause;
          $r = $db->query($sql);
          $f = $db->fetcharray($r);

          array_push($recipients, $f['email']);
 
         }
       }
     }
    else 
     {
      if (!isset($_POST['agent'])) $_POST['agent'] = '';
      $sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE u_id = ' . $db->makeSafe( $_POST['agent'] ) .' AND '.$whereClause;
      $r = $db->query($sql);
      $f = $db->fetcharray($r);

      $recipients = $f['email'];
     }
    
	 if ( DEMO == false )
	 {
	      send_mailing( 
	      	$conf['general_e_mail'], 
	      	$conf['general_e_mail_name'], 
	      	$conf['general_e_mail'], 
	      	$form['subject'], 
	      	$form['message'],
	      	$recipients
	      );
      }

    echo $lang['Admin_Mailer_Sent'];

   }

  // If we open mailer.php for the first time
  // or there were errors found in the form fields 
  // we output the form again with the old variables 
  // included
  if (!isset($count_error) || $count_error > '0')
   {

    echo table_header ( $lang['Admin_Mailer'] );

    // Define the form variables if the form is loaded for the first time
    if (!isset($form))
     {
      $form = array();
      $form['subject'] = '';
      $form['message'] = '';
     }

    // Output the form
    echo '
     <form action="' . URL . '/admin/mailer.php" method="POST" name="form" id="form">
      <table width="100%" cellpadding="5" cellspacing="0" border="0">
         ';

    // Check if this user exist
    $sql = 'SELECT * FROM ' . USERS_TABLE .' WHERE '.$whereClause;;
    $r = $db->query($sql);

    $options = '';

    while ($f = $db->fetcharray($r))
     $options .= '<option value="' . $f['u_id'] . '">' . $f['first_name'] . ' ' . $f['last_name'] . ' (User ID #' . $f['u_id'] . ')</option>';

    echo userform ($lang['Admin_Mailer_To'] , '<select multiple size="5" name="agent[]">' . $options . '</select>');
    echo userform ($lang['Admin_Mailer_Subject'], '<input type="text" name="subject" value="' . $form['subject'] . '">', '1');
    echo userform ($lang['Admin_Mailer_Message'], '<textarea class="ckeditor" cols="45" rows="4"  name="message" id="message" >' . unsafehtml($form['message']) . '</textarea>', '1');
 
    echo userform ('', '<input type="Submit" name="submit_mailer" value="' . $lang['Admin_Mailer_Submit'] . '">');

    echo '
      </table>
     </form>
    ';

    echo table_footer ();
   }

 }

else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>