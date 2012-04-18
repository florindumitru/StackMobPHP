<?php

require_once('OAuth.php');

define('CONSUMER_KEY', 'YOUR-CONSUMER-KEY');
define('CONSUMER_SECRET', 'YOUR-CONSUMER-SECRET');

define('API_URL_GET', 'http://api.mob1.stackmob.com/api/0/YOURAPP');
define('API_URL_PUT', 'http://api.mob1.stackmob.com');
define('API_URL_POST', 'http://api.mob1.stackmob.com/api/0/YOURAPP');
define('API_URL_DELETE', 'http://api.mob1.stackmob.com/api/0/YOURAPP');




class StackMob { 

  	
	var $consumer;

  	function __construct() {
		$this->consumer = new OAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET, NULL);
 	} 

	function get($entityName, $entityId = NULL){
		
		
		$http_method = GET;
		$endpoint = API_URL_GET."/$entityName";
		$params = array();

		if ($entityId){
			$params[$entityName.'_id'] = $entityId;
			$profileStr = http_build_query($params);
			$endpoint .='?'.$profileStr;
		}
		// Setup OAuth request - Use NULL for OAuthToken parameter
		$request = OAuthRequest::from_consumer_and_token($this->consumer, NULL, $http_method, $endpoint, $params);
		// Sign the constructed OAuth request using HMAC-SHA1 - Use NULL for OAuthToken parameter
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, NULL);
		// Extract OAuth header from OAuth request object and keep it handy in a variable
		$oauth_header = $request->to_header();
		$response = $this->send_request($request->get_normalized_http_method(), $endpoint, $oauth_header, $params);
		return $response;
	}

	function post($entityName, $entityObj){
		
		$http_method = POST;
		$endpoint = API_URL_POST."/$entityName";
		
		$profileStr = http_build_query($entityObj);
		$endpoint .='?'.$profileStr;
			
		
		$request = OAuthRequest::from_consumer_and_token($this->consumer, NULL, $http_method, $endpoint, '');
		// Sign the constructed OAuth request using HMAC-SHA1 - Use NULL for OAuthToken parameter
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, NULL);
		// Extract OAuth header from OAuth request object and keep it handy in a variable
		$oauth_header = $request->to_header();
		$response = $this->send_request($request->get_normalized_http_method(), $endpoint, $oauth_header, $entityObj);
		return $response;
	}
	
	function delete($entityName, $entityId){
		$http_method = DELETE;
		$endpoint = API_URL_DELETE."/$entityName";
		$params = array();
		$params[$entityName.'_id'] = $entityId;
		$profileStr = http_build_query($params);
		$endpoint .='?'.$profileStr;
		
		// Setup OAuth request - Use NULL for OAuthToken parameter
		$request = OAuthRequest::from_consumer_and_token($this->consumer, NULL, $http_method, $endpoint, $params);
		// Sign the constructed OAuth request using HMAC-SHA1 - Use NULL for OAuthToken parameter
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, NULL);
		// Extract OAuth header from OAuth request object and keep it handy in a variable
		$oauth_header = $request->to_header();
		$response = $this->send_request($request->get_normalized_http_method(), $endpoint, $oauth_header, $params);
		return $response;
	}
	
	
	
	function put($entityName, $entityObj){
		$http_method = PUT;
		$endpoint = API_URL_PUT.'/'.$entityName.'/'.$entityObj[$entityName.'_id'];
		unset($entityObj[$entityName.'_id']);
		$request = OAuthRequest::from_consumer_and_token($this->consumer, NULL, $http_method, $endpoint, '');
		// Sign the constructed OAuth request using HMAC-SHA1 - Use NULL for OAuthToken parameter
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, NULL);
		// Extract OAuth header from OAuth request object and keep it handy in a variable
		$oauth_header = $request->to_header();
		//var_dump($oauth_header);
		$response = $this->send_request($request->get_normalized_http_method(), $endpoint, $oauth_header, $entityObj);
		return $response;
	}



	function send_request($http_method, $url, $auth_header=null, $postData=null) {  

	  $curl = curl_init($url);  
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
	  curl_setopt($curl, CURLOPT_FAILONERROR, false);  
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	  switch($http_method) {  
	    case 'GET':  
			if ($auth_header) {  
				curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));   
			}  
		break;  
	    case 'POST':  
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.stackmob+json; version=0', $auth_header));   
			curl_setopt($curl, CURLOPT_POST, 1);                                         
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
		break;  
	    case 'PUT':
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/vnd.stackmob+json;",
					'Content-Length: '.strlen(json_encode($postData)),
					"Accept: application/vnd.stackmob+json; version=0",
					$auth_header));
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);  
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));  
		break;  
	    case 'DELETE':
				curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));   
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);   
		break;  
	  }  
	  $response = curl_exec($curl);  
	  if (!$response) {  
	    $response = curl_error($curl);  
	  }  
	  curl_close($curl);  
	  return $response;  
	}

}
?>