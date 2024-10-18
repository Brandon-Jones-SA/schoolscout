jQuery(document).ready(function ($) {
  $(".loader-container").hide();
  $("#step-1").show();
  $("#step-2").hide();

  $("#next-step-1").on("click", function (e) {
    e.preventDefault();

    $(".loader-container").show();

    var formData = {
      action: "school_signup_step_1",
      username: $("#username").val(),
      email_address: $("#email_address").val(),
      password: $("#password").val(),
      confirm_password: $("#confirm_password").val(),
      security: school_signup_object.nonce,
    };

    $.post(school_signup_object.ajax_url, formData, function (response) {
      $(".loader-container").hide();

      if (response.success) {
        $("#step-1 input").each(function () {
          $(this).prop("required", false);
        });
        $("#step-1").hide();
        $("#step-2").show();
        $(".line").addClass("active");
        $(".circle.step-2").addClass("active");
        $(".error-message").hide();
      } else {
        $(".error-message").text(response.data.message).show();
        console.log(response.data.message);
      }
    });
  });

  $("#submit_step_2").on("click", function (e) {
    e.preventDefault();
    $(".loader-container").show();

    var step2Data = {
      action: "school_signup_step_2",
      school_name: $("#school_name").val(),
      street: $("#street").val(),
      province: $("#province").val(),
      contact_number: $("#contact_number").val(),
      contact_email: $("#contact_email").val(),
      person: $("#person").val(),
      security: school_signup_object.nonce,
    };

    console.log(step2Data);

    $.post(school_signup_object.ajax_url, step2Data, function (response) {
      $(".loader_container").hide();

      if (!response.success) {
        $(".error-message").text(response.data.message).show();
      } else {
        window.location.href = response.data.redirect_url;
      }
    });
  });
});
