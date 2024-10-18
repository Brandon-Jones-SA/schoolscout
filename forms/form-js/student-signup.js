jQuery(document).ready(function ($) {
  $(".loader-container").hide();
  $(".form-step").hide();

  // Drag and drop event listeners
  var uploadArea = $("#upload-area");
  var fileInput = $("#file-input");
  var previewArea = $("#preview-area");
  var uploadedFiles = []; // Keep track of uploaded files

  // Dragover and dragleave effects
  uploadArea.on("dragover", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).css("background-color", "#f0f0f0");
  });

  uploadArea.on("dragleave", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).css("background-color", "#fff");
  });

  // Handle drop event
  uploadArea.on("drop", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).css("background-color", "#fff");

    var files = e.originalEvent.dataTransfer.files;
    handleFiles(files);
  });

  // File browse trigger
  $("#file-browse").click(function () {
    fileInput.click();
  });

  // Handle file selection from the file dialog
  fileInput.on("change", function () {
    var files = this.files;
    handleFiles(files);
  });

  // Function to handle file preview and deletion
  function handleFiles(files) {
    for (var i = 0; i < files.length; i++) {
      var file = files[i];

      // Only allow image uploads
      if (file.type.startsWith("image/")) {
        uploadedFiles.push(file);
        displayPreview(file);
      } else {
        alert("Only images are allowed.");
      }
    }
  }

  // Display file preview
  function displayPreview(file) {
    var reader = new FileReader();
    reader.onload = function (e) {
      var imagePreview = `
         <div class="preview-item">
           <img src="${e.target.result}" alt="${file.name}" class="preview-image"/>
           <span class="remove-preview" data-file="${file.name}">&times;</span>
         </div>`;
      previewArea.append(imagePreview);
    };
    reader.readAsDataURL(file);
  }

  // Remove file from preview and file list
  previewArea.on("click", ".remove-preview", function () {
    var fileName = $(this).data("file");
    $(this).closest(".preview-item").remove();

    // Remove the file from the uploadedFiles array
    uploadedFiles = uploadedFiles.filter(function (file) {
      return file.name !== fileName;
    });
  });

  $("#sport-type-label").hide();
  $("#other-activity-label").hide();

  $("input[name='activity-name']").on("change", function () {
    var selectedActivity = $(this).val(); // Get the value of the selected radio button

    if (selectedActivity === "sport") {
      $("#sport-type-label").show();
      $("#other-activity-label").hide();
    } else if (selectedActivity === "cultural") {
      $("#sport-type-label").hide();
      $("#other-activity-label").show();
      // Perform actions for cultural
      // e.g., show cultural-related content, hide sport-related content, etc.
    }
  });

  $("#sport-type").on("change", function () {
    if ($(this).val() === "other") {
      $("#other-activity-label").show();
    } else {
      $("#other-activity-label").hide();
    }
  });

  let currentStep = localStorage.getItem("currentStep") || "step-1";

  $("#" + currentStep).show();

  if (currentStep === "step-2") {
    // If the current step is 2, show the first line and circle for step 2
    $(".line-1").addClass("active");
    $(".circle.step-2").addClass("active");
  } else if (currentStep === "step-3") {
    // If the current step is 3, show the first two lines and circles for step 3
    $(".line-1, .line-2").addClass("active");
    $(".circle.step-2, .circle.step-3").addClass("active");
  } else if (currentStep === "step-4") {
    // If the current step is 4, show all progress up to step 4
    $(".line-1, .line-2, .line-3").addClass("active");
    $(".circle.step-2, .circle.step-3, .circle.step-4").addClass("active");
  } else {
    // Default to step 1 (if no step is stored or if it's the initial load)
    $(".circle.step-1").addClass("active");
  }

  // Handle Next button click to move between steps
  $("#next-step-1").on("click", function (e) {
    e.preventDefault();

    $(".loader-container").show();

    var formData = {
      action: "student_signup_step_1",
      username: $("#username").val(),
      email_address: $("#email_address").val(),
      password: $("#password").val(),
      confirm_password: $("#confirm_password").val(),
      security: student_signup_object.nonce,
    };

    console.log(formData);

    $.post(student_signup_object.ajax_url, formData, function (response) {
      $(".loader-container").hide();

      if (response.success) {
        $("step-1 input").each(function () {
          $(this).prop("required", false);
        });
        localStorage.setItem("currentStep", "step-2");
        $("#step-1").hide();
        $("#step-2").show();
        $(".line-1").addClass("active");
        $(".circle.step-2").addClass("active");
        $(".error-message").hide();
      } else {
        $(".error-message").text(response.data.message).show();
        console.log(response.data.message);
      }
    });
  });

  $("#next-step-2").on("click", function (e) {
    e.preventDefault();

    $(".loader-container").show();

    let formData = {
      action: "student_signup_step_2",
      firstName: $("#first_name").val(),
      lastName: $("#last_name").val(),
      age: $("#age").val(),
      gender: $("#gender").val(),
      race: $("#race").val(),
      idNumber: $("#id_number").val(),
      emailAddress: $("#email_address").val(),
      parentName: $("#parent_name").val(),
      parentSurname: $("#parent_surname").val(),
      parentContactNumber: $("#parent_contact_number").val(),
      parentEmail: $("#parent_email").val(),
      address: $("#address").val(),
      grade: $("#grade").val(),
      school: $("#school").val(),
      schoolOutreachNumber: $("#school_outreach_number").val(),
      schoolOutreachEmail: $("#school_outreach_email").val(),
      scholarship: $("#scholarship").val(),
      security: student_signup_object.nonce,
    };

    console.log(formData);
    $.post(student_signup_object.ajax_url, formData, function (response) {
      $(".loader-container").hide();

      if (response.success) {
        localStorage.setItem("currentStep", "step-3");
        $("#step-2").hide();
        $("#step-3").show();
        $(".line-3").addClass("active");
        $(".step-3").addClass("active");
        $(".error-message").hide();
      } else {
        $(".error-message").text(response.data.message).show();
      }
    });
  });

  $("#next-step-3").on("click", function (e) {
    e.preventDefault();

    $(".loader-container").show();

    let activity, type;

    // Check the selected activity
    if ($("#activity-sport").is(":checked")) {
      type = "sport";
      if ($("#sport-type").val() === "other") {
        activity = $("#other-activity").val();
      } else {
        activity = $("#sport-type").val();
      }
    } else if ($("#activity-cultural").is(":checked")) {
      type = "cultural";
      activity = $("#other-activity").val();
    }

    // Declare FormData as a variable
    let formData = new FormData(); // Changed this to FormData object for file handling

    // Append the form fields to FormData
    formData.append("action", "student_signup_step_3");
    formData.append("type", type);
    formData.append("name", activity);
    formData.append("achievements", $("#achievements").val());
    formData.append("passion", $("#passion").val());
    formData.append("sets_apart", $("#sets-apart").val());
    formData.append("other_info", $("#other-info").val());
    formData.append("security", student_signup_object.nonce);

    // Handle media files
    let files = $("#file-input")[0].files; // Assuming you have an input with ID media-files
    console.log("Selected files:");
    $.each(uploadedFiles, function (index, file) {
      console.log(`File ${index + 1}:`);
      console.log("Name:", file.name);
      console.log("Type:", file.type);
      console.log("Size:", file.size, "bytes");
    });
    // Append each selected file to FormData
    if (uploadedFiles.length > 0) {
      $.each(uploadedFiles, function (i, file) {
        console.log(i);
        formData.append("media_files[]", file); // Append each file to the FormData object
      });
    }

    console.log("FormData content (without files)", formData);

    $.ajax({
      url: student_signup_object.ajax_url,
      type: "POST",
      data: formData,
      processData: false, // Prevent jQuery from processing the FormData object into a query string
      contentType: false, // Let the browser set the content type, especially important for file uploads
      success: function (response) {
        $(".loader-container").hide();
        if (response.success) {
          $(".line-1, .line-2, .line-3").addClass("active");
          $(".circle.step-2, .circle.step-3, .circle.step-4").addClass(
            "active"
          );
          $("#step-3").hide();
          $("#step-4").show();
          $(".error-message").hide();
          localStorage.setItem("currentStep", "step-4");
        } else {
          console.log("Submission error:", response.data.message);
          // Handle the error case
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("AJAX error:", textStatus, errorThrown);
        $(".loader-container").hide();
      },
    });
  });

  $("#next-step-4").on("click", function (e) {
    e.preventDefault();
    console.log("Clicked");
    let selectedSchools = [];
    $("input[name='schools[]']:checked").each(function () {
      selectedSchools.push($(this).val());
    });
    let formData = {
      action: "student_signup_step_4", // Action hook for handling Step 4
      selected_schools: selectedSchools, // The array of selected school IDs
      security: student_signup_object.nonce, // Nonce for security validation (if using it)
    };

    console.log(formData);

    // Send the AJAX request
    $.post(student_signup_object.ajax_url, formData, function (response) {
      $(".loader-container").hide(); // Hide loader

      if (response.success) {
        console.log("Step 4 successfully submitted:", response.data);
        // Redirect to the account page
        if (response.data.redirect_url) {
          window.location.href = response.data.redirect_url; // Redirect to the URL sent by the server
          // Proceed to next step or handle success (e.g., show confirmation)
        }
      } else {
        console.log("Step 4 submission error:", response);
        // Handle error case
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log("AJAX error:", textStatus, errorThrown);
      $(".loader-container").hide(); // Hide loader on error
    });
  });
});
