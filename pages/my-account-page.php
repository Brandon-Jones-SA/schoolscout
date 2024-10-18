<?php

if (!defined('ABSPATH')) {
  exit;
}

function school_scout_my_account_page()
{
  wp_enqueue_style('school-scout-my-account', plugin_dir_url(__FILE__) . '../pages/page-assets/my-account.css', array(), '1.0.0', 'all');
  wp_enqueue_script('school-scout-my-account', plugin_dir_url(__FILE__) . '../pages/page-assets/my-account.js', array('jquery'), '1.0.0', true);

  if (is_user_logged_in()) {
    $user = wp_get_current_user();
    // Get the first role of the user (users can have multiple roles)
    $roles = (array) $user->roles;  // Converts roles to array
    $role = $roles[0];  // Return the first role in the array
  }
  ob_start();
  if ($role === "student") {
    student_account_page();
  } else if ($role === "school") {
    school_account_page();
  }
  return ob_get_clean();
}

add_shortcode('school_scout_my_account', 'school_scout_my_account_page');

function student_account_page()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'ss_students';  // Replace 'your_table_name' with the actual table name

  // Query to select all rows from the table
  $user_id = get_current_user_id();  // Assuming you are fetching data for the logged-in user

  // Fetch the data from the database
  $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));

  // Check if data was returned
  if ($row) {
    // Extract the data and assign to variables
    $name = $row->name;  // Column name should match your table's column
    $surname = $row->surname;  // Same for all other fields
    $email = $row->email;
    $age = $row->age;
    $gender = $row->gender;
    $race = $row->race;
    $id_number = $row->id_number;
    $parent_name = $row->parent_name;
    $parent_surname = $row->parent_surname;
    $parent_number = $row->parent_number;
    $parent_email = $row->parent_email;
    $parent_id_number = $row->parent_id_number;
    $address = $row->address;
    $grade = $row->grade;
    $school = $row->school;
    $school_outreach_number = $row->school_outreach_number;
    $school_outreach_email = $row->school_outreach_email;
    $scholarship = $row->scholarship;  // Assuming 1 or 0
    $activities = $row->activities;
  }
  $first_letter_var1 = substr($name, 0, 1);  // Returns "H"
  $first_letter_var2 = substr($surname, 0, 1);  // Returns "W"

  // Combine the two first letters
  $initials = $first_letter_var1 . $first_letter_var2;


  ?>

  <div class='my-account-container'>
    <div class="my-account-header">
      <div class='user-placeholder'><?php echo $initials ?></div>
      <div class="user-info">
        <div class="user-name"><?php echo $name ?></div>
        <div class="user-email"><?php echo $email ?></div>
      </div>
    </div>

    <?php account_not_active() ?>

    <!-- Tabs for navigation -->
    <div class="tabs-container">
      <div class="tab-item tab-1 active" data-tab="1">Account Overview</div>
      <div class="tab-item tab-2" data-tab="2">Schools</div>
      <div class="tab-item tab-3" data-tab="3">Activities</div>
      <div class="tab-item tab-4" data-tab="4">Edit Profile</div>
    </div>

    <!-- Tab contents -->
    <div class="tab-content-container">

      <div class="tab-content tab-1-content active" data-content="1">
        <div> Name : <?php echo $name; ?> </div>
        <div> Surname : <?php echo $surname; ?> </div>
        <div> Email : <?php echo $email; ?> </div>
        <div> Age : <?php echo $age; ?> </div>
        <div> Gender : <?php echo $gender; ?> </div>
        <div> Race : <?php echo $race; ?> </div>
        <div> ID Number : <?php echo $id_number; ?> </div>
        <div> Parent Name : <?php echo $parent_name; ?> </div>
        <div> Parent Surname : <?php echo $parent_surname; ?> </div>
        <div> Parent Number : <?php echo $parent_number; ?> </div>
        <div> Parent Email : <?php echo $parent_email; ?> </div>
        <div> Address : <?php echo $address; ?> </div>
        <div> Grade : <?php echo $grade; ?> </div>
        <div> School : <?php echo $school; ?> </div>
        <div> School Outreach Number : <?php echo $school_outreach_number; ?> </div>
        <div> School Outreach Email : <?php echo $school_outreach_email; ?> </div>
        <label>Interested in Scholarships?
          <div class="checkbox-wrapper-31">
            <input type="checkbox" name="schools[]" disabled <?php if ($scholarship == 1)
              echo 'checked'; ?>>
            <svg viewBox="0 0 35.6 35.6">
              <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
              <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
              <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
            </svg>
          </div>
        </label>
      </div>

      <!-- Additional content for other tabs -->
      <div class="tab-content tab-2-content" data-content="2">
        <div class="tab-2-content-container">
          <div>Select schools</div>
          <?php echo display_user_schools(); ?>
        </div>
      </div>

      <div class="tab-content tab-3-content" data-content="3">
        <div class="activity-container">
          <?php
          display_user_activities_form()
            ?>
        </div>
      </div>
      <div class="tab-content tab-4-content" data-content="4">
        <div class="edit-profile-container">
          <form class="my-account-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="update_my_account">

            <label>Name:
              <input type="text" name="name" value="<?php echo esc_attr($name); ?>" required>
            </label>

            <label>Surname:
              <input type="text" name="surname" value="<?php echo esc_attr($surname); ?>" required>
            </label>

            <label>Email:
              <input type="email" name="email" value="<?php echo esc_attr($email); ?>" required>
            </label>

            <label>Age:
              <input type="number" name="age" value="<?php echo esc_attr($age); ?>" required>
            </label>

            <label for="gender">Gender
              <select name="gender" id="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </label>

            <label for="race">Race
              <select name="race" id="race" required>
                <option value="african">African</option>
                <option value="caucasian">Caucasian</option>
                <option value="asian">Asian</option>
                <option value="other">Other</option>
              </select>
            </label>

            <label>ID Number:
              <input type="text" name="id_number" value="<?php echo esc_attr($id_number); ?>" required>
            </label>

            <label>Parent Name:
              <input type="text" name="parent_name" value="<?php echo esc_attr($parent_name); ?>" required>
            </label>

            <label>Parent Surname:
              <input type="text" name="parent_surname" value="<?php echo esc_attr($parent_surname); ?>" required>
            </label>

            <label>Parent Number:
              <input type="tel" name="parent_number" value="<?php echo esc_attr($parent_number); ?>" required>
            </label>

            <label>Parent Email:
              <input type="email" name="parent_email" value="<?php echo esc_attr($parent_email); ?>" required>
            </label>

            <label>Address:
              <input type="text" name="address" value="<?php echo esc_attr($address); ?>" required>
            </label>

            <label>Grade:
              <input type="text" name="grade" value="<?php echo esc_attr($grade); ?>" required>
            </label>

            <label>School:
              <input type="text" name="school" value="<?php echo esc_attr($school); ?>" required>
            </label>

            <label>School Outreach Number:
              <input type="tel" name="school_outreach_number" value="<?php echo esc_attr($school_outreach_number); ?>"
                required>
            </label>

            <label>School Outreach Email:
              <input type="email" name="school_outreach_email" value="<?php echo esc_attr($school_outreach_email); ?>"
                required>
            </label>
            <label class="checkbox-wrapper">Interested in Scholarships?
              <div class="checkbox-wrapper-31">
                <input type="checkbox" name="schools[]" <?php if ($scholarship == 1)
                  echo 'checked'; ?>>
                <svg viewBox="0 0 35.6 35.6">
                  <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
                  <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
                  <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
                </svg>
              </div>
            </label>
            <button type="submit">Update Profile</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>

  <?php

}

