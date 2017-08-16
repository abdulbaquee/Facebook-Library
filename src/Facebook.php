<?php

class Facebook
{

    /**
     * @var string Default Graph API version for requests.
     */
    private $default_graph_version = 'v2.10';

    /**
     * @var string Default Graph API login uri for requests.
     */
    private $graph_login_uri = 'https://www.facebook.com/dialog/oauth?';

    /**
     * @var string Default Graph API token uri for requests.
     */
    private $graph_token_uri;

    /**
     * @var string Default Graph uri for requests.
     */
    private $graph_uri;

    /**
     * @var string call back url for requests.
     */
    private $call_back_url;

    /**
     * @var string The name of the environment variable that contains the app ID.
     */
    private $app_id;

    /**
     * @var string The name of the environment variable that contains the app secret.
     */
    private $app_secret;

    /**
     * @var scope for the graph uri.
     */
    private $scope;

    /**
     * @var value the app access token.
     */
    protected $value;

    /**
     * @var FacebookApp The FacebookApp entity.
     */
    protected $app;

    /**
     * @var AccessToken|null The default access token to use with requests.
     */
    protected $defaultAccessToken;

    /**
     * @var string|null The default Graph version we want to use.
     */
    protected $defaultGraphVersion;
    private $limit = 20;

    public function __construct(array $config = [])
    {
        if (!isset($config['app_id']) || empty($config['app_id']))
        {
            throw new \Exception('Required "app_id" key not supplied in config and could not find fallback environment variable');
        }
        if (!isset($config['app_secret']) || empty($config['app_secret']))
        {
            throw new \Exception('Required "app_secret" key not supplied in config and could not find fallback environment variable');
        }
        if (!isset($config['call_back_url']) || empty($config['call_back_url']))
        {
            throw new \Exception('Required "call_back_url" key not supplied in config and could not find fallback environment variable');
        }
        if (isset($config['default_graph_version']) && !empty($config['default_graph_version']))
        {
            $this->default_graph_version = $config['default_graph_version'];
        }
        if (isset($config['scope']) || !empty($config['scope']))
        {
            $this->scope = $config['scope'];
        }
        $this->graph_token_uri = "https://graph.facebook.com/{$this->default_graph_version}/oauth/access_token?";
        $this->graph_uri = "https://graph.facebook.com/{$this->default_graph_version}/";
        $this->app_id = (string) $config['app_id'];
        $this->app_secret = $config['app_secret'];
        $this->call_back_url = $config['call_back_url'];
        $this->value = $this->_set_app_access_token();
    }

    public function login($scopes = array())
    {
        $uri = $this->graph_login_uri;
        if (count($scopes) > 0)
        {
            $this->scope = implode(",", $scopes);
        }
        $uri .= "client_id=" . $this->app_id . "&redirect_uri=" . $this->call_back_url . "&scope=" . $this->scope;
        return $uri;
    }

    /**
     * Returns the graph_login_uri.
     *
     * @return string
     */
    public function get_graph_login_uri()
    {
        return $this->graph_login_uri;
    }

    /**
     * Returns the graph_token_uri.
     *
     * @return string
     */
    public function get_graph_token_uri()
    {
        return $this->graph_token_uri;
    }

    /**
     * Returns the graph_uri.
     *
     * @return string
     */
    public function get_graph_uri()
    {
        return $this->graph_uri;
    }

    /**
     * Returns the graph version.
     *
     * @return string
     */
    public function get_default_graph_version()
    {
        return $this->default_graph_version;
    }

    /**
     * Returns the app ID.
     *
     * @return string
     */
    public function get_app_id()
    {
        return $this->app_id;
    }

    /**
     * Returns the app secret.
     *
     * @return string
     */
    public function get_app_secret()
    {
        return $this->app_secret;
    }

    /**
     * Returns an app access token.
     *
     * @return value
     */
    public function get_app_access_token()
    {
        return $this->value;
    }

