<?php
/*
Plugin Name: Recent Pages
Plugin URI: http://titusbicknell.com/wp-recent-changes
Description: <strong>This plugin has been replaced by <a href=http://wordpress.org/extend/plugins/recent-changes/>Recent Changes</a> and will no longer be developed.</strong>
Version: 0.4
Author: Titus Bicknell
Author URI: http://www.titusbicknell.com
*/

function widget_RecentPages($args) {
extract($args);

$rp_options = get_option('widget_RecentPages');
$rp_title = empty($rp_options['title']) ? __('Recent Pages') : apply_filters('widget_title', $rp_options['title']);
if ( !$rp_number = (int) $rp_options['number'] )
	$rp_number = 10;
else if ( $rp_number < 1 )
	$rp_number = 1;
else if ( $rp_number > 15 )
	$rp_number = 15;
$rp_sql = "SELECT post_title, ID FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_modified DESC LIMIT ".$rp_number;
global $wpdb;
echo $before_widget;
echo $before_title.$rp_title.$after_title.'<ul>';
$recentpages = $wpdb->get_results($rp_sql);
if ($recentpages)
foreach ($recentpages as $recentpage) :
$rp_url = get_page_link($recentpage->ID);
echo '<li><a href='.$rp_url.'>'.$recentpage->post_title.'</a></li>';
endforeach;
echo '</ul>'.$after_widget;
$wpdb->flush();
}

function widget_RecentPages_control() {
	$rp_options = $rp_newoptions = get_option('widget_RecentPages');
	if ( isset($_POST["RecentPages-submit"]) ) {
		$rp_newoptions['title'] = strip_tags(stripslashes($_POST["RecentPages-title"]));
		$rp_newoptions['number'] = (int) $_POST["RecentPages-number"];
	}
	if ( $rp_options != $rp_newoptions ) {
		$rp_options = $rp_newoptions;
		update_option('widget_RecentPages', $rp_options);
	}
	$rp_title = attribute_escape($rp_options['title']);
	if ( !$rp_number = (int) $rp_options['number'] )
		$rp_number = 5;
?>

<p><label for="RecentPages-title"><?php _e('Title:'); ?> <input class="widefat" id="RecentPages-title" name="RecentPages-title" type="text" value="<?php echo $rp_title; ?>" /></label></p>
<p><label for="RecentPages-number"><?php _e('Number of pages to show:'); ?> <input style="width: 25px; text-align: center;" id="RecentPages-number" name="RecentPages-number" type="text" value="<?php echo $rp_number; ?>" /></label><br />
<small><?php _e('(at most 15)'); ?></small></p>
<input type="hidden" id="RecentPages-submit" name="RecentPages-submit" value="1" />
<?php
}

function init_RecentPages(){
register_sidebar_widget("Recent Pages", "widget_RecentPages");
register_widget_control( 'Recent Pages', 'widget_RecentPages_control');   
}

add_action("plugins_loaded", "init_RecentPages");
?>