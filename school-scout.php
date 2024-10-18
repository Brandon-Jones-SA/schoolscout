<?php

/**
 * Plugin Name: School Scout
 * Description: Custom plugin developed for school scout
 * Author: SDDS Web
 * Version: 1.0.1
 */

if (!defined('ABSPATH')) {
  exit;
}

require_once 'forms/school-signup-form.php';
require_once 'forms/student-signup-form.php';
require_once 'pages/my-account-page.php';
require_once 'includes/activation.php';
require_once 'includes/ajax-handlers.php';
require_once 'includes/user-creation-data.php';
require_once 'includes/admin-school.php';
require_once 'includes/purchased-subscription.php';
require_once 'pages/interested-students.php';

register_activation_hook(__FILE__, 'school_scout_activate');


// Check if WooCommerce is active
function my_plugin_check_woocommerce_dependency()
{
  // Check if WooCommerce is installed and active
  if (!class_exists('WooCommerce')) {
    // Deactivate the plugin
    deactivate_plugins(plugin_basename(__FILE__));

    // Display an admin notice
    add_action('admin_notices', 'my_plugin_woocommerce_required_notice');
  }
}
add_action('admin_init', 'my_plugin_check_woocommerce_dependency');

// Admin notice to display if WooCommerce is not active
function my_plugin_woocommerce_required_notice()
{
  ?>
  <div class="notice notice-error">
    <p><?php _e('My Plugin requires WooCommerce to be installed and activated.', 'my-plugin'); ?></p>
  </div>
  <?php
}
