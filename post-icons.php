<?php
/**
 * Plugin Name: Post Icons
 * Plugin URI:  
 * Description: Add icon to post title
 * Version:     1.0
 * Author:      Vladimir Prokopets
 * Author URI:  https://github.com/Vladimir988
 * Text Domain: post-icons
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die();
}

// If class `PostIconsClass` doesn't exists yet.
if( ! class_exists( 'PostIconsClass' ) ) {

  /**
   * Sets up and initializes the plugin.
   */
  class PostIconsClass {

    /**
     * A reference to an instance of this class.
     *
     * @since  1.0
     * @access private
     * @var    object
     */
    private static $instance = null;

    /**
     * Holder for base plugin URL
     *
     * @since  1.0
     * @access private
     * @var    string
     */
    private $plugin_url = null;

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0';

    /**
     * Holder for base plugin path
     *
     * @since  1.0
     * @access private
     * @var    string
     */
    private $plugin_path = null;

    public function __construct() {

      // Load modules.
      add_action( 'init', array( $this, 'init' ), -999 );

      // Register activation and deactivation hook.
      register_activation_hook( __FILE__, array( $this, 'activation' ) );
      register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
    }

    /**
     * Load modules
     * @since  1.0
     * @access public
     */
    public function init() {
      require $this->plugin_path('includes/classes/PostIconsIntegration.php');
    }

    /**
     * Returns path to file or dir inside plugin folder
     *
     * @param  string $path Path inside plugin dir.
     * @return string
     */
    public function plugin_path( $path = null ) {

      if ( !$this->plugin_path ) {
        $this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
      }

      return $this->plugin_path . $path;
    }

    /**
     * Returns url to file or dir inside plugin folder
     *
     * @param  string $path Path inside plugin dir.
     * @return string
     */
    public function plugin_url( $path = null ) {

      if ( !$this->plugin_url ) {
        $this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
      }

      return $this->plugin_url . $path;
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
      return apply_filters( 'post-icons/template-path', 'post-icons/' );
    }

    /**
     * activation plugin hook function
     * @since  0.1
     * @access public
     * @return none
     */
    public function activation() {}

    /**
     * deactivation plugin hook function
     * @since  0.1
     * @return none
     */
    public function deactivation() {}

    /**
     * Returns the instance.
     *
     * @since  0.1
     * @access public
     * @return object
     */
    public static function get_instance() {
      // If the single instance hasn't been set, set it now.
      if ( null == self::$instance ) {
        self::$instance = new self;
      }
      return self::$instance;
    }
  }
}

if ( !function_exists( 'post_icons' ) ) {

  /**
   * Returns instanse of the plugin class.
   *
   * @since  0.1
   * @return object
   */
  function post_icons() {
    return PostIconsClass::get_instance();
  }
}

post_icons();