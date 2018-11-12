<?php

namespace app\Http\Utils;

class GotoMeetingApi{

	private $api_url = 'https://api.getgo.com/';

	function __construct($key=null,$api_secret=null)
	{
		$this->api_key=$key;
	   	$this->api_secret=$api_secret;
	}

	function sendRequest($calledFunction, $headersFields, $method, $postFields=null)
	{
		$requestUrl = $this->api_url.$calledFunction;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $requestUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headersFields);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if($postFields) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		$response = curl_exec($ch);
		
		if(!$response){
			return false;
		}

		$response = json_decode($response,true);
		return $response;
	}

	function postUrlencodedHeaders() {
		$headersFields = array(
			'Content-Type: application/x-www-form-urlencoded',
			'Accept: application/json'
		);
		return $headersFields;
	}

	function postJsonHeaders($token=null) {
		$headersFields = array(
			'Content-Type: application/json',
			'Accept: application/json'
		);
		if($token)
			array_push($headersFields, 'Authorization: OAuth oauth_token='.$token);
		return $headersFields;
	}

	function getJsonHeaders($token=null) {
		$headersFields = array(
			'Accept: application/json'
		);
		if($token)
			array_push($headersFields, 'Authorization: OAuth oauth_token='.$token);
		return $headersFields;
	}

	function getAccessToken($data)
	{
		$api_data = array(
			'grant_type' => "password",
			'user_id' => $data['host_email'],
			'password' => $data['host_password'],
			'client_id' => $this->api_key
		);

		return $this->sendRequest(
			'oauth/access_token',
			$this->postUrlencodedHeaders(),
			'POST',
			http_build_query($api_data)
		);
	}

	function createMeeting($data) {
		$api_data = array(
			"subject" => $data['subject'],
			"starttime" => $data['starttime'],
			"endtime" => $data['endtime'],
			"passwordrequired" => false,
			"conferencecallinfo" => "US: +1 (646) 749-3122\nAccess Code: 240-539-357",
			"timezonekey" => $data['timezonekey'],
			"meetingtype" => "scheduled",
			"coorganizerKeys" => []
		);

		return $this->sendRequest(
			'G2M/rest/meetings',
			$this->postJsonHeaders($data['access_token']),
			'POST',
			json_encode($api_data)
		);
	}
}
