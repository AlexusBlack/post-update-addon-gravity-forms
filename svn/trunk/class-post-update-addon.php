<?php
require_once('feed-settings.php');

GFForms::include_feed_addon_framework();
 
class ACGF_PostUpdateAddOn extends GFFeedAddOn {
  use ACGF_PostUpdateAddon_FeedSettings;

  protected $_version = ACGF_POST_UPDATE_ADDON_VERSION;
  // Earlier versions maybe supported but not tested
  protected $_min_gravityforms_version = '2.5';
  protected $_slug = 'post-update-addon-gravity-forms';
  protected $_path = 'post-update-addon-gravity-forms/post-update-addon-gravity-forms.php';
  protected $_full_path = __FILE__;
  protected $_title = 'Post Update Add-On';
  protected $_short_title = 'Post Update';

  private static $_instance = null;
               
  public static function get_instance() {
    if(self::$_instance == null) {
      self::$_instance = new ACGF_PostUpdateAddOn();
    }
    return self::$_instance;
  }
               
  public function init() {
    parent::init();
  }

  public function feed_list_columns() {
    return array(
      'feedName' => __('Name', $this->_slug)
    );
  }

  public function get_menu_icon() {
    return 'dashicons-welcome-write-blog';
  }

  public function process_feed($feed, $entry, $form) {
    $this->log_debug(__METHOD__ . '(): Start feed processing');

    $raw_post_id = rgars($feed, 'meta/post_id');
    $raw_post_id = trim($raw_post_id);

    // get current post id
    $current_post_id = get_the_ID();
    if($current_post_id !== false) {
      $raw_post_id = str_replace('{current_post_id}', $current_post_id, $raw_post_id);
    }

    // replacing merge tags
    $raw_post_id = GFCommon::replace_variables($raw_post_id, $form, $entry, false, false, false);
    if($raw_post_id == '') {
      $this->log_debug(__METHOD__ . sprintf('(): After processing merge tags Post ID is an empty string. Cancelling feed processing.'));
      return;
    }

    $post_id = intval($raw_post_id);
    $this->log_debug(__METHOD__ . sprintf('(): Provided Post ID "%d"', $post_id));
  
    $postarr = array(
      'ID' => $post_id
    );

    // Preparing standard post fields
    $this->prepare_author_id($feed, $entry, $form, $postarr);
    $this->prepare_post_status($feed, $entry, $form, $postarr);
    $this->prepare_post_title($feed, $entry, $form, $postarr);
    $this->prepare_post_content($feed, $entry, $form, $postarr);

    // Updating standard post fields
    $this->process_standard_post_fields($postarr);

    // Updating meta fields
    $this->process_meta_fields($feed, $entry, $post_id);
  }

  function process_standard_post_fields($postarr) {
    $this->log_debug(__METHOD__ . sprintf('(): Starting post update'));
    $result = wp_update_post($postarr, $wp_error = true);
    if(is_wp_error($result)) {
      $this->log_debug(__METHOD__ . sprintf('(): ERROR: Can\'t update the post - "%s"', $result->get_error_message()));
      return;
    }
  }

  function process_meta_fields($feed, $entry, $post_id) {
    $this->log_debug(__METHOD__ . sprintf('(): Starting meta fields (custom fields) update'));
    $metaMap = $this->get_dynamic_field_map_fields($feed, 'meta_field_map');
    foreach($metaMap as $target_meta_key => $source_field_id) {
      $form_field_value = rgar($entry, $source_field_id);
      update_post_meta($post_id, $target_meta_key, $form_field_value);
    }
  }

  function prepare_author_id($feed, $entry, $form, &$postarr) {
    $author_id = rgars($feed, 'meta/author_id');
    $author_id = trim($author_id);
    $author_id = GFCommon::replace_variables($author_id, $form, $entry, false, false, false);
    if($author_id !== '') {
      $postarr['post_author'] = $author_id;
      $this->log_debug(__METHOD__ . sprintf('(): Provided Author ID "%d"', $author_id));
    }
  }

  function prepare_post_status($feed, $entry, $form, &$postarr) {
    $post_status = rgars($feed, 'meta/post_status');
    $post_status = trim($post_status);
    $post_status = GFCommon::replace_variables($post_status, $form, $entry, false, false, false);
    if($post_status !== '') {
      $postarr['post_status'] = $post_status;
      $this->log_debug(__METHOD__ . sprintf('(): Provided Post Status "%s"', $post_status));
    }
  }

  function prepare_post_title($feed, $entry, $form, &$postarr) {
    $post_title = rgars($feed, 'meta/post_title');
    $post_title = trim($post_title);
    $post_title = GFCommon::replace_variables($post_title, $form, $entry, false, false, false);
    if($post_title !== '') {
      $postarr['post_title'] = $post_title;
      $this->log_debug(__METHOD__ . sprintf('(): Provided Post Title "%s"', $post_title));
    }
  }

  function prepare_post_content($feed, $entry, $form, &$postarr) {
    $post_content = rgars($feed, 'meta/post_content');
    $post_content = trim($post_content);
    $post_content = GFCommon::replace_variables($post_content, $form, $entry, false, false, false);
    $allow_empty_content = rgars($feed, 'meta/allow_empty_content');
    if($allow_empty_content === '1' || $post_content !== '') {
      $postarr['post_content'] = $post_content;
      $this->log_debug(__METHOD__ . sprintf('(): Provided Post Content "%s"', $post_content));
    }
  }
}
?>
