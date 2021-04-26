<?php
/**
 * Description of produits
 *
 * @author bde
 */
namespace models;
use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;

class users {
    private $MailChimp;
    //private $api_endpoint = 'https://us13.api.mailchimp.com/3.0/';
    //private $api_key = '16786e43a7b0c088d74907c18292e475-us13';
    //private $listId = '7dc5f10983';

    private $api_endpoint = 'https://us18.api.mailchimp.com/3.0/';
    private $api_key = '3a7675c53bd91039d3d052bb8a77cf3c-us18';
    private $listId = '77964287b8';

    private $verify_ssl = false;
    const TIMEOUT = 10;

    private $operations = array();
    private $batch_id;
	
	/**
     * Constructeur
     *
     */
    public function __construct(){
        $this->MailChimp = new MailChimp($this->api_key);
    }
	
	/**
     * Lecture des users
     *
     * @return void
     */
    public function lire($offset){
        $result = $this->MailChimp->get('lists');
        print_r($result);
        $method = "lists/$this->listId/members";
        $args = array();
        $args["offset"] = $offset;
        return $this->makeRequest('get', $method, $args);
	}

    /**
     * Ajout user
     *
     * @return void
     */
    public function add($requestMethodArray){
        $method = "lists/$this->listId/members/";
        $args = array();
        $args["email_address"] = $requestMethodArray['email_address'];
        $args["status"] = "subscribed";
        return $this->makeRequest('post', $method, $args);
    }

    /**
     * Modif user
     *
     * @return void
     */
    public function modif($requestMethodArray){
        $subscriberHash = $this::subscriberHash($requestMethodArray['email_address']);
        $method = "lists/$this->listId/members/$subscriberHash";
        $args = array();
        $args["merge_fields"]['FNAME'] = $requestMethodArray['FNAME'];
        $args["merge_fields"]['LNAME'] = $requestMethodArray['LNAME'];
        return $this->makeRequest('patch', $method, $args);
    }

    /**
     * Modif user
     *
     * @return void
     */
    public function delete($email){
        $subscriberHash = $this::subscriberHash($email);
        $method = "lists/$this->listId/members/$subscriberHash";
        $args = array();
        return $this->makeRequest('delete', $method, $args);
    }

    public function new_batch($batch_id = null)
    {
        return new Batch($this->MailChimp, $batch_id);
    }

    public function check_status($batch_id = null)
    {
        if ($batch_id === null && $this->batch_id) {
            $batch_id = $this->batch_id;
        }

        return $this->makeRequest('get', 'batches/' . $batch_id);
    }

    private function makeRequest($http_verb, $method, $args = array(), $timeout = self::TIMEOUT)
    {
        $url = $this->api_endpoint . '/' . $method;

        $response = $this->prepareStateForRequest($http_verb, $method, $url, $timeout);

        $httpHeader = array(
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
            'Authorization: apikey ' . $this->api_key
        );

        if (isset($args["language"])) {
            $httpHeader[] = "Accept-Language: " . $args["language"];
        }

        if ($http_verb === 'put') {
            $httpHeader[] = 'Allow: PUT, PATCH, POST';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-BDE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        switch ($http_verb) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                $this->attachRequestPayload($ch, $args);
                break;

            case 'get':
                $query = http_build_query($args, '', '&');
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
                break;

            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            case 'patch':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                $this->attachRequestPayload($ch, $args);
                break;

            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                $this->attachRequestPayload($ch, $args);
                break;
        }

        $responseContent     = curl_exec($ch);
        $response['headers'] = curl_getinfo($ch);
        $response            = $this->setResponseState($response, $responseContent, $ch);
        $formattedResponse   = $this->formatResponse($response);

        curl_close($ch);

        $isSuccess = $this->determineSuccess($response, $formattedResponse, $timeout);

        return is_array($formattedResponse) ? $formattedResponse : $isSuccess;
    }

