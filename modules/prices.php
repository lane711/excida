<?php

if (!defined('PMR') || (defined('PMR') && PMR != 'true')) die();

// Set the number of listing per row in the table
$setup_rows = 2;

echo '<div id="tab-priceRange" class="tabcontent">';

echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">
       <tr>
        <td width="' . 100 / $setup_rows . '%" align="left" valign="top">
         <ul>
     ';

$price = array();

// Create an array of price ranges in the following format "min_price-max_price"
for ($a = $conf['price_range_min']; $a <= $conf['price_range_max']; $a = $a + $conf['price_range_step'])
 {
  array_push($price, $a . '-' . ($a + $conf['price_range_step']));
 }

// Calculate the number of listings per row ($setup_rows rows)
$results_amount = ceil (count ($price) / $setup_rows);

$rows = 0;

for ($i = 0; $i < count ($price); $i = $i + 1)

 {

  $rows++;

  // Explode each item into an array or max_price and min_price
  $prices = explode ('-', $price[$i]);
 
  // Create the links
  if ($conf['rewrite'] == 'ON')
   echo '<li class="arrow"> <a href="' . URL . '/Price/' . $prices[0] . '-' . $prices[1] . '.html">' . $conf['currency'] . ' ' . pmr_number_format($prices[0]) . ' - ' . pmr_number_format($prices[1]) . '</a><br />';
  else
   echo '<li class="arrow"> <a href="' . URL . '/search.php?price_min=' . $prices[0] . '&price_max=' . $prices[1] . '">' . $conf['currency'] . ' ' . pmr_number_format($prices[0]) . ' - ' . pmr_number_format($prices[1]) . '</a><br />';

  // Restart the row each time we reach the maximum listing per row

  // Check if this is not the last element of the array
  $z = count ($price)-1;

  if ($rows == $results_amount && $price[$i] != $price[$z])
   {
    echo ' </ul> 
            </td>
            <td width="' . 100 / $setup_rows . '%" align="left" valign="top">
           <ul>
         ';
    // Clean the $rows variable for the new row listings counter
    $rows = 0;
   }
 }

echo '   </ul>
	</td>
       </tr>
      </table>
     ';

echo '</div>';

?>