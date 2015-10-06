<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Facebook PHP SDK for CodeIgniter 3
 *
 * Library wrapper for Facebook PHP SDK. Check user login status, publish to feed
 * and more with easy to use CodeIgniter syntax.
 *
 * This library requires the Facebook PHP SDK to be installed with Composer, and that CodeIgniter
 * config is set to autoload the vendor folder. More information in the CodeIgniter user guide at
 * http://www.codeigniter.com/userguide3/general/autoloader.html?highlight=composer
 *
 * It also requires CodeIgniter session library to be correctly configured.
 *
 * @package     CodeIgniter
 * @category    Libraries
 * @author      Mattias Hedman
 * @license     MIT
 * @link        https://github.com/darkwhispering/facebook-sdk-v4-codeigniter
 * @version     3.0.0
 */

Class Facebook {

    /**
     * Facebook object
     */
    private $fb;

    /**
     * Helper object
     */
    private $helper;

    // ------------------------------------------------------------------------

    public function __construct()
    {
        // Load config
        $this->load->config('facebook');

        // Load required libraries and helpers
        $this->load->library('session');
        $this->load->helper('url');

        // Initiate the Facebook SDK
        if (!isset($this->fb))
        {
            $this->fb = new Facebook\Facebook([
                'app_id'                => $this->config->item('facebook_app_id'),
                'app_secret'            => $this->config->item('facebook_app_secret'),
                'default_graph_version' => $this->config->item('facebook_graph_version')
            ]);
        }

        // Load correct helper depending on login type
        // set in the config file
        switch ($this->config->item('facebook_login_type'))
        {
            case 'js': // Javascript helper
                $this->helper = $this->fb->getJavaScriptHelper();
                break;

            case 'canvas': // Canvas helper
                $this->helper = $this->fb->getCanvasHelper();
                break;

            case 'page_tab': // Page Tab helper
                $this->helper = $this->fb->getPageTabHelper();
                break;

            case 'web': // Web helper (redirect)
                $this->helper = $this->fb->getRedirectLoginHelper();
                break;
        }

        // Try an authenticate the user right away
        $this->authenticate();
    }

    // ------------------------------------------------------------------------

    /**
     * Return Facebook Object
     *
     * @return  [type]  [description]
     */
    public function object()
    {
        return $this->fb;
    }

    // ------------------------------------------------------------------------
    /**
    * Check if user are logged in by checking if we have a Facebook
    * session active.
    *
    * @return  bool
    **/
    public function is_authenticated()
    {
        // Check if user is authenticated already
        $access_token  = $this->get_access_token();
        if ($access_token && !$access_token->isExpired())
        {
            return $access_token;
        }
        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Do Graph request
     *
     * @param   string  $method        Method type [get, post, delete]
     * @param   string  $endpoint      Graph endpoint
     * @param   array   $params        Optional Graph parameters
     * @param   string  $access_token  Optional access token
     *
     * @return  string
     */
    public function request($method, $endpoint, $params = array(), $access_token = null)
    {
        // Try to make the Graph request
        try
        {
            $response = $this->fb->{strtolower($method)}($endpoint, $params, $access_token);
            return $response->getDecodedBody();
        }
        // When Graph returns an error
        catch(Facebook\Exceptions\FacebookResponseException $e)
        {
            // Log error
            log_message('error', '[FACEBOOK PHP SDK] code: ' . $e->getCode().' | message: '.$e->getMessage());
            return array('error' => $e->getCode(), 'message' => $e->getMessage());
        }
        // When validation fails or other local issues
        catch (Facebook\Exceptions\FacebookSDKException $e)
        {
            // Log error
            log_message('error', '[FACEBOOK PHP SDK] code: ' . $e->getCode().' | message: '.$e->getMessage());
            return array('error' => $e->getCode(), 'message' => $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Generate Facebook login url for Facebook Redirect Login (web)
     *
     * @return  string
     */
    public function login_url()
    {
        // Login type must be web, else return empty string
        if ($this->config->item('facebook_login_type') != 'web')
        {
            return '';
        }

        // Create login url
        return $this->helper->getLoginUrl(base_url() . $this->config->item('facebook_login_redirect_url'), $this->config->item('facebook_permissions'));
    }

    // ------------------------------------------------------------------------

    /**
     * Generate Facebook login url for Facebook Redirect Login (web)
     *
     * @return  string
     */
    public function logout_url()
    {
        // Login type must be web, else return empty string
        if ($this->config->item('facebook_login_type') != 'web')
        {
            return '';
        }

        // Create logout url
        return $this->helper->getLogoutUrl($this->get_access_token(), base_url() . $this->config->item('facebook_logout_redirect_url'));
    }

    // ------------------------------------------------------------------------

    /**
     * Destroy our local Facebook session
     */
    public function destroy_session()
    {
        // Remove our Facebook token from session
        $this->session->unset_userdata('fb_access_token');
    }

    // ------------------------------------------------------------------------

    /**
    * Get a new access token from Facebook
    *
    * @return  mixed
    **/
    private function authenticate()
    {
        // Get stored access token
        $access_token = $this->get_access_token();

        // If we did not have a stored access token or
        // if it has expired, try get a new access token
        if (!$access_token OR $access_token->isExpired())
        {
            try
            {
                // Get access token
                $access_token = $this->helper->getAccessToken();
            }
            catch (Facebook\Exceptions\FacebookSDKException $e)
            {
                // Log error as debug
                log_message('error', '[FACEBOOK PHP SDK] code: ' . $e->getCode().' | message: '.$e->getMessage());
                return;
            }
        }

        // If we got a session we need to exchange it for
        // a long lived session.
        if (isset($access_token))
        {
            // Get long lived token
            $access_token = $this->long_lived_token($access_token);

            // Save the token to the current session
            $this->set_access_token($access_token);

            // Set default access token
            $this->fb->setDefaultAccessToken($access_token);

            // Return the token
            return $access_token;
        }

        // Collect errors if any when using
        // web redirect based login
        if ($this->config->item('facebook_login_type') === 'web')
        {
            if ($this->helper->getError())
            {
                // Collect error data
                $error = array(
                    'error'             => $this->helper->getError(),
                    'error_code'        => $this->helper->getErrorCode(),
                    'error_reason'      => $this->helper->getErrorReason(),
                    'error_description' => $this->helper->getErrorDescription()
                );

                return $error;
            }
        }

        // Could not get a session, so return null
        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Exchange short lived token for a long lived token
     *
     * @param   object  $access_token  Short lived token
     *
     * @return  object
     */
    private function long_lived_token($access_token)
    {
        // Check if the token alreayd is a long lived token
        if (!$access_token->isLongLived())
        {
            // Get auth client
            $oauth2_client = $this->fb->getOAuth2Client();

            try
            {
                // Get long lived token
                return $oauth2_client->getLongLivedAccessToken($access_token);
            }
            catch (Facebook\Exceptions\FacebookSDKException $e)
            {
                // Log error as debug
                log_message('error', '[FACEBOOK PHP SDK] code: ' . $e->getCode().' | message: '.$e->getMessage());
                return;
            }
        }

        return $access_token;
    }

    // ------------------------------------------------------------------------

    /**
     * Get stored access token
     *
     * @return  object
     */
    private function get_access_token()
    {
        return $this->session->userdata('fb_access_token');
    }

    // ------------------------------------------------------------------------

    /**
     * Store access token
     *
     * @param  object  $access_token  Access token object
     */
    private function set_access_token($access_token)
    {
        $this->session->set_userdata('fb_access_token', $access_token);
    }

    // ------------------------------------------------------------------------

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
