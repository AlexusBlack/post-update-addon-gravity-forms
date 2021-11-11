<?php 
trait ACGF_PostUpdateAddOn_FeedSettings {
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
        ),
      ),

      array(
        'title'  => __('Target Post Settings', $this->_slug),
        'description' => __('Enter Post ID, custom post ID or a merge tag that contains such id. Use {current_post_id} to update page with submitted form.', $this->_slug),
        'fields' => array(
          array(
            'name' => 'post_id',
            'label' => __('Post ID', $this->_slug),
            'type' => 'text',
            'required' => true,
            'class' => 'medium merge-tag-support mt-position-right',
          ),
        ),
      ),
      
      array(
        'title' => __('Post Settings', $this->_slug),
        'description' => __('Empty value means - no change', $this->_slug),
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
                'label' => esc_html__('No Change', $this->_slug),
                'value' => ''
              ),
              array(
                'label' => esc_html__('Published', $this->_slug),
                'value' => 'publish'
              ),
              array(
                'label' => esc_html__('Draft', $this->_slug),
                'value' => 'draft'
              ),
              array(
                'label' => esc_html__('Pending', $this->_slug),
                'value' => 'pending'
              ),
              array(
                'label' => esc_html__('Private', $this->_slug),
                'value' => 'private'
              ),
              array(
                'label' => esc_html__('Trash', $this->_slug),
                'value' => 'trash'
              )
            )
          ),

        )
      ),

      array(
        'title' => __('Post Content', $this->_slug),
        'description' => __('Empty value means - no change', $this->_slug),
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
            'type' => 'checkbox',
            'horizontal' => true,
            'choices' => array(
              array(
                'label' => __('Allow empty value for content'),
                'name' => 'allow_empty_content',
                'value' => 1
              ),
            )
          ),
        )
      ),

      array(
        'title' => __('Custom Fields', $this->_slug),
        'description' => __('Enter custom field name and then select form field with value for it', $this->_slug),
        'fields' => array(
          array(
            'name' => 'meta_field_map',
            'label' => __('Custom Fields', $this->_slug),
            'type' => 'dynamic_field_map',
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
}
?>
