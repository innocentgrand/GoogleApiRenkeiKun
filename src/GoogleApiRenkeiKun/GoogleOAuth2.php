<?php

namespace Innocentgrand\GoogleApiRenkeiKun;

class GoogleOAuth2 {
	const TOKEN_CREDENTIAL_URI = 'https://oauth2.googleapis.com/token';
	protected $_scope;
	protected $_json;
	protected $_token;
	protected $_grantType = "urn:ietf:params:oauth:grant-type:jwt-bearer";
	function __construct($scope, $jsonKey) {
		$this->_scope = $scope;
		$this->_json = json_decode($jsonKey, true);
	}
	public function token() {
		$jwt = new JWT($this->_json['private_key']);
		$jwtData = $jwt->encode(array("iss" =>  $this->_json['client_email'],"aud" => $this->_json['token_uri'],"scope" => $this->_scope));
		$client = new HttpClient($this->_json['token_uri']);
		$body = $client->post(array('grant_type' => $this->_grantType,'assertion' =>$jwtData));
		if (empty($body['body'])) {
			throw Exception("not token");
		}
		return json_decode($body['body'], true);
	}
}