    private function prepareStateForRequest($http_verb, $method, $url, $timeout)
    {
        $this->last_error = '';

        $this->request_successful = false;

        $this->last_response = array(
            'headers'     => null, // array of details from curl_getinfo()
            'httpHeaders' => null, // array of HTTP headers
            'body'        => null // content of the response
        );

        $this->last_request = array(
            'method'  => $http_verb,
            'path'    => $method,
            'url'     => $url,
            'body'    => '',
            'timeout' => $timeout,
        );

        return $this->last_response;
    }

    private function setResponseState($response, $responseContent, $ch)
    {
        if ($responseContent === false) {
            $this->last_error = curl_error($ch);
        } else {

            $headerSize = $response['headers']['header_size'];

            $response['httpHeaders'] = $this->getHeadersAsArray(substr($responseContent, 0, $headerSize));
            $response['body']        = substr($responseContent, $headerSize);

            if (isset($response['headers']['request_header'])) {
                $this->last_request['headers'] = $response['headers']['request_header'];
            }
        }

        return $response;
    }

    private function getHeadersAsArray($headersAsString)
    {
        $headers = array();

        foreach (explode("\r\n", $headersAsString) as $i => $line) {
            if (preg_match('/HTTP\/[1-2]/', substr($line, 0, 7)) === 1) { // http code
                continue;
            }

            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            list($key, $value) = explode(': ', $line);

            if ($key == 'Link') {
                $value = array_merge(
                    array('_raw' => $value),
                    $this->getLinkHeaderAsArray($value)
                );
            }

            $headers[$key] = $value;
        }

        return $headers;
    }

    private function attachRequestPayload(&$ch, $data)
    {
        $encoded                    = json_encode($data);
        $this->last_request['body'] = $encoded;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
    }

    private function formatResponse($response)
    {
        $this->last_response = $response;

        if (!empty($response['body'])) {
            return json_decode($response['body'], true);
        }

        return false;
    }

    /**
     * Check if the response was successful or a failure. If it failed, store the error.
     *
     * @param array       $response          The response from the curl request
     * @param array|false $formattedResponse The response body payload from the curl request
     * @param int         $timeout           The timeout supplied to the curl request.
     *
     * @return bool     If the request was successful
     */
    private function determineSuccess($response, $formattedResponse, $timeout)
    {
        $status = $this->findHTTPStatus($response, $formattedResponse);

        if ($status >= 200 && $status <= 299) {
            $this->request_successful = true;
            return true;
        }

        if (isset($formattedResponse['detail'])) {
            $this->last_error = sprintf('%d: %s', $formattedResponse['status'], $formattedResponse['detail']);
            return false;
        }

        if ($timeout > 0 && $response['headers'] && $response['headers']['total_time'] >= $timeout) {
            $this->last_error = sprintf('Request timed out after %f seconds.', $response['headers']['total_time']);
            return false;
        }

        $this->last_error = 'Unknown error, call getLastResponse() to find out what happened.';
        return false;
    }

    private function findHTTPStatus($response, $formattedResponse)
    {
        if (!empty($response['headers']) && isset($response['headers']['http_code'])) {
            return (int)$response['headers']['http_code'];
        }

        if (!empty($response['body']) && isset($formattedResponse['status'])) {
            return (int)$formattedResponse['status'];
        }

        return 418;
    }

    public static function subscriberHash($email)
    {
        return md5(strtolower($email));
    }

    public function success()
    {
        return $this->request_successful;
    }

    public function getLastError()
    {
        return $this->last_error ?: false;
    }

    public function post($id, $method, $args = array())
    {
        $this->queueOperation('POST', $id, $method, $args);
    }

    public function execute($timeout = 10)
    {
        $req = array('operations' => $this->operations);

        $result = $this->makeRequest('post', 'batches', $req, $timeout);

        if ($result && isset($result['id'])) {
            $this->batch_id = $result['id'];
        }

        return $result;
    }

    private function queueOperation($http_verb, $id, $method, $args = null)
    {
        $operation = array(
            'operation_id' => $id,
            'method'       => $http_verb,
            'path'         => $method,
        );

        if ($args) {
            if ($http_verb == 'GET') {
                $key             = 'params';
                $operation[$key] = $args;
            } else {
                $key             = 'body';
                $operation[$key] = json_encode($args);
            }
        }

        $this->operations[] = $operation;
    }

}
