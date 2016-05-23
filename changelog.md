# Changelog for Facebook PHP SDK for CodeIgniter

#### 3.0.0 (May 2016)
- NEW: New config setting `facebook_auth_on_load` to control on page load authorization (try to get valid access token). On as default for backward compatibility.
- NEW: New way of checking if access token in session is still valid. Should be more reliable now.
- NEW: New `user_upload_request()` method. Supports upload of images and video to a users profile.
- NEW: New `add_to_batch_pool()` method. Add multiple Graph request to the pool and send all in one go later.
- NEW: New `remove_from_batch_pool()` method. Remove a graph request from the batch pool.
- NEW: New `send_batch_pool()` method. Send all Graph request in the pool.
- UPDATE: Default graph version in config now v2.6
- UPDATE: Cleanup of the code
- CHANGE: Page load authorization (try to get valid access token) made optional but on as default. Control with new config settings.

#### 3.0.0-b02 (March 2016)
- FIX: Solved authentication problems by using `authenticate` method in `is_authenticated` method instead of checking only local session.
- UPDATE: Tested with Facebook PHP SDK v5.1.2
- UPDATE: Updated example code to use latest Graph API v2.5
- UPDATE: Updated example web login code to include users email.

#### 3.0.0-b01 (October 2015)
- NEW: Support for Facebook PHP SDK v5.
- NEW: Now using one singly dynamic method to do any type of Facebook Graph request.
- CHANGE: Dropped support for Facebook PHP SDK v4.

#### 2.0.0 (April 2015)
- NEW: Added support for Redirect login.
- NEW: Added support for Canvas login.
- CHANGE: Changed format of most methods return data. Now include status codes.
- UPDATE: Updated example code.

#### 1.1.0 (April 2015)
- First public release of library
