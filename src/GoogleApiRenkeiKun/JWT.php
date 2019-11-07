<?php

namespace Innocentgrand\GoogleApiRenkeiKun;


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
