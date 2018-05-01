<?php

ob_start();
session_start();
set_time_limit(0);

$page = 'install';

define( 'PMR', 'true' );

include '../config.php';
include '../includes/functions/db.php';
include '../includes/functions/system.php';

// If resetting the installation
if ( $_REQUEST['reset'] == true )
{
	// Reset the configuration file
	$data = file_get_contents( 'config.sample.php' );
	if ( @file_put_contents( '../config.php', $data ) )
	{
		$reset = true;
	}
	else
	{
		$reset_failed = true;
	}
}

// Default step of the install process
if ( $_REQUEST['step'] == '' )
{
	$_SESSION['step'] = 1;
}
else
{
	$_SESSION['step'] = $_REQUEST['step'];
}

if ( $_SESSION['step'] > 2 )
{
	$db = new Dbaccess();
	$db->connect( $_SESSION['db_host'], $_SESSION['db_username'], $_SESSION['db_password'], $_SESSION['db_name'] );
}

?>

<html>

<!-- jQuery Library -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- jQuery qTip2 -->
<script src="http://cdn.jsdelivr.net/qtip2/2.2.0/jquery.qtip.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen" href="http://cdn.jsdelivr.net/qtip2/2.2.0/jquery.qtip.min.css">

<head>
	<title>RealtyScript <?php echo VERSION; ?> Installer</title>
	
	<style type="text/css">
	body
	{
		font-family: arial;
		font-size: 14px;
		color: #000;
		text-align:center;
	}	
	input, textarea, select
	{
		width:400px;
		height:40px;
		font-size:15px;
	}
	
	input[type=submit]
	{
		margin-top:15px;
		background-color:#fff;
		width:250px;
		border:1px solid #04aeda;
	}
	input[type=submit]:hover
	{
		background-color: #04aeda;
		color: #fff;
	}
	.help
	{
		width:20px;
		height:20px;
	}
	.ui-tooltip .ui-tooltip-content,
	.ui-tooltip p,
	.ui-tooltip ul,
	.ui-tooltip li,
	.ui-tooltip,
	.qtip {
	    max-width: 300px;
	    min-width: 75px;
	    font-size: 16px;
	    line-height: 20px;
	    text-align: center;
	    border-color: #04aeda;
	    font-family: arial;
	    color: #fff;
	    background-color: #04aeda;
	}
	.error
	{
		margin-top:10px;
		margin-bottom:10px;
		width:98%;
		background-color:red;
		color:#fff;
		padding:10px;
	}
	.success
	{
		margin-top:10px;
		margin-bottom:10px;
		width:98%;
		background-color:green;
		color:#fff;
		padding:10px;
	}
	.form
	{
		font-family: arial;
		font-size: 12px;
		color: #000;
	}
	.small
	{
		font-size: 11px;
	}
	</style>
	
	<script type="text/javascript">
	jQuery(document).ready(function(){
		//jQuery('#formID').validationEngine();
	
		jQuery('.tooltip').qtip({
			show: 'click',
			hide: 'click'
		});
	});
	</script>
</head>

<body>

<?php echo main_header( 'RealtyScript ' .  VERSION . ' Installer' ); ?>

<br />

<a href="index.php?reset=true" class="small">Did you make a mistake? You can restart the installer.</a>

<?php

if ( $reset == true )
{
	echo '<div class="success">The installer has been reset.</div>';
}
elseif ( $reset_failed == true )
{
	echo '<div class="error">The installer could not be reset because config.php is not writeable. Please chmod 755 or 777 and try again.</div>'; 
}
else
{
	echo '<br /><br />';
}

?>

Welcome to RealtyScript! This installer wizard will attempt to configure the software for you, however it relies on you to enter in the correct settings. If in doubt, please check with your web hosting provider. You can also find a wealth of information through our <a href="http://realtyscript.com/support.php" target="_blank"><b>Support Center</b></a>, such as our community forums, helpful guides, and FAQ.

<br /><br />

If you would rather not install the software yourself, expert assistance is available. We offer paid installation, which you can purchase on our <a href="http://realtyscript.com/pricing.php" target="_blank"><b>pricing page</b></a>.

<br /><br />

