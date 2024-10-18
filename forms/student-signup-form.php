<?php


function school_scout_student_signup_form()
{
  wp_enqueue_script('jquery');
  wp_enqueue_style('student-signup-form', plugin_dir_url(__FILE__) . 'form-css/student-signup.css');
  wp_enqueue_script('student-signup-form', plugin_dir_url(__FILE__) . 'form-js/student-signup.js', array('jquery'), null, true);

  wp_localize_script('student-signup-form', 'student_signup_object', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('student_signup_nonce')
  ));

  ob_start();
  ?>
  <div class="form-progress">
    <div class="progress-bar">
      <div class="circle step-1 active"></div>
      <div class="line line-1"></div>
      <div class="circle step-2"></div>
      <div class="line line-2"></div>
      <div class="circle step-3"></div>
      <div class="line line-3"></div>
      <div class="circle step-4"></div>
    </div>
  </div>

  <form action="" method="post" id="student-signup-form">
    <!-- Step 1 -->
    <div class="form-step" id="step-1">
      <label for="username">Username
        <input type="text" name="username" id="username" required>
      </label>
      <label for="email_address">Email Address
        <input type="email" name="email_address" id="email_address" required>
      </label>
      <label for="password">Password
        <input type="password" name="password" id="password" required>
      </label>
      <label for="confirm_password">Confirm Password
        <input type="password" name="confirm_password" id="confirm_password" required>
      </label>
      <input type="button" class="next-step" value="Next" id="next-step-1">
      <div class="loader-container">
        <div class="custom-loader"></div>
      </div>
    </div>

    <!-- Step 2 -->
    <div class="form-step" id="step-2" style="display:none;">
      <label for="first_name">First Name
        <input type="text" name="first_name" id="first_name" required>
      </label>
      <label for="last_name">Last Name
        <input type="text" name="last_name" id="last_name" required>
      </label>
      <label for="age">Age
        <input type="number" name="age" id="age" required>
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
      <label for="id_number">ID Number
        <input type="number" name="id_number" id="id_number" required>
      </label>
      <label for="email_address">Email Address
        <input type="email" name="email_address" id="email_address" required>
      </label>
      <label for="parent_name">Parent Name
        <input type="text" name="parent_name" id="parent_name" required>
      </label>
      <label for="parent_surname">Parent Surname
        <input type="text" name="parent_surname" id="parent_surname" required>
      </label>
      <label for="parent_contact_number">Parent Contact Number
        <input type="number" name="parent_contact_number" id="parent_contact_number" required>
      </label>
      <label for="parent_email">Parent Email Address
        <input type="email" name="parent_email" id="parent_email" required>
      </label>
      <label for="address">Address
        <input type="text" name="address" id="address" required>
      </label>
      <label for="grade"> Current Grade
        <input type="number" name="grade" id="grade" required>
      </label>
      <label for="school">Current School
        <input type="text" name="school" id="school" required>
      </label>
      <label for="school_outreach_number">Number that schools can contact you on
        <input type="number" name="school_outreach_number" id="school_outreach_number" value="1">
      </label>
      <label for="school_outreach_email">Email that schools can contact you on
        <input type="email" name="school_outreach_email" id="school_outreach_email" value="1">
      </label>
      <label for="scholarship">Are you interested in a scholarship?
        <input type="checkbox" name="scholarship" id="scholarship" value="1">
      </label>
      <input type="button" class="next-step" value="Next" id="next-step-2">
      <div class="loader-container">
        <div class="custom-loader"></div>
      </div>
    </div>

    <!-- Step 3 -->
    <div class="form-step" id="step-3" style="display:none;">
      <label>
        <input type="radio" name="activity-name" id="activity-sport" value="sport">
        Sport
      </label>

      <label>
        <input type="radio" name="activity-name" id="activity-cultural" value="cultural">
        Cultural
      </label>
      <label for="sport-type" id="sport-type-label">Type of sport
        <select id="sport-type" name="sport-type" required>
          <option value="" disabled selected>Select a sport</option>
          <option value="rugby">Rugby</option>
          <option value="water-polo">Water polo</option>
          <option value="cricket">Cricket</option>
          <option value="hockey">Hockey</option>
          <option value="swimming">Swimming</option>
          <option value="soccer">Soccer</option>
          <option value="athletics">Athletics</option>
          <option value="basketball">Basketball</option>
          <option value="golf">Golf</option>
          <option value="other">Other</option>
        </select>
      </label>
      <label for="'other-activity" id="other-activity-label">Describe your activity
        <input type="text" name="other-activity" id="other-activity">
      </label>
      <label for="achievements">Achievements in this activity
        <input type="text" name="achievements" id="achievements">
      </label>
      <label for="sets-apart">What sets you apart from the rest?
        <input type="text" name="sets-apart" id="sets-apart">
      </label>
      <label for="passion">Why are you passionate about this activity
        <input type="text" name="passion" id="passion">
      </label>
      <label for="other-info">Other important information worth noting
        <input type="text" name="other-info" id="other-info">
      </label>




      <div id="upload-area">
        <p>Add images or video of this activity</p>
        <p>Drag & Drop files here or <span id="file-browse">browse</span></p>
        <input type="file" id="file-input" multiple style="display: none;" />
      </div>
      <div id="preview-area"></div>
      <input type="button" class="next-step" value="Next" id="next-step-3">
      <div class="loader-container">
        <div class="custom-loader"></div>
      </div>
    </div>


    <!-- Step 4 -->
    <div class="form-step" id="step-4" style="display:none;">

      <?php display_schools_selection(); ?>


    </div>

  </form>
  <?php
  return ob_get_clean();

}

add_shortcode('student_signup_form', 'school_scout_student_signup_form');
function display_schools_selection()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'ss_schools'; // Your actual schools table name

  // Fetch all approved schools from the database, grouped by province
  $approved_schools = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'approved' ORDER BY province, name ASC");

  // Group schools by province
  $schools_by_province = [];
  foreach ($approved_schools as $school) {
    $schools_by_province[$school->province][] = $school;
  }

  // Output the selection form with grouped schools
  echo '<div>Select schools</div>';

  foreach ($schools_by_province as $province => $schools) {
    $human_readable_province = ucwords(str_replace('_', ' ', $province));

    echo "<h4>" . esc_html($human_readable_province) . "</h4>";

    foreach ($schools as $school) {
      ?>
      <label><?php echo esc_html($school->name . ', ' . $school->street); ?>
        <div class="checkbox-wrapper-31">
          <input type="checkbox" name="schools[]" value="<?php echo esc_attr($school->user_id); ?>">
          <!-- Use the school ID here -->
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

  echo '<input type="submit" value="Submit" id="next-step-4">';
  echo '<div class="loader-container"><div class="custom-loader"></div></div>';
}
