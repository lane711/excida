<div class="container">
    <div>
        <div id="main">
        
		<div class="list-your-property-form">
		    <h2 class="page-header">{@heading}</h2>
		
		    <div class="content">
		        <div class="row">
		            <div class="span8">
		            
		                <p>
		            		<a href="adduserlistings.php">{@add_listing}</a> | 
		            		<a href="viewuserlistings.php">{@edit_listings}</a> | 
		            		<a href="user.php">{@control_panel}</a>
		            	</p>
		                
		                <p>{output_message}</p>
		                
				        <?php if ( $custom['show_packages'] == true ) { ?>
				        <h1 class="page-header">{packages_header}</h1>                
				        {package_list}
				        <?php } ?>
		                
		            </div><!-- /.span8 -->
		        </div><!-- /.row -->
		
		        <form method="post" action="user.php" enctype="multipart/form-data">
		            <div class="row">
		                <div class="span5">
		                    <h3><span>{@personal_info}</span></h3>
		
		                    <div class="control-group">
		                        <label class="control-label" for="inputFirstName">
		                            {@firstname}
		                            <span class="form-required" title="This field is required."></span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_first_name" value="{firstname}" id="inputFirstName">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		
		                    <div class="control-group">
		                        <label class="control-label" for="inputLastName">
		                            {@lastname}
		                            <span class="form-required" title="This field is required."></span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_last_name" value="{lastname}" id="inputLastName">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		
		                    <div class="control-group">
		                        <label class="control-label" for="inputPropertyEmail">
		                            {@email}
		                            <span class="form-required" title="This field is required.">*</span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_e_mail" value="{email}" id="inputPropertyEmail">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		                    
		                    <div class="control-group">
		                        <label class="control-label" for="inputPass">
		                            {@password}
		                            <span class="form-required" title="This field is required.">*</span>
		                        </label>
		                        <div class="controls">
		                            <input type="password" name="realtor_password" value="{password}" id="inputPass">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		                  
		                    <div class="control-group">
		                        <label class="control-label" for="inputDescription">
		                            {@description}
		                        </label>
		                        <div class="controls">
		                            <textarea id="inputDescription" class="ckeditor" name="realtor_description">{description}</textarea>
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->

		                </div><!-- /.span5 -->
		
		                <div class="span5">
		                    <h3><span>{@contact_info}</span></h3>

		                    <div class="control-group">
		                        <label class="control-label" for="inputCompany">
		                            {@company}
		                            <span class="form-required" title="This field is required."></span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_company_name" value="{company}" id="inputCompany">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->

		                    <div class="control-group">
		                        <label class="control-label" for="inputAddress">
		                            {@address}
		                            <span class="form-required" title="This field is required."></span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_address" value="{address}" id="inputAddress">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->

				            <div class="control-group">
				                <label class="control-label" for="inputLocation">
				                    {@location}
				                    <span class="form-required" title="This field is required."></span>
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
		                    
							<?php if ( $conf['show_postal_code'] != 'OFF' ) { ?> 
		                    <div class="control-group">
		                        <label class="control-label" for="inputZip">
		                            {@zip}
		                            <span class="form-required" title="This field is required."></span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_zip_code" value="{zip}" id="inputZip">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		                    <?php } ?>
		                    
		                    <div class="bathrooms control-group">
		                        <label class="control-label" for="inputPhone">
		                            {@phone}
		                            <span class="form-required" title="This field is required."></span>
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_phone" value="{phone}" id="inputPhone">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		                    
		                    <div class="area control-group">
		                        <label class="control-label" for="inputMobile">
		                            {@mobile}
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_mobile" value="{mobile}" id="inputMobile">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		
		                    <div class="control-group">
		                        <label class="control-label" for="inputFax">
		                            {@fax}
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_fax" value="{fax}" id="inputFax">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		
		                    <div class="control-group">
		                        <label class="control-label" for="inputURL">
		                            {@url}
		                        </label>
		                        <div class="controls">
		                            <input type="text" name="realtor_website" value="{url}" id="inputURL">
		                        </div><!-- /.controls -->
		                    </div><!-- /.control-group -->
		                  
		                </div><!-- /.span5 -->
		
		                <div class="span2">
		                    <h3><span>{@image_info}</span></h3>
		                    
		                    <?php
		                    
		                    if ( $custom['image'] != '' )
		                    {
		                    	echo '<img src="{image}" border="0">';
		                    }
		                    else
		                    {
		                    	echo '{image}';
		                    }
		                    
		                    ?>
		                    
		                    <br /><br />
		
		                    <div class="fileupload fileupload-new control-group" data-provides="fileupload">		
		                        <div class="input-append">
		                            <div class="uneditable-input">
		                                <i class="icon-file fileupload-exists"></i>
		                                <span class="fileupload-preview"></span>
		                            </div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Select file</span>
                                        <span class="fileupload-exists">Change</span>
                                        <input type="file" name="submit_logo" />
                                    </span>
		                        </div><!-- /.input-append -->
		                    </div><!-- .fileupload -->
		                    
		                    <?php if ( $custom['image'] != '' ) { ?>
		                    <a href="user.php?action=remove_logo">{@remove_logo}</a>
		                    <?php } ?>
		                    
		                    <br /><br />
		                    
		                    <strong>{@added}:</strong><br />{added}<br /><br />
		                    <strong>{@updated}:</strong><br />{updated}<br /><br />
		                    <strong>{@hits}:</strong><br />{hits}
		                    
		                    <br /><br />
		                    
		                    <strong>{@package_name}:</strong><br />{package_name}<br /><br />
		                    <strong>{@package_date}:</strong><br />{package_date}
		                </div><!-- /.span2 -->
		            </div><!-- /.row -->
		            
		            <br />
		            
					<div class="form-actions">
					<input type="submit" name="submit" class="btn btn-primary arrow-right" value="{@submit}">
					</div><!-- /.form-actions -->
		            
		        </form>
		    </div><!-- /.content -->
		</div><!-- /.list-your-property-form -->

        </div>
    </div>
</div>