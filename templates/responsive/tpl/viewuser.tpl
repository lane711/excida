<div class="container">
    <div>
        <div id="main">

			<div class="our-agents-large">
			    <h2 class="page-header">{@heading}</h2>
			    
			    <p>{output_message}</p>
			
				<?php if ( $custom['show_profile'] == true ) { ?>
			    <div class="content">
			        <div class="agent">
			            <div class="row">
			                <div class="image span2">
			                    <br />
			                    
			                    <img src="{show_image}" border="0">
			                    
			                    <br /><br />
			                    
			                    {rating}
			                    
			                    <br /><br />
			                   
								<form action="<?php echo URL; ?>/viewuser.php?id=<?php echo $_REQUEST['id']; ?>" method="post">															
									<select name="vote" onChange="this.form.submit()">
									<option value="">{rate_listing}</option>
									<option value="5">{vote_5}</option>    
									<option value="4">{vote_4}</option>    
									<option value="3">{vote_3}</option>    
									<option value="2">{vote_2}</option>    
									<option value="1">{vote_1}</option> 
								</select>
								</form>
			                    
			                    <strong>{@added}:</strong><br />{added}<br /><br />
			                    <strong>{@updated}:</strong><br />{updated}<br /><br />
			                    <strong>{@hits}:</strong><br />{hits}
	                    
			                </div><!-- /.image -->
			
			                <div class="body span6">
			                    <h3>{company_name}</h3>
			                    <p>{description}</p>
			                </div><!-- /.body -->
			
			                <div class="info span4">
			                    <div class="box">
			                    
			                    	<?php if ( $custom['show_seller_details'] == true ) { ?>
			                        <div class="phone">{mobile}</div>
			                        <div class="office">{phone}</div>
			                        <div class="email"><a href="{send_message}">{@send_message}</a></div>	
			                        
			                        <br />
			                        
			                        {address}<br />
									{city} {state} {country} {zip}<br />
									
									<br />
									
									{@fax}: {fax}<br />
									<a href="{url}" target="_blank">{url}</a><br />
									<?php } else { ?>
									{seller_details_restriction}
									<?php } ?>
			                        
			                    </div>
			                </div><!-- /.info -->
			            </div><!-- /.row -->
			        </div><!-- /.agent -->
				
				</div><!-- /.content -->
				<?php } ?>
				
			</div><!-- /.our-agents-large -->

		</div><!-- /.main -->
	</div>
</div><!-- /.container -->

<br />

<?php if ( $custom['show_profile_listings'] == true ) { ?>
<div class="container">
    <div id="main">
        <div class="row">
            <div class="span12">
                <h1 class="page-header">{@heading2}</h1>

			    <div class="content">
			        
					<div class="properties-grid">
					    <div class="row">
					    
					    {user_listings}
	
					    </div><!-- /.row -->
					</div><!-- /.properties-grid -->
			
					<div class="pagination pagination-centered">
					    <ul>			        
				        <?php
	
				        if ( is_array( $custom['pagination'] ) )
				        {	
				        	$num = 1;
					        foreach ( $custom['pagination'] AS $page )
					        {				        	
					        	if ( $_REQUEST['page'] == $page['page'] || ( $_REQUEST['page'] == '' && $num == 1 ) )
					        	{
					        		$class = ' class="active"';
					        	}
					        	else
					        	{
					        		$class = '';
					        	}
					        	
					        	echo '<li' . $class . '><a href="' . $page['url'] . '">' . $page['page'] . '</a></li>';
					        
								$num++;
					        }
				        }
				        
				        ?>
					    </ul>
					</div><!-- /.pagination -->
				
				</div>

            </div>
        </div>
    </div>
</div>
<?php } ?>