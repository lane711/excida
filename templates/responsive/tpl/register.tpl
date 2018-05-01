<div class="container">

    <div id="main">
        <div class="row">
        	<div class="span12">
				<h1 class="page-header">{header}</h1>
	        
		        <p>{register_text}</p>
		        
		        {output_message}
	        
		        <?php if ( $custom['show_packages'] == true ) { ?>
		        <h1 class="page-header">{packages_header}</h1>                
		        {package_list}
		        <?php } ?>
	        
				<?php if ( $custom['hide_form'] == false ) { ?>
				<form method="post" class="contact-form" action="register.php">
				
					<h3>Account Information</h3>
					
					<div class="row">
						<div class="span4">							
							<div class="control-group">
						        <label class="control-label" for="inputContactEmail">
						            {@email}
						            <span class="form-required" title="This field is required.">*</span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputContactEmail" name="email" value="{email}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">		
							<div class="control-group">
						        <label class="control-label" for="inputUsername">
						            {@login}
						            <span class="form-required" title="This field is required.">*</span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputUsername" name="login" value="{login}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->	
						</div>
					</div>
					
					<div class="row">
						<div class="span4">						    
						    <div class="control-group">
						        <label class="control-label" for="inputPassword">
						            {@password}
						            <span class="form-required" title="This field is required.">*</span>
						        </label>
						        <div class="controls">
						            <input type="password" id="inputPassword" name="password" value="{password}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
						    <div class="control-group">
						        <label class="control-label" for="inputPassword2">
						            {@password_confirm}
						            <span class="form-required" title="This field is required.">*</span>
						        </label>
						        <div class="controls">
						            <input type="password" id="inputPassword2" name="password_confirm" value="{password_confirm}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->	
						</div>
					</div>
					
					
					<h3 class="clearfix">Personal Information</h3>
					
					<div class="row">
						<div class="span4">						
							<div class="control-group">
						        <label class="control-label" for="inputFirstName">
						            {@first_name}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputFirstName" name="first_name" value="{first_name}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputLastName">
						            {@last_name}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputLastName" name="last_name" value="{last_name}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
					
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputCompany">
						            {@company}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputCompany" name="company" value="{company}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
					</div>
					
					<div class="row">
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputPhone">
						            {@phone}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputPhone" name="phone" value="{phone}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputFax">
						            {@fax}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputFax" name="fax" value="{fax}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputMobile">
						            {@mobile}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputMobile" name="mobile" value="{mobile}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
					</div>
				
					<div class="row">					
						<div class="span4">						    
						    <div class="control-group">
						        <label class="control-label" for="inputAddress">
						            {@address}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputAddress" name="address" value="{address}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<div class="control-group">
				                <label class="control-label" for="inputLocation">
				                    {@location}
				                    <span class="form-required" title="This field is required."></span>
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
							<div class="control-group">
						        <label class="control-label" for="inputZip">
						            {@zip}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputZip" name="zip" value="{zip}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
					</div>
					
					
					
					<h3 class="clearfix">About You</h3>
					
					<div class="row">
						<div class="span8">
							<div class="control-group">
						        <label class="control-label" for="inputAbout">
						            {@description}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						
						        <div class="controls">
						            <textarea id="inputAbout" name="description">{description}</textarea>
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
						
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputURL">
						            {@url}
						            <span class="form-required" title="This field is required."></span>
						        </label>
						        <div class="controls">
						            <input type="text" id="inputURL" name="url" value="{url}">
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
					</div>
					
					<?php if ( $conf['captcha_private_key'] != '' && $conf['captcha_public_key'] != '' && $conf['captcha_status'] == 'ON' ) { ?>
					<br />
					
					<div class="row">
						<div class="span4">
							<div class="control-group">
						        <label class="control-label" for="inputMath">
						            &nbsp;
						            <span class="form-required" title="This field is required.">*</span>
						        </label>
						        <div class="controls">
						            <div class="g-recaptcha" data-sitekey="<?php echo $conf['captcha_public_key']; ?>"></div>
						        </div><!-- /.controls -->
						    </div><!-- /.control-group -->
						</div>
					</div>
					<?php } ?>				
				
					<div class="form-actions">
				        <input type="submit" name="submit" class="btn btn-primary arrow-right" value="{register}">
				    </div><!-- /.form-actions -->
			    
				</form>
				<?php } ?>

			</div>
		</div>
	</div>
</div>