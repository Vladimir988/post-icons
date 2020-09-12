<?php

namespace includes\classes\PostIconsIntegration;

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
  die;
}

if( !class_exists( 'PostIconsIntegration' ) ) {

  class PostIconsIntegration {

    private static $instance = null;

    public function __construct() {
      return $this->init();
    }

    public function init() {
      add_action( 'admin_menu', array( $this, 'post_icons_settings_page' ), 99 );
      add_action( 'admin_init', array( $this, 'post_icons_setting' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
      add_action( 'the_title', array( $this, 'filter_post_title' ), 2, 99 );
    }

    public function filter_post_title( $title, $id ) {
      $options       = get_option('post_icons_settings');
      $plugin_enable = $options['plugin_enable'] ?? false;
      $post_ids      = ( $options['post_ids'] != '' ) ? @explode( ',', $options['post_ids'] ) : false;

      if ( is_admin() ) return $title;
      if( ! $plugin_enable ) return $title;
      if( ! $post_ids || ! in_array( $id, $post_ids ) ) return $title;

      if( is_single() || is_home() ) {

        $dashicon_name = $options['post_dashicon'] ? $options['post_dashicon'] : 'dashicons-awards';
        $icon_position = $options['icon_position'] ? $options['icon_position'] : 'right';

        $icon = '<span class="pi-dashicons dashicons ' . $dashicon_name . ' ' . $icon_position . '"></span>';

        return  ( $icon_position == 'left' ) ? $icon . $title : $title . $icon;
      }

      return $title;
    }

    public function enqueue_scripts() {
      $instance = post_icons();

      wp_enqueue_style(
        'wp-dashicons-custom',
        'https://cdn.jsdelivr.net/npm/@icon/dashicons@0.9.0-alpha.3/dashicons.css',
        array(),
        ''
      );

      wp_enqueue_style(
        'post-icons-css',
        $instance->plugin_url( 'assets/css/post-icons.css' ),
        array(),
        $instance->version
      );
    }

    public function admin_enqueue_scripts() {
      $instance = post_icons();

      wp_enqueue_script(
        'post-icons-js',
        $instance->plugin_url( 'assets/js/post-icons.js' ),
        array( 'jquery' ),
        $instance->version,
        true
      );
    }

    public function post_icons_settings_page() {
      add_options_page(
        esc_html__( 'Post Icons', 'post-icons' ),
        esc_html__( 'Post Icons', 'post-icons' ),
        'manage_options',
        'post-icons-settings',
        array( $this, 'post_icons_options_page' )
      );
    }

    public function post_icons_options_page() {
      ?>
        <div class="advanced-settings-wrapper">
          <form action="options.php" method="POST" enctype="multipart/form-data">
            <?php settings_fields('advanced_options_group'); ?>
            <div class="toggler"><?php do_settings_sections('post_icons_enable'); ?></div>
            <div class="toggled"><?php do_settings_sections('post_icons_upload_plugin_page'); ?></div>
            <?php submit_button('Save'); ?>
          </form>
        </div>
      <?php
    }

    public function post_icons_setting() {
      register_setting('advanced_options_group', 'post_icons_settings', 'post_icons_settings_sanitize');
      add_settings_section('post_icons_section_enable', esc_html__( 'Post Icons Options:', 'post-icons' ), '', 'post_icons_enable');
      add_settings_section('post_icons_section', '', '', 'post_icons_upload_plugin_page');

      add_settings_field(
        'post_icons_plugin_enable',
        'Enable plugin:',
        array( $this, 'post_icons_enable_callback' ),
        'post_icons_enable',
        'post_icons_section_enable',
        array('label_for' => 'post_icons_plugin_enable')
      );

      add_settings_field(
        'post_icons_plugin_post_ids',
        'Post ids:',
        array( $this, 'post_icons_ids_callback' ),
        'post_icons_upload_plugin_page',
        'post_icons_section',
        array('label_for' => 'post_icons_plugin_post_ids')
      );

      add_settings_field(
        'post_icons_dashicon',
        'Dashicon name:',
        array( $this, 'post_icons_dashicon_callback' ),
        'post_icons_upload_plugin_page',
        'post_icons_section',
        array('label_for' => 'post_icons_dashicon')
      );

      add_settings_field(
        'post_icons_position',
        'Dashicon position:',
        array( $this, 'post_icons_position_callback' ),
        'post_icons_upload_plugin_page',
        'post_icons_section',
        array('label_for' => 'post_icons_position')
      );
    }

    public function post_icons_enable_callback() {
      $options = get_option('post_icons_settings');
      $checked = $options['plugin_enable'] ?? false;
      $checked = $checked ? 'checked' : '';
      ?>
        <label for="plugin_enable">
          <input class="regular-text" name="post_icons_settings[plugin_enable]" type="checkbox" id="post_icons_plugin_enable" value="1" <?= $checked; ?>>
        </label>
      <?php
    }

    public function post_icons_ids_callback() {
      $options  = get_option('post_icons_settings');
      $post_ids = $options['post_ids'] ?? '';
      ?>
        <label for="post_ids">
          <input class="regular-text" name="post_icons_settings[post_ids]" type="text" id="post_icons_plugin_post_ids" placeholder="<?php _e( '1,2,3', 'post-icons' ); ?>" value="<?= $post_ids; ?>">
          <br>
          <?php _e( 'Separate post ids by comma', 'post-icons' ); ?>
        </label>
      <?php
    }

    public function post_icons_dashicon_callback() {
      $options       = get_option('post_icons_settings');
      $post_dashicon = $options['post_dashicon'] ?? '';
      ?>
        <label for="post_dashicon">
          <input class="regular-text" name="post_icons_settings[post_dashicon]" type="text" id="post_dashicon" placeholder="<?php _e( 'dashicons-awards', 'post-icons' ); ?>" value="<?= $post_dashicon; ?>">
          <br>
          <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank"><?php _e( 'WordPress Dashicons', 'post-icons' ); ?></a>
        </label>
      <?php
    }

    public function post_icons_position_callback() {
      $options       = get_option('post_icons_settings');
      $icon_position = $options['icon_position'] ?? '';
      ?>
        <label for="icon_position">
          <select name="post_icons_settings[icon_position]" id="icon_position"><?php $this->icons_position_dropdown( $icon_position ); ?></select>
        </label>
      <?php
    }

    public function icons_position_dropdown( $selected = 'right' ) {
      $html = '';
      $positions = array( 'left', 'right' );
      foreach ( $positions as $position ) {
        if ( $selected == $position ) {
          $html .= "\n\t<option selected='selected' value='" . $position . "'>{$position}</option>";
        } else {
          $html .= "\n\t<option value='" . $position . "'>{$position}</option>";
        }
      }
      echo $html;
    }

    public static function get_instance() {
      if ( ! self::$instance )
        self::$instance = new self;
      return self::$instance;
    }
  }
}

new PostIconsIntegration();