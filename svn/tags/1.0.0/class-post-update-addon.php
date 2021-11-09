<?php
GFForms::include_feed_addon_framework();
 
class ACGF_PostUpdateAddOn extends GFFeedAddOn {
      protected $_version = ACGF_POST_UPDATE_ADDON_VERSION;
      // Earlier versions maybe supported but not tested
      protected $_min_gravityforms_version = '2.5';
      protected $_slug = 'acgf_update_post';
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

      public function feed_settings_fields() {
        return array(
          array(
            'title'  => __('Post Update Settings', $this->_slug),
            'fields' => array(
              array(
                'name' => 'feedName',
                'label' => __('Name', $this->_slug),
                'type' => 'text',
                'required' => true,
                'class' => 'medium',
              ),
              array(
                'name' => 'post_id',
                'label' => __('Post ID', $this->_slug),
                'type' => 'text',
                'required' => true,
                'class' => 'medium merge-tag-support mt-position-right',
                'tooltip' => __('Post ID, custom post ID or a merge tag that contains such id', $this->_slug)
              ),
            ),
          ),
          
          array(
            'title' => 'Post Settings',
            'tooltip' => 'Empty value means - no change',
            'fields' => array(
              array(
                'name' => 'author_id',
                'label' => __('Author ID', $this->_slug),
                'type' => 'text',
                'class' => 'medium merge-tag-support mt-position-right',
              ),
              array(
                'name' => 'post_status',
                'label' => __('Status', $this->_slug),
                'type' => 'select',
                'choices' => array(
                  array(
                    'label' => esc_html__('No Change'),
                    'value' => ''
                  ),
                  array(
                    'label' => esc_html__('Published'),
                    'value' => 'publish'
                  ),
                  array(
                    'label' => esc_html__('Draft'),
                    'value' => 'draft'
                  ),
                  array(
                    'label' => esc_html__('Pending'),
                    'value' => 'pending'
                  ),
                  array(
                    'label' => esc_html__('Private'),
                    'value' => 'private'
                  ),
                  array(
                    'label' => esc_html__('Trash'),
                    'value' => 'Trash'
                  )
                )
              ),

            )
          ),

          array(
            'title' => 'Post Content',
            'tooltip' => 'Empty value means - no change',
            'fields' => array(
              array(
                'name' => 'post_title',
                'label' => __('Title', $this->_slug),
                'type' => 'text',
                'class' => 'medium merge-tag-support mt-position-right',
              ),
              array(
                'name' => 'post_content',
                'label' => __('Content', $this->_slug),
                'type' => 'textarea',
                'class' => 'merge-tag-support mt-position-right',
              ),
              array(
                'name' => 'meta_field_map',
                'label' => __('Custom Fields', $this->_slug),
                'type' => 'dynamic_field_map',
                'tooltip' => __('Enter custom field name and then select for field with value for it', $this->_slug)
              ),
            )
          ),

          array(
            'fields' => array(
              array(
                'name'  => 'feed_condition',
                'label' => __('Conditional Logic', $this->_slug),
                'type'  => 'feed_condition',
              )
            )
          ),
          
        );
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

        $author_id = rgars($feed, 'meta/author_id');
        $author_id = trim($author_id);
        $author_id = GFCommon::replace_variables($author_id, $form, $entry, false, false, false);
        if($author_id !== '') {
          $postarr['post_author'] = $author_id;
          $this->log_debug(__METHOD__ . sprintf('(): Provided Author ID "%d"', $author_id));
        }

        $post_status = rgars($feed, 'meta/post_status');
        $post_status = trim($post_status);
        $post_status = GFCommon::replace_variables($post_status, $form, $entry, false, false, false);
        if($post_status !== '') {
          $postarr['post_status'] = $post_status;
          $this->log_debug(__METHOD__ . sprintf('(): Provided Post Status "%s"', $post_status));
        }

        $post_title = rgars($feed, 'meta/post_title');
        $post_title = trim($post_title);
        $post_title = GFCommon::replace_variables($post_title, $form, $entry, false, false, false);
        if($post_title !== '') {
          $postarr['post_title'] = $post_title;
          $this->log_debug(__METHOD__ . sprintf('(): Provided Post Title "%s"', $post_title));
        }

        $post_content = rgars($feed, 'meta/post_content');
        $post_content = trim($post_content);
        $post_content = GFCommon::replace_variables($post_content, $form, $entry, false, false, false);
        if($post_content !== '') {
          $postarr['post_content'] = $post_content;
          $this->log_debug(__METHOD__ . sprintf('(): Provided Post Content "%s"', $post_content));
        }
        // Updating base post fields
        $this->log_debug(__METHOD__ . sprintf('(): Starting post update'));
        $result = wp_update_post($postarr, $wp_error = true);
        if(is_wp_error($result)) {
          $this->log_debug(__METHOD__ . sprintf('(): ERROR: Can\'t update the post - "%s"', $result->get_error_message()));
          return;
        }

        // Updating meta fields
        $this->log_debug(__METHOD__ . sprintf('(): Starting meta fields (custom fields) update'));
        $metaMap = $this->get_dynamic_field_map_fields($feed, 'meta_field_map');
        foreach($metaMap as $target_meta_key => $source_field_id) {
          $form_field_value = rgar($entry, $source_field_id);
          update_post_meta($post_id, $target_meta_key, $form_field_value);
        }
      }
}
?>
