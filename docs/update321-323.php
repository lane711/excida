<?php

include ('../config.php');
include ( PATH . '/includes/functions.php');
include ( PATH . '/includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

// New in 3.2.3
$db->query('INSERT IGNORE INTO ' . CONFIGURATION_TABLE . ' (name, val, descr)
    VALUES ("show_mls", "ON", "Enable or disable showing the MLS field (ON/OFF)"),
           ("show_postal_code", "ON", "Enable or disable showing the ZIP code field (ON/OFF)"),
           ("paypal_mode", "LIVE", "Production PayPal payment gateway (LIVE) or the sandbox test environment (TEST)."),
           ("free_listings_expire", "0", "Number of days after which a free listing expires."),
           ("slideshow_limit", 25, "Number of listings to show in the slideshow module"),
           ("allow_same_title", "OFF", "Allow duplicate listing property titles (ON/OFF)"),
           ("allow_same_e_mail", "OFF", "Allow multiple accounts with same e-mail address (ON/OFF)"),
           ("price_monthly_range_min", "0", "Search Form: Minimum Monthly Rental Price Range"),
           ("price_monthly_range_max", "10000", "Search Form: Maximum Monthly Rental Price Range"),
           ("price_monthly_range_step", "500", "Search Form: Monthly Rental Price Range Step"),
           ("decimal_point", ".", "Character to use as decimal point (number formatting)"),
           ("1000_separator", ",", "Character to use as thousands separator (number formatting)")')
    or error('Critical Error', mysql_error () );

echo 'New settings were added.<br />';

$db->query('ALTER TABLE '.TYPES2_TABLE.' ADD COLUMN class VARCHAR(31) NOT NULL DEFAULT "sale"')
    or error('Critical Error', mysql_error () );

$db->query('UPDATE '.TYPES2_TABLE.'
            SET class = "monthly"
            WHERE name LIKE "%rent%" OR name LIKE "%subl%"')
    or error('Critical Error', mysql_error() );

echo 'New listing types price categories.<br />';

$db->query("ALTER TABLE `".PROPERTIES_TABLE."`
            ADD COLUMN `date_approved` DATETIME NULL DEFAULT NULL AFTER `date_added`")
    or error('Critical Error', mysql_error () );

$db->query("UPDATE `".PROPERTIES_TABLE."`
            SET `date_approved` = `date_added`
            WHERE approved = 1 AND date_approved IS NULL")
    or error('Critical Error', mysql_error () );

echo 'New approval date added.<br />';

$db->query("CREATE TABLE IF NOT EXISTS `".FIELDS_TABLE."` (
          `id` mediumint(5) NOT NULL auto_increment,
          `name` varchar(50) NOT NULL,
          `type` enum('select','input','textarea') NOT NULL,
          `name2` varchar(255) NOT NULL,
          `name3` varchar(255) NOT NULL,
          `name4` varchar(255) NOT NULL,
          `name5` varchar(255) NOT NULL,
          `name6` varchar(255) NOT NULL,
          `name7` varchar(255) NOT NULL,
          `name8` varchar(255) NOT NULL,
          `name9` varchar(255) NOT NULL,
          `name10` varchar(255) NOT NULL,
          `name11` varchar(255) NOT NULL,
          `name12` varchar(255) NOT NULL,
          `name13` varchar(255) NOT NULL,
          `name14` varchar(255) NOT NULL,
          `name15` varchar(255) NOT NULL,
          `name16` varchar(255) NOT NULL,
          `name17` varchar(255) NOT NULL,
          `name18` varchar(255) NOT NULL,
          `name19` varchar(255) NOT NULL,
          `name20` varchar(255) NOT NULL,
          `name21` varchar(255) NOT NULL,
          `name22` varchar(255) NOT NULL,
          `name23` varchar(255) NOT NULL,
          `name24` varchar(255) NOT NULL,
          `name25` varchar(255) NOT NULL,
          `name26` varchar(255) NOT NULL,
          `name27` varchar(255) NOT NULL,
          `name28` varchar(255) NOT NULL,
          `name29` varchar(255) NOT NULL,
          `name30` varchar(255) NOT NULL,
          `field` varchar(50) NOT NULL,
          PRIMARY KEY  (`id`)
          )
		  CHARACTER SET utf8
		  COLLATE utf8_general_ci
		  ")
    or error('Critical Error', mysql_error () );

$db->query("CREATE TABLE IF NOT EXISTS `".VALUES_TABLE."` (
          `id` mediumint(5) NOT NULL auto_increment,
          `f_id` mediumint(5) NOT NULL,
          `name` varchar(255) NOT NULL,
          `name2` varchar(255) NOT NULL,
          `name3` varchar(255) NOT NULL,
          `name4` varchar(255) NOT NULL,
          `name5` varchar(255) NOT NULL,
          `name6` varchar(255) NOT NULL,
          `name7` varchar(255) NOT NULL,
          `name8` varchar(255) NOT NULL,
          `name9` varchar(255) NOT NULL,
          `name10` varchar(255) NOT NULL,
          `name11` varchar(255) NOT NULL,
          `name12` varchar(255) NOT NULL,
          `name13` varchar(255) NOT NULL,
          `name14` varchar(255) NOT NULL,
          `name15` varchar(255) NOT NULL,
          `name16` varchar(255) NOT NULL,
          `name17` varchar(255) NOT NULL,
          `name18` varchar(255) NOT NULL,
          `name19` varchar(255) NOT NULL,
          `name20` varchar(255) NOT NULL,
          `name21` varchar(255) NOT NULL,
          `name22` varchar(255) NOT NULL,
          `name23` varchar(255) NOT NULL,
          `name24` varchar(255) NOT NULL,
          `name25` varchar(255) NOT NULL,
          `name26` varchar(255) NOT NULL,
          `name27` varchar(255) NOT NULL,
          `name28` varchar(255) NOT NULL,
          `name29` varchar(255) NOT NULL,
          `name30` varchar(255) NOT NULL,
          PRIMARY KEY  (`id`)
		  )
		  CHARACTER SET utf8
		  COLLATE utf8_general_ci
		  ")
    or error('Critical Error', mysql_error () );

echo 'New custom fields tables created.<br />';

$db->query("ALTER TABLE `".PROPERTIES_TABLE."`
            ADD COLUMN `custom1` varchar(255) NOT NULL,
            ADD COLUMN `custom2` varchar(255) NOT NULL,
            ADD COLUMN `custom3` varchar(255) NOT NULL,
            ADD COLUMN `custom4` varchar(255) NOT NULL,
            ADD COLUMN `custom5` varchar(255) NOT NULL,
            ADD COLUMN `custom6` varchar(255) NOT NULL,
            ADD COLUMN `custom7` varchar(255) NOT NULL,
            ADD COLUMN `custom8` varchar(255) NOT NULL,
            ADD COLUMN `custom9` varchar(255) NOT NULL,
            ADD COLUMN `custom10` varchar(255) NOT NULL")
    or error('Critical Error', mysql_error () );

echo 'New custom fields added.<br />';

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>