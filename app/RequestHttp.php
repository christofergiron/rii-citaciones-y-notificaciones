<?php

namespace App;

use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class RequestHttp
{

  const ENDPOINTS_YAML = '../config/endpoints.yml';
  protected $endpoints;
  protected $client;
  private $log;

  public function __construct()
  {
      $this->log = new \Log;
      $this->client = new Client;
      $this->endpoints = Yaml::parse(file_get_contents(self::ENDPOINTS_YAML));
  }

  protected function err_msg($msg, $params) {
    $response = new \stdClass();
    $response->code = 500;
    $response->msg = "Error: ".$msg;
    $response->contents = "";
    $response->params = $params;
    return $response;
  }

  public function send($params) {
    $response = new \stdClass();
    if (is_null($params)) {
      return $this->err_msg("params is null", $params);
    }

    if (!(    property_exists($params, 'endpoint')
          //  and property_exists($params, 'body')
           and property_exists($params, 'method')
           and property_exists($params, 'service')
        )){
      return $this->err_msg("missing properties in params",$params);
    }

    $request_body = array('json'=>'{}');
    if (property_exists($params, 'body')) {
      $request_body = array('json'=> $params->body);
    }
    $endpoint = $this->endpoints[$params->endpoint][$params->service];

    # check if $token is needed, token is optional
    if (property_exists($params, 'token')) {
      $this->client = new Client(['headers'=> ['Accept'=>'application/json', 'Authorization' => "Bearer {$params->token}"] ]);
      $this->log::alert("Bearer {$params->token}");
    }

    try {
      $res = $this->client->request($params->method, $endpoint, $request_body);
      $response->contents = $res->getBody()->getContents();
      $response->code = $res->getStatusCode();
      $response->msg = "Ok";
    } catch (RequestException $e) {
      $this->log::error(json_encode($e));
      return $this->err_msg("Exception: ".$e,$params);
    }
    return $response;
  }
}
