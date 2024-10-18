<?php

function school_scout_school_signup_form()
{


  wp_enqueue_script('jquery');
  wp_enqueue_style('school-signup-form', plugin_dir_url(__FILE__) . 'form-css/school-signup.css');
  wp_enqueue_script('school-signup-form', plugin_dir_url(__FILE__) . 'form-js/school-signup.js');

  wp_localize_script('school-signup-form', 'school_signup_object', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('school_signup_nonce')
  ));

  ob_start();
  ?>
  <div class="form-progress">
    <div class="progress-bar">
      <div class="circle step-1 active"></div>
      <div class="line"></div>
      <div class="circle step-2"></div>
    </div>
  </div>

  <form action="" method="post" id="school-signup-form">
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
      <div class="error-message"></div>
      <input type="button" class="next-step" id="next-step-1" value="Next">
      <div class="loader-container">
        <div class="custom-loader"></div>
      </div>
    </div>

    <!-- Step 2 -->
    <div class="form-step" id="step-2" style="display:none;">
      <label for="school_name">School Name
        <input type="text" name="school_name" id="school_name" required>
      </label>
      <label for="street">School Street Address
        <input type="text" name="street" id="street" required>
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
      <label for="'person">Contact Person
        <input type="text" name="person" id="person" required>
      </label>
      <label for="contact_number">Contact Number
        <input type="text" name="contact_number" id="contact_number" required>
      </label>
      <label for="contact_email">Contact Email
        <input type="email" name="contact_email" id="contact_email" required>
      </label>
      <input type="submit" name="submit_step_2" id="submit_step_2" value="Submit">
      <div class="loader-container">
        <div class="custom-loader"></div>
      </div>
    </div>
  </form>

  <?php
  return ob_get_clean();
}

add_shortcode('school_signup_form', 'school_scout_school_signup_form');
?>