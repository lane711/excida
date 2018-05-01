<?php

if (!defined('PMR') || (defined('PMR') && PMR != 'true')) die();

// Set the number of listing per row in the table
$setup_rows = 3;

echo '<div id="tab-propertyType" class="tabcontent">';

// Select all property types from the database
$sql = 'SELECT id, ' . $language_in . ', name, counter FROM ' . TYPES_TABLE . ' ORDER BY ' . $language_in;
$r = $db->query ( $sql ) or error ('Critical Error', mysql_error () );

echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">
       <tr>
        <td width="' . 100 / $setup_rows . '%" align="left" valign="top">
         <ul>
     ';

// Calculate the number of listings per row ($setup_rows rows)
$results_amount = ceil ($db->numrows($r) / $setup_rows);

// Total Results
$results_total = $db->numrows($r);

$results = 0;
$rows = 0;

while ($f = $db->fetcharray($r))

 {

	// Default
	if ($f[1] == '')
		$f[1] = $f['name'];

  $rows++;
  $results++;

// $fcount  = $db->query ( 'SELECT id FROM ' . PROPERTIES_TABLE . ' WHERE type = "' . $f['id'] . '"' ) or error ('Critical Error', mysql_error () );
// $f['counter'] = $db->numrows($fcount);

  if ($conf['rewrite'] == 'ON')
   echo '<li class="arrow"> <a href="' . URL . '/Type/' . rewrite ( $f[1] ) . '/' . $f['id'] . '.html">' . $f[1] . '</a> [' . $f['counter'] . ']<br />';
  else
   echo '<li class="arrow"> <a href="' . URL . '/search.php?type=' . $f['id'] . '">' . $f[1] . '</a> [' . $f['counter'] . ']<br />';

  // Restart the row each time we reach the maximum listing per row
  // Do not create new row if this is a last element of the array
  if ($rows == $results_amount && $results != $results_total)

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