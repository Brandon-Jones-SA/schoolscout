// Tab switching
jQuery(document).ready(function ($) {
  // TAB SWITCHING LOGIC
  $(".tab-item").click(function () {
    // Remove 'active' class from all tabs and contents
    $(".tab-item").removeClass("active");
    $(".tab-content").removeClass("active");

    // Add 'active' class to the clicked tab
    $(this).addClass("active");

    // Get the tab ID from the clicked tab's data attribute
    let tabId = $(this).data("tab");

    // Show the corresponding tab content based on the tab ID
    $(".tab-content[data-content='" + tabId + "']").addClass("active");
  });
});

// Media Handling

jQuery(document).ready(function ($) {
  const mediaArrays = {}; // Store file URLs for each activity

  // Initialize media handling for each activity
  $(".activity-section").each(function () {
    const activityIndex = $(this)
      .find("input[type=hidden][name^='activities']")
      .val(); // Get the activity index

    // Initialize media array for this activity
    mediaArrays[activityIndex] = []; // Ensure the array is initialized

    // Fetch existing media from the server
    fetchMedia(activityIndex);
    $(`#file-browse-${activityIndex}`).on("click", function () {
      $(`#file-input-${activityIndex}`).click(); // Trigger the hidden file input click
    });

    $(`#file-input-${activityIndex}`).on("change", function (e) {
      handleFileUpload(e.target.files, activityIndex);
    });

    // Handle drag and drop for files
    $(`#upload-area-${activityIndex}`).on("drop", function (e) {
      e.preventDefault(); // Prevent default behavior
      const files = e.originalEvent.dataTransfer.files;
      handleFileUpload(files, activityIndex); // Call upload function
    });
  });

  function handleFileUpload(files, activityIndex) {
    const formData = new FormData();
    $.each(files, function (i, file) {
      formData.append("files[]", file); // Append each file to the FormData object
    });

    // Include the activity ID in the formData
    formData.append("activity_id", activityIndex); // Send the activity ID

    // Send the file data via AJAX to your server-side PHP handler
    $.ajax({
      url: "/wp-admin/admin-ajax.php?action=file_upload", // Correct endpoint for file uploads
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response && response.data.urls) {
          // Update mediaArrays with new URLs
          response.data.urls.forEach(function (url) {
            mediaArrays[activityIndex].push(url); // Add file URL to the media array
          });
          updatePreview(activityIndex); // Update the preview area with uploaded files
          console.log(mediaArrays[activityIndex]);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("File upload error: " + textStatus, errorThrown);
      },
    });
  }

  // Function to fetch and display existing media from the server
  function fetchMedia(activityIndex) {
    $.ajax({
      url: "/wp-admin/admin-ajax.php?action=get_media", // Endpoint to fetch existing media
      type: "GET",
      data: { activity_id: activityIndex }, // Send activity index to fetch media
      success: function (response) {
        if (response && response.data.urls) {
          mediaArrays[activityIndex] = JSON.parse(response.data.urls); // Load existing media into the array
          updatePreview(activityIndex); // Display the media in the preview area
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error fetching media: " + textStatus, errorThrown);
      },
    });
  }

  // Function to update the preview area with current media
  function updatePreview(activityIndex) {
    const previewArea = $(`#preview-area-${activityIndex}`);
    previewArea.html(""); // Clear the preview area

    mediaArrays[activityIndex].forEach(function (url) {
      const content = document.createElement("div");
      const img = document.createElement("img");
      const removeBtn = document.createElement("span");

      img.src = url;
      img.classList.add("preview-image");

      removeBtn.innerText = "X";
      removeBtn.classList.add("remove-btn");

      removeBtn.addEventListener("click", function () {
        // Call the removeMedia function when clicked
        removeMedia(activityIndex, url);
        content.remove(); // Remove the preview when deleted
      });

      removeBtn.addEventListener("click", function () {
        // You will need to implement the removeMedia function later
        removeMedia(activityIndex, url);
        content.remove(); // Remove the preview when deleted
      });

      content.classList.add("preview-item");
      content.appendChild(img);
      content.appendChild(removeBtn);
      previewArea.append(content);
    });
  }

  function removeMedia(activityIndex, mediaItem) {
    $.ajax({
      url: "/wp-admin/admin-ajax.php?action=remove_media", // Correct endpoint to handle media removal
      type: "POST",
      data: { media_url: mediaItem, activity_id: activityIndex },
      success: function (response) {
        if (response.success) {
          // Remove the media URL from the media array
          const index = mediaArrays[activityIndex].indexOf(mediaItem);
          if (index !== -1) {
            mediaArrays[activityIndex].splice(index, 1); // Remove the media from the array
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error removing media: " + textStatus, errorThrown);
      },
    });
  }
});

jQuery(document).ready(function ($) {
  // Function to handle radio button change (Cultural or Sport)
  $("input[type=radio][name^='activities']").on("change", function () {
    let activityIndex = $(this).attr("id").split("-").pop(); // Get the activity index
    let selectedType = $(
      "input[name='activities[" + activityIndex + "][activity-type]']:checked"
    ).val();

    if (selectedType === "sport") {
      // Show the sport dropdown and hide the "other activity" field
      $("#sport-type-label-" + activityIndex).show();
      $("#other-activity-label-" + activityIndex).hide();
    } else if (selectedType === "cultural") {
      // Show the "other activity" field and hide the sport dropdown
      $("#sport-type-label-" + activityIndex).hide();
      $("#other-activity-label-" + activityIndex).show();
    }
  });

  // Function to handle sport type dropdown change
  $("select[name^='activities']").on("change", function () {
    let activityIndex = $(this).attr("id").split("-").pop(); // Get the activity index
    let selectedSport = $("#sport-type-" + activityIndex).val();

    if (selectedSport === "other") {
      // Show the "other activity" field if "Other" is selected
      $("#other-activity-label-" + activityIndex).show();
    } else {
      // Hide the "other activity" field if a specific sport is selected
      $("#other-activity-label-" + activityIndex).hide();
    }
  });

  // Initialize display based on current selection on page load
  $("input[type=radio][name^='activities']:checked").each(function () {
    let activityIndex = $(this).attr("id").split("-").pop(); // Get the activity index
    let selectedType = $(
      "input[name='activities[" + activityIndex + "][activity-type]']:checked"
    ).val();

    if (selectedType === "sport") {
      // Show the sport dropdown and hide the "other activity" field
      $("#sport-type-label-" + activityIndex).show();
      $("#other-activity-label-" + activityIndex).hide();
    } else if (selectedType === "cultural") {
      // Show the "other activity" field and hide the sport dropdown
      $("#sport-type-label-" + activityIndex).hide();
      $("#other-activity-label-" + activityIndex).show();
    }

    // Initialize sport dropdown check for "Other" selection
    let selectedSport = $("#sport-type-" + activityIndex).val();
    if (selectedSport === "other") {
      $("#other-activity-label-" + activityIndex).show();
    } else {
      $("#other-activity-label-" + activityIndex).hide();
    }
  });
});
