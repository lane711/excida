<?php

// Print the configuration data row
function configform ($name, $value, $description = '', $tooltip = '' )
{
$output = '
  <div class="col6">
  	' . ucwords( str_replace( '_', ' ', $name ) ) . '
  </div>
  <div class="col6 last">
  	<input type="text" name="' . $name . '" value="' . $value . '" size="45" maxlength="255">&nbsp;' . $tooltip .  '
  </div>
  ';
  
  return $output;
}

// Print the configuration data row
function alertform ($name, $value)
{
	$output = '
	<div class="row">
		<div class="span6">' . $name . '</div>
		<div class="span6">' . $value . '</div>
	</div>
	';
	return $output;
}

function privilegeform ($name, $var_name, $value, $current_value)
{

if ($current_value == $value) $checked = 'CHECKED'; else $checked = '';
$output = '
	  	<tr>
	  		<td><input type="checkbox" name="' . $var_name . '" value="' . $value . '" ' . $checked . '>&nbsp;' . $name . '</td>
	  	</tr>
      ';

	  return $output;
}

// This functions generates a table cell for the privilege
function packageform ($name, $var_name, $value)
{
	$output = '
	  <div class="clearfix col3">
	    ' . $name . '&nbsp;
	  </div>
	  <div class="col9 last">
	   <input type="text" name="' . $var_name . '" value="' . $value . '" size="20" maxwidth="255">
	  </div>
	 ';

	 return $output;
}

// ----------------------------------------------------------------------------
// userform($name,$field,$required='')
//
// generates the html table cell for the Name and Field data
//
// $name - field name string
// $field - form field html string
// $required - 1 for required, 0 or empty if not required field
//

function userform($name,$field,$required='') {

 /*
 $output = '
  <tr>
   <td width="30%" align="right" valign="top">
    ' . $name;
 // If the field is required we add the red asterisk
 if ($required == '1')
  $output.= ' <span class="warning">*</span> ';

 $output.= '
  </td>
  <td width="70%" align="left" valign="top">
   ' . $field . '
  </td>
 </tr>
 ';
 */
 
 $output = '
  <div class="clearfix col3">
    ' . $name;
 // If the field is required we add the red asterisk
 if ($required == '1')
  $output.= ' <span class="warning">*</span> ';

 $output.= '&nbsp;
  </div>
  <div class="col9 last">
   ' . $field . '
  </div>
 ';

 return $output;

}

// ----------------------------------------------------------------------------
// generate_options_list($name,$selected='')
//
// generates <option> tags for all the items from the database table and
// selects one.
//
// the table structure must be 'id', 'name' where 'id' is the item id
// and 'name' is the value.
//
// $name - database table name
// $selected - selected item id
// $class - Whether to get the column "class" from the table and assign its value
//          to a class. Pass the class name to be prepended. 
//

function generate_options_list($name,$selected='', $class = null) {

 global $db;
 global $cookie_language;
 global $language_in;


 $sql = 'SELECT id, ' . $language_in . ', name ';
 if ($class !== null) {
     $sql .= ', class ';
 }
 $sql .= ' FROM ' . $name . ' ORDER BY ' . $language_in;
 $r = $db->query ($sql) or error ('Critical Error', mysql_error () );

 $output = '';
 $className = '';
 while ($f = $db->fetcharray ($r) ) {
 	// Default value
 	if ($f[1] == '') {
 		$f[1] = $f['name'];
 	}
    if ($class !== null) {
        $className = ' class="'.$class.$f['class'].'"';
    }

  if ($f['id'] == $selected)
   $output .= '<option value="'.$f['id'].'" selected="selected"'.$className.'>'.$f[1].'</option>';
  else
   $output .= '<option value="'.$f['id'].'"'.$className.'>'.$f[1].'</option>';
 }

 return $output;

}

// ----------------------------------------------------------------------------
// generate_checkbox_list($table_name,$form_name,$selected='')
//
// generates <input type="checkbox" name="" value=""> tags for all 
// the items from the database table and checks the selected ones.
//
// the table structure must be 'id', 'name' where 'id' is the item id
// and 'name' is the value.
//
// $table_name - database table name
// $form_name - form field name
// $selected - selected item ids, can be one or several
// $row_count - number of items per row
function generate_checkbox_list( $table_name, $form_name, $selected = '', $row_count = 3 )
{
	global $db, $cookie_language, $language_in;
	
	$sql = 'SELECT id, ' . $language_in . ', name FROM ' . $table_name . ' ORDER BY ' . $language_in;
	$r = $db->query ($sql) or error ('Critical Error', mysql_error () );
	
	$checkbox_list = array();
	$i = 1;

	while( $f = $db->fetcharray($r) )
	{
		// Default value
		if ($f[1] == '')
			$f[1] = $f['name'];

		$checkbox_list[$i]['id'] = $f['id'];
		$checkbox_list[$i]['name'] = $f[1];
		
		$i++;
	}
	
	return $checkbox_list;
}

// ----------------------------------------------------------------------------
// admin_generate_checkbox_list($table_name,$form_name,$selected='')
//
// generates <input type="checkbox" name="" value=""> tags for all 
// the items from the database table and checks the selected ones.
//
// the table structure must be 'id', 'name' where 'id' is the item id
// and 'name' is the value.
//
// $table_name - database table name
// $form_name - form field name
// $selected - selected item ids, can be one or several
//

function admin_generate_checkbox_list($table_name,$form_name,$selected='') {

 global $db;
 global $cookie_language;
 global $language_in;

 $sql = 'SELECT id, ' . $language_in . ', name FROM ' . $table_name . ' ORDER BY ' . $language_in;
 $r = $db->query ($sql) or error ('Critical Error', mysql_error () );

 $output = '';
 $x = 0;

 $output = '<table><tr><td width="33%" align="left" valign="top">';

 while ($f = $db->fetcharray ($r) ) {
 
 	// Default value
 	if ($f[1] == '')
 		$f[1] = $f['name'];
 
  if ((!is_array($selected) && $f['id'] == $selected) OR (is_array($selected) && in_array($f['id'], $selected))) 
   $output.= '<input type="checkbox" name="' . $form_name . '[]" value="'. $f['id'] . '" CHECKED />' . $f[1] . '<br />';
  else
   $output.= '<input type="checkbox" name="' . $form_name . '[]" value="'. $f['id'] . '" />' . $f[1] . '<br />';

  $x++;

  if ($x >= $db->numrows($r) / 3) {
   $output .= '</td><td width="33%" align="left" valign="top">';
   $x=0;
  }
 }

 $output .= '</td></tr></table>';

 return $output;

}

?>