<?php echo main_header( 'Installation Progress: Step ' . $_SESSION['step'] . ' of 3' ); ?>

<br /><br />

<?php

if ( $_SESSION['step'] == 1 )
{
	echo sub_header( 'Testing Initial System Requirements' );

	echo '
	<br /><br />
	<span style="font-family : arial; font-size : 12px; color : #333333;">
	';

	$proceed = true;

	// Check server dependencies/versions
	
	echo 'Checking to see if ionCube is installed on the server... ';
	$extensions = get_loaded_extensions();
	if ( in_array( 'ionCube Loader', $extensions ) )
	{
		echo '<span style="color:green"><b>PASSED!</b> You have ionCube installed.</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> ionCube was not detected. Please contact your web hosting provider to enable ionCube on your account.</span>';
	}
	
	echo '<br /><br />';
	
	echo 'Checking if PHP is at least v5.3 or higher... ';
	if ( phpversion() >= 5.3 )
	{
		echo '<span style="color:green"><b>PASSED!</b> You have PHP v' . phpversion() . '</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> You have PHP v' . phpversion() . '</span>';
	}
	
	echo '<br /><br />';
	
	/*
	$result = $db->query( 'SELECT VERSION() AS version' );
	if ( $db->numrows( $result ) > 0 )
	{
		$row = $db->fetcharray( $result );
		$match = explode( '.', $row['version'] );
	}
	
	echo 'Checking to see if MySQL is at least v5.1 or higher... ';
	if ( $row['version'] >= 5.1 )
	{
		echo '<span style="color:green"><b>PASSED!</b> You have MySQL v' . $row['version'] . '</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> You have MySQL v' . $row['version'] . '</span>';
	}
	echo '<br /><br />';
	*/
		
	echo 'Checking if GD is at least v2.0 or higher... ';
	if ( gd_version() >= 2.0 )
	{
		echo '<span style="color:green"><b>PASSED!</b> You have GD v' . gd_version() . '</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> You have GD v' . gd_version() . '</span>';
	}
	
	echo '<br /><br />';
	
	$path = str_replace( '/install', '', realpath( dirname( __FILE__ ) ) );
	
	echo 'Checking to see if the configuration file (' . $path . '/config.php) has the correct permissions... ';
	if ( is_writeable( $path . '/config.php' ) )
	{
		echo '<span style="color:green"><b>PASSED!</b> The file is writeable.</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> The file is not writeable. Please try setting chmod 777 or 755 (write permissions).</span>';
	}
	
	echo '<br /><br />';
	
	echo 'Checking to see if the gallery directory (' . $path . '/media/gallery/) is writeable... ';
	if ( is_writeable( $path . '/media/gallery/' ) )
	{
		echo '<span style="color:green"><b>PASSED!</b> The directory is writeable.</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> The directory is not writeable. Please try setting the directory chmod 777 or 755 (write permissions).</span>';
	}
	
	echo '<br /><br />';
	
	echo 'Checking to see if the photos directory (' . $path . '/media/photos/) is writeable... ';
	if ( is_writeable( $path . '/media/photos/' ) )
	{
		echo '<span style="color:green"><b>PASSED!</b> The directory is writeable.</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> The directory is not writeable. Please try setting the directory chmod 777 or 755 (write permissions).</span>';
	}
	
	echo '<br /><br />';
	
	echo 'Checking to see if the cache directory (' . $path . '/media/cache/) is writeable... ';
	if ( is_writeable( $path . '/media/cache/' ) )
	{
		echo '<span style="color:green"><b>PASSED!</b> The directory is writeable.</span>';
	}
	else
	{
		$proceed = false;
		echo '<span style="color:red"><b>FAILED!</b> The directory is not writeable. Please try setting the directory chmod 777 or 755 (write permissions).</span>';
	}
	
	echo '<br /><br />';
	
	if ( $proceed == false )
	{
		echo '<span style="color:red;"><b>FAILED!</b> The installer could not continue. Please review the errors above and try running the installer again.</span><br /><br />';
	}
	else
	{
		echo '<b>PASSED!</b> <a href="index.php?step=2">Continue to Step 2</a>';
	}
	
	echo '
	<br /></span>
	';
}

