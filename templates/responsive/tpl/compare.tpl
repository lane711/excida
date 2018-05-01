<div class="container">
    <div id="content">
        <div id="main">
           
			<div class="pricing boxed">
			    <h2 class="page-header">{header}</h2>
			    
			    <p>{compare_text}</p>
			   
			    <h2 class="page-header">{header_agent}</h2>
			
			    <div class="row">
			    	<?php

					$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE . ' ORDER BY position ASC';
					$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );			
					$total = $db->numrows ( $r );
					if ( $total > 0 )
					{
					
						$num = 1;
						
						if ( $total <= 4 )
						{
							$col_width = 12 / $total;	
						}
						else
						{
							$col_width = 4;
						}	
						
						if ( $total > 0 )
						{
							while( $f = $db->fetcharray( $r ) )
							{
								$f['mainimage'] = ( $f['mainimage'] == 'ON' ) ? '<img src="templates/responsive/images/yes.png">' : '<img src="templates/responsive/images/no.png">';
								$f['phone'] = ( $f['phone'] == 'ON' ) ? '<img src="templates/responsive/images/yes.png">' : '<img src="templates/responsive/images/no.png">';
								$f['photo'] = ( $f['photo'] == 'ON' ) ? '<img src="templates/responsive/images/yes.png">' : '<img src="templates/responsive/images/no.png">';
								$f['address'] = ( $f['address'] == 'ON' ) ? '<img src="templates/responsive/images/yes.png">' : '<img src="templates/responsive/images/no.png">';
							
					    	?>
						        <div class="span<?php echo $col_width; ?>">
						            <div class="column">
						                <h2><?php echo $f['name']; ?></h2>
						                <div class="content">
						                    <h3><?php echo $conf['currency'] . ' ' . $f['price']; ?></h3>
						                    <h4><?php echo $lang['per'] . ' ' . $f['days'] . ' ' . $lang['days']; ?></h4>
						                    <ul class="unstyled">
						                        <li class="important"><?php echo $lang['Admin_Agent_Packages_Listings']; ?>: <?php echo $f['listings']; ?></li>
						                        <li class="important"><?php echo $lang['Admin_Agent_Packages_Gallery']; ?>: <?php echo $f['gallery']; ?></li>
						                        <li><?php echo $lang['Admin_Agent_Packages_Mainimage']; ?>: <?php echo $f['mainimage']; ?></li>
						                        <li><?php echo $lang['Admin_Agent_Packages_Photo']; ?>: <?php echo $f['photo']; ?></li>
						                        <li><?php echo $lang['Admin_Agent_Packages_Phone']; ?>: <?php echo $f['phone']; ?></li>
						                        <li><?php echo $lang['Admin_Agent_Packages_Address']; ?>: <?php echo $f['address']; ?></li>
						                        
						                    </ul>
						                </div><!-- /.content -->
						            </div><!-- /.column -->
						        </div><!-- /.span3 -->
					        <?php
	
					        	$num++;
					        	
					        	if ( $num % 4 == 0 )
					        	{
					        		echo '</div><!-- /.row -->';
					        		echo '<div class="row">';
					        	}
					        }
					    }   
					}
					?>
			    </div><!-- /.row -->
			    
			    <br /><br />
			    
			    <h2 class="page-header">{header_property}</h2>

			    <div class="row">
			    	<?php

					$sql = 'SELECT * FROM ' . PACKAGES_TABLE . ' ORDER BY position ASC';
					$r = $db->query( $sql ) or error ('Critical Error', mysql_error () );
					$num = 1;
					$total = $db->numrows ( $r );
					
					if ( $total > 0 )
					{
					
						if ( $total <= 4 )
						{
							$col_width = 12 / $total;	
						}
						else
						{
							$col_width = 4;
						}					
						
						
						if ( $total > 0 )
						{
							while( $f = $db->fetcharray( $r ) )
							{
					    	?>			    
						        <div class="span<?php echo $col_width;?>">
						            <div class="column">
						                <h2><?php echo $f['name']; ?></h2>
						                <div class="content">
						                    <h3><?php echo $conf['currency'] . ' ' . $f['price']; ?></h3>
						                    <h4><?php echo $lang['per'] . ' ' . $f['days'] . ' ' . $lang['days']; ?></h4>
						                    <ul class="unstyled">
						                        <li class="important"><?php echo $lang['Featured_Listing_Reason_1']; ?></li>
						                        <li><?php echo $lang['Featured_Listing_Reason_2']; ?></li>
						                    </ul>
						                </div><!-- /.content -->
						            </div><!-- /.column -->
						        </div><!-- /.span3 -->
					        <?php
	
					        	$num++;
					        	
					        	if ( $num % 4 == 0 )
					        	{
					        		echo '</div><!-- /.row -->';
					        		echo '<div class="row">';
					        	}
					        }
					    }   
					 }
					?>
				</div><!-- /.row -->
			</div><!-- /.pricing -->            
        </div>
    </div>
</div>