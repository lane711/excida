<div class="container">
    <div id="main">
    
    	<h1 class="page-header">{header}</h1>
        <div class="row">            
            
            <div class="span8">
            
                <p>{contact_text}</p>
                
                {output_message}

				<?php if ( $custom['display_form'] == true ) { ?>
				<form method="post" class="contact-form" action="contact.php">
				
				    <div class="control-group">
				        <label class="control-label" for="inputContactName">
				            {@name}
				            <span class="form-required" title="This field is required.">*</span>
				        </label>
				        <div class="controls">
				            <input type="text" id="inputContactName" name="name" value="{name}">
				        </div><!-- /.controls -->
				    </div><!-- /.control-group -->
				    
				    <br clear="both">
				
				    <div class="control-group">
				        <label class="control-label" for="inputContactEmail">
				            {@email}
				            <span class="form-required" title="This field is required.">*</span>
				        </label>
				        <div class="controls">
				            <input type="text" id="inputContactEmail" name="email" value="{email}">
				        </div><!-- /.controls -->
				    </div><!-- /.control-group -->
				    
				    <br clear="both">
				
				    <div class="control-group">
				        <label class="control-label" for="inputContactMessage">
				            {@message}
				            <span class="form-required" title="This field is required.">*</span>
				        </label>
				
				        <div class="controls">
				            <textarea id="inputContactMessage" name="message">{message}</textarea>
				        </div><!-- /.controls -->
				    </div><!-- /.control-group -->
				    
				    <br clear="both">
				    
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
				    
				    <br clear="both">
				
				    <div class="form-actions">
				        <input type="submit" name="submit" class="btn btn-primary arrow-right" value="{send}">
				    </div><!-- /.form-actions -->
				</form>
				<?php } ?>				
            </div>
            
            <div class="span4">
               
                <?php if ( $conf['latitude'] != '' && $conf['longitude'] != '' ) { ?>
                <iframe class="map" width="425" height="350" src="https://maps.google.com/maps?q={latitude},{longitude}&amp;num=1&amp;ie=UTF8&amp;ll={latitude},{longitude}&amp;spn=0.041038,0.077162&amp;t=m&amp;z=14&amp;output=embed"></iframe>
				<?php } ?>

                <div class="row">
                    <div class="span4">
                        <h3 class="address">{@address}</h3>
                        <p class="content-icon-spacing">
                            {conf_address1}<br />
                            {conf_city}, {conf_state}<br />
                            {conf_country}
                        </p>
                    </div>
                    <div class="span4">
                        <h3 class="call-us">{@phone}</h3>
                        <p class="content-icon-spacing">
                            <a href="tel:{conf_phone}">{conf_phone}</a>
                        </p>
                    </div>
                    <div class="span4">
                        <h3 class="email">{@email}</h3>
                        <p class="content-icon-spacing">
                            <a href="mailto:{conf_email}">{conf_email}</a>
                        </p>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>