<?php

if (!defined("ABSPATH")) {
  exit;
}

add_action('wp_ajax_nopriv_school_signup_step_1', 'school_signup_step_1_handler');
add_action('wp_ajax_school_signup_step_1', 'school_signup_step_1_handler');

function school_signup_step_1_handler()
{
  if (defined("DOING_AJAX") && DOING_AJAX) {
    $username = sanitize_text_field($_POST['username']);
    $email_address = sanitize_email($_POST['email_address']);
    $password = sanitize_text_field($_POST['password']);
    $confirm_password = sanitize_text_field($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
      wp_send_json_error(array('message' => 'Passwords do not match.'));
      wp_die();
    }

    if (username_exists($username) || email_exists($email_address)) {
      wp_send_json_error(array('message' => 'Username or email already exists.'));
      wp_die();
    }

    $user_data = array(
      'user_login' => $username,   // The username
      'user_pass' => $password,   // The password
      'user_email' => $email_address, // The email
      'role' => 'school',    // Assign the 'school' role
    );

    // Insert the user and assign the role
    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
      wp_send_json_error(array('message' => 'Error creating user account: ' . $user_id->get_error_message()));
      wp_die();
    }


    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);



    wp_send_json_success();
  }
}

// Registering AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_nopriv_school_signup_step_2', 'school_signup_step_2_handler');
add_action('wp_ajax_school_signup_step_2', 'school_signup_step_2_handler');

function school_signup_step_2_handler()
{
  if (defined("DOING_AJAX") && DOING_AJAX) {
    global $wpdb;



    $user_id = get_current_user_id();

    $school_name = sanitize_text_field(($_POST['school_name']));
    $school_address = sanitize_text_field(($_POST['street']));
    $province = sanitize_text_field($_POST['province']);
    $contact_number = sanitize_text_field($_POST['contact_number']);
    $email = sanitize_email($_POST['contact_email']);
    $person = sanitize_text_field($_POST['person']);

    // if (empty($school_name) || empty($school_address) || empty($contact_number)) {
    //   wp_send_json_error(array('message' => 'All fields are required.'));
    //   wp_die();
    // }

    // Insert the data into the custom table (ss_schools)
    $table_name = $wpdb->prefix . 'ss_schools';
    $result = $wpdb->update(
      $table_name,
      array(
        'name' => $school_name,
        'street' => $school_address,
        'contact' => $contact_number,
        'province' => $province,
        'person' => $person,
        'email' => $email
      ),
      array(
        "user_id" => $user_id
      ),
      array(
        '%s'
      )
    );

    // Check if the insert was successful
    if ($result === false) {
      wp_send_json_error(array('message' => 'Failed to save school information.'));
    } else {
      wp_send_json_success(array('redirect_url' => home_url('/my-account')));
    }

    wp_die(); // Properly terminate the request
  }

  wp_die(); // Required to properly terminate the AJAX request
}

add_action('wp_ajax_nopriv_student_signup_step_1', 'student_signup_step_1_handler');
add_action('wp_ajax_student_signup_step_1', 'student_signup_step_1_handler');

function student_signup_step_1_handler()
{


  if (defined("DOING_AJAX") && DOING_AJAX) {
    $username = sanitize_text_field($_POST['username']);
    $email_address = sanitize_email($_POST['email_address']);
    $password = sanitize_text_field($_POST['password']);
    $confirm_password = sanitize_text_field($_POST['confirm_password']);



    if ($password !== $confirm_password) {
      wp_send_json_error(array("message" => 'Passwords do not match'));
      wp_die();
    }

    if (username_exists($username) || email_exists($email_address)) {
      wp_send_json_error(array('message' => 'Username or email already exists'));
      wp_die();
    }

    $user_data = array(
      'user_login' => $username,
      'user_pass' => $password,
      'user_email' => $email_address,
      'role' => 'student',
    );

    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
      wp_send_json_error(array('message' => 'Error creating user account: ' . $user_id->get_error_message()));
      wp_die();
    }

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    wp_send_json_success();
  }
}

add_action('wp_ajax_nopriv_student_signup_step_2', "student_signup_step_2_handler");
add_action('wp_ajax_student_signup_step_2', "student_signup_step_2_handler");

