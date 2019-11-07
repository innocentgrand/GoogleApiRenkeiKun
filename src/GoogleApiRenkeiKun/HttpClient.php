<?php

namespace Innocentgrand\GoogleApiRenkeiKun;


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
		if (is_array($data) || is_object($data)) {
			$postd = http_build_query($data, "", "&");
		}
		else {
			$postd = $data;
		}
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
