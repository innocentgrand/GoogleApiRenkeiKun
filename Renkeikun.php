<?php
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
		error_log(var_export($body, true));
		if (empty($body['body'])) {
			throw Exception("not token");
		}
		return json_decode($body['body'], true);
	}

}


class JWT {

	public $_support =  array(
		"HS256" => array("hash_hmac", "sha256"),
		"HS512" => array("hash_hmac", "sha512"),
		"HS384" => array("hash_hmac", "sha384"),
		"RS256" => array("openssl", "sha256"),
		"RS384" => array("openssl", "sha384"),
		"RS512" => array("openssl", "sha512"),
	);

	protected $_key;
	
	function __construct($key) {
		$this->_key = $key;
	}

	public function encode($body, $header = array("typ" => "JWT", "alg" => "RS256")) {
		$iat = time();
		$exp = $iat + 3600;
		
		$head = $this->urlsafeB64Encode(json_encode($header));
		$body['iat'] = $iat;
		$body['exp'] = $exp;
		$payload = $this->urlsafeB64Encode(json_encode($body));

		$signing = $this->sign($head . "." . $payload);

		return $head . "." . $payload . "." . $this->urlsafeB64Encode($signing);

	}

	protected function sign($msg, $alg = 'RS256') {
		if (empty($this->_support[$alg])) {
			throw new Exception("error no JWT algorithm");
		}
		list($func, $algorithm) = $this->_support[$alg];
		if ($func == "hash_hmac") {
			return hash_hmac($algorithm, $msg, $this->_key, true);
		}
		else if ($func == "openssl") {
			$s = '';
			$success = openssl_sign($msg, $s, $this->_key, $algorithm);
			if ($success) {
				return $s;
			}
			else {
				throw new Exception("open ssl sign error.");
			}
		}
	}

	public function urlsafeB64Decode($input){ 
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}

	public function urlsafeB64Encode($input) {
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}

}


class HttpClient {

	protected $_url;

	function __construct($url = null) {
		$this->_url = $url;
	}

	public function get($data = array(), $head = array()) {
		
		if ($data) {
			$getd = http_build_query($data, "". "&");
		}
		if ($head) {
			$sendHeader = $head;
		}
		else {
			$sendHeader = array(
           		'Cache-Control: no-store',
				"Content-Type: application/x-www-form-urlencoded",
			);
		}
			
		
		$context = array(
			"http" => array(
				"protocol_version" => "1.1",
				"method" => "GET",
				"header" => implode("\r\n", $sendHeader),
				"ignore_errors" => true
			)
		);
		
		if ($getd) {
			$response = file_get_contents($this->_url . "?{$getd}", false, stream_context_create($context));
		}
		else {
			$response = file_get_contents($this->_url, false, stream_context_create($context));
		}	
		$pos = strpos($http_response_header[0], '200');
		if ($pos === false) {
			return array(
				'status' => 'error',
				'error' => $http_response_header[0],
				'responce_header' => implode("\n", $http_response_header),
				'body' => $response,
			);
		}
		else {
			return array(
				'status' => 'success',
				'body' => $response
			);
		}

	}
	
	public function post($data, $head = array() , $modeJson = false) {
		$postd = http_build_query($data, "", "&");
		if ($head) {
			$sendHeader = $head;
			if ($modeJson) {
				$postd = $data;
			}
			else {
				$sendHeader[] = "Content-Length: " . strlen($posted);
			}
		}
		else {
			$sendHeader = array(
            	'Cache-Control: no-store',
				"Content-Type: application/x-www-form-urlencoded",
				"Content-Length: " . strlen($postd),
			);
		}

		$context = array(
			"http" => array(
				"protocol_version" => "1.1",
				"method" => "POST",
				"header" => implode("\r\n", $sendHeader),
				"content" => $postd,
				"ignore_errors" => true
			)
		);
		error_log(var_export($context, true));

		$response = file_get_contents($this->_url, false, stream_context_create($context));
		$pos = strpos($http_response_header[0], '200');
		if ($pos === false) {
			return array(
				'status' => 'error',
				'error' => $http_response_header[0],
				'responce_header' => implode("\n", $http_response_header),
				'body' => $response,
			);
		}
		else {
			return array(
				'status' => 'success',
				'body' => $response
			);
		}
		
	}

}
