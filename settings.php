<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_dashboard_setup', 'add_dashboard_widgets');
add_action('admin_enqueue_scripts', 'add_image_select_admin_scripts');
add_action('wp_enqueue_scripts', 'add_image_select_scripts');

add_shortcode('image_select', 'image_select_handler');

function add_image_select_admin_scripts() {
	wp_enqueue_style(  'image_select', plugins_url('/style.admin.css', __FILE__) );
	wp_enqueue_script( 'image_select', plugins_url('/script.admin.js', __FILE__) );
	echo '<script type="text/javascript">' .
			'var info = \'' . __("Paste URI of image", "image-select") . '\'; ' .
			'var delThm = \'' . __("Are you sure to delete \"%s\"", "image-select") . '?\'; ' . 
			'var delImg = \'' . __("Are you sure to delete \"%s\"", "image-select") . '?\'; ' .
    	'</script>';
}

function add_image_select_scripts() {
	wp_enqueue_style( 'image_select', plugins_url('/style.css', __FILE__) );
}

function add_dashboard_widgets() {
	if(current_user_can( 'manage_options' ))
		wp_add_dashboard_widget('image_select_widget', __('Image Select Widget', "image-select"), 'image_select_widget');
}

function get_image_select_option($theme, $select) { ?>
<li id="image_select_wrap_<?php echo $theme->id ?>" class="image_select_wrap<?php if($theme->id %2 == 0) { ?> alternate<?php } ?>">
	<span class="image_select_span">
        <input name="image_select" type="radio" value="<?php echo $theme->id ?>" id="image_select_<?php echo $theme->id ?>" <?php 
			if($theme->id == $select) { ?> checked="checked" <?php } ?> />
        
        <label id="image_select_label_<?php echo $theme->id ?>" for="image_select_<?php echo $theme->id ?>"><?php echo $theme->name ?></label>
		<?php if($theme->name != __("none", "image-select")) { ?>
        <div class="image_select_edit" title="<?php _e("Edit name", "image-select"); ?>" onclick="javascript:void(imageSelectCategory(<?php echo $theme->id ?>))"></div>
        <?php } ?>
    </span>
    <?php if($theme->name != __("none", "image-select")) { ?>
    <div id="image_select_edit_<?php echo $theme->id ?>" class="button button-primary edit" onclick="javascript:void(imageSelectUpdate('<?php 
		echo $theme->id ?>', 'image_select_wrap_'))"><?php _e("Edit", "image-select") ?></div>
		<?php global $wpdb;
		
			$content = $wpdb->get_results("SELECT * FROM `" . is_CONTENT . "` WHERE `theme` = '" . $theme->id . "'");
		
			if($content) {
            ?><div class="image_select_preview"><span>
            <?php foreach($content as $url) {
                if($url) {
                ?><img src="<?php echo $url->url ?>" title="<?php echo $url->title ?>" />
            <?php } } ?></span></div>
        <?php } ?>
    <div id="image_select_helper_<?php echo $theme->id ?>" class="image_select_helper"><?php foreach ($content as $url) { echo $url->url . ";"; } ?></div><?php } ?>
</li>
<?php
}

