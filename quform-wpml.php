<?php

/**
 * Plugin Name: Quform WPML
 * Plugin URI: https://www.quform.com
 * Description: Translate Quform forms using WPML.
 * Version: 1.0.1
 * Author: ThemeCatcher
 * Author URI: https://www.themecatcher.net
 * Text Domain: quform-wpml
 */

// Prevent direct script access
if ( ! defined('ABSPATH')) {
    exit;
}

define('QUFORM_WPML_VERSION', '1.0.1');
define('QUFORM_WPML_PATH', dirname(__FILE__));
define('QUFORM_WPML_LIBRARY_PATH', QUFORM_WPML_PATH . '/library');


require_once QUFORM_WPML_LIBRARY_PATH . '/Quform/WPML/ClassLoader.php';
Quform_WPML_ClassLoader::register();

add_action('quform_container_setup', array('Quform_WPML', 'containerSetup'));
add_action('quform_bootstrap', array('Quform_WPML', 'bootstrap'));
