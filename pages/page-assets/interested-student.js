jQuery(document).ready(function ($) {
  // Function to fetch and display the student details in the popup
  function showStudentPopup(studentId) {
    $.ajax({
      url: interested_students_object.ajax_url, // Use localized ajax_url
      type: "POST",
      data: {
        action: "get_student_details", // Ensure this matches your AJAX handler in PHP
        student_id: studentId,
      },
      success: function (response) {
        if (response.success) {
          var student = response.data;
          console.log(student.student.name);

          // Populate popup with student details
          $("#popup-name").text(student.student.name);
          $("#popup-surname").text(student.student.surname);
          $("#popup-age").text(student.student.age);
          $("#popup-gender").text(student.student.gender);
          $("#popup-race").text(student.student.race);
          $("#popup-grade").text(student.student.grade);
          $("#popup-scholarship").text(
            student.student.scholarship ? "Yes" : "No"
          );
          $("#popup-email").text(student.student.email);
          $("#popup-school").text(student.student.school);
          $("#popup-outreach-number").text(
            student.student.school_outreach_number
          );
          $("#popup-outreach-email").text(
            student.student.school_outreach_email
          );
          $("#school-outreach-number-link").attr(
            "href",
            `tel:${student.school_outreach_number}`
          );

          $("#school-outreach-email-link").attr(
            "href",
            `mailto:${student.school_outreach_email}`
          );

          // Populate activities
          $("#popup-activities").html(""); // Clear previous activities
          student.activities.forEach(function (activity) {
            var activityHtml = `
              <div class="activity">
             <h4>${
               activity.name.charAt(0).toUpperCase() + activity.name.slice(1)
             }</h4>

                <p><strong>Achievements:</strong> ${
                  activity.achievements || "N/A"
                }</p>
                <p><strong>Passion:</strong> ${activity.passion || "N/A"}</p>
                <p><strong>Sets Apart:</strong> ${
                  activity.sets_apart || "N/A"
                }</p>
                <p><strong>Other Info:</strong> ${
                  activity.other_info || "N/A"
                }</p>
                <div class="activity-media">
                  <strong>Media:</strong> <div class="media-gallery"></div>
                </div>
              </div>`;
            $("#popup-activities").append(activityHtml);

            // If there are media items, render them
            if (activity.media) {
              // Fix the format by parsing the JSON string to an array
              var mediaUrls = JSON.parse(activity.media);
              mediaUrls.forEach(function (url) {
                url = url.trim(); // Clean up any extra spaces
                var mediaHtml = "";

                // Check if it's an image
                if (url.match(/\.(jpeg|jpg|gif|png|webp)$/)) {
                  mediaHtml = `<img src="${url}" alt="Activity Media" style="max-width:100%; height:auto; margin-bottom: 10px;" />`;
                }
                // Check if it's a video
                else if (url.match(/\.(mp4|webm|ogg)$/)) {
                  mediaHtml = `
                    <video controls style="max-width:100%; height:auto; margin-bottom: 10px;">
                      <source src="${url}" type="video/${url.split(".").pop()}">
                      Your browser does not support the video tag.
                    </video>`;
                }

                // Append the media HTML to the correct activity's media-gallery
                $("#popup-activities")
                  .find(".media-gallery")
                  .last()
                  .append(mediaHtml);
              });
            }
          });

          // Show the popup
          $("#studentPopup").fadeIn();
        } else {
          alert("Failed to load student details");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error loading student details:", status, error);
      },
    });
  }

  // Show the student popup when a row is clicked
  $("#students-table tbody").on("click", "tr", function () {
    var studentId = $(this).data("student-id"); // Get student_id from the data attribute
    showStudentPopup(studentId);
  });

  // Close the popup
  $(".close-popup").on("click", function () {
    $("#studentPopup").fadeOut();
  });

  // Hide the popup when clicking outside of it
  $(window).on("click", function (e) {
    if ($(e.target).hasClass("popup-overlay")) {
      $("#studentPopup").fadeOut();
    }
  });
});
