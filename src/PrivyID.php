<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-15
 * Time: 20:26
 */

namespace BlackIT\PrivyID;

use Exception;
use GuzzleHttp\Client;

class PrivyID
{
    private $server_key;
    private $is_production;

    CONST SANDBOX_API_BASE_URL = 'https://api-sandbox.privy.id/v3/merchant';
    CONST PRODUCTION_API_BASE_URL = 'https://core.privy.id/v3/merchant';

    CONST SANDBOX_API_BASE_URL_V1 = 'http://oauth.privydev.id';
    CONST PRODUCTION_API_BASE_URL_V1 = 'http://oauth.privy.id';

    public function __construct()
    {
    }

    private function baseUrl()
    {
        return (config('privyid.is_production')) ? self::PRODUCTION_API_BASE_URL : self::SANDBOX_API_BASE_URL;
    }

    private function baseUrlV1()
    {
        return (config('privyid.is_production')) ? self::PRODUCTION_API_BASE_URL_V1 : self::SANDBOX_API_BASE_URL_V1;
    }


    private function requestHeader()
    {
        return [
            'Merchant-Key' => $this->getMerchantKey(),
            'Content-Type' => 'multipart/form-data'
        ];
    }

    private function requestHeaderV1()
    {
        return [
            'Merchant-Key' => $this->getMerchantKey(),
            'Content-Type' => 'multipart/form-data'
        ];
    }

