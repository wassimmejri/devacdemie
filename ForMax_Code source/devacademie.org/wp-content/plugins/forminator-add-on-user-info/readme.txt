=== Forminator Add-on : User Info ===
Contributors: auslee986
Tags: forminator, forminator add-on, user info, submissions
Requires at least: 6.4
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Forminator Add-on : User Info enhancements.

== Description ==

**This plugin adds custom logic to [Forminator](https://wordpress.org/plugins/forminator/) as following:**

- Automatically record logged in user's information on form submission record.

- Restrict non-admin users to see only their own submission.

**Special thanks to [WPMU DEV](https://wpmudev.com/) for developing such a great plugin.**

## Motivation & Features

### Motivation

- Forminator form does not record logged in user's information on their submissions.

- Also there is no restrictions, so every one can see any submissions if permissions are given.

### Feature 1

This plugin utilized [Forminator](https://wordpress.org/plugins/forminator/) 's hook so that it logs user's information (user id, login id, full name, display name and email address) on submitted records.

*Automatically logged information will appear only for admin users.*

### Feature 2

This plugin enhances submission dashboard so that non-admin users can see only their own submissions.

#### Usecase

- In admin dashboard, go to Forminator/Settings/Permissions to add Submissions permission to specific users.

- Login as the specific user, to see it's own submissions.

== Changelog ==


= 1.0.0 ( 2024-10-30 ) =

First public release

== About Me ==
I am a enthusiastic web developer.

== Contact and Credits ==

[Auslee](https://profiles.wordpress.org/auslee986/)