function school_account_page()
{

  global $wpdb;

  $table_name = $wpdb->prefix . 'ss_schools';  // Replace 'your_table_name' with the actual table name

  // Query to select all rows from the table
  $user_id = get_current_user_id();  // Assuming you are fetching data for the logged-in user

  // Fetch the data from the database
  $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));

  // Check if data was returned
  if ($row) {
    // Extract the data and assign to variables
    $name = $row->name;  // Column name should match your table's column
    $email = $row->email;
    $street = $row->street;
    $province = $row->province;
    $contact = $row->person;
    $status = $row->status;
    $phone = $row->contact;

  }
  $first_letter_var1 = substr($name, 0, 1);  // Returns "H"

  ?>

  <div class='my-account-container'>
    <div class="my-account-header">
      <div class='user-placeholder'><?php echo $first_letter_var1 ?></div>
      <div class="user-info">
        <div class="user-name"><?php echo $name ?></div>
        <div class="user-email"><?php echo $email ?></div>
      </div>
    </div>

    <div class="tabs-container">
      <div class="tab-item tab-1 active" data-tab="1">Account Overview</div>
      <div class="tab-item tab-2" data-tab="2">Edit Details</div>
    </div>

    <div class="tab-content-container">

      <div class="tab-content tab-1-content active" data-content="1">
        <div> Name : <?php echo $name; ?> </div>
        <div> Street : <?php echo $street; ?></div>
        <div> Province : <?php echo $province; ?></div>
        <div> Contact Person : <?php echo $contact; ?></div>
        <div> Contact Email : <?php echo $email; ?></div>
        <div> Phone : <?php echo $phone; ?></div>
        <div> Status : <?php echo $status; ?></div>
      </div>


      <div class="tab-content tab-2-content" data-content="2">
        <form class="my-account-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
          <div class="tab-2-content-container edit-profile-container">
            <input type="hidden" name="action" value="update_school_account">
            <label for="school_name">School Name
              <input type="text" name="school_name" id="school_name" required value="<?php echo $name; ?>">
            </label>
            <label for="street">School Street Address
              <input type="text" name="street" id="street" required value="<?php echo $street ?>">
            </label>
            <label for="province">Province
              <select name="province" id="province" required>
                <option value="kzn">KwaZulu-Natal</option>
                <option value="gauteng">Gauteng</option>
                <option value="western_cape">Western Cape</option>
                <option value="eastern_cape">Eastern Cape</option>
                <option value="free_state">Free State</option>
                <option value="mpumalanga">Mpumalanga</option>
                <option value="limpopo">Limpopo</option>
                <option value="north_west">North West</option>
                <option value="northern_cape">Northern Cape</option>
              </select>
            </label>
            <label for="person">Contact Person
              <input type="text" name="person" id="person" required value="<?php echo $contact; ?>">
            </label>
            <label for="contact_number">Contact Number
              <input type="text" name="contact_number" id="contact_number" required value="<?php echo $phone; ?>">
            </label>
            <label for="contact_email">Contact Email
              <input type="email" name="contact_email" id="contact_email" required value="<?php echo $email; ?>">
            </label>
            <button type="submit" name="submit-details">Update</button>
          </div>
        </form>
      </div>
    </div>

  </div>
  <?php

}

