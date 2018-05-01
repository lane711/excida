<div class="container">
    <div id="main">
        <div class="row">
            <div class="span4">
                <h1 class="page-header">{heading}</h1>
                
                <p>{@login_text}</p>
                
                <p>{output_message}</p>

				<form method="post" class="contact-form" action="login.php">
				
				    <div class="control-group">
				        <label class="control-label" for="login">
				            {@login}
				            <span class="form-required" title="This field is required.">*</span>
				        </label>
				        <div class="controls">
				            <input type="text" id="login" name="login" value="{login}">
				        </div><!-- /.controls -->
				    </div><!-- /.control-group -->

				    <div class="control-group">
				        <label class="control-label" for="password">
				            {@password}
				            <span class="form-required" title="This field is required.">*</span>
				        </label>
				        <div class="controls">
				            <input type="password" id="password" name="password" value="{password}">
				        </div><!-- /.controls -->
				    </div><!-- /.control-group -->
				    
				    <br clear="both">
				
				    <div class="form-actions">
				        <input type="submit" name="submit" class="btn btn-primary arrow-right" value="{@login}">&nbsp;&nbsp;
				        <a href="register.php">{@register}</a> | <a href="reminder.php">{password_reminder}</a>
				    </div><!-- /.form-actions -->
				</form>

			</div>
		</div>
	</div>
</div>