# School Scout Plugin

## Description

School Scout is a custom WordPress plugin developed by SDDS Web. It provides functionality for school and student signups, as well as a user account management system.

## Features

1. School Signup Form
2. Student Signup Form
3. My Account Page

## Shortcodes

The plugin uses the following shortcodes:

1. `[school_scout_school_signup_form]`: Displays the school signup form.
2. `[school_scout_student_signup_form]`: Displays the student signup form.
3. `[school_scout_my_account_page]`: Displays the user's account page.

## File Structure

- `school-scout.php`: Main plugin file
- `forms/`
  - `school-signup-form.php`
  - `student-signup-form.php`
  - `form-js/`
    - `school-signup.js`
    - `student-signup.js`
  - `form-css/`
    - `student-signup.css`
- `pages/`
  - `my-account-page.php`
  - `page-assets/`
    - `my-account.css`
    - `my-account.js`

## Usage

1. Install and activate the plugin in your WordPress installation.
2. Use the provided shortcodes to display the forms and account page on your desired pages or posts.

## Dependencies

- jQuery (included with WordPress)

## Version

1.0.0

## Author

SDDS Web

## Notes

- The plugin uses ABSPATH to ensure direct script access is not allowed.
- Custom CSS and JavaScript files are enqueued for styling and functionality.
- The forms use a multi-step process with progress indicators.
- The My Account page includes tabs for different sections of user information.

For more detailed information on each component, please refer to the individual files in the plugin directory.
