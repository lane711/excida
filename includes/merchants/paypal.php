<?php

define( 'PMR', true );

include '../../config.php';
include PATH . '/defaults.php';

// Title tag content
$title = $conf['website_name_short'];

// Template header
include ( PATH . '/templates/' . $cookie_template . '/header.php' );

$output = '';

// If paypal returned the submitted data we start
if ( isset ( $_POST["payment_status"] ) )
 {
  // Generating the return data
  $postipn = 'cmd=_notify-validate';
  
  foreach ($_POST as $ipnkey => $ipnval) 
   {
    if (get_magic_quotes_gpc()) 
     {
      $ipnval = stripslashes ($ipnval);
     }
        
    // Remove the incorrect IPN values and keys
    if (!preg_matchi( "/^[_0-9a-z-]{1,30}$/i", $ipnkey) || !strcasecmp ($ipnkey, 'cmd')) 
     {
      unset ($ipnkey); unset ($ipnval);
     }

    if (@$ipnkey != '') 
     {

      // Generate the PAYPAL global variable
      @$_PAYPAL[$ipnkey] = $ipnval;
      unset ($_POST);

      $postipn.='&'.@$ipnkey.'='.urlencode(@$ipnval); 
     }
   }

  // Generate header
  $socket = fsockopen ( "www.paypal.com", 80, $errno, $errstr, 30 );
  $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
  $header.= "Host: www.paypal.com\r\n";
  $header.= "Content-Type: application/x-www-form-urlencoded\r\n";
  $header.= "Content-Length: " . strlen($postipn) . "\r\n\r\n";


  // If there were errors found
  if ( !$socket && !$error ) 
   {
    $output .= "Problem: Error Number: " . $errno . " Error String: " . $errstr;
   }
  else
   {

    // Send the data via the socket 
    fputs ( $socket ,$header . $postipn );

    // Receive the returned data
    while ( !feof ( $socket ) ) 
     {
      $reply = fgets ( $socket, 1024 );
      $reply = trim ( $reply );
     }

    @$receiver_email = $_PAYPAL['receiver_email'];
    @$receiver_id = $_PAYPAL['receiver_id'];
    @$business = $_PAYPAL['business'];
    @$item_name = $_PAYPAL['item_name'];
    @$item_number = $_PAYPAL['item_number'];
    @$quantity = $_PAYPAL['quantity'];
    @$invoice = $_PAYPAL['invoice'];
    @$custom = $_PAYPAL['custom'];
    @$option_name1 = $_PAYPAL['option_name1'];
    @$option_selection1 = $_PAYPAL['option_selection1'];
    @$option_name2 = $_PAYPAL['option_name2'];
    @$option_selection2 = $_PAYPAL['option_selection2'];
    @$num_cart_items = $_PAYPAL['num_cart_items'];
    @$payment_status = $_PAYPAL['payment_status'];
    @$pending_reason = $_PAYPAL['pending_reason'];
    @$payment_date = $_PAYPAL['payment_date'];
    @$settle_amount = $_PAYPAL['settle_amount'];
    @$settle_currency = $_PAYPAL['settle_currency'];
    @$exchange_rate = $_PAYPAL['exchange_rate'];
    @$payment_gross = $_PAYPAL['payment_gross'];
    @$payment_fee = $_PAYPAL['payment_fee'];
    @$mc_gross = $_PAYPAL['mc_gross'];
    @$mc_fee = $_PAYPAL['mc_fee'];
    @$mc_currency = $_PAYPAL['mc_currency'];
    @$tax = $_PAYPAL['tax'];
    @$txn_id = $_PAYPAL['txn_id'];
    @$txn_type = $_PAYPAL['txn_type'];
    @$reason_code = $_PAYPAL['reason_code'];
    @$for_auction = $_PAYPAL['for_auction'];
    @$auction_buyer_id = $_PAYPAL['auction_buyer_id'];
    @$auction_close_date = $_PAYPAL['auction_close_date'];
    @$auction_multi_item = $_PAYPAL['auction_multi_item'];
    @$memo = $_PAYPAL['memo'];
    @$first_name = $_PAYPAL['first_name'];
    @$last_name = $_PAYPAL['last_name'];
    @$address_street = $_PAYPAL['address_street'];
    @$address_city = $_PAYPAL['address_city'];
    @$address_state = $_PAYPAL['address_state'];
    @$address_zip = $_PAYPAL['address_zip'];
    @$address_country = $_PAYPAL['address_country'];
    @$address_status = $_PAYPAL['address_status'];
    @$payer_email = $_PAYPAL['payer_email'];
    @$payer_id = $_PAYPAL['payer_id'];
    @$payer_business_name = $_PAYPAL['payer_business_name'];
    @$payer_status = $_PAYPAL['payer_status'];
    @$payment_type = $_PAYPAL['payment_type'];
    @$notify_version = $_PAYPAL['notify_version'];
    @$verify_sign = $_PAYPAL['verify_sign'];
    @$subscr_date = $_PAYPAL['subscr_date'];
    @$subscr_effective = $_PAYPAL['subscr_effective'];
    @$period1 = $_PAYPAL['period1'];
    @$period2 = $_PAYPAL['period2'];
    @$period3 = $_PAYPAL['period3'];
    @$amount1 = $_PAYPAL['amount1'];
    @$amount2 = $_PAYPAL['amount2'];
    @$amount3 = $_PAYPAL['amount3'];
    @$mc_amount1 = $_PAYPAL['mc_amount1'];
    @$mc_amount2 = $_PAYPAL['mc_amount2'];
    @$mc_amount3 = $_PAYPAL['mc_amount3'];
    @$recurring = $_PAYPAL['recurring'];
    @$reattempt = $_PAYPAL['reattempt'];
    @$retry_at = $_PAYPAL['retry_at'];
    @$recur_times = $_PAYPAL['recur_times'];
    @$username = $_PAYPAL['username'];
    @$password = $_PAYPAL['password'];
    @$subscr_id = $_PAYPAL['subscr_id'];
     
    // Check the reply and payment status
    if ( !strcmp ( $reply, "VERIFIED" ) )
     {    
      if ( $payment_status == "Completed" ) 
       {
        $date = date ( "Y-m-d" );

        // Explode the custom variable into listing id and package id	
        $custom2 = explode (":", $custom);
        $listing_id = $custom2[0];
	$package_id = $custom2[1];

        // SELECT the package details
        $sql = 'SELECT * FROM ' . PACKAGES_TABLE . ' WHERE id = "' . $package_id . '" LIMIT 1';
        $r = $db->query($sql);
	$f_package = $db->fetcharray($r);

	// Make sure there is such price and the paypal email is okay
        // and this is the correct package id

        if ( $db->numrows($r) > 0 && $business == $conf['paypal_email'] && $f_package['id'] == $package_id )
	 {

          $output .= "Your paypal transaction is complete. Thank you.";
	  		update_package( $listing_id, $package_id );

         }
        else 
         {
          $output .= "Paypal: Incorrect values returned";
         }

       }

      if ( $payment_status == "Pending" ) 
       {
        $result = "Your payment is pending. ";

        if ( $pending_reason == "echeck" ) $result = "Pending echeck";
        if ( $pending_reason == "multi_currency" ) $result = "Pending currency";
        if ( $pending_reason == "verify" ) $result = "Pending verify";
        if ( $pending_reason == "address" ) $result = "Pending address";
        if ( $pending_reason == "upgrade" ) $result = "Pending upgrade";

        $output .= "Paypal: $result";
       }

      if ( ( $payment_status == "Failed" ) or ( $payment_status == "Denied" ) ) 
       {
        $output .= "Paypal: Transaction Failed/Denied";
       }
     }

    if ( !strcmp ( $reply, "INVALID" ) ) 
     {
      $output .= "Paypal: Invalid Transaction";
     }

    fclose ( $socket );

   } 

 }    
else 
 {
  echo 'Paypal returned nothing';
 }

echo $output;

// Template footer
include ( PATH . '/templates/' . $cookie_template . '/footer.php' );

?>