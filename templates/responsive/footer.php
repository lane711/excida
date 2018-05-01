    </div><!-- /#content -->
</div><!-- /#wrapper-inner -->

<div id="footer-wrapper">
    <div id="footer-top">
        <div id="footer-top-inner" class="container">
            <div class="row">
                <div class="widget properties span3">
                    <div class="title">
                        <h2><?php echo $lang['Module_Featured_Listings']; ?></h2>
                    </div><!-- /.title -->
                    
                    <div class="content">
                    	<?php
                    	
						// Title/descr language to use (if available)
						$title = str_replace( 'name', 'title', $language_in );
						$description = str_replace( 'name', 'description', $language_in );
                    	
						// Select featured listings
						$sql = "
						SELECT
							" . $title . ", 
							" . $description . ", 
							p.*,
							l1.location_name AS country,
							l2.location_name AS state,
							l3.location_name AS city
						FROM " . PROPERTIES_TABLE  . " AS p
						LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
						LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
						LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
						WHERE 
							approved = 1 
							AND featured = 'A'
						ORDER BY RAND()
						LIMIT 3
						";
						$q = $db->query ( $sql ) or die( mysql_error() );
						if ( $db->numrows( $q ) > 0 )
						{
							while( $f = $db->fetcharray( $q ) )
							{
								// Check a seller's package, if any, to determine if we can show pictures, address, etc.
								$f_package = package_check( $f['userid'], 'seller' );
							
								$images = get_images( 'gallery', $f['listing_id'], 352, 232, 1, 1 );
								
								$link = generate_link( 'listing', $f );
								
								$f['price'] = pmr_number_format( $f['price'] );
								
								// Location
								if ( $f['city'] != '' && $f['state'] != '' )
								{
									$location = $f['city'] . ', ' . $f['state'];
								}
								elseif ( $f['city'] != '' && $f['state'] == '' )
								{
									$location = $f['city'];
								}
								elseif ( $f['city'] == '' && $f['state'] != '' )
								{
									$location = $f['state'];
								}
								elseif ( $f['country'] != '' )
								{
									$location = $f['country'];
								}
								
								if ( $f['display_address'] == 'YES' && $f_package['address'] == 'ON' )
								{
									$address = $f['address1']; 
								}
								elseif ( $f['display_address'] != 'YES' || $f_package['address'] = 'OFF' )
								{
									$address = $lang['View_Listing_Details'];
								}

								echo '
		                        <div class="property">
		                            <div class="image">
		                                <a href="' . $link . '"></a>
		                                <img src="' . $images[0] . '" border="0" width="100" height="74">
		                            </div><!-- /.image -->
		                            <div class="wrapper">
		                                <div class="title">
		                                    <h3>
		                                        <a href="' . $link . '">' . $address . '</a>
		                                    </h3>
		                                </div><!-- /.title -->
		                                <div class="location">' . $location . '</div><!-- /.location -->
		                                <div class="price">' . $conf['currency'] . ' ' . $f['price'] . '</div><!-- /.price -->
		                            </div><!-- /.wrapper -->
		                        </div><!-- /.property -->
								';
							}
						}
                    	
                    	?>
                    </div><!-- /.content -->
                </div><!-- /.properties-small -->

                <div class="widget span3">
                    <div class="title">
                        <h2><?php echo $lang['Contact_Info']; ?></h2>
                    </div><!-- /.title -->

                    <div class="content">
                        <table class="contact">
                            <tbody>
                            
                            <?php if ( $conf['contact_address'] != '' ) { ?>
                            <tr>
                                <th class="address"></th>
                                <td><?php echo html_entity_decode( $conf['contact_address'] ); ?></td>
                            </tr>
                            <?php } ?>
                            
                            <?php if ( $conf['contact_phone'] != '' ) { ?>
                            <tr>
                                <th class="phone"></th>
                                <td><?php echo $conf['contact_phone']; ?></td>
                            </tr>
                            <?php } ?>
                            
                            <?php if ( $conf['general_e_mail'] != '' ) { ?>
                            <tr>
                                <th class="email"></th>
                                <td><a href="mailto:<?php echo $conf['general_e_mail']; ?>"><?php echo $conf['general_e_mail']; ?></a></td>
                            </tr>
                            <?php } ?>
                            
                            <?php if ( $conf['skype'] != '' ) { ?>
                            <tr>
                                <th class="skype"></th>
                                <td><?php echo $conf['skype']; ?></td>
                            </tr>
                            <?php } ?>
                            
                            </tbody>
                        </table>
                    </div><!-- /.content -->
                </div><!-- /.widget -->

                <div class="widget span3">
                    <div class="title">
                        <h2 class="block-title"><?php echo $lang['Navigation_Resources']; ?></h2>
                    </div><!-- /.title -->

                    <div class="content">
                        <ul class="menu nav">
                        	<li class="first leaf"><a href="<?php echo URL; ?>/"><?php echo $lang['Menu_Home']; ?></a></li>
							<li class="leaf"><a href="<?php echo URL; ?>/alerts.php"><?php echo $lang['Alert']; ?></a></li>
							<li class="leaf"><a href="<?php echo URL; ?>/search_listings.php"><?php echo $lang['Menu_Search']; ?></a></li>
							<li class="leaf"><a href="<?php echo URL; ?>/search_sellers.php"><?php echo $lang['Realtor_Search']; ?></a></li>
                            <li class="leaf"><a href="<?php echo URL; ?>/compare.php"><?php echo $lang['Compare']; ?></a></li>
                            <li class="leaf"><a href="<?php echo URL; ?>/sitemap.php"><?php echo $lang['Menu_Site_Map']; ?></a></li>
                            <li class="leaf"><a href="<?php echo URL; ?>/contact.php"><?php echo $lang['Menu_Contact_Us']; ?></a></li>
                        </ul>
                    </div><!-- /.content -->
                </div><!-- /.widget -->

                <div class="widget span3">
                    <div class="title">
                        <h2 class="block-title"><?php echo $lang['Realtor_Send_Message']; ?></h2>
                    </div><!-- /.title -->

                    <div class="content">
                        <form method="post" action="<?php echo URL; ?>/contact.php">
                        <input type="hidden" name="external" value="true">                        
                            <div class="control-group">
                                <label class="control-label" for="inputName">
                                    <?php echo $lang['Mailer_Name']; ?>
                                    <span class="form-required" title="This field is required.">*</span>
                                </label>
                                <div class="controls">
                                    <input type="text" name="name" id="inputName">
                                </div><!-- /.controls -->
                            </div><!-- /.control-group -->

                            <div class="control-group">
                                <label class="control-label" for="inputEmail">
                                    <?php echo $lang['Mail_Friend_Your']; ?>
                                    <span class="form-required" title="This field is required.">*</span>
                                </label>
                                <div class="controls">
                                    <input type="text" name="email" id="inputEmail">
                                </div><!-- /.controls -->
                            </div><!-- /.control-group -->

                            <div class="control-group">
                                <label class="control-label" for="inputMessage">
                                    <?php echo $lang['Mail_Friend_Message']; ?>
                                    <span class="form-required" title="This field is required.">*</span>
                                </label>

                                <div class="controls">
                                    <textarea id="inputMessage" name="message"></textarea>
                                </div><!-- /.controls -->
                            </div><!-- /.control-group -->
                            
							<?php if ( $conf['captcha_private_key'] != '' && $conf['captcha_public_key'] != '' && $conf['captcha_status'] == 'ON' ) { ?>
							<div class="control-group">
						        <label class="control-label" for="inputMath">
						            &nbsp;
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <div class="g-recaptcha" data-sitekey="<?php echo $conf['captcha_public_key']; ?>"></div>
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
							<?php } ?>

                            <div class="form-actions">
                                <input type="submit" name="submit" class="btn btn-primary arrow-right" value="<?php echo $lang['Admin_Mailer_Submit']; ?>">
                            </div><!-- /.form-actions -->
                        </form>
                    </div><!-- /.content -->
                </div><!-- /.widget -->
            </div><!-- /.row -->
        </div><!-- /#footer-top-inner -->
    </div><!-- /#footer-top -->

    <div id="footer" class="footer container">
        <div id="footer-inner">
            <div class="row">
                <div class="span6 copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo $conf['website_name_short']; ?></p>
                </div><!-- /.copyright -->

                <div class="span6 share">
                    <div class="content">
                        <ul class="menu nav">
                        	
                        	<?php if ( $conf['facebook_url'] != '' ) { ?>
                            <li class="leaf">
                            <a href="<?php echo $conf['facebook_url']; ?>" class="facebook" target="_blank">Facebook</a>
                            </li>
                            <?php } ?>
                            
                            <?php if ( $conf['flickr_url'] != '' ) { ?>
                            <li class="leaf">
                            <a href="<?php echo $conf['flickr_url']; ?>" class="flickr" target="_blank">Flickr</a>
                            </li>
                            <?php } ?>
                            
                            <?php if ( $conf['google_url'] != '' ) { ?>
                            <li class="leaf">
                            <a href="<?php echo $conf['google_url']; ?>" class="google" target="_blank">Google+</a>
                            </li>
                            <?php } ?>
                            
                            <?php if ( $conf['linkedin_url'] != '' ) { ?>
                            <li class="leaf">
                            <a href="<?php echo $conf['linkedin_url']; ?>" class="linkedin" target="_blank">LinkedIn</a>
                            </li>
                            <?php } ?>
                            
                            <?php if ( $conf['twitter_url'] != '' ) { ?>
                            <li class="leaf">
                            <a href="<?php echo $conf['twitter_url']; ?>" class="twitter" target="_blank">Twitter</a>
                            </li>
                            <?php } ?>
                            
                            <?php if ( $conf['vimeo_url'] != '' ) { ?>
                            <li class="leaf">
                            <a href="<?php echo $conf['vimeo_url']; ?>" class="vimeo" target="_blank">Vimeo</a>
                            </li>
                            <?php } ?>
                            
                        </ul>
                    </div><!-- /.content -->
                </div><!-- /.span6 -->
            </div><!-- /.row -->
            
            <br />
            
			<?php include PATH . '/includes/common_footer.php'; ?>
            
        </div><!-- /#footer-inner -->
    </div><!-- /#footer -->
</div><!-- /#footer-wrapper -->
</div><!-- /#wrapper -->
</div><!-- /#wrapper-outer -->

<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/jquery.ezmark.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/jquery.currency.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/jquery.cookie.js"></script>
<?php /*<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/retina.js"></script>*/ ?>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/gmap3.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/gmap3.infobox.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/includes/js/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/iosslider/_src/jquery.iosslider.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/js/realia.js"></script> 

<script type="text/javascript">
$(document).ready(function()
{
	//InitCarousel();
    InitPropertyCarousel();
	InitOffCanvasNavigation();
	//InitMap();
	//InitChosen();
	InitEzmark();
	InitPriceSlider( <?php echo $conf['price_range_min']; ?>, <?php echo $conf['price_range_max']; ?>, '<?php echo $conf['currency']; ?>' );
	InitImageSlider();
	InitAccordion();
	InitTabs();
    InitPalette();
});
</script>

</body>
</html>