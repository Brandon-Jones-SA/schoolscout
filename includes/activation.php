<?php

if (!defined('ABSPATH')) {
  exit;
}

function school_scout_activate()
{
  // Add Custom Roles
  add_role(
    'student',
    'Student',
    array(
      'read' => true,
      'edit_posts' => false,
      'delete_posts' => false,
    )
  );
  add_role(
    'school',
    'School',
    array(
      'read' => true,
      'edit_posts' => false,
      'delete_posts' => false,
    )
  );

  // Create custom tables
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $schools_table = $wpdb->prefix . 'ss_schools';
  $student_table = $wpdb->prefix . 'ss_students';
  $activities_table = $wpdb->prefix . 'ss_activities';
  $media_table = $wpdb->prefix . 'ss_media';
  $student_school_table = $wpdb->prefix . 'ss_student_school';

  $schools_table_sql = "CREATE TABLE $schools_table (
  id INT NOT NULL AUTO_INCREMENT,
  user_id mediumint NOT NULL,
  name varchar(255),
  street varchar(255),
  province varchar(255),
  person varchar(255),
  contact varchar(255),
  email varchar(255),
  status varchar(255),
  PRIMARY KEY  (id)
  ) $charset_collate;";

  $student_table_sql = "CREATE TABLE $student_table (
  id INT NOT NULL AUTO_INCREMENT,
  user_id mediumint NOT NULL,
  name varchar(255),
  surname varchar(255),
  age mediumint,
  gender varchar(255),
  race varchar(255),
  id_number mediumint,
  email varchar(255),
  parent_name varchar(255),
  parent_surname varchar(255),
  parent_number mediumint,
  parent_email varchar(255),
  parent_id_number mediumint,
  address varchar(255),
  grade mediumint,
  school varchar(255),
  school_outreach_number varchar(255),
  school_outreach_email varchar(255),
  scholarship tinyint(1),
  activities mediumint,
  status varchar(255),
  expires DATE,
  PRIMARY KEY  (id)
  ) $charset_collate;";

  $student_school_sql = "CREATE TABLE $student_school_table (
  id int NOT NULL AUTO_INCREMENT,
  school_ids text ,
  student_id int not null,
  PRIMARY KEY  (id)
  ) $charset_collate;";

  $student_activities = "CREATE TABLE $activities_table (
  id int NOT NULL AUTO_INCREMENT,
  student_id INT NOT NULL,
  type varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  achievements varchar(255),
  passion varchar(255),
  sets_apart varchar(255),
  other_info varchar(255),
  media longtext, 
  PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($student_table_sql);
  dbDelta($schools_table_sql);
  dbDelta($student_school_sql);
  dbDelta($student_activities);

  if (class_exists('WooCommerce')) {
    // Check if the Subscribe product exists using WP_Query
    $subscribe_query = new WP_Query(array(
      'post_type' => 'product',
      'title' => 'Subscribe',
      'post_status' => 'publish',
      'posts_per_page' => 1,
    ));

    if (!$subscribe_query->have_posts()) {
      // Create the Subscribe product if it doesn't exist
      $subscribe_product_data = array(
        'post_title' => 'Subscribe',
        'post_content' => 'Subscription to the service.',
        'post_status' => 'publish',
        'post_type' => 'product',
      );
      $product_id_subscribe = wp_insert_post($subscribe_product_data);
      update_post_meta($product_id_subscribe, '_regular_price', '300');
      update_post_meta($product_id_subscribe, '_price', '300');
    }
    wp_reset_postdata(); // Reset the query

    // Check if the Add Another Activity product exists using WP_Query
    $activity_query = new WP_Query(array(
      'post_type' => 'product',
      'title' => 'Add Another Activity',
      'post_status' => 'publish',
      'posts_per_page' => 1,
    ));

    if (!$activity_query->have_posts()) {
      // Create the Add Another Activity product if it doesn't exist
      $activity_product_data = array(
        'post_title' => 'Add Another Activity',
        'post_content' => 'Add an additional activity to your account.',
        'post_status' => 'publish',
        'post_type' => 'product',
      );
      $product_id_activity = wp_insert_post($activity_product_data);
      update_post_meta($product_id_activity, '_regular_price', '50');
      update_post_meta($product_id_activity, '_price', '50');
    }
    wp_reset_postdata(); // Reset the query

    // Check if the Renewal product exists using WP_Query
    $renewal_query = new WP_Query(array(
      'post_type' => 'product',
      'title' => 'Renewal',
      'post_status' => 'publish',
      'posts_per_page' => 1,
    ));

    if (!$renewal_query->have_posts()) {
      // Create the standard Renewal product if it doesn't exist
      $renewal_product_data = array(
        'post_title' => 'Renewal',
        'post_content' => 'Renew your subscription.',
        'post_status' => 'publish',
        'post_type' => 'product',
      );
      $product_id_renewal = wp_insert_post($renewal_product_data);
      update_post_meta($product_id_renewal, '_regular_price', '300');
      update_post_meta($product_id_renewal, '_price', '300');
    }
    wp_reset_postdata(); // Reset the query
  }

}
