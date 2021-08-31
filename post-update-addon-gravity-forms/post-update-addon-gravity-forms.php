<?php
/*
Plugin Name: Post Update Addon - Gravity Forms 
Description: Update/Edit a post or a custom post type with Gravity Forms.
Version: 1.0.0
Author: Alex Chernov
Author URI: https://alexchernov.com
*/
define('GF_POST_UPDATE_ADDON_VERSION', '1.0.0');

add_action('gform_loaded', array('PostUpdate_AddOn_Bootstrap', 'load'), 5);
 
class PostUpdate_AddOn_Bootstrap {
  public static function load() {
    // Check if Gravity Forms installed
    if(!method_exists('GFForms', 'include_addon_framework')) return;
    // Include primary class
    require_once('class-post-update-addon.php');
    GFAddOn::register('PostUpdateAddOn');
  }
}
 
function gf_post_update_addon() {
      return PostUpdateAddOn::get_instance();
}
?>
