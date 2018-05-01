<div class="container">
    <div id="main listing">
        <div class="row">
            <div class="span9">				
				<p>{output_message}</p>

				<?php if ( $custom['show_listing'] == true ) { ?>
				<h1 class="page-header">{title}</h1>
				
				{new} {updated} {featured}
				
                <div class="carousel property">
                    <div class="preview">
                    	<?php if ( $custom['show_image'] == true ) { ?>
                        <img src="{image}" alt="">
                        <?php } ?>
                    </div><!-- /.preview -->

					<?php if ( $custom['show_images'] == true ) { ?>
                    <div class="content">
                        <ul>
                        	<?php 
                        	
                        	if ( is_array( $custom['full'] ) && is_array( $custom['thumbs'] ) )
                        	{
                        		foreach ( $custom['full'] AS $key => $image )
                        		{
                        			echo '
                        			<li>
                        				<img src="' . $image . '" rel="' . $custom['full'][$key] . '" alt="" width="155" height="75">
									</li>
									';
                        		}                        	
                        	}
                        	
                        	?>
                        </ul>
                    </div>
                    <!-- /.content -->
                    <?php } ?>
                    
                </div>
                <!-- /.carousel -->
                <div class="clearfix"></div>

                <div class="property-detail">
                    <div class="pull-left overview">
                        <div class="row">
                            <div class="span4">
                                <h2>{@overview}</h2>

                                <table>
                                    <tr>
                                        <th>{@price}:</th>
                                        <td>{currency} {price}</td>
                                    </tr>
                                    <?php if ( $conf['show_mls'] == 'ON' ) { ?>
                                    <tr>
                                        <th>{@mls}:</th>
                                        <td>{mls}</td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <th>{@type2}:</th>
                                        <td>{type2}</td>
                                    </tr>
                                    <tr>
                                        <th>{@type}:</th>
                                        <td>{type}</td>
                                    </tr>
                                    <tr>
                                        <th>{@style}:</th>
                                        <td>{style}</td>
                                    </tr>
                                    <tr>
                                        <th>{@status}:</th>
                                        <td>{status}</td>
                                    </tr>
                                    <tr>
                                        <th>{@address1}:</th>
                                        <td>{address1}</td>
                                    </tr>
                                    <tr>
                                        <th>{@location}:</th>
                                        <td>{city} {state} {country} {zip}</td>
                                    </tr>
                                    <tr>
                                        <th>{@year_built}:</th>
                                        <td>{year_built}</td>
                                    </tr>
                                    <tr>
                                        <th>{@bedrooms}:</th>
                                        <td>{bedrooms}</td>
                                    </tr>
                                    <tr>
                                        <th>{@bathrooms}:</th>
                                        <td>{bathrooms}</td>
                                    </tr>
                                    <tr>
                                        <th>{@half_bathrooms}:</th>
                                        <td>{half_bathrooms}</td>
                                    </tr>
                                    <tr>
                                        <th>{@garage}:</th>
                                        <td>{garage} ({garage_cars})</td>
                                    </tr>
                                    <tr>
                                        <th>{@basement}:</th>
                                        <td>{basement}</td>
                                    </tr>
                                    <tr>
                                        <th>{@dimensions}:</th>
                                        <td>{dimensions}</td>
                                    </tr>
                                    <tr>
                                        <th>{@lot_size}:</th>
                                        <td>{lot_size}</td>
                                    </tr>
                                    <?php
                                    
                                    // Custom fields
                                    if ( $custom['show_custom_fields'] == true )
                                    {
                                    	if ( is_array( $custom['custom_fields'] ) )
                                    	{
                                    		foreach( $custom['custom_fields'] AS $custom_field )
                                    		{
                                    			if ( $custom_field['value'] != '' )
                                    			{
                                    				echo '
                                    				<tr>
                                    					<th>' . $custom_field['field'] . ':</th>
                                    					<td>' . $custom_field['value'] . '</td>
                                    				</tr>
                                    				';
                                    			}
                                    		}
                                    	}
                                    }
                                    
                                    ?>
                                </table>
                                
                                <br />
                                
                                <?php /*{favorite}*/ ?>
                                
                                <?php if ( $conf['share_this'] != '' ) { ?>
								<span class='st_facebook_large' displayText='Facebook'></span>
								<span class='st_twitter_large' displayText='Tweet'></span>
								<span class='st_googleplus_large' displayText='Google +'></span>
								<span class='st__large' displayText=''></span>
								<span class='st_linkedin_large' displayText='LinkedIn'></span>
								<span class='st_pinterest_large' displayText='Pinterest'></span>
								<span class='st_sharethis_large' displayText='ShareThis'></span>
								<span class='st_email_large' displayText='Email'></span>

								<br /><br />
								<?php } ?>
                                
                                <?php echo print_pdf_widget(); ?>
                                
                                <br /><br />
                                
                                <a href="{realtor_mail}">{@realtor_mail}</a> | <a href="{realtor_website}">{@realtor_website}</a>
                                
                            </div>
                            <!-- /.span2 -->
                        </div>
                        <!-- /.row -->
                    </div>

                    <p>{description}</p>
                    
                    <p>{@directions}: {directions}</p>
                    
                    <br clear="both">

                    <h2>{@general_amenities}</h2>

                    <div class="row">
                    	<ul class="span2">
                    
                    	<?php
                    	
                    	if ( is_array( $custom['features'] ) )
                    	{
                    		$count = count( $custom['features'] );
                    		
                    		if ( $count > 0 )
                    		{
                    			$total_per_col = $count / 4;

	                    		$num = 1;
	                    		foreach( $custom['features'] AS $value )
	                    		{                   			
	                    			echo '<li class="checked">' . $value . '</li>';
	                    			
	                    			// New column
	                    			if ( $total_per_col >= 1 )
	                    			{
		                    			if ( $num % $total_per_col == 0 )
		                    			{
		                    				echo '</ul><ul class="span2">';
		                    			}
	                    			}
	                    			
	                    			$num++;
	                    		}
	                    	}
                    	}
                    	
                    	?>
                     </div>

                    <h2>{@map}</h2>

					<?php if ( $custom['latitude'] != '' && $custom['longitude'] != '' ) { ?>test
                    <iframe class="map" width="425" height="350" src="https://maps.google.com/maps?q=<?php echo $custom['latitude']; ?>,<?php echo $custom['longitude']; ?>&amp;num=1&amp;ie=UTF8&amp;ll=<?php echo $custom['latitude']; ?>,<?php echo $custom['longitude']; ?>&amp;spn=0.041038,0.077162&amp;t=m&amp;z=14&amp;output=embed"></iframe>
                    <?php } else { ?>test2
                    <iframe class="map" width="425" height="350" src="https://maps.google.com/maps?q={address1}{city},{state},{zip}{country}&amp;num=1&amp;ie=UTF8&amp;t=m&amp;z=14&amp;output=embed"></iframe>
                    <?php } ?>
                    
                    <?php if ( $conf['show_calendar'] == 'ON' ) { ?>
                    <h2>{@calendar}</h2>
                    
                    <center>{calendar}</center>
                    <?php } ?>
                 
					<?php if ( $custom['video'] == true ) { ?>
						<h2>{@video}</h2>
						<?php if ( stripos( $custom['video_embed'], 'iframe' ) === false ) { ?>
						<iframe id="ytplayer" type="text/html" width="640" height="390" src="{video}" frameborder="0"/></iframe>
						<?php } else { ?>
						{video}
						<?php } ?>
					<?php } ?>
                 
                </div>

            </div>
            <div class="sidebar span3">

				<div class="widget contact">
				    <div class="title">
				        <h2 class="block-title">{@seller_details}</h2>
				    </div><!-- /.title -->
				
				    <div class="content">
				        <form method="post" action="<?php echo URL; ?>/sendmessage.php">
				        <input type="hidden" name="listing_id" value="<?php echo $_REQUEST['id']; ?>">
				        <input type="hidden" name="u_id" value="<?php echo $_REQUEST['u_id']; ?>">
				        <input type="hidden" name="external" value="true">
				        
				            <div class="control-group">
				                <label class="control-label" for="inputName">
				                    {@name}
				                    <span class="form-required" title="This field is required.">*</span>
				                </label>
				                <div class="controls">
				                    <input type="text" id="inputName" name="name">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
				
				            <div class="control-group">
				                <label class="control-label" for="inputEmail">
				                    {@email}
				                    <span class="form-required" title="This field is required.">*</span>
				                </label>
				                <div class="controls">
				                    <input type="text" id="inputEmail" name="email">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->

				            <div class="control-group">
				                <label class="control-label" for="inputMessage">
				                    {@message}
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
						    <br />
							<?php } ?>	
				
				            <div class="form-actions">
				                <input type="submit" name="submit" class="btn btn-primary arrow-right" value="{@send}">
				            </div><!-- /.form-actions -->
				            
				            <br />
				            
				            <center><a href="tel:{phone1}">{phone1}</a></center>
				        </form>
				    </div><!-- /.content -->
				</div><!-- /.widget -->

				<div class="widget our-agents">
				    
				    <?php 
						
					include PATH . '/modules/all-sellers.php';
				   				    
				   ?>
				    
				</div><!-- /.our-agents -->

				<div class="widget properties last">
				
					<?php
					
					include PATH . '/modules/recent.php';
					
					?>
			
				</div><!-- /.properties -->
		
             <?php } ?>          
             
            </div>                      
        </div>
    </div>
</div>