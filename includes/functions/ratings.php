<?php

// ----------------------------------------------------------------------------
// rating($rating, $votes)
//
// outputs the rating stars depending on the votes and current total rating
//
// $rating - current total rating
// $votes - total number of votes
//

function rating( $rating, $votes ) {

 global $lang;
 global $cookie_template;

 if ($votes != 0) {

  $current_rating = round (($rating / $votes) , 2);

  $full_stars = floor ($rating / $votes);
  $empty_stars = 5 - ceil ($rating / $votes);
  $half_stars = 5 - $full_stars - $empty_stars;

  $output = '';

  for ($i = 0; $i < $full_stars; $i++)
   $output.='<img src="' . URL . '/templates/' . $cookie_template . '/images/star-full.gif" border="0" alt="" />';

  for ($i = 0; $i < $half_stars; $i++)
   $output.='<img src="' . URL . '/templates/' . $cookie_template . '/images/star-half.gif" border="0" alt="" />';

  for ($i = 0; $i < $empty_stars; $i++)
   $output.='<img src="' . URL . '/templates/' . $cookie_template . '/images/star-empty.gif" border="0" alt="" />';

   $output.=' ( ' . $current_rating . ' / ' . $votes . ' ' . $lang['Realtor_Votes'] . ' ) ';

  return $output;

 }
}

?>