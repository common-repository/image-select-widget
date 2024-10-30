<?php

function image_select_install() {
	// List names for the .mo (text domain) file
	__("seasons") . __("spring") . __("summer") . __("autumn") . __("winter");
	
	global $wpdb;
	
	load_plugin_textdomain( 'image-select', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	$wpdb->query("CREATE TABLE IF NOT EXISTS `" . is_THEME . "` ( " .
  					"`id` int(5) NOT NULL AUTO_INCREMENT, " .
  					"`name` varchar(45) NOT NULL, " .
  					"PRIMARY KEY (`id`) " .
				") ENGINE = InnoDB DEFAULT CHARSET = 'utf8' COLLATE 'utf8_general_ci' AUTO_INCREMENT = 0 ;"
				);
				
	$wpdb->query("CREATE TABLE IF NOT EXISTS `" . is_SHORT . "` ( " .
					"`id` int(5) NOT NULL AUTO_INCREMENT, " .
					"`theme` int(5) NOT NULL, " .
					"`name` VARCHAR(45) DEFAULT NULL, " .
					"PRIMARY KEY (`id`) " .
				") ENGINE = InnoDB DEFAULT CHARSET = 'utf8' COLLATE 'utf8_general_ci' AUTO_INCREMENT = 0 ;"
				);
			
	$wpdb->query("CREATE TABLE IF NOT EXISTS `" . is_CONTENT . "` ( " .
				  "`id` int(5) NOT NULL AUTO_INCREMENT, " .
				  "`theme` int(5) NOT NULL, " .
				  "`url` longtext NOT NULL, " .
				  "`title` longtext DEFAULT NULL, " .
				  "PRIMARY KEY (`id`), " .
				  "INDEX `theme_idx` (`theme` ASC), " .
				  "CONSTRAINT `theme` " .
				  	"FOREIGN KEY (`theme`) " .
					"REFERENCES `" . is_THEME . "` (`id`) " .
					"ON DELETE CASCADE " .
					"ON UPDATE CASCADE " .
				") ENGINE = InnoDB  DEFAULT CHARSET = 'utf8' COLLATE 'utf8_general_ci' AUTO_INCREMENT = 0 ;");
	
	$images 	= array("", "", "", "");
	$shortcode	= "seasons";
	$url		= array(
					"spring",
					"summer",
					"autumn",
					"winter"
				);
	$count	= array(
					"spring" =>	4,
					"summer" => 4,
					"autumn" =>	4,
					"winter" =>	5
				);
				
	$src = "http://marcel-online.magix.net/images/";
	
	$wpdb->query("INSERT INTO `" . is_THEME . "` (`name`) VALUES ('" . __("none", "image-select") . "'); ");
		
	foreach($url as $name) {
		$wpdb->query("INSERT INTO `" . is_THEME . "` (`name`) VALUES ('" . __($name, "image-select") . "'); ");
		
		$id = $wpdb->get_results("SELECT `id` FROM `" .is_THEME . "` WHERE `name` = '" . __($name, "image-select") . "'; ");
		
		for($i = 0; $i < $count[$name]; $i++)
			$wpdb->query("INSERT INTO `" . is_CONTENT . "` (`theme`, `url`) VALUES ('" . $id[0]->id . "', '" . $src . $name . "/IMG" . ($i + 1) . ".jpg'); ");
	}
				
	$wpdb->query("INSERT INTO `" . is_SHORT . "` (`theme`, `name`) VALUES ('1', '" . __($shortcode, "image-select") . "'); ");
}

function image_select_uninstall() {
	global $wpdb;
	
	$wpdb->query("DROP TABLE IF EXISTS `" . is_CONTENT 	. "`");
	$wpdb->query("DROP TABLE IF EXISTS `" . is_THEME 	. "`;");
	$wpdb->query("DROP TABLE IF EXISTS `" . is_SHORT 	. "`;");
}

?>