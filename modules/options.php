<?php

if (!defined('PMR') || (defined('PMR') && PMR != 'true')) die();

echo table_header ( $lang['Settings'] );

?>

<!-- Choose language and template forms : start -->

   <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
     <td align="center" valign="middle">

    <?php echo $lang['Language']; ?>:
     <br />
      <form action="" method="POST">
       <select name="option_language" onchange="this.form.submit();" style="width:70px;">

		<?php
		
		 // Read /languages folder
		 $option_language = array();
		 $option_handle = opendir ( PATH . '/languages');
		 while (false !== ($file = readdir($option_handle)))
		  {
		   // We select only files containing .lng.php pattern
		   if ( preg_match('.lng.php', $file) && $file != 'svenska.lng.php' )
		   {
		   		$add_lang = explode ('.', $file);
		  		$option_language[] = $add_lang[0];
		  	}
		  }
		  
		  if (!empty($option_language) && $option_language[0] != '')
		  {
		  	asort($option_language);	  	
		  	foreach ($option_language AS $key => $value)
		  	{
		  		$sel = ($cookie_language == $value) ? 'selected' : '';
		  		echo '<option value="' . $value . '" ' . $sel . '>' . ucfirst($value) . '</option>';
		  	}
		  }
		 closedir ($option_handle);
		
		?>

       </select>
      </form>

     </td>
     <td align="center" valign="middle">

    <?php echo $lang['Template']; ?>:
     <br />
      <form action="" method="POST">
       <select name="option_template" onchange="this.form.submit();" style="width:70px;">

		<?php
		
		 // Read /templates folder
		 $option_template = array();
		 $option_handle = opendir ( PATH . '/templates');
		 while (false !== ($file = readdir($option_handle)))
		  {
		   // Omit the files and directories we don't need
		   if ( $file != 'index.html' && $file != 'index.php' && $file != 'php.ini' && strpos('._~', $file[0]) === false )
		    {
		    	$option_template[] = $file;
		    }
		  }
		  
		  if (!empty($option_template) && $option_template[0] != '')
		  {
		  	asort($option_template);
		  	foreach ($option_template AS $key => $value)
		  	{
		  		$sel = ($cookie_template == $value) ? 'selected' : '';
		  		echo '<option value="' . $value . '" ' . $sel . '>' . ucfirst($value) . '</option>';
		  	}
		  }
		 closedir ($option_handle);
		
		?>

       </select>
      </form>

     </td>
    </tr>
   </table>

<?php echo table_footer(); ?>