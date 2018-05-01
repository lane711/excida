<?php
include PATH . '/includes/common_header.php';
include PATH . '/templates/' . $cookie_template . '/tables.php'; 
?>
<!DOCTYPE html>
<html>
<head>
	
    <meta charset="<?php echo $lang['Encoding']; ?>"/>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['Encoding']; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" dir="<?php echo $lang['Direction']; ?>">
    
	<meta name="author" content="RealtyScript.com" />
	<meta name="copyright" content="RealtyScript.com" />
	
	<meta name="title" content="<?php if (isset($meta_title)) echo $meta_title; else echo $conf['title_meta']; ?>" />
	<meta name="description" content="<?php if (isset($meta_description)) echo $meta_description; else echo $conf['description_meta']; ?>" />
	<meta name="keywords" content="<?php if (isset($meta_keywords)) echo $meta_keywords; else echo $conf['keywords_meta']; ?>" />
	
	<meta name="robots" content="<?php echo $conf['robots_meta']; ?>" />

	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo URL . '/rss.php'; ?>" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/css/bootstrap-responsive.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/chosen/chosen.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/bootstrap-fileupload/bootstrap-fileupload.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/jquery-ui-1.10.2.custom/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/css/realia-blue.css" type="text/css" id="color-variant-default">
	
	<link rel="stylesheet" href="<?php echo URL; ?>/includes/jsCalendar/calendar.css" type="text/css">

	<script type="text/javascript" src="<?php echo URL; ?>/includes/jsCalendar/calendar.js"></script>
	<?php 
	if (empty($iso_language_codes[$cookie_language])) {
	    $_jsCalendarLang = 'en';
	} else {
	    $_jsCalendarLang = $iso_language_codes[$cookie_language];
	} 
	if (!file_exists(PATH.'/includes/jsCalendar/lang/calendar-'.$_jsCalendarLang.'.js')) {
	    if (file_exists(PATH.'/includes/jsCalendar/lang/calendar-'.$_jsCalendarLang.'-utf8.js')) {
	        $_jsCalendarLang .= '-utf8';
	    } elseif (file_exists(PATH.'/includes/jsCalendar/lang/calendar-'.$_jsCalendarLang.'-win.js')) {
	        $_jsCalendarLang .= '-win';
	    } else {
	        $_jsCalendarLang = 'en';
	    }
	}
	?>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/jsCalendar/lang/calendar-<?php echo $_jsCalendarLang; ?>.js"></script>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/jsCalendar/calendar-setup.js"></script>
	<script type="text/javascript">
		var fileLoadingImage = "<?php echo URL; ?>/includes/lightbox/images/loading.gif";		
		var fileBottomNavCloseImage = "<?php echo URL; ?>/includes/lightbox/images/closelabel.gif";
	</script>
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		var path_var = "<?php echo URL; ?>";
	</script>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/js/site.js"></script>

	<!-- AJAX Bulk Image Upload Script -->
	<link href="<?php echo URL; ?>/includes/css/uploadfilemulti.css" rel="stylesheet">
	<script src="<?php echo URL; ?>/includes/js/jquery.fileuploadmulti.min.js"></script>
	<!-- END AJAX Image Script -->

	<?php $map->printGMapsJS();	?>
	
	<script type="text/javascript" src="<?php echo URL; ?>/includes/ckeditor/ckeditor.js"></script>
	
	<?php
	
	// ShareThis
	if ( $conf['share_this'] != '' )
	{
		echo '
		<script type="text/javascript">var switchTo5x=true;</script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		<script type="text/javascript">stLight.options({publisher: "' . $conf['share_this'] . '", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
		';
	}
	
	// Captcha
	if ( $conf['captcha_public_key'] != '' && $conf['captcha_private_key'] != '' )
	{
		echo '
		<script src="https://www.google.com/recaptcha/api.js"></script>
		';	
	}
	
	?>
	
	<title><?php echo $title; ?></title>
</head>
<body style class="blue pattern-none header-light">
<div id="wrapper-outer" >
    <div id="wrapper">
        <div id="wrapper-inner">
            <!-- BREADCRUMB -->
            <div class="breadcrumb-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="span12">
                            <ul class="breadcrumb pull-left">
                                <li><a href="<?php echo URL; ?>"><?php echo $lang['Menu_Home']; ?></a></li>
                            </ul><!-- /.breadcrumb -->

                            <div class="account pull-right">
                                <ul class="nav nav-pills">
                                       
								<?php
								
								// If Logged In
								if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
								{
									$user_name = $session->fetch('login');
									
									echo '
									<li><a href="">' . $lang['Welcome'] . ', ' . ucfirst( $user_name ) . '!</a></li>
									<li><a href="' . URL . '/user.php">' . $lang['Menu_User_Login'] . '</a></li>
									<li><a href="' . URL . '/login.php?action=logout">' . $lang['Realtor_Logout'] . '</a></li>
									';
								}
								else
								{
									echo '
									<li><a href="">' . $lang['Welcome'] . ', ' . $lang['Guest'] . '!</a></li>
									';
									
									if ( $conf['allow_registration'] == 'ON' )
									{
                                    	echo '<li><a href="' . URL . '/register.php">' . $lang['Menu_Submit_Listing'] . '</a></li>';
                                    }
                                    
                                    echo '
                                    <li><a href="' . URL . '/login.php">' . $lang['Seller_Control_Panel'] . '</a></li>
									';
									echo '
                                    <li><a href="' . URL . '/signup.php">Sign Up</a></li>
									';
								}
													
								?>
		                                
                                </ul>
                            </div>
                        </div><!-- /.span12 -->
                    </div><!-- /.row -->
                </div><!-- /.container -->
            </div><!-- /.breadcrumb-wrapper -->

            <!-- HEADER -->
            <div id="header-wrapper">
                <div id="header">
                    <div id="header-inner">
                        <div class="container">
                            <div class="navbar">
                                <div class="navbar-inner">
                                    <div class="row">
                                        <div class="logo-wrapper span4">
                                            <!--<a href="#nav" class="hidden-desktop" id="btn-nav">Toggle navigation</a>-->

                                            <div class="logo">
                                                <a href="<?php echo URL; ?>" title="Home">
                                                    <img src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/img/logo.png" alt="Home">
                                                </a>
                                            </div><!-- /.logo -->

                                            <div class="site-name">
                                                <a href="<?php echo URL; ?>" title="Home" class="brand"><?php echo $conf['website_name_short'];?></a>
                                            </div><!-- /.site-name -->

<?php /*
                                            <div class="site-slogan">
                                                <span>Real estate &amp; Rental<br>made easy</span>
                                            </div><!-- /.site-slogan -->
*/ ?>
                                        </div><!-- /.logo-wrapper -->

                                        <div class="info">
                                            <div class="site-email">
                                                <a href="mailto:<?php echo $conf['general_e_mail']; ?>"><?php echo $conf['general_e_mail']; ?></a>
                                            </div><!-- /.site-email -->

                                            <div class="site-phone">
                                                <span><?php echo $conf['contact_phone']; ?></span>
                                            </div><!-- /.site-phone -->
                                        </div><!-- /.info -->

                                        <a class="btn btn-primary btn-large list-your-property arrow-right" href="<?php echo URL; ?>/adduserlistings.php"><?php echo $lang['Menu_List_Now']; ?></a>
                                    </div><!-- /.row -->
                                </div><!-- /.navbar-inner -->
                            </div><!-- /.navbar -->
                        </div><!-- /.container -->
                    </div><!-- /#header-inner -->
                </div><!-- /#header -->
            </div><!-- /#header-wrapper -->

            <!-- NAVIGATION -->
            <div id="navigation">
                <div class="container">
                    <div class="navigation-wrapper">
                        <div class="navigation clearfix-normal">

                            <ul class="nav">
                                <li><a href="<?php echo URL; ?>"><?php echo $lang['Menu_Home']; ?></a></li>
                                <?php
                                
                                // Grab all custom pages that belong in the navigation
                                
								// Language to show article in
								$menu2 = str_replace( 'name', 'menu', $language_in );
							
								if ( $menu2 == '' )
								{
									$menu2 = 'menu';
								}
							
								$sql = 'SELECT ' . $menu2 . ', id, string, menu FROM ' . PAGES_TABLE . " WHERE navigation = '1' AND status = '1' ";
								$r_pages = $db->query( $sql ) or error ( 'Critical Error', mysql_error () );
								if ( $db->numrows( $r_pages ) > 0 )
								{
									while( $f_pages = $db->fetcharray( $r_pages ) )
									{
										// Default page title
										if ( $f_pages[0] == '' )
										{
											$f_pages[0] = $f_pages[3];
										}

										if ( $conf['rewrite'] == 'ON' )
										{
											$link = URL . '/Pages/' . $f_pages['string'] . '.html';
										}
										else
										{
											$link = URL . '/pages.php?id=' . $f_pages['id'];
										}
									
										echo '<li><a href="' . $link . '">' . $f_pages[0] . '</a></li>';
									}
								}
                                
                                ?>
                                <li><a href="<?php echo URL; ?>/adduserlistings.php"><?php echo $lang['Menu_List_Property']; ?></a></li>
                                <li class="menuparent">
                                    <span class="menuparent nolink"><?php echo $lang['Menu_Search']; ?></span>
                                    <ul>
                                        <li><a href="<?php echo URL; ?>/search_listings.php"><?php echo $lang['Property_Search']; ?></a></li>
                                        <li><a href="<?php echo URL; ?>/search_sellers.php"><?php echo $lang['Realtor_Search']; ?></a></li>
                                    </ul>
                                </li>
                                <li class="menuparent">
                                    <span class="menuparent nolink"><?php echo $lang['Navigation_Resources']; ?></span>
                                    <ul>
                                        <li><a href="<?php echo URL; ?>/compare.php"><?php echo $lang['Package_Agent']; ?></a></li>
                                        <li><a href="<?php echo URL; ?>/compare.php"><?php echo $lang['Package_Listing']; ?></a></li>
                                        <li><a href="<?php echo URL; ?>/alerts.php"><?php echo $lang['Alert']; ?></a></li>
		                                <?php
		                                
		                                // Grab all custom pages that belong in the navigation
		                                
										// Language to show article in
										$menu2 = str_replace( 'name', 'menu', $language_in );
									
										if ( $menu2 == '' )
										{
											$menu2 = 'menu';
										}
									
										$sql = 'SELECT ' . $menu2 . ', id, string, menu FROM ' . PAGES_TABLE . " WHERE navigation = '0' AND status = '1' ";
										$r_pages = $db->query( $sql ) or error ( 'Critical Error', mysql_error () );
										if ( $db->numrows( $r_pages ) > 0 )
										{
											while( $f_pages = $db->fetcharray( $r_pages ) )
											{
												// Default page title
												if ( $f_pages[0] == '' )
												{
													$f_pages[0] = $f_pages[3];
												}
		
												if ( $conf['rewrite'] == 'ON' )
												{
													$link = URL . '/Pages/' . $f_pages['string'] . '.html';
												}
												else
												{
													$link = URL . '/pages.php?id=' . $f_pages['id'];
												}
											
												echo '<li><a href="' . $link . '">' . $f_pages[0] . '</a></li>';
											}
										}
		                                
		                                ?>
		                                <li><a href="<?php echo URL; ?>/sitemap.php"><?php echo $lang['Menu_Site_Map']; ?></a></li>
                                    </ul>
                                </li>
                                <li><a href="<?php echo URL; ?>/contact.php"><?php echo $lang['Menu_Contact_Us']; ?></a></li>
                            </ul><!-- /.nav -->

							<?php if ( $conf['settings_box'] == 'ON' && installed_languages() > 1 ) { ?>
                            <div class="language-switcher">
                            	<form method="get" action="<?php echo URL; ?>">
                            	<select name="lang" style="width:100px;" onchange="this.form.submit()">
                            	<option value="<?php echo $cookie_language; ?>" selected><?php echo ucwords( $cookie_language ); ?></option>
	                            
								<?php
								
								// Read /languages folder
								$option_language = array();
								$option_handle = opendir( PATH . '/languages' );
								
								while( false !== ($file = readdir($option_handle)))
								{
									// We select only files containing .lng.php pattern
									if ( preg_match( '/.lng.php/', $file ) )
									{
										$add_lang = explode ('.', $file);
										$option_language[] = $add_lang[0];
									}
								}
								
								if ( !empty( $option_language ) && $option_language[0] != '' )
								{
									asort($option_language);	  	
									foreach ($option_language AS $key => $value)
									{
										if ( $value != $cookie_language )
										{
											$sel = ($cookie_language == $value) ? 'selected' : '';
											echo '<option value="' . $value . '">' . ucwords( $value ) . '</option>';
										}
									}
								}
								
								closedir ($option_handle);
								
								?>
								
								</select>
								</form>
                            </div><!-- /.language-switcher -->
                            <?php } ?>

                            <form method="post" class="site-search" action="<?php echo URL; ?>/search_listings_results.php">
                                <div class="input-append">
                                    <input title="Enter the terms you wish to search for." name="keyword" class="search-query span2 form-text" placeholder="Search" type="text">
                                    <button type="submit" class="btn" type="submit" name="property_search"><i class="icon-search"></i></button>
                                </div><!-- /.input-append -->
                            </form><!-- /.site-search -->
                        </div><!-- /.navigation -->
                    </div><!-- /.navigation-wrapper -->
                </div><!-- /.container -->
            </div><!-- /.navigation -->            
           
        	<div class="container mobile-nav-wrapper ">
	            <select onchange="window.location = $(this).val();">
		            <option value="<?php echo URL; ?>"><?php echo $lang['Menu_Home']; ?></option>
					<option value="<?php echo URL; ?>/adduserlistings.php"><?php echo $lang['Menu_List_Property']; ?></option>
					<option value="<?php echo URL; ?>/search_listings.php"><?php echo $lang['Property_Search']; ?></option>
					<option value="<?php echo URL; ?>/search_sellers.php"><?php echo $lang['Realtor_Search']; ?></option>
					<option value="<?php echo URL; ?>/compare.php"><?php echo $lang['Package_Agent']; ?></option>
					<option value="<?php echo URL; ?>/compare.php"><?php echo $lang['Package_Listing']; ?></option>
					<option value="<?php echo URL; ?>/alerts.php"><?php echo $lang['Alert']; ?></option>
					<option value="<?php echo URL; ?>/sitemap.php"><?php echo $lang['Menu_Site_Map']; ?></option>
					<option value="<?php echo URL; ?>/contact.php"><?php echo $lang['Menu_Contact_Us']; ?></option>

	                <?php
	                
	                // Grab all custom pages that belong in the navigation
	                
					// Language to show article in
					$menu2 = str_replace( 'name', 'menu', $language_in );
				
					if ( $menu2 == '' )
					{
						$menu2 = 'menu';
					}
				
					$sql = 'SELECT ' . $menu2 . ', id, string, menu FROM ' . PAGES_TABLE . " WHERE status = '1' ";
					$r_pages = $db->query( $sql ) or error ( 'Critical Error', mysql_error () );
					if ( $db->numrows( $r_pages ) > 0 )
					{
						while( $f_pages = $db->fetcharray( $r_pages ) )
						{
							// Default page title
							if ( $f_pages[0] == '' )
							{
								$f_pages[0] = $f_pages[3];
							}
	
							if ( $conf['rewrite'] == 'ON' )
							{
								$link = URL . '/Pages/' . $f_pages['string'] . '.html';
							}
							else
							{
								$link = URL . '/pages.php?id=' . $f_pages['id'];
							}
						
							echo '<option value="' . $link . '">' . $f_pages[0] . '</option>';
						}
					}
	                
	                ?>
	            </select>
	            
	            <?php if ( $conf['settings_box'] == 'ON' && installed_languages() > 1 ) { ?>
                    <div class="language-switcher">
                    	<form method="get" action="<?php echo URL; ?>">
                    	<select name="lang" style="width:100px;" onchange="this.form.submit()">
                    	<option value="<?php echo $cookie_language; ?>" selected><?php echo ucwords( $cookie_language ); ?></option>
                        
						<?php
						
						// Read /languages folder
						$option_language = array();
						$option_handle = opendir( PATH . '/languages' );
						
						while( false !== ($file = readdir($option_handle)))
						{
							// We select only files containing .lng.php pattern
							if ( preg_match( '/.lng.php/', $file ) )
							{
								$add_lang = explode ('.', $file);
								$option_language[] = $add_lang[0];
							}
						}
						
						if ( !empty( $option_language ) && $option_language[0] != '' )
						{
							asort($option_language);	  	
							foreach ($option_language AS $key => $value)
							{
								if ( $value != $cookie_language )
								{
									$sel = ($cookie_language == $value) ? 'selected' : '';
									echo '<option value="' . $value . '">' . ucwords( $value ) . '</option>';
								}
							}
						}
						
						closedir ($option_handle);
						
						?>
						
						</select>
						</form>
                    </div><!-- /.language-switcher -->
                    <div class="clearfix"></div>
                <?php } ?>
        	</div>
            
            
            
            

            <!-- CONTENT -->
            <div id="content">
