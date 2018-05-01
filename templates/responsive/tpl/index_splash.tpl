<div class="container">
    <div id="main">

	<div class="slider-wrapper">
	    <div class="slider">
	        <div class="slider-inner">
	            <div class="row">
	                <div class="images span9">
		                
		                <?php if ( $custom['num_listings'] > 1 ) { ?>
	                    
	                    <div class='iosSlider'>
	                        <div class='slider-content'>
							{featured_listings}
	                        </div><!-- /.slider-content -->
	                    </div><!-- .iosSlider -->
	                    
	                    <?php } else { ?> 
						
						<a href="{link}">{featured_listings}</a>
						<h2><a href="{link}">{title}</a></h2>
						
						<?php } ?>
						
						<?php if ( $custom['num_listings'] > 1 ) { ?>
	                    <ul class="navigation">
	                    	<?php
	                    	
	                    	for ( $i = 1; $i <= $custom['num_listings']; $i++ )
		                    {
		                    	$class = ( $i == 1 ) ? ' class="active"' : '';
		                    	echo '<li' . $class . '><a>' . $i . '</a></li>';
		                    }
		                    
	                    	?>
	                    </ul><!-- /.navigation-->
	                    <?php } ?>
	                    
	                </div><!-- /.images -->
	                <div class="span3">
	                    <div class="property-filter">
	                        <div class="content">
	                            <form method="post" action="<?php echo URL; ?>/search_listings_results.php">
	                                <div class="location control-group">
	                                    <label class="control-label" for="inputLocation">
	                                        {@location}
	                                    </label>
	                                    <div class="controls">
	                                        <select name="location1" id="location1">
	                                            {location1}
	                                        </select>
	                                        <select name="location2" id="location2">
	                                        </select>
	                                        <select name="location3" id="location3">
	                                        </select>
	                                    </div><!-- /.controls -->
	                                </div><!-- /.control-group -->
	
	                                <div class="type control-group">
	                                    <label class="control-label" for="inputType">
	                                        {@type}
	                                    </label>
	                                    <div class="controls">
	                                        <select name="type" id="inputType">
					                    		<option value="">{select}</option>
												<?php
						                    
												echo generate_options_list( TYPES_TABLE, $custom['type'] );
						                    
												?>
	                                        </select>
	                                    </div><!-- /.controls -->
	                                </div><!-- /.control-group -->
	
	                                <div class="beds control-group">
	                                    <label class="control-label" for="inputBeds">
	                                        {@bedrooms}
	                                    </label>
	                                    <div class="controls">
	                                        <select name="bedrooms" id="inputBeds">
		                                        <option value="">{select}</option>
	                                            <?php
	                                            
	                                            for ( $i = 1; $i <= 10; $i++ )
	                                            {
	                                            	echo '<option value="' . $i . '">' . $i . '+</option>';
	                                            }
	                                            
	                                            ?>
	                                        </select>
	                                    </div><!-- /.controls -->
	                                </div><!-- /.control-group -->
	
	                                <div class="baths control-group">
	                                    <label class="control-label" for="inputBaths">
	                                        {@bathrooms}
	                                    </label>
	                                    <div class="controls">
	                                        <select name="bathrooms" id="inputBaths">
		                                        <option value="">{select}</option>
	                                            <?php
	                                            
	                                            for ( $i = 1; $i <= 10; $i++ )
	                                            {
	                                            	echo '<option value="' . $i . '">' . $i . '+</option>';
	                                            }
	                                            
	                                            ?>
	                                        </select>
	                                    </div><!-- /.controls -->
	                                </div><!-- /.control-group -->
	
	                                <div class="price-from control-group">
	                                    <label class="control-label" for="price_min">
	                                        Price from
	                                    </label>
	                                    <div class="controls">
	                                        <input type="text" id="price_min" name="price_min">
	                                    </div><!-- /.controls -->
	                                </div><!-- /.control-group -->
	
	                                <div class="price-to control-group">
	                                    <label class="control-label" for="price_max">
	                                        Price to
	                                    </label>
	                                    <div class="controls">
	                                        <input type="text" id="price_max" name="price_max">
	                                    </div><!-- /.controls -->
	                                </div><!-- /.control-group -->
	
	                                <div class="price-value">
	                                    <span class="from"></span><!-- /.from -->
	                                    -
	                                    <span class="to"></span><!-- /.to -->
	                                </div><!-- /.price-value -->
	
	                                <div class="price-slider">
	                                </div><!-- /.price-slider -->
	
	                                <div class="form-actions">
	                                    <input type="submit" value="{@search}" class="btn btn-primary btn-large">
	                                </div><!-- /.form-actions -->
	                            </form>
	                        </div><!-- /.content -->
	                    </div><!-- /.property-filter -->
	
	                </div><!-- /.span3 -->
	            </div><!-- /.row -->
	        </div><!-- /.slider-inner -->
	    </div><!-- /.slider -->
	</div><!-- /.slider-wrapper -->

	</div>
</div>