function image_select_widget() {
	global $wpdb;
	
	if(isset($_POST['change_theme']))
		if($_POST['change_theme'] == 1)
			if($_POST['id'])
				$wpdb->query("DELETE FROM `" . is_SHORT . "` WHERE `id` = '" . (int)$_POST['id'] . "' LIMIT 1;");
			else
				if(empty($_POST['theme_name']))
					$wpdb->query("INSERT INTO `" . is_SHORT . "` (`theme`) VALUES ('1') ;");
				else
					$wpdb->query("INSERT INTO `" . is_SHORT . "` (`theme`, `name`) VALUES ('1', '" . esc_sql($_POST['theme_name']) . "') ;");
	if(isset($_POST['theme_post']))
		if($_POST['theme_post'] == 1)
			if(isset($_POST['image_select']))
				$wpdb->query("UPDATE `" . is_SHORT . "` SET `theme` = '" . (int)$_POST['image_select'] . "' WHERE `id` = '" . (int)$_POST['theme'] . "';");
	
	if(isset($_POST['ak'])) {
		if($_POST['ak'] == 'click') {
			$content = str_replace(" ", "", esc_sql($_POST['image_select_update']));
			$content = str_replace("\r", "\n", $content);
			while(strpos($content, "\n\n"))
				$content = str_replace("\n\n", "\n", $content);
			
			$wpdb->query("UPDATE `" . is_THEME . "` SET `content` = '" . $content . "' WHERE `id` = '" . (int)$_POST['id'] . "'");
		} else if($_POST['ak'] == 'images') {
			$theme_id = $_POST['theme'];
			
			if($_POST['count_img']) {
				for($i = 1; $i <= $_POST['count_img']; $i++) {
					$id = $wpdb->get_results("SELECT `id` FROM `" . is_CONTENT . "` WHERE `theme` = '" . $theme_id . "';");			
					$wpdb->query("UPDATE `" . is_CONTENT . "` SET `url` = '" . $_POST['image_' . $i] . "' WHERE `theme` ='" . $theme_id . "' AND `id` = '" . $id[$i - 1]->id . "'");
				}
			}
			if($_POST['count_new']) {
				for($i = 1; $i <= $_POST['count_new']; $i++)
					if($_POST['new_image_' . $i] != __("Paste URI of image", "image-select") && !empty($_POST['new_image_' . $i])) {
						if(isset($_POST['new_image_title_' . $i]))
							$wpdb->query("INSERT INTO `" . is_CONTENT . "` (`theme`, `url`, `title`) VALUES ('" 
									. $theme_id . "', '" . $_POST['new_image_' . $i] ."', '" . $_POST['new_image_title_' . $i] . "');");
						else
							$wpdb->query("INSERT INTO `" . is_CONTENT . "` (`theme`, `url`) VALUES ('" . $theme_id . "', '" . $_POST['new_image_' . $i] ."');");
					}
			}
		} else if($_POST['ak'] == 'delete') {
			$wpdb->query("DELETE FROM `" . is_THEME . "` WHERE `id` = '" . (int)$_POST['id'] . "' LIMIT 1;");			
			$wpdb->query("DELETE FROM `" . is_CONTENT . "` WHERE `theme` = '" . (int)$_POST['id'] . "';");
		} else if($_POST['ak'] == 'deleteImg') {
			if($id = $wpdb->get_results("SELECT `id` FROM `" . is_CONTENT . "` WHERE `theme` = '" . $_POST['theme'] . "';"))
				$wpdb->query("DELETE FROM `" . is_CONTENT . "` WHERE `theme` = '" . $_POST['theme'] . "' AND `id` = '" . $id[$_POST['id'] - 1]->id . "' LIMIT 1;");
		}
	}
	if(isset($_POST['image_select_theme_new'])) {
		if($_POST['image_select_theme_new'] != "")
			if($_POST['id'] == 0)
				$wpdb->query("INSERT INTO `" . is_THEME . "` (`name`) VALUES ('" . esc_sql($_POST['image_select_theme_new']) . "');");
			else
				$wpdb->query("UPDATE `" . is_THEME . "` SET `name` = '" . esc_sql($_POST['image_select_theme_new']) . 
					"' WHERE `id` = '" . (int)$_POST['id'] . "' LIMIT 1;");
	}
	
	$image_select_theme = 0;
	
	if(isset($_GET['theme']))
		$image_select_theme = (int)$_GET['theme'];
	
	if(!$image_select_theme) {
		
		$query = $wpdb->get_results("SELECT * FROM `" . is_SHORT . "`");
?>
<div id="image_select_theme">
	<form name="select" id="select" method="post">
    	<input type="hidden" name="change_theme" value="1" />
        <input type="hidden" name="id" value="0" />
        <input type="hidden" name="theme" value="0" />
        <ul>
        	<li class="alternate"><?php _e("Select Theme", "image-select") ?>:</li>
        <?php		
            $i = 0;
            
            foreach($query as $theme) {
                $i++; ?>
            	<li<?php if($i % 2 == 0) { ?> class="alternate"<?php } ?>>
					<a id="image_select_theme_<?php echo $theme->id ?>" class="image_select_text" href="?theme=<?php echo $theme->id ?>"><?php 
						if(empty($theme->name)) echo __("Theme", "image-select") . " " . $theme->id; else echo $theme->name 
					?></a>
                    <div class="image_select_delete" title="<?php _e("Delete Theme", "image-select") ?>" onclick="javascript:void(imageSelectDeleteTheme(<?php echo $theme->id ?>))"></div>
                </li>
            <?php } ?>
            <li>
            	<label for="theme_name"><?php _e("Name", "image-select") ?>: </label>
            	<input type="text" name="theme_name" id="theme_name"  />
            	<input type="submit" class="button button-primary" value="<?php _e("Add", "image-select") ?>" />
            </li>
        </ul>
    </form>
</div>
<?php } else { 
		$query = $wpdb->get_results("SELECT * FROM `" . is_SHORT . "` WHERE `id` = '" . (int)$image_select_theme . "'");
		
		if(!$query) { ?>
<div id="image_select">
	<ul>
    	<li id="image_select_change_theme" class="image_select_wrap alternate image_select_error_theme">
        	<span class="image_select_span"><?php _e("The selected Theme is not available", "image-select") ?></span>
        	<a class="button button-primary" href="<?php echo $_SERVER['PHP_SELF'] ?>"><?php _e("Change Theme", "image-select") ?></a>
        </li>
    </ul>
</div>
<?php		return;
		}

?>
<div id="image_select">
	<form name="theme" id="theme" method="post">
    	<input name="theme_post" type="hidden" value="1" />
        <input name="theme" type="hidden" value="<?php echo $image_select_theme ?>" />
        <input id="image_select_count_img" name="count_img" type="hidden" value="0" />
        <input id="image_select_count_new" name="count_new" type="hidden" value="0" />
        <input name="ak" type="hidden" />
        <input name="id" type="hidden" />
        <ul>
        	<li id="image_select_change_theme" class="image_select_wrap alternate">
            	<span class="image_select_span_shortcode"><?php _e("Current", "image-select") ?>: <em><?php if(empty($query[0]->name)) echo __("Theme", "image-select") . " " . $query[0]->id; else echo $query[0]->name; ?></em></span>
                <a class="button button-primary" href="<?php echo $_SERVER['PHP_SELF'] ?>"><?php _e("Change Theme", "image-select") ?></a>
            </li>
            <li id="image_select_shortcode" class="image_select_wrap alternate">
            	<span class="image_select_span_shortcode">
                	<?php _e("Shortcode", "image-select")?>: 
                    <input type="text" value='[image_select id="<?php echo $image_select_theme ?>"]' onclick="javascript:void(this.select())" readonly="readonly" title="<?php _e("Copy shortcode and paste it into a side or post", "image-select") ?>" />
                    <div id="info" class="button button-primary help" onclick="javascript:void(imageSelectInfo())"><?php _e("Info", "image-select") ?></div>
                </span>               	
            </li>
        	<?php
			
				$categories = $wpdb->get_results("SELECT * FROM `" . is_THEME . "`");
				
				foreach($categories as $type) {
					get_image_select_option($type, $query[0]->theme);
				}
			?>
        </ul>
        <div id="image_select_info" style="position: absolute; display: none;" >
        	<div id="image_select_info_overlay">
            	<h2><?php _e("Info", "image-select") ?></h2>
                <h3><?php _e("Paste shortcode", "image-select") ?></h3>
                <input type="text" value='[image_select id="<?php echo $image_select_theme ?>" height="200px"]' onclick="javascript:void(this.select())" readonly="readonly" /><br />
                <ul>
                    <li><?php _e("Copy shortcode and paste it into a side or post", "image-select") ?></li>
                    <li><?php _e("You can add more options to the shortcode", "image-select") ?></li>
                </ul>
                <h3><?php _e("Options", "image-select") ?></h3>
                <table>
                	<thead>
                    	<tr>
                        	<td><?php _e("Options", "image-select") ?></td>
                            <td><?php _e("Explanation", "image-select") ?></td>
                            <td><?php _e("Default", "image-select") ?></td>
                        </tr>
                    </thead>
                    <tbody>
                    	<tr class="alternate">
                        	<td><input type="text" value="id" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td>
								<?php _e("ID of Theme", "image-select") ?><br />
                                <em><?php _e("Higher priority than name", "image-select") ?></em>
                            </td>
                            <td><?php echo $query[0]->id; ?></td>
                        </tr>
                        <tr>
                        	<td><input type="text" value="name" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Name of Theme", "image-select") ?></td>
                            <td><?php echo $query[0]->name; ?></td>
                        </tr>
                        <tr class="alternate">
                        	<td><input type="text" value="height" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Height", "image-select") ?> <?php _e("of <strong>all</strong> images", "image-select") ?></td>
                            <td>200px</td>
                        </tr>
                        <tr>
                        	<td><input type="text" value="width" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Width", "image-select") ?> <?php _e("of <strong>all</strong> images", "image-select") ?></td>
                            <td>0</td>
                        </tr>
                        <tr class="alternate">
                        	<td><input type="text" value="boxheight" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Height", "image-select") ?> <?php _e("of box where images placed in", "image-select") ?></td>
                            <td>0</td>
                        </tr>
                        <tr>
                        	<td><input type="text" value="boxwidth" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Width", "image-select") ?> <?php _e("of box where images placed in", "image-select") ?></td>
                            <td>0</td>
                        </tr>
                        <tr class="alternate">
                        	<td><input type="text" value="float" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Float box left or right", "image-select") ?></td>
                            <td>none</td>
                        </tr>
                        <tr>
                        	<td><input type="text" value="margin" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Margin between images", "image-select") ?></td>
                            <td>15px 15px 15px 15px</td>
                        </tr>
                        <tr class="alternate">
                        	<td><input type="text" value="boxmargin" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Margin of box", "image-select") ?></td>
                            <td>15px 5px 5px 5px</td>
                        </tr>
                        <tr>
                        	<td><input type="text" value="padding" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Padding of images", "image-select") ?></td>
                            <td>5px 5px 5px 5px</td>
                        </tr>
                        <tr class="alternate">
                        	<td><input type="text" value="boxpadding" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Padding of box", "image-select") ?></td>
                            <td>5px 5px 5px 5px</td>
                        </tr>   
                        <tr>
                        	<td><input type="text" value="overflow" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Show or hide images, which are outside of the box", "image-select") ?></td>
                            <td>hidden</td>
                        </tr>
                        <tr class="alternate">
                        	<td><input type="text" value="spacex" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Space between all images", "image-select") ?> (<?php _e("x-axis", "image-select") ?>)</td>
                            <td>0</td>
                        </tr>
                        <tr>
                        	<td><input type="text" value="spacey" onclick="javascript:void(this.select())" readonly="readonly" /></td>
                            <td><?php _e("Space between all images", "image-select") ?> (<?php _e("y-axis", "image-select") ?>)</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="image_select_info_options">
            	<div class="button button-primary" onclick="imageSelectInfo()"><?php _e("Close", "image-select") ?></div>
            </div>
        </div>
        <div id="image_select_update" style="position: absolute; display: none;" >
        	<div id="image_select_overlay">
                <div id="image_select_update_content">
                </div>
            </div>
            <input class="button button-primary" type="submit" value="<?php _e("Save", "image-select") ?>" />
            <div class="button button-primary" onclick="javascript:void(imageSelectHide())"><?php _e("Reject", "image-select") ?></div>
        </div>
        <div id="image_select_theme_new" style="display: none;">
        	<input id="image_select_theme_new_input" type="text" name="image_select_theme_new" />
            <div class="image_select_delete" title="<?php _e("Delete Theme", "image-select") ?>" onclick="javascript:void(imageSelectDeleteCategory())"></div>
            <div class="button button-primary" onclick="javascript:void(imageSelectRejectCategory())"><?php _e("Reject", "image-select") ?></div>
            <input class="button button-primary" type="submit" value="<?php _e("Save", "image-select") ?>" />
            
        </div>
        <input class="button button-primary" type="submit" value="<?php _e("Save", "image-select") ?>" />
        <div id="image_select_new" class="button button-primary" onclick="javascript:void(imageSelectCategory(0))"><?php _e("New topic", "image-select") ?></div>
    </form>
</div>
<?php }
}

