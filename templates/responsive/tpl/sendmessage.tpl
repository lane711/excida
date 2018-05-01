<div class="container">
    <div id="main">
        <div class="row">
            <div class="span12">
                <h1 class="page-header">{header}</h1>
                
                {output_message}

				<?php if ( $custom['display_form'] == true ) { ?>
				<form method="post" class="contact-form" action="sendmessage.php">
				<input type="hidden" name="u_id" value="<?php echo $_REQUEST['u_id']; ?>">
				<input type="hidden" name="listing_id" value="<?php echo $_REQUEST['listing_id']; ?>">
				
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
		</div>
	</div>
</div>