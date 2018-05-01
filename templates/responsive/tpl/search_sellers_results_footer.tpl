					{output_message}

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