function display_user_schools()
{
  global $wpdb;

  // Get the current user ID
  $user_id = get_current_user_id();

  // Query all schools
  $schools_table = $wpdb->prefix . 'ss_schools';
  $schools = $wpdb->get_results("SELECT * FROM $schools_table ORDER BY province, name ASC");

  // Query selected schools for the user (school_ids stored as a comma-separated string)
  $student_schools_table = $wpdb->prefix . 'ss_student_school';
  $school_data = $wpdb->get_var($wpdb->prepare("SELECT school_ids FROM $student_schools_table WHERE student_id = %d", $user_id));

  // Convert the stored school_ids string to an array (if it's not empty)
  $selected_schools = !empty($school_data) ? explode(',', $school_data) : [];

  // Organize schools by province
  $schools_by_province = [];
  foreach ($schools as $school) {
    $schools_by_province[$school->province][] = $school;
  }

  // Start the form
  ob_start();
  ?>

  <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="update-schools">
    <input type="hidden" name="action" value="update_user_schools">
    <input type="hidden" name="student_id" value="<?php echo esc_attr($user_id); ?>">

    <?php
    foreach ($schools_by_province as $province => $province_schools) {
      $province_name = ucwords(str_replace('_', ' ', $province));
      echo "<h4>" . esc_html($province_name) . "</h4>";

      foreach ($province_schools as $school) {
        // Check if the school ID is in the selected_schools array
        $checked = in_array($school->user_id, $selected_schools) ? 'checked' : '';
        ?>
        <label><?php echo esc_html($school->name . ', ' . $school->province); ?>
          <div class="checkbox-wrapper-31">
            <input type="checkbox" name="schools[]" value="<?php echo esc_attr($school->user_id); ?>" <?php echo $checked; ?>>
            <svg viewBox="0 0 35.6 35.6">
              <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
              <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
              <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
            </svg>
          </div>
        </label>
        <?php
      }
    }
    ?>

    <!-- Submit button -->
    <button type="submit">Update Selected Schools</button>
  </form>

  <?php
  return ob_get_clean();
}

add_action('admin_post_update_user_schools', 'update_user_schools');
add_action('admin_post_nopriv_update_user_schools', 'update_user_schools');

