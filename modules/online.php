<?php

if (!defined('PMR') || (defined('PMR') && PMR != 'true')) die();

 $sql = 'DELETE FROM ' . ONLINE_TABLE . ' WHERE time < ' . (time() - 600);
 $r = $db->query($sql);

 if (isset($_SESSION['login'])) {

  $sql = 'SELECT * FROM ' . ONLINE_TABLE . ' WHERE username = "' . $_SESSION['login'] . '"';
  $r = $db->query($sql);

  if ($db->numrows($r) > 0)
   $db->query( 'UPDATE ' . ONLINE_TABLE . ' SET time = "' . time() . '" WHERE username = "' . $_SESSION['login'] . '"' ) or die (mysql_error());
  else 
   $db->query( 'INSERT INTO ' . ONLINE_TABLE . ' (username, time) VALUES ("' . $_SESSION['login'] . '",  "' . time() . '")' )  or die (mysql_error());
 }   

 $sql = 'SELECT * FROM ' . ONLINE_TABLE;
 $r = $db->query($sql);

 if ($db->numrows($r) > 0) {

  echo table_header ( $lang['Agents_Online'] ); 

  while ($f=$db->fetcharray($r)) {
   $sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE login = "' . $f['username'] . '" AND approved = 1 LIMIT 1';
   $r_user = $db->query($sql);
   $f_user = $db->fetcharray($r_user);

   if ($conf['rewrite'] == 'ON')
    echo '<ul><li class="arrow"><a href="' . URL . '/Realtor/' . $f_user['id'] . '.html">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a></li></ul>';
   else
    echo '<ul><li class="arrow"><a href="' . URL . '/viewuser.php?id=' . $f_user['id'] . '">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a></li></ul>';
  }

  echo table_footer ( ); 

 }

?>