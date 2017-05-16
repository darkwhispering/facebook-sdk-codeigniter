![Not maintained](https://img.shields.io/badge/maintained%3F-no!-red.svg?style=flat)

# Facebook PHP SDK for CodeIgniter

Library for integration of Facebook PHP SDK v5+ with CodeIgniter 3+

**Version:** 3.0.0

> This library is meant to help you to use the Facebook PHP SDK in a simpler way with CodeIgniter. It is however not at a "can do everything" type of library. If you required highly advanced integrations with the Facebook PHP SDK I recommend you read up on the [documentation](https://developers.facebook.com/docs/reference/php) and use the Facebook PHP SDK directly.

## Requirements

- PHP 5.4+
- [CodeIgniter 3](http://www.codeigniter.com/)
- [CodeIgniter session library](http://www.codeigniter.com/userguide3/libraries/sessions.html)
- [Facebook PHP SDK v5](https://developers.facebook.com/docs/php/gettingstarted/5.0.0)
- [Composer](https://getcomposer.org/)

## Notice

Facebook *Canvas* and *Page Tab* support is experimental as I have not been able to test or confirm it working. If you test it, please report back if you had success or failure.

## Installation

**It is very important that you follow the installation steps closely to get the library and Facebook SDK to work**

1. Download the library files and add the files to your CodeIgniter installation. Only the library, config and composer.json files are required.
1. In your CodeIgniter `/application/config/config.php` file, set `$config['composer_autoload']` to `TRUE`. [Read more](https://www.codeigniter.com/user_guide/general/autoloader.html)
2. In your CodeIgniter `/application/config/config.php` file, configure the `Session Variables` section. [Read more](https://www.codeigniter.com/user_guide/libraries/sessions.html)
3. Update the `facebook.php` config file in `/application/config/facebook.php` with your Facebook App details.
4. Install the Facebook PHP SDK by navigating to your applications folder in the terminal and run Composer with `composer install`. [Read more](https://developers.facebook.com/docs/php/gettingstarted#install-composer)
6. Autoload the library in `application/config/autoload.php` or load it in needed controllers with `$this->load->library('facebook');`.
5. Enjoy!

## Usage

The library download includes a sample controller and views. The example code might not be the best or most beautiful code, but it is there to help you get started quicker.

## Methods

#### is_authenticated()

Check if user is authenticated. Returns access token object if user is, else empty.

```php
$this->facebook->is_authenticated();
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

#### request(string $method, string $endpoint [, array $params [, string $access_token]])

Main method to do Graph requests. Should support all, or at least most of the avaialble Facebook Graph methods.

> *All methods has not been tested, please submit an issue report if you encounter any issues*

This method returns an array on both success or failure.

| Parameter | Type | Description |
| --- | --- | --- |
| $method | string | Request type. [get, post, delete] |
| $endpoint | string | Graph endpoint, eg /me to get user information. |
| $params | array | Array with extra graph parameters. This is optional. |
| $access_token | string | Optional access token. |

##### Example

Short exampel to get user information and print it out.

```php
$user = $this->facebook->request('get', '/me');
if (!isset($user['error']))
{
    print_r($user);
}
```

##### Notes

- Facebook Graph API documentation can be found [here](https://developers.facebook.com/docs/graph-api).
- List of Facebook Graph API Error codes can be found [here](https://developers.facebook.com/docs/graph-api/using-graph-api/v2.4#errors).

#### user_upload_request(string $path_to_file [, array $params [, string $type [, string $access_token]]])
Upload a image or video file to a users profile.

> This method only support upload to a users profile. Upload to a page is not possible at this moment.

| Parameter | Type | Description |
| --- | --- | --- |
| $path_to_file | string | Path to file on server or external URL |
| $params | array | Array with extra graph parameters. This is optional. |
| $type | string | Type of upload. [image, video] This is optional, Default: image |
| $access_token | string | Optional access token. |

##### Example

Short exampel to upload a file from the server.

```php
$this->facebook->user_upload_request('/path/to/file.jpg', ['message' => 'This is a test upload']);
```

##### Notes

- Facebook Graph API documentation can be found [here](https://developers.facebook.com/docs/graph-api).
- List of Facebook Graph API Error codes can be found [here](https://developers.facebook.com/docs/graph-api/using-graph-api/v2.4#errors).

#### add_to_batch_pool(string $key, string $method, string $endpoint [, array $params [, string $access_token = null]])

Add a request to the batch pool to be sent later in batch request.

| Parameter | Type | Description |
| --- | --- | --- |
| $key | string | Key for identifying this request in the batch response |
| $method | string | Request type. [get, post, delete] |
| $endpoint | string | Graph endpoint, eg /me to get user information. |
| $params | array | Array with extra graph parameters. This is optional. |
| $access_token | string | Optional access token. |

##### Example

```php
$this->facebook->add_to_batch_pool('user-profile', 'get', '/me');
```

#### remove_from_batch_pool(string $key)

Add a request to the batch pool to be sent later in batch request.

| Parameter | Type | Description |
| --- | --- | --- |
| $key | string | Key for identifying request to remove |

##### Example

```php
$this->facebook->remove_from_batch_pool('user-profile');
```

#### send_batch_pool()

Send the batch pool requests.

Example on how batch requests works in the [Facebook documentation](https://developers.facebook.com/docs/php/howto/example_batch_request).

##### Example

```php
$this->facebook->send_batch_pool();
```

##### Full example

```php
$this->facebook->add_to_batch_pool('user-profile', 'get', '/me');
$this->facebook->remove_from_batch_pool('user-email');
$responses = $this->facebook->send_batch_pool();

foreach ($responses as $key => $data)
{
	print_r($key);
	print_r($data);
}
```

#### object()
If you want to work directly with the Facebook\Facebook service class, you can do so. The `object()` method will return the full object of `new Facebook\Facebook` service class that you can use however you would like.

> *The library will still take care of the loading of the SDK and load configured login helper.*

Documentation for Facebook\Facebook service class can be found [here](https://developers.facebook.com/docs/php/Facebook/5.0.0) and full SDK reference list [here](https://developers.facebook.com/docs/php/api/5.0.0).

##### Example

```php
$fb = $this->facebook->object();

// Get user info
$response = $fb->get('/me');
$user     = $response->getDecodedBody();

print_r($user);
```
