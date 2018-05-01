<div class="container">

    <div id="main">
        <div class="row">
            <div class="span12">
                <h1 class="page-header">{header}</h1>
                
                {output_message}

				<form method="post" action="<?php echo URL; ?>/search_sellers_results.php">
				
					<h3>{@by_location}</h3>
					
					<div class="row">
						<div class="span4">
							<div class="control-group">
				                <label class="control-label" for="inputPropertyLocation">
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
						</div>
						
						<div class="span4">
							<div class="bathrooms control-group">
				                <label class="control-label" for="inputAddress">
				                    {@address}
				                </label>
				                <div class="controls">
				                    <input type="text" name="address" value="{address}" id="inputAddress">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<?php if ( $conf['show_postal_code'] == 'ON' ) { ?>
						    <div class="control-group">
						        <label class="control-label" for="zip">
						            {@zip}
						        </label>
						        <div class="controls">
						            <input type="text" id="zip" name="zip" value="{zip}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						    <?php } ?>
						</div>
					</div>
					
					<div class="form-actions">
						<input type="submit" name="submit" class="btn btn-primary arrow-right" value="{search}">
					</div><!-- /.form-actions -->  
					
					<hr>
					
					<h3>{@by_name}</h3>
					
					<div class="row">
						<div class="span4">
							<div class="bathrooms control-group">
				                <label class="control-label" for="firstName">
				                    {@first_name}
				                </label>
				                <div class="controls">
				                    <input type="text" name="first_name" value="{first_name}" id="firstName">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->

						</div>
						
						<div class="span4">
							<div class="bathrooms control-group">
				                <label class="control-label" for="lastName">
				                    {@last_name}
				                </label>
				                <div class="controls">
				                    <input type="text" name="last_name" value="{last_name}" id="lastName">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<div class="bathrooms control-group">
				                <label class="control-label" for="company">
				                    {@company_name}
				                </label>
				                <div class="controls">
				                    <input type="text" name="company_name" value="{company_name}" id="company">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->

						</div>
					</div>	
					
					<div class="form-actions">
						<input type="submit" name="submit" class="btn btn-primary arrow-right" value="{search}">
					</div><!-- /.form-actions -->  
					
					<hr>
					
					<h3>{@advanced}</h3>
					<div class="row">
						<div class="span3">
							<div class="bathrooms control-group">
				                <label class="control-label" for="keyword">
				                    {@keyword}
				                </label>
				                <div class="controls">
				                    <input type="text" name="keyword" value="{keyword}" id="keyword">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
						</div>
						
						<div class="span3">
							<div class="bathrooms control-group">
				                <label class="control-label" for="email">
				                    {@email}
				                </label>
				                <div class="controls">
				                    <input type="text" name="email" value="{email}" id="email">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
						</div>
						
						<div class="span3">
							<div class="bathrooms control-group">
				                <label class="control-label" for="phone">
				                    {@phone}
				                </label>
				                <div class="controls">
				                    <input type="text" name="phone" value="{phone}" id="phone">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
						</div>
						
						<div class="span3">
							<div class="bathrooms control-group">
				                <label class="control-label" for="mobile">
				                    {@mobile}
				                </label>
				                <div class="controls">
				                    <input type="text" name="mobile" value="{mobile}" id="mobile">
				                </div><!-- /.controls -->
				            </div><!-- /.control-group -->
						</div>
					</div>				
					 
				    <br />
				    
					<div class="form-actions">
					<input type="submit" name="submit" class="btn btn-primary arrow-right" value="{search}">
					</div><!-- /.form-actions -->  

				</form>

            </div>
        </div>
    </div>
</div>