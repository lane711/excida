<?php

if ( $conf['main_search'] == 'ON' )
{
	// Include index page splash box (search/listings usually)
	include PATH . '/modules/index_splash.php';
}

?>

<div class="container">
    <div id="main">
        <div class="row">
            <div class="span9">
            
			<?php
			
			// Include Featured Listings box
			if ( $conf['featured_listings'] == 'ON' )
			{
				echo '<h1 class="page-header">' . $lang['Module_Featured_Listings'] . '</h1>';
				include PATH . '/modules/featured.php';
			}
			
			// Include Featured Agents box
			/*
			if ( $conf['featured_agents'] == 'ON' )
			{
				include PATH . '/modules/featured-sellers.php';
			}
			*/
			
			// All listings
			/*
			if ( $conf['all_listings'] == 'ON' )
			{
				include PATH . '/modules/all-listings.php';
			}
			*/
			
			// All Agents
			if ($conf['all_agents'] == 'ON')
			{		
				include PATH . '/modules/all-sellers.php';
			}
			
			// Tabbed box with Most Visited, Recent Listings, Recent Agents and Recently Visited Listings
			/*
			if ( $conf['recent_listings'] == 'ON' )
			{	
				// Include Most Visited Listings box
				include PATH . '/modules/mostvisited.php';
				
				// Include Recent Agents box
				include PATH . '/modules/recent-sellers.php';
				
				// Include Recent Listings box
				include PATH . '/modules/recent.php';
				
				// Include Recently Visited Listings
				include PATH . '/modules/visited.php';
			}
			*/
				
			?>
            
            </div>
            <div class="sidebar span3">
                <?php include PATH . '/modules/all-sellers.php'; ?>
                <div class="hidden-tablet">
                	<?php include PATH . '/modules/recent.php'; ?>
                </div>
            </div>
        </div>
        <?php //include PATH . '/modules/slideshow.php'; ?>
    </div>
</div>