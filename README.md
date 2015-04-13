# Facebook PHP SDK v4 for CodeIgniter
Library for integration of Facebook PHP SDK v4 with CodeIgniter 3

**Version:** 2.0.0

## Requirements
- PHP 5.4+
- [CodeIgniter 3](http://www.codeigniter.com/)
- CodeIgniter session library
- [Facebook PHP SDK v4](https://packagist.org/packages/facebook/php-sdk-v4)
- [Composer](https://getcomposer.org/)

## Notice
Facebook Canvas support is experimental as I have not been able to test or confirm it working. If you test it, please report back if you had success or failure.

This library do not include or support all available Facebook Graph methods. Any contribution is welcome to add more. But, please read the contributing rules before submitting any pull requests.

## Installation
1. Download the library files and add the files to your CodeIgniter installation. Only the library, config and composer.json files are required.
1. In CodeIgniter `/application/config/config.php` set `$config['composer_autoload']` to `TRUE`.
2. In CodeIgniter `/application/config/config.php`, configure the `Session Variables`.
3. Update the `facebook.php` config file in `/application/config/facebook.php` with you Facebook App details.
4. Install the Facebook PHP SDK by navigating to your applications folder and execute `composer install`.
6. Autoload the library in `autoload.php` or load it in needed controllers with `$this->load->library('facebook');`.
5. Enjoy!

## Usage
The library download includes a sample controller and views. The example code might not be the best or most beautiful code, but it is there to help you get started quicker.

## Methods

#### logged_in()
Check if user is logged
```php
$this->facebook->logged_in();
```

#### login_url()
Get login url. This method will only return a URL when using the redirect (web) login method.
```php
$this->facebook->login_url();
```

#### logout_url()
Check if user is logged. This method will only return a URL when using the redirect (web) login method.
```php
$this->facebook->logout_url();
```

#### destroy_session()
Should only be used on the logout redirect url location. This method will unset the Facebook token cookie set by this library only. **This method can not be used to log out a user!**
```php
$this->facebook->destroy_session();
```

#### user_id()
Check user id.
```php
$this->facebook->user_id();
```

#### user()
Check user details.
```php
$this->facebook->user();
```

#### get_post()
Get post from users wall.
*Requires user has approved `read_stream` permission*
```php
/**
* Retrieve a single post from users wall
*
* Required permission: read_stream
*
* @param   int     $id   Post ID
*
* @return  array
**/
$this->facebook->get_post($id);
```

#### publish_text()
Publish a text to users wall.
*Requires user has approved `publish_actions` permission*
```php
/**
* Publish a post to the users feed
*
* Required permission: publish_actions
*
* @param   string  $message  Message to publish
*
* @return  array
**/
$this->facebook->publish_text($message);
```

#### publish_video()
Publish a video to users wall.
*Requires user has approved `publish_actions` permission*
```php
/**
* Publish (upload) a video to the users feed
*
* Required permission: publish_actions
*
* @param   string  $file         Path to video file
* @param   string  $description  Video description text
* @param   string  $title        Video title text
*
* @return  array
**/
$this->facebook->publish_video($file, $description, $title);
```

#### publish_image()
Publish a image to users wall. This method support externally hosted images **only**.
*Requires user has approved `publish_actions` permission*
```php
/**
* Publish image to users feed
*
* Supports externally hosted images only! No direct upload
* to Facebook.com albums at this time.
*
* Required permission: publish_actions
*
* @param   string  $image    URL to image
* @param   string  $message  Image description text
*
* @return  array
**/
$this->facebook->publish_image($image, $message);
```

#### Return data format
Most methods will return an array that include status code and message values so that you can do appropiet actions depending on if, for example, a publishing of a image was successfull or not. A list of more error codes and messages can be found [here](https://developers.facebook.com/docs/graph-api/using-graph-api/v2.3#errors)

Example of returned result
```
Array
(
    [code] => 200
    [message] => success
    [data] => Array
    (
        [id] => 10152906820691185
        [email] => hedman.mattias.90@gmail.com
        [first_name] => Mattias
        [gender] => male
        [last_name] => Hedman
        [link] => https://www.facebook.com/app_scoped_user_id/10152906820691185/
        [locale] => en_US
        [name] => Mattias Hedman
        [timezone] => -7
        [updated_time] => 2015-04-03T03:22:50+0000
        [verified] => 1
    )
)
```