function student_signup_step_2_handler()
{
  global $wpdb;
  if (defined("DOING_AJAX") && DOING_AJAX) {
    $first_name = sanitize_text_field($_POST["firstName"]);
    $last_name = sanitize_text_field($_POST["lastName"]);
    $age = intval($_POST['age']);
    $gender = sanitize_text_field($_POST["gender"]);
    $race = sanitize_text_field($_POST['race']);
    $id_number = intval($_POST['idNumber']);
    $email_address = sanitize_email($_POST['emailAddress']);
    $parent_name = sanitize_text_field($_POST['parentName']);
    $parent_surname = sanitize_text_field($_POST['parentSurname']);
    $parent_contact_number = sanitize_text_field($_POST['parentContactNumber']);
    $parent_email = sanitize_email($_POST['parentEmail']);
    $address = sanitize_text_field($_POST['address']);
    $grade = intval($_POST['grade']);
    $school = sanitize_text_field($_POST['school']);
    $school_outreach_number = sanitize_text_field($_POST['schoolOutreachNumber']);
    $school_outreach_email = sanitize_email($_POST['schoolOutreachEmail']);
    $scholarship = intval($_POST['scholarship']);
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'ss_students';
    error_log($id_number);
    $data = array(
      'name' => $first_name,
      'surname' => $last_name,
      'age' => $age,
      'gender' => $gender,
      'race' => $race,
      'id_number' => $id_number,
      'email' => $email_address,
      'parent_name' => $parent_name,
      'parent_surname' => $parent_surname,
      'parent_number' => $parent_contact_number,
      'parent_email' => $parent_email,
      'address' => $address,
      'grade' => $grade,
      'school' => $school,
      'school_outreach_number' => $school_outreach_number,
      'school_outreach_email' => $school_outreach_email,
      'scholarship' => $scholarship,
      'activities' => 1
    );

    $where = array(
      'user_id' => $user_id,
    );

    $result = $wpdb->update($table_name, $data, $where);

    if ($result === false) {
      wp_send_json_error(array("message" => 'Failed to save student data'));
      wp_die();
    } else {
      wp_send_json_success();
      wp_die();
    }

  }
}

add_action("wp_ajax_student_signup_step_3", 'student_signup_step_3_handler');
add_action("wp_ajax_nopriv_student_signup_step_3", 'student_signup_step_3_handler');