function update_user_schools()
{
  global $wpdb;
  $user_id = get_current_user_id();

  // Get submitted schools from the form (as an array of school IDs)
  $submitted_schools = isset($_POST['schools']) ? array_map('intval', $_POST['schools']) : [];

  // Convert the selected school IDs into a comma-separated string
  $school_ids = implode(',', $submitted_schools);

  // Check if the user already has an entry in the student school table
  $student_schools_table = $wpdb->prefix . 'ss_student_school';
  $existing_entry = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $student_schools_table WHERE student_id = %d", $user_id));

  if ($existing_entry) {
    // Update the existing entry
    $wpdb->update(
      $student_schools_table,
      array('school_ids' => $school_ids),  // Update the school_ids field
      array('student_id' => $user_id),     // Where student_id matches
      array('%s'),  // Format for school_ids (string)
      array('%d')   // Format for student_id (integer)
    );
  } else {
    // Insert a new entry if none exists
    $wpdb->insert(
      $student_schools_table,
      array(
        'student_id' => $user_id,
        'school_ids' => $school_ids,
      ),
      array('%d', '%s')
    );
  }

  // Redirect back to the account page or another page after update
  wp_redirect(site_url('/my-account'));
  exit;
}

function display_user_activities_form()
{
  global $wpdb;

  // Get the current user ID
  $user_id = get_current_user_id();

  // Query the number of activities from the `ss_students` table
  $student_table = $wpdb->prefix . 'ss_students';
  $activities_count = $wpdb->get_var($wpdb->prepare("SELECT activities FROM $student_table WHERE user_id = %d", $user_id));

  // Query the activities from the `ss_activities` table
  $activities_table = $wpdb->prefix . 'ss_activities';
  $activities = $wpdb->get_results($wpdb->prepare("SELECT * FROM $activities_table WHERE student_id = %d", $user_id));

  // Define the sport options
  $sports_options = array("rugby", "water-polo", "cricket", "hockey", "swimming", "soccer", "athletics", "basketball", "golf");

  ?>
  <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data"
    id="activities-form">
    <input type="hidden" name="action" value="update_activities">
    <input type="hidden" name="activity_count" value="<?php echo esc_attr($activities_count); ?>">

    <?php
    // Loop through the activities
    for ($x = 1; $x <= $activities_count; $x++) {
      $current_activity = $activities[$x - 1];
      ?>

      <div class="activity-section">
        <h3>Activity <?php echo $x; ?></h3>

        <!-- Hidden ID Field for Each Activity -->
        <input type="hidden" name="activities[<?php echo $x; ?>][id]"
          value="<?php echo esc_attr($current_activity->id); ?>">

        <!-- Activity type selection (Sport / Cultural) -->
        <label>
          <input type="radio" name="activities[<?php echo $x; ?>][activity-type]" value="sport"
            id="activity-sport-<?php echo $x; ?>" <?php if ($current_activity->type === 'sport')
                 echo 'checked'; ?>> Sport
        </label>
        <label>
          <input type="radio" name="activities[<?php echo $x; ?>][activity-type]" value="cultural"
            id="activity-cultural-<?php echo $x; ?>" <?php if ($current_activity->type === 'cultural')
                 echo 'checked'; ?>>
          Cultural
        </label>

        <!-- Sport Type Dropdown -->
        <label for="sport-type-<?php echo $x; ?>" id="sport-type-label-<?php echo $x; ?>" <?php if ($current_activity->type !== 'sport')
                echo 'style="display:none"'; ?>>Type of Sport:
          <select id="sport-type-<?php echo $x; ?>" name="activities[<?php echo $x; ?>][sport-type]">
            <option value="" disabled>Select a sport</option>
            <?php
            $is_in_dropdown = false;
            foreach ($sports_options as $option) {
              if ($current_activity->name === $option) {
                $is_in_dropdown = true;
                echo '<option value="' . $option . '" selected>' . ucfirst($option) . '</option>';
              } else {
                echo '<option value="' . $option . '">' . ucfirst($option) . '</option>';
              }
            }
            ?>
            <option value="other" <?php if (!$is_in_dropdown)
              echo 'selected'; ?>>Other</option>
          </select>
        </label>

        <!-- Other Activity Text Field -->
        <label for="other-activity-<?php echo $x; ?>" id="other-activity-label-<?php echo $x; ?>" <?php if ($is_in_dropdown || $current_activity->type !== 'sport')
                echo 'style="display:none"'; ?>>Describe your activity
          <input type="text" name="activities[<?php echo $x; ?>][other-activity]" id="other-activity-<?php echo $x; ?>"
            value="<?php echo !$is_in_dropdown ? esc_attr($current_activity->name) : ''; ?>">
        </label>

        <!-- Achievements, Passion, and Other Info Fields -->
        <label for="achievements-<?php echo $x; ?>">Achievements in this activity
          <input type="text" name="activities[<?php echo $x; ?>][achievements]" id="achievements-<?php echo $x; ?>"
            value="<?php echo esc_attr($current_activity->achievements); ?>">
        </label>

        <label for="sets-apart-<?php echo $x; ?>">What sets you apart from the rest?
          <input type="text" name="activities[<?php echo $x; ?>][sets-apart]" id="sets-apart-<?php echo $x; ?>"
            value="<?php echo esc_attr($current_activity->sets_apart); ?>">
        </label>

        <label for="passion-<?php echo $x; ?>">Why are you passionate about this activity
          <input type="text" name="activities[<?php echo $x; ?>][passion]" id="passion-<?php echo $x; ?>"
            value="<?php echo esc_attr($current_activity->passion); ?>">
        </label>

        <label for="other-info-<?php echo $x; ?>">Other important information worth noting
          <input type="text" name="activities[<?php echo $x; ?>][other-info]" id="other-info-<?php echo $x; ?>"
            value="<?php echo esc_attr($current_activity->other_info); ?>">
        </label>

        <!-- FILE UPLOAD SECTION -->
        <div class="upload-area" id="upload-area-<?php echo $x; ?>">
          <p>Add images or video of this activity</p>
          <p>Drag & Drop files here or <span id="file-browse-<?php echo $current_activity->id; ?>"
              class="file-browse">browse</span></p>
          <input type="file" id="file-input-<?php echo $current_activity->id; ?>" multiple style="display: none;" />
        </div>
        <div class="preview-area" id="preview-area-<?php echo $current_activity->id; ?>"></div>

      </div>

      <?php
    }
    ?>
    <button type="submit">Update All Activities</button>
  </form>
  <?php
}
function handle_update_activities()
{
  global $wpdb;
  error_log("Called");

  // Ensure the user is logged in
  if (!is_user_logged_in()) {
    wp_send_json_error(['message' => 'User not logged in']);
    return;
  }

  $user_id = get_current_user_id();
  $activities = isset($_POST['activities']) ? $_POST['activities'] : [];

  // Loop through each activity and update the database
  foreach ($activities as $activity_index => $activity_data) {
    $activity_id = intval($activity_data['id']);
    $type = sanitize_text_field($activity_data['activity-type']);
    $name = sanitize_text_field($activity_data['sport-type'] ?? $activity_data['other-activity']);
    $achievements = sanitize_textarea_field($activity_data['achievements']);
    $sets_apart = sanitize_textarea_field($activity_data['sets-apart']);
    $passion = sanitize_textarea_field($activity_data['passion']);
    $other_info = sanitize_textarea_field($activity_data['other-info']);

    // Update the activity in the database
    $wpdb->update(
      $wpdb->prefix . 'ss_activities',
      [
        'type' => $type,
        'name' => $name,
        'achievements' => $achievements,
        'sets_apart' => $sets_apart,
        'passion' => $passion,
        'other_info' => $other_info,
      ],
      ['id' => $activity_id],
    );
  }

  // Return success response
  wp_redirect(site_url('/my-account?success=1'));
  exit;
}