if ( $_SESSION['step'] == 2 )
{
	if ( $_POST['submit'] == true ) 
	{
		$req_fields = array(
			'url', 'license', 'path', 'db_host', 'db_name', 'db_username', 'db_password', 'media_url'
		);
		$errors = 0;
		foreach ( $req_fields AS $key )
		{
			if ( $_POST[$key] == '' )
			{
				$errors++;
				$error_msg .= "<li>The following field is blank: " . $key . "</li>";
			}
		}
		
		// Test database details
		$db = new Dbaccess();
		
		if ( !$db->connect( $_POST['db_host'], $_POST['db_username'], $_POST['db_password'], $_POST['db_name'] ) ) 
		{
			$error++;
			$error_msg .= "<li>The database details are incorrect. Please check the MySQL username, password, database name, and host to ensure they are all set properly.</li>";
		}
	
		if ( $errors == 0 )
		{
			// Save database details
			$_SESSION['db_host'] = $_POST['db_host'];
			$_SESSION['db_username'] = $_POST['db_username'];
			$_SESSION['db_password'] = $_POST['db_password'];
			$_SESSION['db_name'] = $_POST['db_name'];
			$_SESSION['path'] = $_POST['path'];
			$_SESSION['table_prefix'] = $_POST['table_prefix'];
			$_SESSION['media_url'] = $_POST['media_url'];
		
			$data = file_get_contents( 'config.sample.php' );
			$all_fields = array(
				'url', 'license', 'copyright_license', 'path', 'db_host', 'db_name', 'db_username', 'db_password', 'table_prefix', 'media_url'
			);
			foreach ( $all_fields AS $key )
			{
				$data = str_replace( '{' . strtoupper( $key ) . '}', $_POST[$key], $data );
			}
			
			// Save the config file
			if ( file_put_contents( '../config.php', $data ) )
			{	
				echo '<div class="success"><b>Success!</b> The configuration file has been updated and saved. <a href="index.php?step=3">Continue to step 3</a>.</div>';
			}
			else
			{
				echo '<div class="error"><b>Error:</b> There was an error saving the configuration file. Please make sure the file is chmod 777 or 755 and try again.</div>';
			}
		}
		else
		{
			echo '<div class="error"><b>Error:</b> Please review the following errors and try again: <ul>' . $error_msg . '</ul></div>';
		}
	}

	echo sub_header( 'General Settings (Required)' );
	
	// Defaults
	$url = ( $_REQUEST['url'] != '' ) ? $_REQUEST['url'] : 'http://' . $_SERVER['SERVER_NAME'];
	$media_url = ( $_REQUEST['media_url'] != '' ) ? $_REQUEST['media_url'] : 'http://' . $_SERVER['SERVER_NAME'] . '/media';
		
	$path = ( $_REQUEST['path'] != '' ) ? $_REQUEST['path'] : $_SERVER['DOCUMENT_ROOT'] . $install_folder;
	$table_prefix = ( $_REQUEST['table_prefix'] != '' ) ? $_REQUEST['table_prefix'] : 'rs_';
	$db_host = ( $_REQUEST['db_host'] != '' ) ? $_REQUEST['db_host'] : 'localhost';
	
	?>
	
	<br /><br />
	
	<form method="post" action="index.php" id="formID">
	<input type="hidden" name="step" value="2">
	<table width="50%" border="0" cellpadding="4" align="center" class="form">
	
	<tr>
		<td align="right" style="width:20%">License Key</td>
		<td align="left"><input type="text" name="license" value="<?php echo $_REQUEST['license']; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="You can generate a license key in the <a href='http://realtyscript.com/login.php' target='_blank'>Client Center</a>"></td>
	</tr>
	
	<tr>
		<td align="right">Full URL</td>
		<td align="left"><input type="text" name="url" value="<?php echo $url; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="The full URL to your installation (e.g., http://www.domain.com, http://www.domain.com/realestate, etc.)"></td>
	</tr>

	<tr>
		<td align="right">Full Media URL</td>
		<td align="left"><input type="text" name="media_url" value="<?php echo $media_url; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="The full URL to all media/images (e.g., http://www.domain.com/media)"></td>
	</tr>
	
	<tr>
		<td align="right">Installation Full Path</td>
		<td align="left"><input type="text" name="path" value="<?php echo $path; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="The full path to your software. Usually, the detected location is correct."></td>
	</tr>
	
	<tr>
		<td align="right">Database Name:</td>
		<td align="left"><input type="text" name="db_name" value="<?php echo $_REQUEST['db_name']; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="MySQL database name. You must create this using your web hosting control panel / CPanel."></td>
	</tr>
	
	<tr>
		<td align="right">Database Username:</td>
		<td align="left"><input type="text" name="db_username" value="<?php echo $_REQUEST['db_username']; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="MySQL username. You must create this using your web hosting control panel / CPanel."></td>
	</tr>
	
	<tr>
		<td align="right">Database Password:</td>
		<td align="left"><input type="text" name="db_password" value="<?php echo $_REQUEST['db_password']; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="MySQL password. You must create this using your web hosting control panel / CPanel."></td>
	</tr>
	
	</table>
	
	<br /><br />
	
	<?php echo sub_header( 'Advanced Settings (Optional)' ); ?>
	
	<table width="50%" border="0" cellpadding="4" align="center" class="form">
	
	<tr>
		<td align="right" style="width:20%">Copyright Removal License Key</td>
		<td align="left"><input type="text" name="copyright_license" value="<?php echo $_REQUEST['copyright_license']; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="Optional. You can remove the 'Powered by RealtyScript' by purchasing branding rights on our <a href='http://realtyscript.com/pricing.php' target='_blank'>pricing page</a>"></td>
	</tr>
	
	<tr>
		<td align="right">Database Host:</td>
		<td align="left"><input type="text" name="db_host" value="<?php echo $db_host; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="It is usually localhost, however your web hosting provider may use something else. Please check with them."></td>
	</tr>
	
	<tr>
		<td align="right">Database Table Prefix:</td>
		<td align="left"><input type="text" name="table_prefix" value="<?php echo $table_prefix; ?>">&nbsp;<img src="images/help.png" border="0" class="help tooltip" title="Optional. Default is usually fine."></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td align="left"><input type="submit" name="submit" value="Save Settings"></td>
	</tr>
	
	</table>
	</form>
<?php } ?>

<?php

if ( $_SESSION['step'] == 3 )
{
	echo 'Creating database tables... ';

	// Install the default DB schema
	$data = file_get_contents( 'db.schema.sql' ) or die( 'Could not locate the database installation file to import. Please check config.php to make sure you set the correct PATH.' );
	$data = str_replace( '{TABLE_PREFIX}', $_SESSION['table_prefix'], $data );
	$data = str_replace( '{DB_NAME}', $_SESSION['db_name'], $data );
	
	$strings = array();
	
	$string = str_replace( "\r\n", "\n", $data );
	$array = explode( "\n", $string );
	
	foreach ( $array AS $key => $value )
	{
		$value = trim( $value );
		if ( $value != '' )
		{
			$strings[] = $value;
		}
	}
	
	$sql = implode( "\n", $strings );
	$sqlarray = explode( ";\n", $sql );
	
	foreach( $sqlarray AS $key => $value )
	{
		if ( $value != '' )
		{	
			$db->query( $value ) or die( 'SQL insert failed: ' . $value . '<br /><br />' . mysql_error() . '' );
		}
	}
	
	echo 'done.<br /><br />';
	
	$db->close();

?>

<?php echo sub_header( 'Congratulations! RealtyScript has been installed!' ); ?>

<br /><br />

Administrative Details

<br /><br />

Username: admin<br />
Password: admin

<br />

<p>After you have removed the /install and /docs directories, you can <a href="../" target="_blank">view your web site</a> or access your <a href="../admin/" target="_blank">administration panel</a>.</p>

<?php } ?>

</body>
</html>

<?php

function sub_header( $text )
{
	echo '
	<span style="font-family : arial; font-size : 15px; font-weight : bold; color : #04aeda;">
	' . $text . '
	</span>
	';
}

function main_header( $text )
{
	echo '
	<span style="font-family : arial; font-size : 25px; color : #04aeda;">
	' . $text . '
	</span>
	';
}

?>