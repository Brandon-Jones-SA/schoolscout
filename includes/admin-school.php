<?php

// Hook into the admin menu action
add_action('admin_menu', 'register_schools_admin_page');

function register_schools_admin_page()
{
  add_menu_page(
    'Manage Schools', // Page title
    'Schools', // Menu title
    'manage_options', // Capability
    'manage-schools', // Menu slug
    'schools_admin_page', // Function to display the page content
    'dashicons-admin-home', // Icon
    6 // Position
  );
}

function schools_admin_page()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'ss_schools'; // Replace 'schools_table' with your actual table name

  // Fetch all schools grouped by status
  $pending_schools = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'pending' ORDER BY id ASC");
  $approved_schools = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'approved' ORDER BY id ASC");
  $declined_schools = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'not approved' ORDER BY id ASC");

  ?>
  <div class="wrap">
    <h1>Manage Schools</h1>

    <h2>Pending Schools</h2>
    <table class="widefat fixed" cellspacing="0">
      <thead>
        <tr>
          <th>Name</th>
          <th>Street</th>
          <th>Province</th>
          <th>Contact Person</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($pending_schools): ?>
          <?php foreach ($pending_schools as $school): ?>
            <tr>
              <td><?php echo esc_html($school->name); ?></td>
              <td><?php echo esc_html($school->street); ?></td>
              <td><?php echo esc_html($school->province); ?></td>
              <td><?php echo esc_html($school->person); ?></td>
              <td>
                <button class="button approve-school" data-id="<?php echo esc_attr($school->id); ?>">Approve</button>
                <button class="button decline-school" data-id="<?php echo esc_attr($school->id); ?>">Decline</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">No pending schools found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <h2>Approved Schools</h2>
    <table class="widefat fixed" cellspacing="0">
      <thead>
        <tr>
          <th>Name</th>
          <th>Street</th>
          <th>Province</th>
          <th>Contact Person</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($approved_schools): ?>
          <?php foreach ($approved_schools as $school): ?>
            <tr>
              <td><?php echo esc_html($school->name); ?></td>
              <td><?php echo esc_html($school->street); ?></td>
              <td><?php echo esc_html($school->province); ?></td>
              <td><?php echo esc_html($school->person); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No approved schools found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <h2>Declined Schools</h2>
    <table class="widefat fixed" cellspacing="0">
      <thead>
        <tr>
          <th>Name</th>
          <th>Street</th>
          <th>Province</th>
          <th>Contact Person</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($declined_schools): ?>
          <?php foreach ($declined_schools as $school): ?>
            <tr>
              <td><?php echo esc_html($school->name); ?></td>
              <td><?php echo esc_html($school->street); ?></td>
              <td><?php echo esc_html($school->province); ?></td>
              <td><?php echo esc_html($school->person); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No declined schools found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script type="text/javascript">
    jQuery(document).ready(function ($) {
      // Handle approve button click
      $('.approve-school').on('click', function () {
        var schoolId = $(this).data('id');
        updateSchoolStatus(schoolId, 'approved');
      });

      // Handle decline button click
      $('.decline-school').on('click', function () {
        var schoolId = $(this).data('id');
        updateSchoolStatus(schoolId, 'declined');
      });

      // Function to update school status
      function updateSchoolStatus(schoolId, status) {
        $.ajax({
          url: ajaxurl, // WordPress AJAX URL
          method: 'POST',
          data: {
            action: 'update_school_status',
            school_id: schoolId,
            status: status,
            security: '<?php echo wp_create_nonce("update_school_status_nonce"); ?>' // Nonce for security
          },
          success: function (response) {
            if (response.success) {
              location.reload(); // Reload the page on success
            } else {
              alert('Failed to update school status.');
            }
          }
        });
      }
    });
  </script>
  <?php
}

add_action('wp_ajax_update_school_status', 'update_school_status');

function update_school_status()
{
  global $wpdb;
  check_ajax_referer('update_school_status_nonce', 'security'); // Verify nonce for security

  $school_id = intval($_POST['school_id']);
  $new_status = sanitize_text_field($_POST['status']);

  if (!$school_id || !$new_status) {
    wp_send_json_error(['message' => 'Invalid request.']);
  }

  // Update the school's status in the database
  $table_name = $wpdb->prefix . 'ss_schools'; // Your actual table name
  $updated = $wpdb->update(
    $table_name,
    ['status' => $new_status], // Data to update
    ['id' => $school_id],      // Where clause
    ['%s'],                    // Data format
    ['%d']                     // Where format
  );

  if ($updated !== false) {
    wp_send_json_success(['message' => 'School status updated successfully.']);
  } else {
    wp_send_json_error(['message' => 'Failed to update school status.']);
  }
}

