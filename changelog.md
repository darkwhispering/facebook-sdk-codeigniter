# Changelog for Facebook PHP SDK for CodeIgniter

#### 3.0.0-b02 (March 2016)
- FIX: Solved authentication problems by using `authenticate` method in `is_authenticated` method instead of checking only local session.
- UPDATE: Teested with Facebook PHP SDK v5.1.2
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
