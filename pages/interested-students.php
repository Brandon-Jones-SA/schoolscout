<?php


function display_students_interested_in_school($atts)
{
  // Start output buffering
  wp_enqueue_script('jquery');
  wp_enqueue_style('school-scout-interested-students', plugin_dir_url(__FILE__) . '../pages/page-assets/interested-students.css', array(), '1.0.0', 'all');
  wp_enqueue_script('school-scout-interested-students', plugin_dir_url(__FILE__) . '../pages/page-assets/interested-student.js');

  wp_localize_script('school-scout-interested-students', 'interested_students_object', array(
    'ajax_url' => admin_url('admin-ajax.php'),
  ));

  // Start output buffering
  ob_start();

  // Get the current school's ID (replace with your logic for retrieving the school ID)
  $current_school_id = get_current_user_id(); // Assuming the logged-in user is the school

  if (!$current_school_id) {
    echo '<p>No school ID found.</p>';
    return ob_get_clean(); // Return the buffered content
  }

  // Fetch all students interested in this school
  global $wpdb;
  $students_table = $wpdb->prefix . 'ss_students'; // Student table
  $student_school_table = $wpdb->prefix . 'ss_student_school'; // Student-school table
  $activities_table = $wpdb->prefix . 'ss_activities'; // Activities table

  // Query to get student IDs interested in this school
  $student_ids = $wpdb->get_col($wpdb->prepare(
    "SELECT student_id FROM $student_school_table WHERE FIND_IN_SET(%d, school_ids)",
    $current_school_id
  ));

  if (empty($student_ids)) {
    echo '<p>No students found for this school.</p>';
    return ob_get_clean(); // Return the buffered content
  }

  // Fetch student details for all students interested in the school
  $student_data = $wpdb->get_results("SELECT * FROM $students_table WHERE user_id IN (" . implode(',', $student_ids) . ")");

  // Fetch activity data for those students
  $activity_data = $wpdb->get_results("SELECT student_id, name, type FROM $activities_table WHERE student_id IN (" . implode(',', $student_ids) . ")");

  // Create an array to map student_id to activity name and type
  $activities_by_student = [];
  foreach ($activity_data as $activity) {
    $activities_by_student[$activity->student_id][] = [
      'name' => ucfirst($activity->name),
      'type' => $activity->type
    ];
  }

  // Generate unique filter options for activity types
  $available_activities = [];
  foreach ($activity_data as $activity) {
    $available_activities[$activity->type][] = ucfirst($activity->name);
  }

  // Remove duplicates and sort
  foreach ($available_activities as &$activity_names) {
    $activity_names = array_unique($activity_names);
    sort($activity_names);
  }

  // Start building the HTML output
  ?>
  <div class="filters">
    <select id="filter-gender">
      <option value="">Filter by Gender</option>
      <option value="male">Male</option>
      <option value="female">Female</option>
      <option value="other">Other</option>
    </select>

    <select id="filter-race">
      <option value="">Filter by Race</option>
      <option value="african">African</option>
      <option value="caucasian">Caucasian</option>
      <option value="asian">Asian</option>
      <option value="other">Other</option>
    </select>

    <select id="filter-age">
      <option value="">Filter by Age</option>
      <?php foreach (range(16, 21) as $age): ?>
        <option value="<?php echo $age; ?>"><?php echo $age; ?></option>
      <?php endforeach; ?>
    </select>

    <select id="filter-grade">
      <option value="">Filter by Grade</option>
      <?php foreach (range(8, 12) as $grade): ?>
        <option value="<?php echo $grade; ?>">Grade <?php echo $grade; ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Activity Type Dropdown -->
    <select id="filter-activity">
      <option value="">Filter by Activity Type</option>
      <optgroup label="Sport">
        <?php if (!empty($available_activities['sport'])): ?>
          <?php foreach ($available_activities['sport'] as $activity): ?>
            <option value="sport--<?php echo strtolower($activity); ?>">--<?php echo $activity; ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </optgroup>
      <optgroup label="Cultural">
        <?php if (!empty($available_activities['cultural'])): ?>
          <?php foreach ($available_activities['cultural'] as $activity): ?>
            <option value="cultural--<?php echo strtolower($activity); ?>">--<?php echo $activity; ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </optgroup>
    </select>

    <!-- Scholarship Interest Dropdown -->
    <select id="filter-scholarship">
      <option value="">Filter by Scholarship Interest</option>
      <option value="yes">Interested in Scholarship</option>
      <option value="no">Not Interested</option>
    </select>
  </div>

  <table id="students-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Surname</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Race</th>
        <th>Grade</th>
        <th>Scholarship</th>
        <th>Activity Name</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Loop through each student and display their details
      foreach ($student_data as $student) {
        // Get the scholarship status as 'Yes' or 'No'
        $scholarship = $student->scholarship ? 'Yes' : 'No';

        // Get the activities for the current student (if any)
        $activities = isset($activities_by_student[$student->user_id]) ? implode(', ', array_column($activities_by_student[$student->user_id], 'name')) : 'No activities';

        // Build the student row
        echo '<tr>';
        echo '<tr data-student-id="' . esc_attr($student->user_id) . '">';
        echo '<td>' . esc_html($student->name) . '</td>';
        echo '<td>' . esc_html($student->surname) . '</td>';
        echo '<td>' . esc_html($student->age) . '</td>';
        echo '<td>' . esc_html($student->gender) . '</td>';
        echo '<td>' . esc_html($student->race) . '</td>';
        echo '<td>' . esc_html($student->grade) . '</td>';
        echo '<td>' . esc_html($scholarship) . '</td>';
        echo '<td>' . esc_html($activities) . '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
  <div id="studentPopup" class="popup-overlay" style="display:none;">
    <div class="popup-content">
      <span class="close-popup">&times;</span>
      <div class="popup-details">
        <!-- Content will be dynamically filled -->
        <h2 id="popup-name"></h2>
        <p><strong>Surname:</strong> <span id="popup-surname"></span></p>
        <p><strong>Age:</strong> <span id="popup-age"></span></p>
        <p><strong>Gender:</strong> <span id="popup-gender"></span></p>
        <p><strong>Race:</strong> <span id="popup-race"></span></p>
        <p><strong>Email:</strong> <span id="popup-email"></span></p>
        <p><strong>Grade:</strong> <span id="popup-grade"></span></p>
        <p><strong>School:</strong> <span id="popup-school"></span></p>
        <p><strong>School Outreach Number:</strong> <a id="school-outreach-number-link"><span
              id="popup-outreach-number"></span></a></p>
        <strong>School Outreach Email:</strong> <a id="school-outreach-email-link"><span
            id="popup-outreach-email"></span></a></p>
        <p><strong>Scholarship:</strong> <span id="popup-scholarship"></span></p>
        <h3>Activities</h3>
        <div id="popup-activities">
          <!-- Each activity will be appended here -->
          <div id="activity-template" style="display:none;">
            <h4><span class="activity-name"></span></h4>
            <p><strong>Achievements:</strong> <span class="activity-achievements"></span></p>
            <p><strong>Passion:</strong> <span class="activity-passion"></span></p>
            <p><strong>Sets Apart:</strong> <span class="activity-sets-apart"></span></p>
            <p><strong>Other Info:</strong> <span class="activity-other-info"></span></p>
            <div class="activity-media">
              <strong>Media:</strong>
              <div class="media-gallery"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <?php

  // Return the buffered output
  return ob_get_clean();
}
add_shortcode('students_interested_in_school', 'display_students_interested_in_school');