function student_signup_step_3_handler()
{
  $user_id = get_current_user_id();

  if (!$user_id) {
    wp_send_json_error(['message' => 'User is not logged in.']);
    return;
  }

  $type = sanitize_text_field($_POST['type']);
  $name = sanitize_text_field($_POST['name']);
  $achievements = sanitize_text_field($_POST['achievements']);
  $passion = sanitize_text_field($_POST['passion']);
  $sets_apart = sanitize_text_field($_POST['sets_apart']);
  $other_info = sanitize_text_field($_POST['other_info']);

  $uploaded_file_urls = [];
  if (!empty($_FILES['media_files'])) {
    foreach ($_FILES['media_files']['name'] as $key => $value) {
      // Check if the file was uploaded without errors
      if ($_FILES['media_files']['error'][$key] === UPLOAD_ERR_OK) {
        // Get the file details for the current file
        $file = [
          'name' => $_FILES['media_files']['name'][$key],
          'type' => $_FILES['media_files']['type'][$key],
          'tmp_name' => $_FILES['media_files']['tmp_name'][$key],
          'error' => $_FILES['media_files']['error'][$key],
          'size' => $_FILES['media_files']['size'][$key],
        ];

        // Handle file upload using wp_handle_upload
        $upload = wp_handle_upload($file, ['test_form' => false]);

        if ($upload && !isset($upload['error'])) {
          // Successfully uploaded, store the file URL
          $file_url = $upload['url'];
          $uploaded_file_urls[] = $file_url;
        } else {
          // Log upload error
          error_log("File upload error: " . $upload['error']);
        }
      }
    }
  }
  global $wpdb;
  $media_json = json_encode($uploaded_file_urls);

  $table_name = $wpdb->prefix . 'ss_activities';
  $insert_data = [
    'student_id' => $user_id,
    'type' => $type,
    'name' => $name,
    'achievements' => $achievements,
    'passion' => $passion,
    'sets_apart' => $sets_apart,
    'other_info' => $other_info,
    'media' => $media_json
  ];

  $existing_entry = $wpdb->get_row(
    $wpdb->prepare(
      "SELECT id FROM $table_name WHERE id = %d",
      $user_id
    )
  );
  if ($existing_entry) {
    $update_data = [
      'type' => $type,
      'name' => $name,
      'achievements' => $achievements,
      'passion' => $passion,
      'sets_apart' => $sets_apart,
      'other_info' => $other_info,
      'media' => $media_json
    ];

    $update_format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s'];

    $result = $wpdb->update(
      $table_name,
      $update_data,
      ['id' => $user_id],
      $update_format,
      ['%d']
    );

    if ($result !== false) {
      wp_send_json_success([
        'message' => 'Data updated successfully!',
        'file_urls' => $uploaded_file_urls,
      ]);
    } else {
      wp_send_json_error(['message' => 'Error updating data in the database.']);
    }
  } else {
    // If no entry exists, insert a new row
    $insert_data = [
      'student_id' => $user_id,
      'type' => $type,
      'name' => $name,
      'achievements' => $achievements,
      'passion' => $passion,
      'sets_apart' => $sets_apart,
      'other_info' => $other_info,
      'media' => $media_json
    ];

    $insert_format = ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];

    $result = $wpdb->insert($table_name, $insert_data, $insert_format);

    if ($result !== false) {
      wp_send_json_success([
        'message' => 'Data inserted successfully!',
        'file_urls' => $uploaded_file_urls,
      ]);
    } else {
      wp_send_json_error(['message' => 'Error inserting data into the database.']);
    }
  }

}

add_action("wp_ajax_student_signup_step_4", "handle_student_signup_step_4");

function handle_student_signup_step_4()
{
  global $wpdb;
  $user_id = get_current_user_id();
  $table_name = $wpdb->prefix . "ss_student_school";

  if (isset($_POST['selected_schools']) && is_array($_POST['selected_schools'])) {
    // Sanitize and implode the array into a comma-separated string
    $selected_schools = array_map('sanitize_text_field', $_POST['selected_schools']);
    $school_ids = implode(',', $selected_schools);  // Convert the array to a comma-separated string

    // Insert the row into the custom table
    $data = array(
      'student_id' => $user_id,  // The logged-in user ID
      'school_ids' => $school_ids  // Comma-separated list of school IDs
    );

    // Check if the user already has an entry in the table
    $existing_entry = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE student_id = %d", $user_id));

    if ($existing_entry) {
      // If an entry exists, update the existing row
      $wpdb->update(
        $table_name,
        array('school_ids' => $school_ids),  // Update the school_ids field
        array('student_id' => $user_id),     // Where student_id matches
        array('%s'),  // Format for school_ids (string)
        array('%d')   // Format for student_id (integer)
      );
    } else {
      // If no entry exists, insert a new row
      $wpdb->insert(
        $table_name,
        $data,
        array('%d', '%s')  // Data formats: student_id (integer), school_ids (string)
      );
    }

    // Redirect URL after successful submission
    $account_page_url = site_url('/my-account'); // Change this to your actual account page URL

    // Return success response with redirect URL
    wp_send_json_success(array('message' => 'Schools successfully submitted and saved', 'redirect_url' => $account_page_url));
  } else {
    wp_send_json_error(array('message' => 'No schools selected or invalid data'));
  }

  wp_die(); // Always end the AJAX function with wp_die()
}

add_action('wp_ajax_get_student_details', 'get_student_details');
add_action('wp_ajax_nopriv_get_student_details', 'get_student_details');