// Register the AJAX action
add_action('admin_post_update_activities', 'handle_update_activities');
add_action('admin_post_nopriv_update_activities', 'handle_update_activities');


add_action('admin_post_update_my_account', 'handle_update_my_account');
add_action('admin_post_nopriv_update_my_account', 'handle_update_my_account'); // For non-logged-in users if needed

function handle_update_my_account()
{
  // Verify if the user is logged in
  if (!is_user_logged_in()) {
    wp_redirect(home_url()); // Redirect to the home page if the user is not logged in
    exit;
  }

  // Get the current user ID
  $user_id = get_current_user_id();

  // Validate and sanitize form inputs
  $name = sanitize_text_field($_POST['name']);
  $surname = sanitize_text_field($_POST['surname']);
  $email = sanitize_email($_POST['email']);
  $age = intval($_POST['age']);
  $gender = sanitize_text_field($_POST['gender']);
  $race = sanitize_text_field($_POST['race']);
  $id_number = sanitize_text_field($_POST['id_number']);
  $parent_name = sanitize_text_field($_POST['parent_name']);
  $parent_surname = sanitize_text_field($_POST['parent_surname']);
  $parent_number = sanitize_text_field($_POST['parent_number']);
  $parent_email = sanitize_email($_POST['parent_email']);
  $parent_id_number = sanitize_text_field($_POST['parent_id_number']);
  $address = sanitize_text_field($_POST['address']);
  $grade = sanitize_text_field($_POST['grade']);
  $school = sanitize_text_field($_POST['school']);
  $school_outreach_number = sanitize_text_field($_POST['school_outreach_number']);
  $school_outreach_email = sanitize_email($_POST['school_outreach_email']);
  $scholarship = isset($_POST['schools']) ? 1 : 0; // Checkbox handling

  // Update the data in your custom table or user meta
  global $wpdb;
  $table_name = $wpdb->prefix . 'ss_students';

  // Prepare the data to be updated
  $data = array(
    'name' => $name,
    'surname' => $surname,
    'email' => $email,
    'age' => $age,
    'gender' => $gender,
    'race' => $race,
    'id_number' => $id_number,
    'parent_name' => $parent_name,
    'parent_surname' => $parent_surname,
    'parent_number' => $parent_number,
    'parent_email' => $parent_email,
    'parent_id_number' => $parent_id_number,
    'address' => $address,
    'grade' => $grade,
    'school' => $school,
    'school_outreach_number' => $school_outreach_number,
    'school_outreach_email' => $school_outreach_email,
    'scholarship' => $scholarship
  );

  // Where clause for the update
  $where = array('user_id' => $user_id);

  // Perform the update
  $updated = $wpdb->update($table_name, $data, $where);

  // Redirect after the update
  if ($updated !== false) {
    // Success: Redirect to the account page with a success flag
    wp_redirect(site_url('/my-account?success=1'));
  } else {
    // Failure: Redirect with an error flag
    wp_redirect(site_url('/my-account?error=update_failed'));
  }

  exit; // Always exit after wp_redirect
}


