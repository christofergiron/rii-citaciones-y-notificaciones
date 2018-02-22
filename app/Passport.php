<?php

namespace App;

use App\RequestHttp as Client;
use Symfony\Component\Yaml\Yaml;

class Passport
{

  const PASSPORT_API = 'passport-api';
  const LOGIN_SERVICE = "login";
  const REGISTER_SERVICE = 'register';
  const UPDATE_SERVICE = 'update_user';
  const DETAILS_SERVICE = 'details';
  const LIST_ROLES = 'list_roles';
  const USERS_LIST = 'users';
  const LIST_USERS = 'list_users';
  const CREATE_ROL = 'create_rol';
  const UPDATE_ROL = 'update_rol' ;
  const SHOW_ROL = 'show_rol' ;    
  const ASSIGN_ROL = 'assign_rol' ;
  const CHANGE_PASSWORD = 'change_password';
  const AUTH_WORKFLOW_ACTION = 'auth_workflow_action';
  const USER_WORKFLOW_ACTIONS = 'user_workflow_actions';
  const EMAILS = 'emails';
  const POST_METHOD = 'POST';
  const GET_METHOD = 'GET';
  const PUT_METHOD = 'PUT';

  protected $client;

  public function __construct()
  {
      $this->client = new Client;
  }

  public function login($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::LOGIN_SERVICE;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function register($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::REGISTER_SERVICE;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function users_list($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::USERS_LIST;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function list_users($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::LIST_USERS;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function list_roles($params)
  {
      #load $request
      #load $request
      $req = new \stdClass();
      $req->service = self::LIST_ROLES;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function create_rol($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::CREATE_ROL;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function show_rol($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::SHOW_ROL;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function update_rol($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::UPDATE_ROL;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function assign_rol($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::ASSIGN_ROL;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function change_password($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::CHANGE_PASSWORD;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function auth_workflow_action($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::AUTH_WORKFLOW_ACTION;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function user_workflow_actions($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::USER_WORKFLOW_ACTIONS;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function emails($params)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::EMAILS;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function update_user($params, $token)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::UPDATE_SERVICE;
      $req->token = $token;
      $req->body = $params;

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }

  public function details($token)
  {
      #load $request
      $req = new \stdClass();
      $req->service = self::DETAILS_SERVICE;
      $req->token = $token;
      $req->body = "";

      # call service
      $response = $this->process_request($req);

      # return response
      return $response;
  }


    private function process_request($options) {
      #load $request
      $req = new \stdClass();
      $req->endpoint = self::PASSPORT_API;
      $req->service = $options->service;
      $req->body = $options->body;

      switch ($req->service) {
        case self::USERS_LIST:
          $req->method = self::GET_METHOD;
        case self::UPDATE_SERVICE:
          $req->method = self::PUT_METHOD;
        default:
          $req->method = self::POST_METHOD;
          break;
      }

      # add token if available
      if (property_exists($options, 'token')) {
            $req->token = $options->token;
      }

      # call service
      $response = new \stdClass();
      $response = $this->client->send($req);

      # return response
      return $response;
    }

}
