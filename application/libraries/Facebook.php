<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Name:  Facebook PHP SDK 4+ Library for CodeIgniter 3+
*
* Version: 1.1.0
*
* Author: Mattias Hedman
*         hedman.mattias.90@gmail.com
*         @silentium90
*
* Added Awesomeness: To all internet guides and stackoverflow
*
* Created: 2014-05-27
* Updated: 2015-04-03
*
* Description: Custom made library for Facebook PHP SDK 4+ for easy check of login status
* and more without the need to include the SDK in every controller or model.
*
* Requirements:
*   - PHP 5.4 or above
*   - Facebook PHP SDK 4+ installed with composer
*   - CodeIgniter 3+
*   - CodeIgniter session library
*
**/

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurl;
use Facebook\FacebookCurlHttpClient;

Class Facebook {

    public function __construct()
    {

        // Load config
        $this->load->config('facebook');

        // Load required libraries
        $this->load->library('session');

        // Get config settings
        $app_id     = $this->config->item('facebook_app_id');
        $app_secret = $this->config->item('facebook_app_secret');

        // Init the Facebook SDK
        FacebookSession::setDefaultApplication($app_id, $app_secret);

        // Create session right away if we have one
        $this->facebook_session();

    }

    // ------------------------------------------------------------------------

    /**
    * Return true/false if user is logged in with Facebook
    *
    * @return  boolen  Returns true/false if FB sessions is found
    **/
    public function logged_in()
    {

        // Check if we have an active Facebook session
        if ($this->facebook_session())
        {
            // Session found
            return true;
        }

        // No session found
        return false;
    }

    // ------------------------------------------------------------------------

    /**
    * Get user ID
    *
    * @return  int  Returns user facebook ID
    **/
    public function user_id()
    {
        // Get users facebook session
        $session = $this->facebook_session();

        if ($session)
        {
            try
            {
                // Get users ID
                $user = (new FacebookRequest($session, 'GET', '/me'))
                    ->execute()
                    ->getGraphObject()
                    ->asArray();

                // Return ID
                return $user['id'];
            }
            catch(FacebookRequestException $e)
            {
                // Log error
                log_message('error', '[FACEBOOK PHP SDK - User ID] code: ' . $e->getCode().' | message: '.$e->getMessage());

                return null;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------


    /**
    * Get all user details, current token and accepted permissions list
    *
    * @return  array  Returns user data
    **/
    public function user()
    {
        // Get users facebook session
        $session = $this->facebook_session();

        if ($session)
        {
            try
            {
                // Get user details
                $user = (new FacebookRequest($session, 'GET', '/me'))
                    ->execute()
                    ->getGraphObject()
                    ->asArray();

                // Get users Facebook token from session
                $user['token'] = $this->session->userdata('fb_token');

                // Get users permissions list
                $user['permissions'] = (new FacebookRequest($session, 'GET', '/me/permissions'))
                    ->execute()
                    ->getGraphObject()
                    ->asArray();

                // Return data
                return $user;
            }
            catch(FacebookRequestException $e)
            {
                // Log error
                log_message('error', '[FACEBOOK PHP SDK - User] code: ' . $e->getCode().' | message: '.$e->getMessage());

                return array();
            }
        }

        return array();
    }

    // ------------------------------------------------------------------------

    /**
    * Get single post
    *
    * Requires: read_stream
    *
    * @param   int    Post ID
    *
    * @return  array  Return post data
    **/
    public function get_post($id = null)
    {
        // ID required, exit if not provided
        if (!$id)
        {
            return null;
        }

        // Get users facebook session
        $session = $this->facebook_session();

        if ($session)
        {
            try
            {
                // Get post data
                $post = (new FacebookRequest($session, 'GET', '/'.$id))
                    ->execute()
                    ->getGraphObject()
                    ->asArray();

                // Return post data
                return $post;
            }
            catch(FacebookRequestException $e)
            {
                // Log error
                log_message('error', '[FACEBOOK PHP SDK - Get post] code: ' . $e->getCode().' | message: '.$e->getMessage());

                return null;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
    * Publish a post to the users feed
    *
    * Requires: publish_actions
    *
    * @param   string  Message to publish
    *
    * @return  int     ID of the created post on success
    **/
    public function publish_text($message = '')
    {
        // Get user facebook session
        $session = $this->facebook_session();

        if ($session)
        {
            try
            {
                // Publish post
                $response = (new FacebookRequest(
                    $session,
                    'POST',
                    '/me/feed',
                    array(
                        'message' => $message
                    )
                ))->execute()->getGraphObject()->asArray();

                // Return post ID
                return $response['id'];
            }
            catch(FacebookRequestException $e)
            {
                // Log error
                log_message('error', '[FACEBOOK PHP SDK - Publish text] code: ' . $e->getCode().' | message: '.$e->getMessage());

                // If error is specifically 506, return duplicate message
                if ($e->getCode() == '506')
                {
                    return 'duplicate';
                }

                return null;
            }
        }

        return null;

    }

    // ------------------------------------------------------------------------

    /**
    * Publish a video to the users feed
    *
    * Requires: publish_actions
    *
    * @param   string  URL to file
    * @param   string  Video description text
    * @param   string  Video title text
    *
    * @return  int     ID of published video on success
    **/
    public function publish_video($file = '', $description = '', $title = '')
    {
        // Get users facebook session
        $session = $this->facebook_session();

        if ($session)
        {
            try
            {
                // Publish video
                $response = (new FacebookRequest(
                    $session,
                    'POST',
                    '/me/videos',
                    array(
                        'description' => $description,
                        'title'       => $title,
                        'source'      => '@'.$file
                    )
                ))->execute()->getGraphObject()->asArray();

                // Return video ID
                return $response['id'];
            }
            catch(FacebookRequestException $e)
            {
                // Log error
                log_message('error', '[FACEBOOK PHP SDK - Publish text] code: ' . $e->getCode().' | message: '.$e->getMessage());

                return null;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

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
    * @return  int     ID of the published image on success
    **/
    public function publish_image($image = '', $message = '')
    {
        // Get users facebook session
        $session = $this->facebook_session();

        if ($session)
        {
            try
            {
                // Publish image
                $response = (new FacebookRequest(
                    $session,
                    'POST',
                    '/me/photos',
                    array(
                        'url'     => $image,
                        'message' => $message
                    )
                ))->execute()->getGraphObject()->asArray();

                // Return image ID
                return $response['id'];
            }
            catch(FacebookRequestException $e)
            {
                // Log error
                log_message('error', '[FACEBOOK PHP SDK - Publish media] code: ' . $e->getCode().' | message: '.$e->getMessage());

                return null;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
    * Checking if the user is already signed in with Facebook
    * and get the session data from the Facebook cookie or
    * our current if it is still valid
    *
    * @return  object  Facebook session object
    **/
    private function facebook_session()
    {

        // Check if our own session token exists
        if ($this->session->userdata('fb_token'))
        {
            // Create new session for the token
            $session = new FacebookSession($this->session->userdata('fb_token'));

            // validate the access_token to make sure it's still valid
            try
            {
                if (!$session->validate())
                {
                    // Not valid, create new session
                    $session = $this->get_new_session();
                }
            }
            catch (Exception $e)
            {
                // Error, create new session
                $session = $this->get_new_session();
            }
        }
        else
        {
            // We don't have a session, create a new
            $session = $this->get_new_session();
        }

        // Return session object data
        return $session;
    }

    // ------------------------------------------------------------------------

    /**
    * Get a new session from Facebook
    *
    * @return  object  Facebook session object
    **/
    private function get_new_session()
    {
        // Load FB javscript login helper
        $js_helper = new FacebookJavaScriptLoginHelper();

        try
        {
            // Get session from JS SDK
            $session = $js_helper->getSession();
        }
        catch (FacebookRequestException $e)
        {
            // Log error
            log_message('error', '[FACEBOOK PHP SDK - Get session 1] code: ' . $e->getCode().' | message: '.$e->getMessage());
        }
        catch (Exception $e)
        {
            // Log error
            log_message('error', '[FACEBOOK PHP SDK - Get session 2] code: ' . $e->getCode().' | message: '.$e->getMessage());
        }

        // If we got a session we need to exchange it for
        // a long lived session.
        if (isset($session))
        {
            // Get long lived token
            $token = $session->getLongLivedSession()->getToken();

            // Create a new session with the long lived token
            $session = new FacebookSession($token);

            // Save the token to the current session
            $this->session->set_userdata('fb_token', $token);

            // Return the token
            return $token;
        }

        // Could not get a session, so return null
        return null;
    }

    /**
    * Enables the use of CI super-global without having to define an extra variable.
    * I can't remember where I first saw this, so thank you if you are the original author.
    *
    * Copied from the Ion Auth library
    *
    * @access  public
    * @param   $var
    * @return  mixed
    */
    public function __get($var)
    {
        return get_instance()->$var;
    }


}