function account_not_active()
{
  global $wpdb;

  $table_name = $wpdb->prefix . "ss_students";
  $user_id = get_current_user_id();

  // Fetch the user's status from the database
  $status = $wpdb->get_var($wpdb->prepare(
    "SELECT status FROM $table_name WHERE user_id = %d",
    $user_id
  ));

  if ($status === "pending") {
    // Use WP_Query to get the "Subscribe" product by title
    $args = array(
      'post_type' => 'product',
      'title' => 'Subscribe', // Product title to search for
      'post_status' => 'publish',
      'posts_per_page' => 1
    );
    $query = new WP_Query($args);

    // Check if the product was found
    if ($query->have_posts()) {
      $query->the_post();
      $subscribe_url = get_permalink(get_the_ID());
    } else {
      // Fallback URL if the product does not exist
      $subscribe_url = home_url();
    }

    // Reset the query (important when using WP_Query)
    wp_reset_postdata();

    ?>
    <div class="pending my-account-header">
      <div class="heading">
        Your account is inactive.
      </div>
      <a href="<?php echo esc_url($subscribe_url); ?>">
        <button class="activate-now">Activate Now</button>
      </a>
    </div>
    <?php
  }
}

add_action('admin_post_update_school_account', 'handle_update_school_account');

function handle_update_school_account()
{
  global $wpdb;

  // Get the current user ID
  $user_id = get_current_user_id();

  // Sanitize form data
  $name = sanitize_text_field($_POST['school_name']);
  $street = sanitize_text_field($_POST['street']);
  $province = sanitize_text_field($_POST['province']);
  $person = sanitize_text_field($_POST['person']);
  $contact = sanitize_text_field($_POST['contact_number']); // Use sanitize_text_field for phone numbers
  $email = sanitize_email($_POST['contact_email']);

  // Define the table name
  $table_name = $wpdb->prefix . 'ss_schools';

  // Data to update
  $data = array(
    'name' => $name,
    'street' => $street,
    'province' => $province,
    'person' => $person,
    'contact' => $contact,
    'email' => $email
  );

  // WHERE clause to update the row based on user ID
  $where = array('user_id' => $user_id);

  // Define the format of the data (all are strings, except for contact number which is treated as a string in this case)
  $format = array('%s', '%s', '%s', '%s', '%s', '%s');

  // Update the database
  $updated = $wpdb->update($table_name, $data, $where, $format, array('%d'));

  // Check if the update was successful
  if ($updated !== false) {
    // Redirect or display a success message
    wp_redirect(site_url('/my-account?success=1'));
    exit;
  } else {
    // Redirect or display an error message
    wp_redirect(site_url('/my-account?error=update_failed'));
    exit;
  }
}
