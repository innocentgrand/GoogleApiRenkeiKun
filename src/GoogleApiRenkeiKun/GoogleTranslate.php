<?php

namespace Innocentgrand\GoogleApiRenkeiKun;

class GoogleTranslate {
    const SCOPE = "https://www.googleapis.com/auth/cloud-translation";
    const BASEURL = "https://translation.googleapis.com/v3beta1/projects/";
    protected $_sourceJson;
    protected $_json;

    protected $_source;
    protected $_target;

    function __construct($json, $source = "ja", $target = "en") {
		$this->_sourceJson = $json;
		if (json_decode($this->_sourceJson) === null) {
			if (file_exists($this->_sourceJson)) {
				$file = file_get_contents($json);
				$json = $file;
				$this->_sourceJson = $json;
			}
			else {
				throw new Exception("File does not exist.");
			}
		}
		$this->_json = json_decode($this->_sourceJson, true);
        $this->_source = $source;
        $this->_target = $target;
    }

    public function translation($text) {
        $google = new GoogleOAuth2(self::SCOPE, $this->_sourceJson);
        $token = $google->token();
        $url = self::BASEURL . $this->_json['project_id'] . ":translateText";
        $client = new HttpClient($url);
        $httpHeader[] = "Authorization: Bearer " . $token['access_token'];
        $httpHeader[] = "Content-type: application/json; charset=utf-8";

        $request = array(
            "sourceLanguageCode" => $this->_source,
            "targetLanguageCode" => $this->_target,
            "contents" => $text
        );
        $reqjson = json_encode($request);

        $translate = $client->post($reqjson, $httpHeader, true);
        $translateArr = json_decode($translate['body'], true);

        return $translateArr;
    }	
}
