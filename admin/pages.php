<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

include ( PATH . '/admin/template/header.php' );

// If logged in we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
 {
 	$whereClause = "";
  	if($session->fetch('role')=="SUPERUSER")
  		$whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
  // Include navigation panel
  $session->set('navigation', '');
  include ( PATH . '/admin/navigation.php' );
  
	// Add CMS Entry
	if ($_GET['action'] == 'add')
	{
		// If adding
		if ($_POST['submit'] == true)
		{
			if ($_POST['menu'] != '' || $_POST['text'] != '')
			{
				$form = array_map('safehtml', $_POST);
				
				// If they have a string, it must be unique
				if ($form['string'] != '')
				{
					$sql = "SELECT * FROM " . PAGES_TABLE . " WHERE string = '" . $form['string'] . "' AND ".$whereClause;
					$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
					if ($db->numrows( $r ) > 0)
					{
						echo $lang['Admin_CMS_String_Error'];
					}
					else
					{
						// Convert any spaces to dashes
						if ($form['string'] != '')
							$form['string'] = str_replace( ' ', '-', $form['string'] );
					
						$sql = "
						INSERT INTO " . PAGES_TABLE . "
						( 
							menu, site_id, menu2, menu3, menu4, menu5, menu6, menu7, menu8, menu9, menu10, menu11, menu12, menu13, menu14, menu15, menu16, menu17, menu18, menu19, menu20, menu21, menu22, menu23, menu24, menu25, menu26, menu27, menu28, menu29, menu30, text, text2, text3, text4, text5, text6, text7, text8, text9, text10, text11, text12, text13, text14, text15, text16, text17, text18, text19, text20, text21, text22, text23, text24, text25, text26, text27,	text28, text29,	text30, string, status, date, navigation
						)
						VALUES
						(
							'" . $form['menu'] . "',
							'" . $session->fetch('site_id') . "',
							'" . $form['menu2'] . "',
							'" . $form['menu3'] . "',
							'" . $form['menu4'] . "',
							'" . $form['menu5'] . "',
							'" . $form['menu6'] . "',
							'" . $form['menu7'] . "',
							'" . $form['menu8'] . "',
							'" . $form['menu9'] . "',
							'" . $form['menu10'] . "',
							'" . $form['menu11'] . "',
							'" . $form['menu12'] . "',
							'" . $form['menu13'] . "',
							'" . $form['menu14'] . "',
							'" . $form['menu15'] . "',
							'" . $form['menu16'] . "',
							'" . $form['menu17'] . "',
							'" . $form['menu18'] . "',
							'" . $form['menu19'] . "',
							'" . $form['menu20'] . "',
							'" . $form['menu21'] . "',
							'" . $form['menu22'] . "',
							'" . $form['menu23'] . "',
							'" . $form['menu24'] . "',
							'" . $form['menu25'] . "',
							'" . $form['menu26'] . "',
							'" . $form['menu27'] . "',
							'" . $form['menu28'] . "',
							'" . $form['menu29'] . "',
							'" . $form['menu30'] . "',
							'" . $form['text'] . "',
							'" . $form['text2'] . "',
							'" . $form['text3'] . "',
							'" . $form['text4'] . "',
							'" . $form['text5'] . "',
							'" . $form['text6'] . "',
							'" . $form['text7'] . "',
							'" . $form['text8'] . "',
							'" . $form['text9'] . "',
							'" . $form['text10'] . "',
							'" . $form['text11'] . "',
							'" . $form['text12'] . "',
							'" . $form['text13'] . "',
							'" . $form['text14'] . "',
							'" . $form['text15'] . "',
							'" . $form['text16'] . "',
							'" . $form['text17'] . "',
							'" . $form['text18'] . "',
							'" . $form['text19'] . "',
							'" . $form['text20'] . "',
							'" . $form['text21'] . "',
							'" . $form['text22'] . "',
							'" . $form['text23'] . "',
							'" . $form['text24'] . "',
							'" . $form['text25'] . "',
							'" . $form['text26'] . "',
							'" . $form['text27'] . "',
							'" . $form['text28'] . "',
							'" . $form['text29'] . "',
							'" . $form['text30'] . "',
							'" . $form['string'] . "',
							'" . $form['status'] . "',
							'" . date('Y-m-d H:i:s') . "',
							'" . $form['navigation'] . "'
						)";
						$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
						echo $lang['Admin_Item_Added'];
					}
				}
			}
			else
			{
				// Output error, one has to be filled in...
			}
		}

		echo table_header ( $lang['Admin_CMS_Add'] );

  		echo '
  		<form action = "' . URL . '/admin/pages.php?action=add" method="POST" name="form">
  		<table cellpadding="5" cellspacing="2" border="0" width="100%" align="center">
		<tr>
			<td align="left" colspan="2">' . $lang['Admin_CMS_Title'] . '<br /><br />';
			
			// Show all available languages
			foreach ($installed_languages AS $language1 => $key1)
			{
				$key1 = str_replace( 'name', 'menu', $key1 );
				echo '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp; ';
			}
			
			echo '<br /><br />';
			
			// Display text boxes for every language
			$num = 1;
			foreach ($installed_languages AS $language1 => $key1)
			{
				// Grab the right text for each textarea
				$strip = str_replace( 'name', 'menu', $key1 );
				$key1 = str_replace( 'name', 'menu', $key1 );

				if ($num == 1)
					$display = 'normal';
				else
					$display = 'none';
				
				echo '	
				<div style="display: ' . $display . ';" id="' . $key1 . '">
				<input type="text" size="100" maxlength="255" name="' . $key1 . '" id="' . $key1 . '" value="' . $form[$strip] . '">
				</div>
				';
				
				$num++;
			}
			
			echo '
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">What should the title of the page be called? For example, \'Company History\' or \'Frequently Asked Questions.\'</span>
			</td>
		</tr>
		<tr>
			<td align="left" style="width: 10%">' . $lang['Admin_CMS_String'] . '</td>
			<td align="left">' . URL . '/Pages/<input type="text" name="string" size="35" maxlength="255" value="' . $form['string'] . '">.html</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">Do you want to use SEO-friendly URLs? Please note that mod_rewrite must be enabled. Only letters, numbers, dashes. Spaces will be converted to dashes.</span>
			</td>
		</tr>
		<tr>
			<td align="left" style="width: 10%">' . $lang['Admin_CMS_Status'] . '</td>
			<td align="left"><select name="status"><option value="1" selected>Enabled</option><option value="0">Disabled</option></select></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">Should this CMS Page be active and publicly viewable? You can change this setting at any time.</span>
			</td>
		</tr>
		<tr>
			<td align="left" style="width: 10%">' . $lang['Admin_CMS_Navigation'] . '</td>
			<td align="left"><select name="navigation"><option value="1" selected>Yes</option><option value="0">No</option></select></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">If yes, it will appear as a standalone link in the navigation. Otherwise, it will be under "Resources"</span>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">' . $lang['Admin_CMS_Content'] . ':<br /><br />';
			
			// Show all available languages
			foreach ($installed_languages AS $language1 => $key1)
			{
				// Grab the right text for each textarea
				$key1 = str_replace( 'name', 'text', $key1 );
				
				echo '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp; ';
			}
			
			echo '</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
		';

			// Display text boxes for every language
			$num = 1;
			foreach ($installed_languages AS $language1 => $key1)
			{
				// Grab the right text for each textarea
				$strip = str_replace( 'name', 'text', $key1 );
				$key1 = str_replace( 'name', 'text', $key1 );

				if ($num == 1)
					$display = 'normal';
				else
					$display = 'none';
				
				echo '	
				<div style="display: ' . $display . ';" id="' . $key1 . '">
				<textarea class="ckeditor" cols="100" rows="20" name="' . $key1 . '" id="' . $key1 . '">' . stripslashes( $form[$strip] ) . '</textarea>
				</div>
				';
				
				$num++;
			}

			echo '			
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">If you\'d like to enter in HTML directly, click \'Source\' at the top-left of the edit box. Note: Only the main language will be able to use the WYSIWYG editor to increase performance of the page.</span>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<input type="submit" name="submit" value="' . $lang['Admin_CMS_Add'] . '">
			</td>
		</tr>
		</table>
		';

		echo table_footer();
	}  

	// Delete CMS Entry
	if ($_GET['id'] != '' && $_GET['action'] == 'delete')
	{
		$sql = "DELETE FROM " . PAGES_TABLE . " WHERE id = '" . intval($_GET['id']) . "' AND ".$whereClause." LIMIT 1";
		$r = $db->query( $sql ) OR error( mysql_error () );
		echo table_header ( $lang['Information'] );
		echo $lang['Admin_Item_Removed'];
		echo table_footer();
	}

	// Add CMS Entry
	if ($_REQUEST['action'] == 'edit' && $_REQUEST['id'] != '')
	{
		// If adding
		if ($_POST['submit'] == true)
		{
			echo table_header ( $lang['Information'] );
		
			if ($_POST['menu'] != '' || $_POST['text'] != '')
			{
				$form = array_map('safehtml', $_POST);
				
				// If they have a string, it must be unique
				if ($form['string'] != '')
				{
					$sql = "SELECT * FROM " . PAGES_TABLE . " WHERE string = '" . $form['string'] . "' AND id != '" . intval($_POST['id']) . "' AND ".$whereClause;
					$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
					if ($db->numrows( $r ) > 0)
					{
						echo $lang['Admin_CMS_String_Error'];
					}
						else
					{
						// Convert any spaces to dashes
						if ($form['string'] != '')
							$form['string'] = str_replace( ' ', '-', $form['string'] );
					
						$sql = "
						UPDATE " . PAGES_TABLE . " SET
							menu = '" . $form['menu'] . "', menu2 = '" . $form['menu2'] . "', menu3 = '" . $form['menu3'] . "', menu4 = '" . $form['menu4'] . "', menu5 = '" . $form['menu5'] . "', menu6 = '" . $form['menu6'] . "', menu7 = '" . $form['menu7'] . "', menu8 = '" . $form['menu8'] . "', menu9 = '" . $form['menu9'] . "', menu10 = '" . $form['menu10'] . "', menu11 = '" . $form['menu11'] . "', menu12 = '" . $form['menu12'] . "', menu13 = '" . $form['menu13'] . "', menu14 = '" . $form['menu14'] . "', menu15 = '" . $form['menu15'] . "', menu16 = '" . $form['menu16'] . "', menu17 = '" . $form['menu17'] . "', menu18 = '" . $form['menu18'] . "', menu19 = '" . $form['menu19'] . "', menu20 = '" . $form['menu20'] . "', menu21 = '" . $form['menu21'] . "', menu22 = '" . $form['menu22'] . "', menu23 = '" . $form['menu23'] . "', menu24 = '" . $form['menu24'] . "', menu25 = '" . $form['menu25'] . "',  menu26 = '" . $form['menu26'] . "', menu27 = '" . $form['menu27'] . "', menu28 = '" . $form['menu28'] . "', menu29 = '" . $form['menu29'] . "', menu30 = '" . $form['menu30'] . "', text = '" . $form['text'] . "', text2 = '" . $form['text2'] . "', text3 = '" . $form['text3'] . "', text4 = '" . $form['text4'] . "', text5 = '" . $form['text5'] . "', text6 = '" . $form['text6'] . "', text7 = '" . $form['text7'] . "', text8 = '" . $form['text8'] . "', text9 = '" . $form['text9'] . "', text10 = '" . $form['text10'] . "', text11 = '" . $form['text11'] . "', text12 = '" . $form['text12'] . "', text13 = '" . $form['text13'] . "', text14 = '" . $form['text14'] . "', text15 = '" . $form['text15'] . "', text16 = '" . $form['text16'] . "', text17 = '" . $form['text17'] . "', text18 = '" . $form['text18'] . "', text19 = '" . $form['text19'] . "', text20 = '" . $form['text20'] . "', text21 = '" . $form['text21'] . "', text22 = '" . $form['text22'] . "', text23 = '" . $form['text23'] . "', text24 = '" . $form['text24'] . "', text25 = '" . $form['text25'] . "', text26 = '" . $form['text26'] . "', text27 = '" . $form['text27'] . "',	text28 = '" . $form['text28'] . "', text29 = '" . $form['text29'] . "',	text30 = '" . $form['text30'] . "', string = '" . $form['string'] . "', status = '" . $form['status']. "', navigation = '" . $form['navigation'] . "' 
						WHERE id = '" . intval($form['id']) . "' AND ".$whereClause;
						$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
						echo $lang['Admin_Item_Updated'];
					}
				}
			}
			else
			{
				// Output error, one has to be filled in...
			}
			echo table_footer();
		}

		echo table_header ( $lang['Admin_CMS_Edit'] );
		
		// Grab the CMS Article data
		$sql = "SELECT * FROM " . PAGES_TABLE . " WHERE id = '" . intval($_REQUEST['id']) . "' AND ".$whereClause." LIMIT 1";
		$r = $db->query( $sql ) OR error ( mysql_error() );
		$form = $db->fetcharray ( $r );
		
		// Status
		if ($form['status'] == 0)
			$status0 = ' selected';
		elseif ($form['status'] == 1)
			$status1 = ' selected';

		// Navigation
		if ($form['navigation'] == 0)
			$navigation0 = ' selected';
		elseif ($form['navigation'] == 1)
			$navigation1 = ' selected';
		
		// Make all fields editable
		foreach ($installed_languages AS $language1 => $key1)
		{
			$key1 = str_replace( 'name', 'text', $key1 );
			$form[$key1] = unsafehtml($form[$key1]);
		}

  		echo '
  		<form action = "' . URL . '/admin/pages.php?action=edit" method="POST" name="form">
  		<input type="hidden" name="id" value="' . $_REQUEST['id'] . '">
  		<table cellpadding="5" cellspacing="2" border="0" width="100%" align="center">
		<tr>
			<td align="left" colspan="2">' . $lang['Admin_CMS_Title'] . '<br /><br />';
			
			// Show all available languages
			foreach ($installed_languages AS $language1 => $key1)
			{
				$key1 = str_replace( 'name', 'menu', $key1 );
				echo '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp; ';
			}
			
			echo '<br /><br />';
			
			// Display text boxes for every language
			$num = 1;
			foreach ($installed_languages AS $language1 => $key1)
			{
				// Grab the right text for each textarea
				$strip = str_replace( 'name', 'menu', $key1 );
				$key1 = str_replace( 'name', 'menu', $key1 );

				if ($num == 1)
					$display = 'normal';
				else
					$display = 'none';
				
				echo '	
				<div style="display: ' . $display . ';" id="' . $key1 . '">
				<input type="text" size="100" maxlength="255" name="' . $key1 . '" id="' . $key1 . '" value="' . $form[$strip] . '">
				</div>
				';
				
				$num++;
			}
			
			echo '
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">What should the title of the page be called? For example, \'Company History\' or \'Frequently Asked Questions.\'</span>
			</td>
		</tr>
		<tr>
			<td align="left" style="width: 10%">' . $lang['Admin_CMS_String'] . '</td>
			<td align="left">' . URL . '/Pages/<input type="text" name="string" size="35" maxlength="255" value="' . $form['string'] . '">.html</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">Do you want to use SEO-friendly URLs? Please note that mod_rewrite must be enabled. Only letters, numbers, dashes. Spaces will be converted to dashes.</span>
			</td>
		</tr>
		<tr>
			<td align="left" style="width: 10%">' . $lang['Admin_CMS_Status'] . '</td>
			<td align="left"><select name="status"><option value="1" ' . $status1 . '>Enabled</option><option value="0" ' . $status0 . '>Disabled</option></select></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">Should this CMS Page be active and publicly viewable? You can change this setting at any time.</span>
			</td>
		</tr>
		<tr>
			<td align="left" style="width: 10%">' . $lang['Admin_CMS_Navigation'] . '</td>
			<td align="left"><select name="navigation"><option value="1"' . $navigation1 . '>Yes</option><option value="0"' . $navigation0 . '>No</option></select></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">If yes, it will appear as a standalone link in the navigation. Otherwise, it will be under "Resources"</span>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">' . $lang['Admin_CMS_Content'] . ':<br /><br />';
			
			// Show all available languages
			foreach ($installed_languages AS $language1 => $key1)
			{
				// Grab the right text for each textarea
				$key1 = str_replace( 'name', 'text', $key1 );
				
				echo '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp; ';
			}
			
			echo '</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
		';

			// Display text boxes for every language
			$num = 1;
			foreach ($installed_languages AS $language1 => $key1)
			{
				// Grab the right text for each textarea
				$strip = str_replace( 'name', 'text', $key1 );
				$key1 = str_replace( 'name', 'text', $key1 );

				if ($num == 1)
					$display = 'normal';
				else
					$display = 'none';
				
				echo '	
				<div style="display: ' . $display . ';" id="' . $key1 . '">
				<textarea class="ckeditor" cols="100" rows="20" name="' . $key1 . '" id="' . $key1 . '">' . stripslashes( $form[$strip] ) . '</textarea>
				</div>
				';
				
				$num++;
			}

			echo '			
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<span style="color: #A3A3A3; font-size: 11px;">If you\'d like to enter in HTML directly, click \'Source\' at the top-left of the edit box. Note: Only the main language will use the WYSIWYG editor to increase performance of this page.</span>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="2">
			<input type="submit" name="submit" value="' . $lang['Admin_CMS_Edit'] . '">
			</td>
		</tr>
		</table>
		';

		echo table_footer();
	}  

	// Enable/disable CMS Entry
	if ($_GET['id'] != '' && ($_GET['action'] == 'disable' || $_GET['action'] == 'enable'))
	{
		echo table_header ( $lang['Admin_CMS_Status'] );
		
		if ($_GET['action'] == 'enable')
			$status = 1;
		else
			$status = 0;
			
		$sql = "UPDATE " . PAGES_TABLE . " SET status = '" . $status . "' WHERE id = '" . intval($_GET['id']) . "' AND ".$whereClause." LIMIT 1";
		$r = $db->query( $sql ) OR error( mysql_error () );
		echo $lang['Admin_Item_Updated'];
		
		echo table_footer();
	}

	// Hide/show pages in site navigation
	if ($_GET['id'] != '' && ($_GET['action'] == 'nav_show' || $_GET['action'] == 'nav_hide'))
	{
		echo table_header ( $lang['Admin_CMS_Navigation'] );
		
		if ($_GET['action'] == 'nav_show')
			$navigation = 1;
		else
			$navigation = 0;
			
		$sql = "UPDATE " . PAGES_TABLE . " SET navigation = '" . $navigation . "' WHERE id = '" . intval($_GET['id']) . "' AND ".$whereClause." LIMIT 1";
		$r = $db->query( $sql ) OR error( mysql_error () );
		echo $lang['Admin_Item_Updated'];
		
		echo table_footer();
	}		

	// Show all CMS Articles in system
	echo table_header ( $lang['Admin_CMS_Manager'] );
	
	$sql = 'SELECT * FROM ' . PAGES_TABLE . ' WHERE '.$whereClause.' ORDER BY menu ASC';
	$r = $db->query( $sql );
	
	echo '
	<a href="' . URL . '/admin/pages.php?action=add">Add Article</a> | <a href="' . URL . '/admin/pages.php">Manage Articles</a>
	<br /><br />
	';
	
	if ($f = $db->numrows( $r ) > 0)
	{
		
		echo '		
		<table width="100%" border="0" cellpadding="4" cellspacing="0" align="center">
		<tr>
			<td align="center"><b>ID</b></td>
			<td align="center"><b>Date Added</b></td>
			<td align="left"><b>Page Title</b></td>
			<td align="left"><b>Page URL</b></td>
			<td align="center"><b>Navigation</b></td>
			<td align="center"><b>Status</b></td>
			<td align="center"><b>Options</b></td>
		</tr>
		';
	
		while ($f = $db->fetcharray( $r ))
		{
		
			// Status
			if ($f['status'] == 0)
				$status = '<a href="' . URL . '/admin/pages.php?action=enable&id=' . $f['id'] . '">Disabled</a>';
			else
				$status = '<a href="' . URL . '/admin/pages.php?action=disable&id=' . $f['id'] . '">Enabled</a>';

			// Navigation
			if ( $f['navigation'] == 0 )
				$navigation = '<a href="' . URL . '/admin/pages.php?action=nav_show&id=' . $f['id'] . '">Hidden</a>';
			else
				$navigation = '<a href="' . URL . '/admin/pages.php?action=nav_hide&id=' . $f['id'] . '">Visible</a>';
				
			// URL
			if ($conf['rewrite'] == 'ON' && $f['string'] != '')
				$url = URL . '/Pages/' . $f['string'] . '.html';
			else
				$url = URL . '/pages.php?id=' . $f['id'];
		
			echo '
			<tr>
				<td align="center">' . $f['id'] . '</td>
				<td align="center">' . $f['date'] . '</td>
				<td align="left">
				
				<a href="' . $url . '" target="_blank">' . $f['menu'] . '</a>
				
				</td>
				<td align="left">
				<input type="text" value="' . $url . '" onClick="select()" size="50">
				</td>
				<td align="center">' . $navigation . '</td>
				<td align="center">' . $status . '</td>
				<td align="center"><a href="' . URL . '/admin/pages.php?action=edit&id=' . $f['id'] . '">Edit</a> | <a href="' . URL . '/admin/pages.php?action=delete&id=' . $f['id'] . '" onClick="return confirmDelete(\'Are you sure you want to delete?\')">Delete</a></td>
			</tr>
			';
		}
		
		echo '
		</table>
		';
	}
  	else
  {
	echo $lang['Admin_CMS_None_Error'];
  }
  
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