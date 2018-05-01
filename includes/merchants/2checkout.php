<?php

define( 'PMR', true );

include '../../config.php';
include PATH . '/defaults.php';

// Title tag content
$title = $conf['website_name_short'];

// Template header
include ( PATH . '/templates/' . $cookie_template . '/header.php' );

$output = '';

// If 2co returned the credit_card_processed variable we proceed
// if not we just return an error
if (isset($_POST['credit_card_processed']))
{
  // Generating the 2co key to compare to the returned one
  $_POST['total'] = str_replace( ',', '.', $_POST['total'] );
  $key=$conf['2co_secret_word'] . $conf['2co_user_id'] . $_POST['order_number'] . $_POST['total'];

  $key=md5("$key");
  $key=strtoupper($key);
  $key2=$_POST['key'];

  // If the key is correct we continue
  if ($key == $key2)
   {

    // If the payment is processed we continue
    if ($_POST['credit_card_processed'] == "Y")
     {

      // Parse the returned order_id variable to user/listing id and package id
      // to upgrade correct listing or user
      $data = explode ("-", $_POST['merchant_order_id']);
      $type = $data[0];
      $id = $data[2];
      $login = $data[1];
      $package_id = $data[3];

      // If this is a listung upgrade we continue
      if ($type == 'LISTING')
       {
        // SELECT the package details
        $sql = 'SELECT * FROM ' . PACKAGES_TABLE . ' WHERE id = ' . $package_id . ' LIMIT 1';
        $r = $db->query($sql);
	$f_package = $db->fetcharray($r);

	// Make sure this package exists
    // and this is the correct package id
     if ( $db->numrows($r) > 0 && $f_package['id'] == $package_id )
	 {

      $output .= "Your 2co transaction is complete. Thank you.";
	  update_package( $id, $package_id );

    }
	else
 	 {
          $output .= "Your 2co transaction is incorrect, we have received incorrect values from the gateway.";
 	 }
       }

      // if this is an agent upgrade we continue
      if ($type == 'USER')
       {
        // SELECT the package details
        $sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE . ' WHERE id = ' . $package_id . ' LIMIT 1';
        $r = $db->query($sql);
	$f_package = $db->fetcharray($r);

	// Make sure this package exists
        // and this is the correct package id
        if ( $db->numrows($r) > 0 && $f_package['id'] == $package_id )
	 {

          $output .= "Your 2co transaction is complete. Thank you.";
	  update_agents_package( $id, $package_id );

         }
	else
 	 {
          $output .= "Your 2co transaction is incorrect, we have received incorrect values from the gateway.";
	 }
       }
     }

    // If card was not processed
    if ($_POST['credit_card_processed'] == "N")
     {

      $output .= "Your 2co transaction is incorrect, we have received incorrect values from the gateway.";
     
     }

    // If this a check payment
    if ($_POST['credit_card_processed'] == "K")
     {
      $output .= "Payed by Check, please, allow 2-3 days to verify";
     }

   }

  else 

   {
    $output .= "2checkout key is incorrect / Make sure you are not in the DEMO mode.";
   }

 }

else 

 {
  $output .= "2checkout returned nothing, please, make sure you have configured it correctly.";
 }

echo $output;

// Template footer
include ( PATH . '/templates/' . $cookie_template . '/footer.php' );

?>