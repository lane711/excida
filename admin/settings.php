<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['Admin_Configuration_Settings'];

include PATH . '/admin/template/header.php';

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
{
	include ( PATH . '/admin/navigation.php' );
  	$whereClause = "";
  	$defaultWhereClause=" site_id=0";
  	if($session->fetch('role')=="SUPERUSER")
  		$whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
  	else
  		$whereClause=" site_id=0";

	// Make sure this administrator have access to this script
	adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

	// If the form is submitted we save the data
	if ( $_POST['submit'] == true )
	{
		echo table_header ( $lang['Information'] );

		$form = array_map ( 'safehtml', $_POST );

	    // Initially we think that no errors were found
	    $count_error = 0;
	    
		// Validate language before setting it
		switch ($form['language']) {
	    case 'svenska':
	        $form['language'] = 'swedish';
	        break;
		}
		if (!file_exists(PATH . '/languages/' . $form['language'] . '.lng.php'))
		{
			$error_msg[] = 'The default language you specified does not exist. All available languages can be viewed in the \'languages/\' directory.';
			$count_error++;
		}
		elseif (empty($installed_languages[$form['language']]))
	    {
	        $error_msg[] = 'The default language you specified is not enabled. All available languages can be set in the \'languages/settings.php\' file.';
	        $count_error++;
	    }
	
		// Validate template before setting it
		if (!is_dir(PATH . '/templates/' . $form['template']))
		{
			$error_msg[] = 'The default template you specified does not exist. All available templates can be viewed in the \'templates/\' directory.';
			$count_error++;
		}
		
		if ($count_error == 0)
	     {
	      // Update the configuration records in the database
	
	      $sql = 'SELECT * FROM ' . CONFIGURATION_TABLE .' WHERE '.$whereClause;
	      $error = 0;
	      $alteration = "";
	    	$r = $db->query( $sql );
  			if($db->numrows( $r )>0){
  				//var_dump($form[]);
	      		while ($f = $db->fetcharray($r))
	       		{
		        	$name = $f['name'];
		        	$sql = 'UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form[$name] . '" WHERE name = "' . $name . '" AND '.$whereClause.' LIMIT 1';
					$db->query( $sql ) or error ('Critical Error', mysql_error () );
					

	       		}	
	      	}
			else
			{
				$sql = 'SELECT * FROM ' . CONFIGURATION_TABLE .' WHERE '.$defaultWhereClause;
	      		$r = $db->query( $sql );
	      		$sql = 'INSERT INTO ' . CONFIGURATION_TABLE . '(site_id,name,val,descr)
					VALUES';
					$flag= false;
				while ($f = $db->fetcharray($r))
	       		{
	       			$flag = true;
		        	$name = mysql_real_escape_string($f['name']);
		        	$val = mysql_real_escape_string($f['val']);
		        	$descr = mysql_real_escape_string($f['descr']);
					$sql.='("'.$session->fetch('site_id').'","'.$name.'","'.$val.'","'.$descr.'"),';
				}	
				if($flag){
					$sqln = substr($sql, 0,strlen($sql)-1);
					//$db->query( $sqln ) or error ('Critical Error', mysql_error () );
				}
			}
	      	echo $lang['Admin_Settings_Updated'];
	
	      
	
	     }
	     	else
	     {
	     
	     	// Output the errors
	     	echo '<span class="warning">' . $count_error . ' ' . $lang['Errors_Found'] . ':<br><br>';
	     	foreach ($error_msg AS $key => $value)
	     	{
	     		echo $value . '<br>';
	     	}
	     	echo '</span>';
	     
	     }

    echo table_footer ( );

   }

  // Fetching the configuration data from the database
  $sql = 'SELECT * FROM ' . CONFIGURATION_TABLE." WHERE ".$whereClause;
  $r = $db->query( $sql." ORDER BY name DESC" );
  
  if($db->numrows( $r )>0){}
  	else{
  		$sql = 'SELECT * FROM ' . CONFIGURATION_TABLE." WHERE ".$defaultWhereClause;
  		$r = $db->query( $sql." ORDER BY name DESC" );
  	}
  // Generating the configuration form
  echo table_header ( $lang['Admin_Configuration_Settings'] );

  echo '<form action = "' . URL . '/admin/settings.php" method="post">';

	while ($f = $db->fetcharray( $r ))
	{
		$tooltip = '<img src="' . URL . '/admin/template/images/help.png" class="help tooltip" title="' . $f['descr'] . '">';
		echo configform( $f['name'], $f['val'], '', $tooltip );
	}
   
   echo '<div class="clearfix"></div>';

  echo ' <div class="row"><input type="Submit" name="submit" value="' . $lang['Admin_Settings_Submit'] . '"></div>';

  echo ' 
       </form>'; 

  echo table_footer ();

 }

else
{
	header( 'Location: index.php' );
	exit();
}

include PATH . '/admin/template/footer.php';

?>