function get_lightbox($image, $title, $alt = NULL) {	
	return "\n" . '
				<span>
					<a rel="lightbox[roadtrip]" href="' . $image . '" title="' . $title . '">
						<img src="' . $image . '" alt="' . $alt . '" title="' . __("zoom in", "image-select") . '" />
					</a>
				</span>';
}

function image_select_handler( $atts ) {
	global $wpdb;
	
	extract( 
		shortcode_atts( 
			array( 
				'id', 
				'name', 
				'height', 
				'width', 
				'boxheight', 
				'boxwidth', 
				'float', 
				'margin', 
				'boxmargin', 
				'padding',
				'boxpadding',
				'overflow', 
				'spacex', 
				'spacey' 
			),
			$atts 
		)
	);
	
	if(isset($atts['id']))
		$current = $wpdb->get_results("SELECT `theme` FROM `" . is_SHORT . "` WHERE `id` = '" . (int)$atts['id'] . "'");	
	else if(isset($atts['name']))
		$current = $wpdb->get_results("SELECT `theme` FROM `" . is_SHORT . "` WHERE `name` = '" . esc_sql($atts['name']) . "'");
	else
		return;
		
	if(!$current)
		return;
			
	$theme = $wpdb->get_results(
		"SELECT * FROM `" . is_THEME . "` " . 
				" INNER JOIN `" . is_CONTENT . "` ON `" . is_CONTENT . "`.`theme` = `" . is_THEME . "`.`id` " . 
		"WHERE `" . is_THEME . "`.`id` = '" . (int)$current[0]->theme . "'"
	);
	
	if(!$theme || $theme[0]->name == __("none", "image-select"))
		return;
		
	$height = $width = $boxHeight = $boxWidth = $float = $padding = $boxpading = $spaceX = $spaceY = "";
	$margin 	= "15px";
	$boxmargin 	= "15px 5px 5px 5px";
	$boxpadding = "5px";
	$overflow = "hidden";
	
	if(isset($atts['overflow']))	
		if($atts['overflow'] == "visible") 
									$overflow 	= $atts['overflow'];
	if(isset($atts['float']))		$float		= $atts['float'];
	if(isset($atts['height']))		$height 	= $atts['height'];
	if(isset($atts['width']))		$width 		= $atts['width'];
	if(isset($atts['boxheight']))	$boxHeight 	= $atts['boxheight'];
	if(isset($atts['boxwidth']))	$boxWidth	= $atts['boxwidth'];
	if(isset($atts['margin']))		$margin		= $atts['margin'];
	if(isset($atts['boxmargin']))	$boxmargin	= $atts['boxmargin'];
	if(isset($atts['padding']))		$padding	= $atts['padding'];
	if(isset($atts['boxpadding']))	$boxpading	= $atts['boxpadding'];
	if(isset($atts['spacex']))		$spaceX		= $atts['spacex'];
	if(isset($atts['spacey']))		$spaceY		= $atts['spacey'];
	
	if($float == "left")
		$float = " float: left; margin-right: " . $margin . ";";
	else if($float == "right")
		$float = " float: right; margin-left: " . $margin . ";";
	
	if(!$height && !$width)
		$height = "200px";
		
	if($height) 	$height		= " height: " 		. $height 		. " !important" 	. ";";
	if($width)		$width		= " width: "		. $width 		. " !important" 	. ";";	
	if($boxHeight) 	$boxHeight	= " height: " 		. $boxHeight 						. ";";
	if($boxWidth)	$boxWidth	= " width: "		. $boxWidth 						. ";";
	if($spaceX)		$spaceX		= " min-width: "	. $spaceX							. ";";
	if($spaceY)		$spaceY		= " min-height: "	. $spaceY							. ";";
	if($padding)	$padding	= " padding: " 		. $padding							. ";";
	if($boxpading)	$boxpading	= " padding: "		. $boxpading						. ";";
	
	$string = "
			<style>
				.image-select { " . $float . $boxHeight . $boxWidth . $boxpading . " margin: " . $boxmargin . "; overflow: " . $overflow . "; }
				.image-select span { float: left; " . $spaceX . $spaceY . " }
				.image-select span a img { " . $height . $width . $padding . " } 
			</style>
			
			<div class=\"image-select\">";
	
	foreach($theme as $img) {
		$title = ($img->title != NULL) ? $img->title : $img->name;
		
		if($img->url)
			$string .= get_lightbox($img->url, $title, $title);
	}
			
	$string .= "
			</div>";
	
	return $string;
} 

?>