    /**
     * Returns an app access token.
     *
     * @return value
     */
    public function get_access_token($code)
    {
        $uri = $this->graph_token_uri;
        if (!isset($code) || empty($code))
        {
            throw new \Exception('Required "code" key not supplied in callback file');
        }
        $uri .= "client_id=" . $this->app_id . "&client_secret=" . $this->app_secret . "&redirect_uri=" . $this->call_back_url . "&code=" . $code;
        $token = $this->_facebook_request('GET', $uri);
        if (!isset($token['access_token']))
        {
            throw new \Exception($token['error']['message'] . ': Access Token not generated!');
        }
        return $token;
    }

    /**
     * Returns an app scopes.
     *
     * @return string
     */
    public function get_app_scopes()
    {
        return $this->scopes;
    }

    /**
     * Returns an app access token.
     *
     * @return value
     */
    public function get_call_back_url()
    {
        return $this->call_back_url;
    }

    /**
     * Returns an app access token.
     *
     * @return value
     */
    private function _set_app_access_token()
    {
        return $this->value = $this->app_id . '|' . $this->app_secret;
    }

    /**
     * Returns an app secret proof.
     * @return string value of app secret proof
     */
    public function get_app_secret_proof()
    {
        return hash_hmac('sha256', $this->value, $this->app_secret);
    }

    /**
     * Sends a GET request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param AccessToken|string|null $accessToken
     * @param array                   $params
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function get($endpoint, $params = [])
    {
        $this->_validate_input_data($params);
        $filter_data = $this->_filter_input_data($params);
        if(gettype($filter_data) !== 'array')
        {
            throw new \Exception('Required "array" values but key not supplied in data');
        }
        $build_query = http_build_query($filter_data);
        $uri = $this->graph_uri;
        $uri .= $endpoint . "?" . $build_query;
        $uri .= "&limit=" . $this->limit;
        return $this->_facebook_request('GET', $uri);
    }

    private function _validate_input_data($data = NULL)
    {
        if (gettype($data) !== 'array')
        {
            throw new \Exception('Required "array" values key not supplied in data');
        } else if (count($data) == 0)
        {
            throw new \Exception('Required "array" values & atleast one data set');
        } else if (!isset($data['access_token']) || empty($data['access_token']))
        {
            throw new \Exception('Required "access_token" for graph request');
        }
    }

    private function _filter_input_data($request_data = NULL)
    {
        $merge = array();
        if (isset($request_data['access_token']) && !empty($request_data['access_token']))
        {
            $merge['access_token'] = $request_data['access_token'];
        }
        if (isset($request_data['fields']) && gettype($request_data['fields']) === 'array' && count($request_data['fields']) > 0)
        {
            $merge['fields'] = implode(',', $request_data['fields']);
        }
        if (isset($request_data['breakdown']) && gettype($request_data['breakdown']) === 'array' && count($request_data['breakdown']) > 0)
        {
            $merge['breakdown'] = implode(',', $request_data['breakdown']);
        }
        if (isset($request_data['level']) && gettype($request_data['level']) === 'string' && !empty(trim($request_data['level'])))
        {
            $merge['level'] = $request_data['level'];
        }
        return $merge;
    }

    /**
     * Sends a POST request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $access_token
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function post($endpoint, $accessToken, $params = [])
    {
        $access_token = $accessToken ? : $this->get_app_access_token();
        $uri = $this->graph_uri;
        $uri .= $endpoint;
        return $this->_facebook_request('POST', $uri, $access_token, $params);
    }

    /**
     * Sends a DELETE request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function delete($endpoint, $accessToken, $params = [])
    {
        $access_token = $accessToken ? : $this->get_app_access_token();
        return $this->_facebook_request('DELETE', $endpoint, $access_token, $params);
    }

    private function _facebook_request($method, $endpoint, $accessToken = NULL, array $params = [])
    {
        $curinit = curl_init($endpoint);
        curl_setopt($curinit, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curinit, CURLOPT_RETURNTRANSFER, true);
        if ($method === 'POST' && count($params) > 0)
        {
            curl_setopt($curinit, CURLOPT_POST, true);
            curl_setopt($curinit, CURLOPT_POSTFIELDS, $params);
        }
        $json = curl_exec($curinit);
        $phpObj = json_decode($json, TRUE);
        return $phpObj;
    }

}
