<?php

if (!defined("ABSPATH")) {
  exit;
}

add_action('user_register', 'add_custom_entry', 10, 1);

function add_custom_entry($user_id)
{
  $user = get_userdata($user_id);
  global $wpdb;
  if (in_array('school', $user->roles)) {
    $table_name = $wpdb->prefix . 'ss_schools';
  } else if (in_array('student', $user->roles)) {
    $table_name = $wpdb->prefix . "ss_students";
  } else {
    return;
  }
  $wpdb->insert(
    $table_name,
    array(
      'user_id' => $user_id,
      'status' => 'pending'
    ),
    array(
      '%d',
      '%s'
    )
  );

}