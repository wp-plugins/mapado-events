<?php

namespace Mapado\Sdk;

use GuzzleHttp\Client as HttpClient;
use Mapado\Sdk\Client;
use Mapado\Sdk\Model\AccessToken;
use Mapado\Sdk\Transformer;

class Client
{
    /**
     * accessToken
     *
     * @var AccessToken
     * @access private
     */
    private $accessToken;

    /**
     * rubric
     *
     * @var Client\RubricClient
     * @access public
     */
    public $rubric;

    /**
     * user
     *
     * @var Client\UserClient
     * @access public
     */
    public $user;

    /**
     * activity
     *
     * @var Client\ActivityClient
     * @access public
     */
    public $activity;

    /**
     * address
     *
     * @var Client\AddressClient
     * @access public
     */
    public $address;

    /**
     * favorites
     *
     * @var Client\FavoriteClient
     * @access public
     */
    public $favorites;

    /**
     * userList
     *
     * @var Client\UserListClient
     * @access public
     */
    public $userList;

    /**
     * __construct
     *
     * @param AccessToken $accessToken
     * @access public
     */
    public function __construct(HttpClient $http, AccessToken $accessToken)
    {
        $this->http =  $http;
        $this->accessToken = $accessToken;

        // create transformers
        $addressTransformer = new Transformer\AddressTransformer();
        $activityTransformer = new Transformer\ActivityTransformer($addressTransformer);
        $rubricTransformer = new Transformer\RubricTransformer();
        $userTransformer = new Transformer\UserTransformer();
        $favoriteTransformer = new Transformer\FavoriteTransformer($activityTransformer);

        // create client
        $this->rubric = new Client\RubricClient($this->http, $accessToken, $rubricTransformer);
        $this->user = new Client\UserClient($this->http, $accessToken, $userTransformer);
        $this->activity = new Client\ActivityClient($this->http, $accessToken, $activityTransformer);
        $this->address = new Client\AddressClient($this->http, $accessToken, $addressTransformer);
        $this->favorites = new Client\FavoriteClient($this->http, $accessToken, $favoriteTransformer);
        $this->userList = new Client\UserListClient($this->http, $accessToken, $activityTransformer);
    }

    /**
     * createClient
     *
     * @static
     * @access public
     * @return Client
     */
    public static function createClient(AccessToken $token)
    {
        $client = new HttpClient();
        return new self($client, $token);
    }
}
