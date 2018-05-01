<!-- Footer : start -->

<!-- Main Content table : stop -->
     </td>
    </tr>
   </table>

  </td>
 </tr>
</table>

<?php

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
 {

	if (isset($announcement_check) || isset($version_check)) { ?>
	
	   <?php echo table_header ( $lang['Admin_Updates'] ); ?>
	   
		<div class="col8">
	    
			<?php
			
			// Announcements check
			if (isset($announcement_check))
			{
				$ch = curl_init( 'http://www.realtyscript.com/announcements' );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
				$announcement_in = curl_exec( $ch );
				curl_close($ch);
				if ($announcement_in != '') 
					echo $announcement_in;
			}
	
			?>
			
		</div>
		<div class="col4 last">
			
			<?php
	
			// Version check
			if (isset($version_check))
			{
				$ch = curl_init( 'http://www.realtyscript.com/version' );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
				$version_in = curl_exec( $ch );
				curl_close( $ch );
				if ( $version_in != VERSION ) 
					echo '<a href="http://www.realtyscript.com" style="padding:5px; color:white; background-color:red; font-weight: bold;">Upgrade Available! Get version ' . $version_in . '</a>';
				else
					echo 'No updates available.';
			}
	
			?>
			
		</div>
		
		<?php echo table_footer(); ?>

	<?php } ?>

<?php } ?>

  </td>
 </tr>
</table>

</div>

<!-- Footer : end -->

<?php include ( PATH . '/includes/common_footer.php' ); ?>

</body>
</html>