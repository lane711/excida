<div class="container">
	<div id="main">
		<div class="row">
			<form method="post" class="contact-form" action="signup.php">
				<h1 class="page-header">{heading}</h1>
					<p>{@sign_text}</p>
					<p>{output_message}</p>
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for="login">
							{@first_name}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="text" id="first_name" name="first_name" value="{first_name}">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->

					<div class="control-group">
						<label class="control-label" for="login">
							{@last_name}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="text" id="last_name" name="last_name" value="{last_name}">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->

					<div class="control-group">
						<label class="control-label" for="password">
							{@email}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="email" id="email" name="email" value="{email}">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->

					<br clear="both">

					<!-- <div class="form-actions">
						<input type="submit" name="submit" class="btn btn-primary arrow-right" value="Sign Up">&nbsp;&nbsp;
						 <a href="register.php">{@register}</a> | <a href="reminder.php">{password_reminder}</a>
					</div> -->
				</div>
				<div class="span4">
					<div class="control-group">
						<label class="control-label" for="login">
							{@company}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="text" id="company" name="company" value="{company}">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->

					<div class="control-group">
						<label class="control-label" for="login">
							{@jobtitle}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="text" id="jobtitle" name="jobtitle" value="{jobtitle}">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->
					<div class="control-group">
						<label class="control-label" for="login">
							{@phone}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="text" id="phone" name="phone" value="{phone}">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->
				</div>
				<br clear="both">
				<div class="span8">
					<div class="control-group">
						<label class="control-label" for="website url">
							{@website_url}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="text" id="wesite_url" name="website_url" value="{website_url}" placeholder="http://">
						</div><!-- /.controls -->
					</div><!-- /.control-group -->

					

				</div>
				<div class="span8">
					<div class="control-group">
						<!--<label class="control-label" for="login">
							{@website_lead_text}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<input type="radio" id="website_radio_text" name="website_radio_text" value="{website_radio_text}">

							<label class="control-label" for="login">
							&nbsp;&nbsp;{@website_radio_text}
							<span class="form-required" title="This field is required."></span>

						</label></br>
						<input type="radio" selected="" id="website_radio_text2" name="website_radio_text" value="{website_radio_text}">

							<label class="control-label" for="login">
							&nbsp;&nbsp;{@website_radio_text2}
							<span class="form-required" title="This field is required."></span>-->
						<!-- /.controls -->
					</div>
					<!-- /.control-group -->

					<div class="control-group">
						<label class="control-label" for="login">
							{@website_textarea}
							<span class="form-required" title="This field is required.">*</span>
						</label>
						<div class="controls">
							<textarea id="website_textarea" name="website_textarea">{website_textarea}</textarea>
						</div><!-- /.controls -->
					</div><!-- /.control-group -->
					<br clear="both">

					<div class="form-actions">
						<input type="submit" name="submit" class="btn btn-primary arrow-right" value="Sign Up">&nbsp;&nbsp;
						<!-- <a href="register.php">{@register}</a> | <a href="reminder.php">{password_reminder}</a> -->
					</div><!-- /.form-actions -->
					
				</div>
			</form>
		</div>

	</div>
</div>