function get_student_details()
{
  global $wpdb;

  // Check if the student_id is passed
  if (!isset($_POST['student_id'])) {
    wp_send_json_error('No student ID provided');
  }

  $student_id = intval($_POST['student_id']);

  // Query the database to get student details from the ss_students table
  $students_table = $wpdb->prefix . 'ss_students';
  $student_details = $wpdb->get_row($wpdb->prepare(
    "SELECT name, surname, age, gender, race, email, grade, school, school_outreach_number, school_outreach_email, scholarship 
        FROM $students_table 
        WHERE user_id = %d",
    $student_id
  ));

  if (!$student_details) {
    wp_send_json_error('Student details not found.');
  }

  // Query the database to get student activities from the ss_activities table
  $activities_table = $wpdb->prefix . 'ss_activities';
  $student_activities = $wpdb->get_results($wpdb->prepare(
    "SELECT name, type, achievements, passion, sets_apart, other_info, media 
        FROM $activities_table 
        WHERE student_id = %d",
    $student_id
  ));

  // Prepare the response data with student details and activities
  $response = [
    'student' => $student_details, // Add student details
    'activities' => $student_activities // Add student activities
  ];

  // Return the response as a JSON object
  wp_send_json_success($response);
}

function handle_ajax_file_upload()
{
  global $wpdb;
  $uploaded_urls = [];

  // Get the activity ID from the request (you need to ensure this is sent from the JS)
  $activity_id = intval($_POST['activity_id']);

  // Loop through uploaded files
  foreach ($_FILES['files']['name'] as $key => $filename) {
    $file = array(
      'name' => $_FILES['files']['name'][$key],
      'type' => $_FILES['files']['type'][$key],
      'tmp_name' => $_FILES['files']['tmp_name'][$key],
      'error' => $_FILES['files']['error'][$key],
      'size' => $_FILES['files']['size'][$key],
    );

    // Handle file upload
    $upload = wp_handle_upload($file, array('test_form' => false));

    if (isset($upload['url'])) {
      $uploaded_urls[] = $upload['url']; // Add URL to the array
    }
  }

  // If any images were uploaded, update the database
  if (!empty($uploaded_urls)) {
    // Fetch existing media
    $existing_media = $wpdb->get_var($wpdb->prepare("SELECT media FROM wp_ss_activities WHERE id = %d", $activity_id));
    $media_array = json_decode($existing_media, true) ?: [];

    // Merge the new URLs with the existing ones
    $media_array = array_merge($media_array, $uploaded_urls);

    // Update the media list in the database
    $wpdb->update(
      $wpdb->prefix . 'ss_activities',
      array('media' => json_encode(array_values($media_array))), // Save the updated media list
      array('id' => $activity_id)
    );
  }

  // Return the uploaded file URLs as a JSON response
  error_log(print_r($uploaded_urls, true));
  wp_send_json_success(array('urls' => $uploaded_urls));
}


add_action('wp_ajax_file_upload', 'handle_ajax_file_upload');
add_action('wp_ajax_nopriv_file_upload', 'handle_ajax_file_upload'); // For non-logged-in users

function handle_ajax_get_media()
{
  global $wpdb;
  $activity_id = intval($_GET['activity_id']);
  $media_urls = $wpdb->get_var($wpdb->prepare("SELECT media FROM wp_ss_activities WHERE id = %d", $activity_id));
  // Return the media URLs as a JSON response
  wp_send_json_success(array('urls' => $media_urls, true));
}

add_action('wp_ajax_get_media', 'handle_ajax_get_media');
add_action('wp_ajax_nopriv_get_media', 'handle_ajax_get_media');

function handle_ajax_remove_media()
{
  global $wpdb;
  $activity_id = intval($_POST['activity_id']);
  $media_url = sanitize_text_field($_POST['media_url']);

  // Fetch the current media list
  $current_media = $wpdb->get_var($wpdb->prepare("SELECT media FROM wp_ss_activities WHERE id = %d", $activity_id));
  $media_array = json_decode($current_media, true);

  // Remove the selected media from the array
  if (($key = array_search($media_url, $media_array)) !== false) {
    unset($media_array[$key]);
  }

  // Update the media list in the database
  $wpdb->update(
    $wpdb->prefix . 'ss_activities',
    array('media' => json_encode(array_values($media_array))),
    array('id' => $activity_id)
  );

  wp_send_json_success();
}

add_action('wp_ajax_remove_media', 'handle_ajax_remove_media');
add_action('wp_ajax_nopriv_remove_media', 'handle_ajax_remove_media');
