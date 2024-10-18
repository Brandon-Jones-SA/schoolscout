<?php

if (!defined("ABSPATH")) {
  exit;
}
add_action('woocommerce_order_status_completed', 'check_subscription_purchase_and_update_table', 10, 1);

function check_subscription_purchase_and_update_table($order_id)
{
  // Get the order object
  $order = wc_get_order($order_id);
  if (!$order) {
    return;
  }

  // Loop through all items in the order
  foreach ($order->get_items() as $item_id => $item) {
    // Get the product ID
    $product_id = $item->get_product_id();
    $product = wc_get_product($product_id);

    // Get the user ID from the order
    $user_id = $order->get_user_id();
    if (!$user_id) {
      return;
    }

    // Fetch the user's current data from the custom table
    global $wpdb;
    $table_name = $wpdb->prefix . 'ss_students';
    $user_data = $wpdb->get_row($wpdb->prepare(
      "SELECT activities, expires FROM $table_name WHERE user_id = %d",
      $user_id
    ));

    if (!$user_data) {
      return;
    }

    // Check if the product is the "Subscribe" product
    if ($product && $product->get_slug() === 'subscribe') {
      // Calculate the expiration date (6 months from now)
      $expiration_date = date('Y-m-d', strtotime('+6 months'));

      // Update the user's status to active and set the new expiration date
      $wpdb->update(
        $table_name,
        array(
          'status' => 'active',         // Set status to active
          'expires' => $expiration_date  // Set the expiration date
        ),
        array('user_id' => $user_id),
        array('%s', '%s'),
        array('%d')
      );
    }

    // Check if the product is "Add Another Activity"
    if ($product && $product->get_slug() === 'add-another-activity') {
      // Increase the activities field by 1
      $new_activity_count = $user_data->activities + 1;

      // Update the activities field in the database
      $wpdb->update(
        $table_name,
        array('activities' => $new_activity_count),
        array('user_id' => $user_id),
        array('%d'),
        array('%d')
      );

      // Add a new blank row to the ss_activities table for the user
      $activities_table = $wpdb->prefix . 'ss_activities';
      $wpdb->insert(
        $activities_table,
        array(
          'student_id' => $user_id,  // Insert the user ID
          // Leave other fields blank
        ),
        array('%d') // Only student_id has a value, others will default to null or blank
      );
    }

    // Check if the product is "Renewal"
    if ($product && $product->get_slug() === 'renewal') {
      // Get the current expiration date from the user data
      $current_expiration_date = $user_data->expires;
      $current_date = date('Y-m-d');

      // Check if the current expiration date has passed
      if ($current_expiration_date > $current_date) {
        // If the expiration date hasn't passed, extend it by 6 months from the current expiration date
        $new_expiration_date = date('Y-m-d', strtotime('+6 months', strtotime($current_expiration_date)));
      } else {
        // If the expiration date has passed, set it to 6 months from today
        $new_expiration_date = date('Y-m-d', strtotime('+6 months'));
      }

      // Update the expiration date in the database
      $wpdb->update(
        $table_name,
        array('expires' => $new_expiration_date),
        array('user_id' => $user_id),
        array('%s'),
        array('%d')
      );
    }
  }
}


add_action('woocommerce_cart_calculate_fees', 'add_activity_fees_for_renewal');
function add_activity_fees_for_renewal($cart)
{
  // Only apply this logic on the frontend
  if (is_admin() && !defined('DOING_AJAX')) {
    return;
  }

  // Get the current user ID
  $user_id = get_current_user_id();
  if (!$user_id) {
    return;
  }

  // Fetch the user's activities from the custom table
  global $wpdb;
  $table_name = $wpdb->prefix . 'ss_students';
  $user_data = $wpdb->get_row($wpdb->prepare(
    "SELECT activities FROM $table_name WHERE user_id = %d",
    $user_id
  ));

  // Ensure the user has activity data and that it's greater than 1
  if ($user_data && $user_data->activities > 1) {
    $additional_activities = $user_data->activities - 1; // Subtract the first activity (free)
    $fee_amount = $additional_activities * 50; // R50 per additional activity

    // Loop through cart items to check if "Renewal" is in the cart
    foreach ($cart->get_cart() as $cart_item) {
      $product = wc_get_product($cart_item['product_id']);
      if ($product && $product->get_slug() === 'renewal') {
        // Add the fee to the cart
        WC()->cart->add_fee('Additional Activities', $fee_amount, false);
        break; // No need to check further once we found the "Renewal" product
      }
    }
  }
}
