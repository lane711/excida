<div class="container">
    <div>
        <div id="main">
			<div class="list-your-property-form">
			    <h2 class="page-header">{header}</h2>

			    <div class="content">
			    	<?php if ( $custom['hide_nav'] == false ) { ?>
			        <div class="row">
			            <div class="span8">
			            	<p>
			            		<a href="adduserlistings.php">{@add_listing}</a> | 
			            		<a href="viewuserlistings.php">{@edit_listings}</a> | 
			            		<a href="user.php">{@control_panel}</a>
			            	</p>
			            </div><!-- /.span8 -->
			        </div><!-- /.row -->
			        <?php } ?>

					<p>{output_message}</p>
					
					<?php if ( $custom['show_listing_form'] == true ) { ?>
					<?php
					
					if ( is_array( $custom['gallery_list'] ) && $custom['gallery_list'][0]['id'] != '' )
					{
						echo '
						<div class="row">
							<div class="span12">
							<h3>{@gallery}</h3>
							<p>{gallery_text}</p>
							';
							
							foreach ( $custom['gallery_list'] AS $image )
							{
								if ( $image['thumb'] == '' )
								{
									$image['thumb'] = MEDIA_URL . '/error.png';
								}

								echo '<a href="' . URL . '/edituserlistings.php?action=delete_image&id=' . $image['id'] . '&listing_id={listing_id}"><img src="' . $image['thumb'] . '" width="100" height="100" border="0" title="Delete Image"></a>&nbsp;';
							}
						
						echo '
							</div>
						</div>
						<br /><br />
						';
					}
					
					?>					
					
					<div class="row">
						<div class="span6">
						
						<h3>{@bulk_upload}</h3>
						
						<div id="fileuploader">{upload}</div>
						<div id="status"></div>

						</div>
						<div class="span6">
						
						<h3>{@statistics}</h3>
						
						<p>
							<b>{@date_added}:</b> {date_added}<br />
							<b>{@date_updated}:</b> {date_updated}<br />
							<b>{@ip_added}:</b> {ip_added}<br />
							<b>{@ip_updated}:</b> {ip_updated}<br />
							<b>{@hits}:</b> {hits}<br />						
						</p>
						
						</div>
					</div>
					
			        <?php if ( $custom['show_upgrade'] == true ) { ?>
			        <h3>{@upgrade_options}</h3>
			        
			        <p>{upgrade_options}</p>
			        <?php } ?>					
					
					<form method="post" action="<?php echo URL; ?>/edituserlistings.php" enctype="multipart/form-data">
					<input type="hidden" name="listing_id" value="{listing_id}">
					    <div class="row">
					    	<div class="span12">
								<h2><span>{@listing_step1}</span></h2>
								
								<?php

								// Title field
								
								// Show all available languages
								$language_list = '';
								if ( is_array( $custom['languages'] ) )
								{
									foreach ( $custom['languages'] AS $language1 => $key1 )
									{
										$key1 = str_replace( 'name', 'title', $key1 );
										$language_list .= '
										<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').show();">
										' . ucwords($language1) . '
										</a>&nbsp;';
									}
								}
								
								// Display text boxes for every language
								$num = 1;
								if ( is_array( $custom['languages'] ) )
								{
									foreach ( $custom['languages'] AS $language1 => $key1 )
									{
										// Grab the right text for each textarea
										$key1 = str_replace( 'name', 'title', $key1 );
														
										$display = ( $num == 1 ) ? 'normal' : 'none';
									
										echo '
										<div style="display: ' . $display . ';" id="' . $key1 . '">		
											<div class="control-group">
											    <label class="control-label" for="' . $key1 . '">
										';
										
										if ( $num != 1 )
										{
											echo ucwords( $language1 ) . ' ';
										}
										
										echo '{@title} ';
										
										if ( $num == 1 )
										{
											echo '<span class="form-required" title="This field is required.">*</span>';
											echo '<br /><span style="font-size:11px;font-weight:normal;">' . $language_list . '</span><br />';
										}
										else
										{
											echo '(<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').hide();" style="font-weight:normal">' . $lang['Hide'] . '</a>)';
										}
										
										echo '
											    </label>
											    <div class="controls">
											        <input type="text" id="' . $key1 . '" name="' . $key1 . '" value="' . $custom[$key1] . '">
											    </div><!-- /.controls -->
											</div><!-- /.control-group -->
										</div>
										';
										
										$num++;
									}
								}
								
								// Description field
								
								// Show all available languages
								$language_list = '';
								if ( is_array( $custom['languages'] ) )
								{
									foreach ( $custom['languages'] AS $language1 => $key1 )
									{
										$key1 = str_replace( 'name', 'description', $key1 );
										$language_list .= '
										<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').show();">
										' . ucwords($language1) . '
										</a>&nbsp;';
									}
								}
					
								// Display text boxes for every language
								$num = 1;
								if ( is_array( $custom['languages'] ) )
								{
									foreach ( $custom['languages'] AS $language1 => $key1 )
									{
						
										// Grab the right text for each textarea
										$key1 = str_replace( 'name', 'description', $key1 );
										
										$display = ( $num == 1 ) ? 'normal' : 'none';
									
										echo '
										<div style="display: ' . $display . ';" id="' . $key1 . '">		
											<div class="control-group">
											    <label class="control-label" for="' . $key1 . '">
										';
										
										// Show the language this title belongs to, e.g. Spanish Title
										if ( $num != 1 )
										{
											echo ucwords( $language1 ) . ' ';
										}
										
										echo '{@description} ';
										
										if ( $num == 1 )
										{
											echo '<span class="form-required" title="This field is required.">*</span>';
											echo '<br /><span style="font-size:11px;font-weight:normal;">' . $language_list . '</span><br />';
										}
										else
										{
											echo '(<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').hide();" style="font-weight:normal">' . $lang['Hide'] . '</a>)';
										}
										
										echo '
											    </label>
											    <div class="controls">
											        <textarea class="ckeditor" id="' . $key1 . '" name="' . $key1 . '">
											        ' . $custom[$key1] . '
											        </textarea>
											    </div><!-- /.controls -->
											</div><!-- /.control-group -->
										</div>
										';
										
										$num++;
									}
								}
								
								?>
					    	
					    	</div>
					        <div class="span12">
					            <h2><span>{@listing_step1}</span></h2>
						
					            <div class="bedrooms control-group">
					                <label class="control-label" for="status">
					                    {@status}
					                </label>
					                <div class="controls">
					                    <select name="status" id="status" class="none">
					                    	<option value="">{select}</option>
						                    <?php
						                    
						                    echo generate_options_list( STATUS_TABLE, $custom['status'] );
						                    
						                    ?>
					                    </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					
					            <div class="bathrooms control-group">
					                <label class="control-label" for="listing_type">
					                    {@listing_type}
					                </label>
					                <div class="controls">
					                    <select name="type2" id="listing_type" class="none">
					                    	<option value="">{select}</option>
						                    <?php
						                    
						                    echo generate_options_list( TYPES2_TABLE, $custom['type2'] );
						                    
						                    ?>
					                    </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
								
								<br clear="both">
								
								<?php if ( $conf['show_mls'] == 'ON' ) { ?>
					            <div class="bedrooms control-group">
					                <label class="control-label" for="mls">
					                    {@mls}
					                </label>
					                <div class="controls">
					                    <input type="text" id="mls" name="mls" value="{mls}">
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
								<?php } ?>
								
								<?php if ( $conf['show_mls'] == 'ON' ) { ?>
								<div class="bathrooms control-group">
								<?php } else { ?>
								<div class="control-group">
								<?php } ?>
					                <label class="control-label" for="inputPropertyPrice">
					                    {@price}
					                    <span class="form-required" title="This field is required.">*</span>
					                </label>
					                <div class="controls">
					                    <input type="text" name="price" value="{price}" id="inputPropertyPrice">
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					            
					            <br clear="both">
								            
					            <div class="control-group">
					                <label class="control-label" for="inputPropertyLocation">
					                    {@location}
					                    <span class="form-required" title="This field is required.">*</span>
					                </label>
					                <div class="controls">
					                    <select name="location1" id="location1">
					                    	<?php
					                    					                    	
					                    	if ( $custom['location1_id'] != '' && $custom['location1_name'] != '' )
					                    	{
					                    		echo '<option value="' . $custom['location1_id'] . '">' . $custom['location1_name'] . '</option>';
					                    	}
					                    	
					                    	?>
				                    	
					                    	{location1}
					                    </select>
					                    <select name="location2" id="location2">
					                    	<?php
					                    					                    	
					                    	if ( $custom['location2_id'] != '' && $custom['location2_name'] != '' )
					                    	{
					                    		echo '<option value="' . $custom['location2_id'] . '">' . $custom['location2_name'] . '</option>';
					                    	}
					                    	
					                    	?>
					                    </select>
					                    <select name="location3" id="location3">
					                    	<?php
					                    					                    	
					                    	if ( $custom['location3_id'] != '' && $custom['location3_name'] != '' )
					                    	{
					                    		echo '<option value="' . $custom['location3_id'] . '">' . $custom['location3_name'] . '</option>';
					                    	}
					                    	
					                    	?>
					                    </select>                    
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					            
							    <div class="control-group">
							        <label class="control-label" for="address1">
							            {@address1}
							        </label>
							        <div class="controls">
							            <input type="text" id="address1" name="address1" value="{address1}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
					
								<?php if ( $conf['show_postal_code'] == 'ON' ) { ?>
							    <div class="bedrooms control-group">
							    <?php } else { ?>
							    <div class="control-group">
							    <?php } ?>
							        <label class="control-label" for="address2">
							            {@address2}
							        </label>
							        <div class="controls">
							            <input type="text" id="address2" name="address2" value="{address2}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
							    
								<?php if ( $conf['show_postal_code'] == 'ON' ) { ?>
							    <div class="bathrooms control-group">
							        <label class="control-label" for="zip">
							            {@zip}
							        </label>
							        <div class="controls">
							            <input type="text" id="zip" name="zip" value="{zip}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
							    <?php } ?>
					
							    <div class="bedrooms control-group">
							        <label class="control-label" for="longitude">
							            {@longitude}
							        </label>
							        <div class="controls">
							            <input type="text" id="longitude" name="longitude" value="{longitude}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->  
							   
							    <div class="bathrooms control-group">
							        <label class="control-label" for="latitude">
							            {@latitude}
							        </label>
							        <div class="controls">
							            <input type="text" id="latitude" name="latitude" value="{latitude}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->  
					
					        </div><!-- /.span4 -->
					        <div class="span12">
					        
					            <h2><span>{@listing_step2}</span></h2>
					
					            <div class="bedrooms control-group">
					                <label class="control-label" for="property_type">
					                    {@property_type}
					                </label>
					                <div class="controls">
					                    <select name="type" id="property_type">
					                    	<option value="">{select}</option>
						                    <?php
						                    
						                    echo generate_options_list( TYPES_TABLE, $custom['type'] );
						                    
						                    ?>
					                    </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					
					            <div class="bathrooms control-group">
					                <label class="control-label" for="style">
					                    {@style}
					                </label>
					                <div class="controls">
					                    <select name="style" id="style">
					                    	<option value="">{select}</option>
						                    <?php
						                    
						                    echo generate_options_list( STYLES_TABLE, $custom['style'] );
						                    
						                    ?>
					                    </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					
					            <div class="bedrooms control-group">
					                <label class="control-label" for="bedrooms">
					                    {@bedrooms}
					                </label>
					                <div class="controls">
							            <select id="bedrooms" name="bedrooms">
							            	<option value="">{select}</option>
								            <?php
								            
								            for( $i = 1; $i <= 10; $i++ )
								            {
								            	$sel = ( $custom['bedrooms'] == $i ) ? ' selected' : '';
								            	echo '<option value="' . $i . '"' . $sel . '>' . $i . '</option>';
								            }
								            
								            ?>
							            </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->

					            <div class="bathrooms control-group">
					                <label class="control-label" for="bathrooms">
					                    {@bathrooms}
					                </label>
					                <div class="controls">
							            <select id="bathrooms" name="bathrooms">
							            	<option value="">{select}</option>
								            <?php
								            
								            for( $i = 1; $i <= 10; $i++ )
								            {
								            	$sel = ( $custom['bathrooms'] == $i ) ? ' selected' : '';
								            	echo '<option value="' . $i . '"' . $sel . '>' . $i . '</option>';
								            }
								            
								            ?>
							            </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					
					            <div class="bedrooms control-group">
					                <label class="control-label" for="half_bathrooms">
					                    {@half_bathrooms}
					                </label>
					                <div class="controls">
							            <select id="half_bathrooms" name="half_bathrooms">
							            	<option value="">{select}</option>
								            <?php
								            
								            for( $i = 0; $i <= 10; $i++ )
								            {
								            	$sel = ( $custom['half_bathrooms'] == $i && $custom['half_bathrooms'] != '' ) ? ' selected' : '';
								            	echo '<option value="' . $i . '"' . $sel . '>' . $i . '</option>';
								            }
								            
								            ?>
							            </select>
					                </div><!-- /.controls -->
					            </div><!-- /.control-group -->
					
							    <div class="bathrooms control-group">
							        <label class="control-label" for="year_built">
							            {@year_built}
							        </label>
							        <div class="controls">
							            <select id="year_built" name="year_built">
							            	<option value="">{select}</option>
								            <?php
						
								            $cur_year = date( 'Y' );
								            
								            for( $i = $cur_year; $i >= 1800; $i-- )
								            {
								            	$sel = ( $custom['year_built'] == $i ) ? ' selected' : '';
								            	echo '<option value="' . $i . '"' . $sel . '>' . $i . '</option>';
								            }
								            
								            ?>
							            </select>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
					            
							    <div class="bedrooms control-group">
							        <label class="control-label" for="inputContactEmail">
							            {@garage}
							        </label>
							        <div class="controls">
							            <select name="garage" id="garage">
							            	<option value="">{select}</option>
											<?php
											
											echo generate_options_list( GARAGE_TABLE, $custom['garage'] );
											
											?>
							            </select>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->						
							
							    <div class="bathrooms control-group">
							        <label class="control-label" for="garage_cars">
							            {@garage_cars}
							        </label>
							        <div class="controls">
							            <select name="garage_cars" id="garage_cars">
							            	<option value="">{select}</option>
								            <?php
					
								            for( $i = 0; $i <= 10; $i++ )
								            {
								            	$sel = ( $custom['garage_cars'] == $i && $custom['garage_cars'] != '' ) ? ' selected' : '';
								            	echo '<option value="' . $i . '"' . $sel . '>' . $i . '</option>';
								            }
								            
								            ?>
							            </select>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
					
							    <div class="bedrooms control-group">
							        <label class="control-label" for="basement">
							            {@basement}
							        </label>
							        <div class="controls">
							            <select name="basement" id="basement">
							            	<option value="">{select}</option>
								            <?php
								            
								            echo generate_options_list( BASEMENT_TABLE, $custom['basement'] );
								            
								            ?>
							            </select>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->  
					
							    <div class="bathrooms control-group">
							        <label class="control-label" for="directions">
							            {@directions}
							        </label>
							        <div class="controls">
							            <input type="text" id="directions" name="directions" value="{directions}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->  
					
							    <div class="bedrooms control-group">
							        <label class="control-label" for="living_area">
							            {@living_area}
							        </label>
							        <div class="controls">
							            <input type="text" id="living_area" name="dimensions" value="{living_area}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->  
							   
							    <div class="bathrooms control-group">
							        <label class="control-label" for="lot_size">
							            {@lot_size}
							        </label>
							        <div class="controls">
							            <input type="text" id="lot_size" name="size" value="{lot_size}">
							        </div><!-- /.controls -->
							    </div><!-- /.control-group --> 
							    
							    <br clear="both">
							    
							    <div class="control-group">
							        <label class="control-label" for="video">
							            {@video}
							        </label>
							        <div class="controls">
							            <input type="text" id="video" name="video" value='{video}'>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->  
							    
					        </div><!-- /.span4 -->
					        <div class="span12">
					           	<h2><span>{@listing_step3}</span></h2>
					        </div>
					        
					        <div class="span4">					
							    <div class="control-group">
							        <label class="control-label" for="amenities">
							            {@amenities}
							        </label>
							        <div class="controls">
							            <?php
							            
						            $list = generate_checkbox_list( BUILDINGS_TABLE, 'buildings', $custom['buildings'], 1 );
						            
						            foreach ( $list AS $data )
						            {
						            	$checked = ( in_array( $data['id'], $custom['buildings'] ) ) ? ' checked' : '';
						            	echo '<input type="checkbox" name="buildings[]" value="' . $data['id'] . '"' . $checked . '>' . $data['name'] . '<br /><br />';
						            }
							            
							            ?>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->	
							</div>					
							
							<div class="span4">
							    <div class="control-group">
							        <label class="control-label" for="appliances">
							            {@appliances}
							        </label>
							        <div class="controls">
										<?php
										
						            $list = generate_checkbox_list( APPLIANCES_TABLE, 'appliances', $custom['appliances'], 1 );
						            
						            foreach ( $list AS $data )
						            {
						            	$checked = ( in_array( $data['id'], $custom['appliances'] ) ) ? ' checked' : '';
						            	echo '<input type="checkbox" name="appliances[]" value="' . $data['id'] . '"' . $checked . '>' . $data['name'] . '<br /><br />';
						            }
										
										?>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->	
							</div>
							
							<div class="span4">
							    <div class="control-group">
							        <label class="control-label" for="inputContactEmail">
							            {@features}
							        </label>
							        <div class="controls">
							            <?php
							            
						            $list = generate_checkbox_list( FEATURES_TABLE, 'features', $custom['features'], 1 );
						            
						            foreach ( $list AS $data )
						            {
						            	$checked = ( in_array( $data['id'], $custom['features'] ) ) ? ' checked' : '';
						            	echo '<input type="checkbox" name="features[]" value="' . $data['id'] . '"' . $checked . '>' . $data['name'] . '<br /><br />';
						            }
							            
							            ?>
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
							</div>
					
					        <?php if ( $custom['show_custom_fields'] == true ) { ?>
					        <div class="span12">
					           	<h2><span>{@listing_step4}</span></h2>
					
							   	<?php
							   	
								$query = "SELECT * FROM " . FIELDS_TABLE . " ORDER BY name ASC";
								$result = $db->query($query) OR error( 'Critical Error:' . $query);
								if ( $db->numrows( $result ) > 0 ) 
								{
									while( $row = $db->fetcharray( $result ) )
									{
										$class = ( $class == 'bedrooms' ) ? 'bathrooms' : 'bedrooms';
									
										echo '
									    <div class="' . $class . ' control-group">
									        <label class="control-label" for="' . $row['name'] . '">
									        ' . $row['name'] . '
									        </label>
									        <div class="controls">
										';
					
										// Type of input
										if ($row['type'] != '')
										{
											if ($row['type'] == 'input')
											{
												$custom[$row['field']] = str_replace( '{INPUT}', '', $custom[$row['field']] );
												echo '<input type="text" id="' . $row['field'] . '" name="' . $row['field'] . '" value="' . $custom[$row['field']] . '">';
											}
											elseif ($row['type'] == 'select')
											{
												// Grab all options for this select
												$options = "SELECT * FROM " . VALUES_TABLE . " WHERE f_id = '" . addslashes($row['id']) . "'";
												$get_options = $db->query($options) OR error( 'Critical Error:' . $options);
												if ($db->numrows($get_options) > 0)
												{								
													echo '
													<select id="' . $row['field'] . '" name="' . $row['field'] . '">
														<option value="">{select}</option>
													';
													
													while($row2 = $db->fetcharray($get_options))
													{
														$sel = ( $row2['id'] == $custom[$row['field']] ) ? ' selected' : '';
														echo '<option value="' . $row2['id'] . '"' . $sel . '>' . $row2['name'] . '</option>';
													}
													
													echo '</select>';
												}
											}
										}
									
										echo '
											</div><!-- /.controls -->
										</div><!-- /.control-group -->
										';
									}
								}
								
							   	?>
					
					        </div><!-- /.span4 -->
					        <?php } ?>
					        <?php if ( $conf['show_calendar'] == 'ON' ) { ?>
					        <div class="span12">
					           	<h2><span>{@listing_step5}</span></h2>
					
							    <div class="control-group">
							        <div class="controls">
					
									<textarea name="calendar" id="calendar_output" style="display:none;"><?php echo unsafehtml( $custom['calendar'] ); ?></textarea>
									<input type="hidden" name="dateTemp" id="dateTemp" value="" />
									<div id="calendar-container"></div>
									
									<script type="text/javascript">
									function dateAvailable(y, m, d) {
										m = (++m < 10) ? "0" + m : m;
										d = (d < 10) ? "0" + d : d;
										return document.getElementById("calendar_output").value.indexOf("" + m + "/" + d + "/" + y + ",");
									}
									
									function toggleDateAvailable(cal) {
										var storage = document.getElementById("calendar_output");
										var d = document.getElementById("dateTemp").value + ",";
										var dateParts = document.getElementById("dateTemp").value.split("/");
										var date = new Date(dateParts[2], dateParts[0] - 1, dateParts[1]);
										var list = storage.value;
										var pos = list.indexOf(d);
									
										if (pos >= 0) {
											if (list.length == 11)
											{
												storage.value = "";
											} else if (pos == 0) {
												storage.value = list.substring(pos + 11);
											} else {
												storage.value = list.substring(0, pos) + list.substring(pos + 11);
											}
										} else {
											storage.value += d;
										}
										
										cal._init(cal.firstDayOfWeek, date);
									}
									
									var today = new Date();
									today.setHours(0, 0, 0 ,0);
									Calendar.setup({
										flat         : "calendar-container", // ID of the parent element
										dateStatusFunc : function(date, y, m, d) {
										if (date < today) {
											return true;
										} else if (dateAvailable(y, m, d) >= 0) {
											return "special";
										}
											return false;
										},
										inputField  : "dateTemp",
										ifFormat    : "%m/%d/%Y",
										weekNumbers : false,
										onUpdate    : function (cal) {
										if (cal.dateClicked) {
											toggleDateAvailable(cal);
										}
										},
											range : new Array('<?php echo date( 'Y' ) . ', ' . ( date( 'Y' ) + 1 ); ?>')
										}
									);
									</script>

									<script type="text/javasscript">
									(function($){
										$(document).ready(function () {
											$("#calendarBox").show();
										});
									})(jQuery);
									</script>
									<div id="calendar-legend">
										<p class="calendarAvailable"><span><?php echo $lang['Availability_Calendar_Vacancy']; ?></span></p>
										<p class="calendarUnavailable"><span><?php echo $lang['Availability_Calendar_No_Vacancy']; ?></span></p>
									</div>
					
							        </div><!-- /.controls -->
							    </div><!-- /.control-group -->
					
					        </div><!-- /.span4 -->
					        <?php } ?>
					        <div class="span12">
					        
							    <div class="form-actions">
							        <input type="submit" name="submit" class="btn btn-primary arrow-right" value="{submit}">
							    </div><!-- /.form-actions -->  
							    
							    <br /><br />
							    
							    <a href="edituserlistings.php?listing_id=<?php echo $_REQUEST['listing_id']; ?>&action=delete_listing">{remove_listing}</a>
					        
					        </div>
					    </div><!-- /.row -->
					</form>
					<?php } ?>

			    </div><!-- /.content -->
			</div><!-- /.list-your-property-form -->
        </div>
    </div>
</div>