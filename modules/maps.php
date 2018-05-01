<?php

if (!defined('PMR') || (defined('PMR') && PMR != 'true')) die();

// Title/descr language to use (if available)
$title = str_replace( 'name', 'title', $language_in );
$description = str_replace( 'name', 'description', $language_in );

$sql = 'SELECT ' . $title . ', ' . $description . ', ' . PROPERTIES_TABLE . '.* FROM ' . PROPERTIES_TABLE  . ' WHERE approved = 1 ORDER BY id DESC LIMIT 200';

$r_maps = $db->query( $sql );

if ($db->numrows($r_maps) > 0) {

	echo table_header ( $lang['View_Map'] );
	
	echo '<div align="center">';
	 
	// Customization options
	$map->setZoomLevel( $conf['map_zoom'] );
	$map->setWidth( $conf['map_width'] );
	$map->setHeight( $conf['map_height'] );
	$map->setBackgroundColor('#d0d0d0');
	$map->setMapDraggable(true);
	$map->setDoubleclickZoom(false);
	$map->setScrollwheelZoom(true);
	
	$map->showDefaultUI(false);
	$map->showMapTypeControl(true, 'DROPDOWN_MENU');
	$map->showNavigationControl(true, 'DEFAULT');
	$map->showScaleControl(true);
	$map->showStreetViewControl(true);
	
	$map->setInfoWindowBehaviour('SINGLE_CLOSE_ON_MAPCLICK');
	$map->setInfoWindowTrigger('CLICK');

 while ($f_maps = $db->fetcharray( $r_maps )) {
 
	// Default
	if (trim($f_maps[0]) != '')
		$f_maps['title'] = trim($f_maps[0]);
		
	if (trim($f_maps[1]) != '')
		$f_maps['description'] = trim($f_maps[1]);

  if (file_exists( PATH . '/images/' . $f_maps['id'] . '-resampled.jpg'))
   $image_link = '<img src=\"' . URL . '/images/' . $f_maps['id'] . '-resampled.jpg\" border=\"0\" alt=\"\" />';
  else
   $image_link = '';

  if ($conf['rewrite'] == 'ON')
   $link = URL . '/Listing/' . rewrite(getnamebyid(TYPES_TABLE, $f_maps['type'])) . '/' . $f_maps['id'] . '_' . rewrite($f_maps['title']) . '.html';
  else
   $link = URL . '/viewlisting.php?id=' . $f_maps['id'];

  $link = '<a href="' . $link . '"><strong>' . $f_maps['title'] . '</strong></a> <br />' . $image_link . '<br /><br />' . $f_maps['address1'] . ' ' . $f_maps['address2'];

  if (strcasecmp($conf['show_postal_code'], 'OFF')) {
    $link .= '<br />' . $f_maps['zip'];
  }

  $cat = explode ("#", $f_maps['location2']);
  $r11 = $db->query('SELECT category FROM ' . LOCATION1_TABLE . ' WHERE selector = "' . $cat[0] . '"');
  $f11 = $db->fetcharray($r11);

  $r22 = $db->query('SELECT subcategory FROM ' . LOCATION2_TABLE . ' WHERE catsubsel = "' . $cat[1] . '"');
  $f22 = $db->fetcharray($r22);

  $r33 = $db->query('SELECT subsubcategory FROM ' . LOCATION3_TABLE . ' WHERE catsubsubsel = "' . $cat[2] . '"');
  $f33 = $db->fetcharray($r33);

	if ( $conf['map_value'] == 'c' && $f['latitude'] != '' && $f['longitude'] != '' )
	{
		$map->addMarker( $f_maps['latitude'], $f_maps['longitude'], $f_maps['title'], $link);
	} 
	else
	{
		$map->addMarkerByAddress($f_maps['address1'] . ' ' . $f_maps['address2'] . ' ' . $f33['subsubcategory'] . ' ' . $f22['subcategory'] . ' ' . $f11['category'] . ' ' . $f_maps['zip'], $f_maps['title'], $link);
	}
 }

 $map->showMap( false );

 echo '</div>';

 echo table_footer ();

}

?>