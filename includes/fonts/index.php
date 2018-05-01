<?php
/**
 * Prevent directory browsing
 */
include '../config.php';
header('Location: '.URL.'/');
exit;
?>