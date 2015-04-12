# Facebook PHP SDK v4 for CodeIgniter
Library for integration of Facebook PHP SDK v4 with CodeIgniter 3+

**Version:** 2.0.0

#### TO DO
Items to complete before release of version 2.0.0
- Updated resposne data to include success/error code and message
- Add contribute information
- Create different example views for web and JS login
- Update readme with new methods

## Requirements
- PHP 5.4+
- [CodeIgniter 3](http://www.codeigniter.com/)
- CodeIgniter session library
- [Facebook PHP SDK v4](https://packagist.org/packages/facebook/php-sdk-v4)
- [Composer](https://getcomposer.org/)

## Notice
Facebook Canvas support is experimental as I have not been able to test or confirm it working. If you test it, please report back if you had success or failure. Thanks.

## Installation
1. Download the library files and add the files to your CodeIgniter installation. Only the library, config and composer.json files are required.
1. In CodeIgniter `/application/config/config.php` set `$config['composer_autoload']` to `TRUE`.
2. In CodeIgniter `/application/config/config.php`, configure the `Session Variables`.
3. Update the `facebook.php` config file in `/application/config/facebook.php` with you Facebook App details.
4. Install the Facebook PHP SDK by navigating to your applications folder and execute `composer install`.
6. Autoload the library in `autoload.php` or load it in needed controllers with `$this->load->library('facebook');`.
5. Enjoy!

## Usage

#### Check if user is logged
```php
// Check if user is logged in. Returns true/false
$this->facebook->logged_in();
```

#### Check user ID
```php
// Get users ID. Returns int
$this->facebook->user_id();
```

#### Check user details
```php
// Get userse details. Returns array
$this->facebook->user();
```

#### Get post from users wall
*Requires user has approved `read_stream` permission*
```php
/**
    * Get single post
    *
    * Requires: read_stream
    *
    * @param   int    Post ID
    *
    * @return  array
    **/
$this->facebook->get_post($post_id);
```

#### Publish a text to users wall
*Requires user has approved `publish_actions` permission*
```php
/**
    * Publish a post to the users feed
    *
    * Requires: publish_actions
    *
    * @param   string  Message to publish
    *
    * @return  int
    **/
$this->facebook->publish_text($message);
```

#### Publish a video to users wall
```php
/**
    * Publish a video to the users feed
    *
    * Requires: publish_actions
    *
    * @param   string  Path to video file
    * @param   string  Video description text
    * @param   string  Video title text
    *
    * @return  int
    **/
$this->facebook->publish_video($file, $description, $title);
```

#### Publish a image to users wall
```php
/**
    * Publish image to users feed
    * Supports externally hosted images only! No direct upload
    * to Facebook.com albums.
    *
    * Requires: publish_actions
    *
    * @param   string  URL to image
    * @param   string  Image description text
    *
    * @return  int
    **/
$this->facebook->publish_image($image, $message);
```