    private function requestHeaderV1OAuth(string $token)
    {
        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => '*/*',
            'Cache-Control' => 'no-cache'
        ];
    }

    private function dataToMultipart($data) {
        $return = [];
        foreach ($data as $index => $content) {
            array_push($return,[
                'name' => $index,
                'contents' => $content
            ]);
        }

        return $return;
    }
    private function clientRequest($url, $type, $data = null)
    {
        try {
            $client = new Client($this->requestHeader());
            $client->setAuth($this->getUsername(), $this->getPassword());
            $request = $client->request($type, $url, [
                'multipart' => $this->dataToMultipart($data)
            ]);

            $response = json_decode($request->getBody()->getContents(),true);
            return $response;
        } catch (Exception $e) {
            throw new Exception ($e->getMessage(), $e->getResponse()->getStatusCode());
        }
    }

    private function clientRequestV1($url, $type, $data = null)
    {
        try {
            $client = new Client($this->requestHeaderV1());

            $options = [];
            switch(strtolower($type)) {
                case 'post' :
                    $options = [
                        'multipart' => $this->dataToMultipart($data)
                    ];
                    break;
                case 'get' :
                    $options = [
                        'data' => $data
                    ];
                    break;
            }

            $request = $client->request($type, $url, $options);

            $response = json_decode($request->getBody()->getContents(),true);
            return $response;
        } catch (Exception $e) {
            throw new Exception ($e->getMessage(), $e->getResponse()->getStatusCode());
        }
    }

    private function clientRequestV1OAuth($url, $type, $data = null, string $token)
    {
        try {
            $client = new Client();

            $options = [
                'headers' => $this->requestHeaderV1OAuth($token)
            ];
            switch(strtolower($type)) {
                case 'post' :
                    $options['multipart'] = $this->dataToMultipart($data);
                    break;
                case 'get' :
                    $options['data'] = $data;
                    break;
            }

            $request = $client->request($type, $url, $options);

            $response = json_decode($request->getBody()->getContents(),true);
            return $response;
        } catch (Exception $e) {
            throw new Exception ($e->getMessage(), $e->getResponse()->getStatusCode());
        }
    }

    private function getMerchantKey() {
        return (config('privyid.is_production')) ? config('privyid.production.merchant_key') : config('privyid.sandbox.merchant_key');
    }

    private function getUsername() {
        return (config('privyid.is_production')) ? config('privyid.production.username') : config('privyid.sandbox.username');
    }

    private function getPassword() {
        return (config('privyid.is_production')) ? config('privyid.production.password') : config('privyid.sandbox.password');
    }

    private function getClientID() {
        return config('privyid.client_id');
    }

    private function getSecretKey() {
        return config('privyid.secret_key');
    }

    public function getResponseStatus($response) {
        if (isset($response['status'])) {
            if ($response['status'] == 1)
                return true;
        }

        return false;
    }

    public function getOAuthLink() {
        $url = 'oauth/authorize';
        $endpoint = $this->baseUrlV1() . '/'. $url ;
        $data = [
            'client_id' => $this->getClientID(),
            'redirect_uri' => 'https://kolegakapital.com/callback/privyid', //url('callback/privyid')
            'scope' => 'read',
            'response_type' => 'code'
        ];

        $query_string = '';
        foreach ($data as $name => $value) {
            $query_string .= $name.'='.urlencode($value).'&';
        }
        $query_string = substr($query_string,0,strlen($query_string)-1);

        return $endpoint.'?'.$query_string;
    }

    public function getOAuthToken(string $code) {
        $url = 'oauth/token';
        $endpoint = $this->baseUrlV1() . '/'. $url ;
        $data = [
            'client_id' => $this->getClientID(),
            'client_secret' => $this->getSecretKey(),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'https://kolegakapital.com/callback/privyid' //url('callback/privyid')
        ];
        $response = $this->clientRequestV1($endpoint, 'POST', $data);

        return $response;
    }

    public function refreshToken(string $refresh_token) {
        $url = 'oauth/token';
        $endpoint = $this->baseUrlV1() . '/'. $url ;
        $data = [
            'client_id' => $this->getClientID(),
            'client_secret' => $this->getSecretKey(),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token
        ];
        $response = $this->clientRequestV1($endpoint, 'POST', $data);

        return $response;
    }

    public function getUserIdentity(string $token) {
        $endpoint = $this->baseUrlV1() . '/api/v1/user/identity';
        $data = [

        ];

        $response = $this->clientRequestV1OAuth($endpoint, 'GET', $data, $token);

        return $response;
    }



    /**
     * @param $email    user@usermail.com	User's mail
     * @param $phone    08233324223	User's phone number
     * @param $selfie   Exampleselfie.png	Face close up photo of Registrant on image format (.png / .jpg / .jpeg)
     * @param $ktp      ExampleKTP.png	User's Identity card on image format (.png / .jpg / .jpeg)
     * @param $identity Object[]	{"nik": "123456564454644", "nama": "Tiffany Kumala", "tanggalLahir":"1983-01-02"}	Registrant's identity. NIK, name and date of birth required. NIK must be 16 digits and the sixteenth digit can't be 0.
     * @return mixed
     *
     */
    public function requestRegistration($email, $phone, $selfie, $ktp, $identity)
    {
        $url = 'registration';
        $endpoint = $this->baseUrl() . '/'. $url ;
        $data = [
            'email' => $email,
            'phone' => $phone,
            'selfie' => $selfie,
            'ktp' => $ktp,
            'identity' => $identity,
        ];
        $response = $this->clientRequest($endpoint, 'POST', $data);

        return $response;
    }


    /**
     * @param string $reference_no
     * @return array
     */
    public function getRegistationStatus($token)
    {
        $endpoint = $this->baseUrl() . '/registration/status';
        $data = [
            'token' => $token
        ];

        $response = $this->clientRequest($endpoint, 'POST', $data);

        return $response;
    }


    /**
     * @param $documentTitle String Example Title	Document title
     * @param $docType String Serial	Document workflow. Value : Serial, Parallel
     * @param $owner String {"privyId":"AB1234", "enterpriseToken": "41bc84b42c8543daf448d893c255be1dbdcc722e"}	Document owner. Contains privyId and enterpriseToken, enterpriseToken on example column can be used for Development Environment. Every merchant has their own enterpriseToken and use them in Production Environment.
     * @param $document File Exampledoc.pdf	Document with pdf format.
     * @param $recipients Object[] [{"privyId":"TES001", "type":"Signer", "enterpriseToken": "companyToken"}, {"privyId":"TES002", "type":"Signer", "enterpriseToken": ""}]	Recipients list. Type can be : Signer, Reviewer. If the document type is Serial, the signing or reviewing process will be based on the order of recipients.
     * @return mixed
     * @throws Exception
     *
     */
    public function uploadDocument($documentTitle, $docType, $owner, $document, $recipients)
    {
        $endpoint = $this->baseUrl() . '/document/upload';
        $data = [
            'documentTitle' => $documentTitle,
            'docType' => $docType,
            'owner' => $owner,
            'document' => $document,
            'recipients' => $recipients,
        ];

        $response = $this->clientRequest($endpoint, 'POST', $data);

        return $response;
    }


    /**
     * @param $token String Document token from Upload API
     * @return mixed
     * @throws Exception
     */
    public function getDocumentStatus($token)
    {
        $endpoint = $this->baseUrl() . '/document/status/'.$token;
        $data = [
            'token' => $token
        ];

        $response = $this->clientRequest($endpoint, 'GET', $data);

        return $response;
    }
}
