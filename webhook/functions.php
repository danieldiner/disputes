<?php

require_once("config.php");
header('content-type: text/html; charset: utf-8');

#This function gets the OAuth2 Access Token which will be valid for 28800 seconds
function get_access_token() {

  global $api_clientId, $api_secret, $host;

  $postFields = 'grant_type=client_credentials';
  $url = $host.'/v1/oauth2/token';

  // curl documentation -> http://php.net/manual/en/book.curl.php

  $curl = curl_init($url); // Initializes a new session and return a cURL handle for use with the curl_setopt(), curl_exec(), and curl_close() functions.

  // curl_setopt documentation -> http://php.net/manual/en/function.curl-setopt.php

  curl_setopt($curl, CURLOPT_POST, true); // TRUE to do a regular HTTP POST.
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // FALSE to stop cURL from verifying the peer's certificate.
  curl_setopt($curl, CURLOPT_USERPWD, $api_clientId . ":" . $api_secret); // A username and password formatted as "[username]:[password]" to use for the connection.
  curl_setopt($curl, CURLOPT_HEADER, false); // TRUE to include the header in the output.
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
  curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields); //The full data to post in a HTTP "POST" operation.
  #curl_setopt($curl, CURLOPT_VERBOSE, TRUE); // TRUE to output verbose information. Writes output to STDERR, or the file specified using CURLOPT_STDERR.

  $response = curl_exec( $curl ); // Returns TRUE on success or FALSE on failure. However, if the CURLOPT_RETURNTRANSFER option is set, it will return the result on success, FALSE on failure.

  if (empty($response)) {
    // Some kind of an error happened
    die(curl_error($curl)); // The die() function prints a message and exits the current script. This function is an alias of the exit() function.
    curl_close($curl); // Closes a cURL session and frees all resources. The cURL handle, $curl, is also deleted.
  } else {
    $info = curl_getinfo($curl); // Gets information about the last transfer.
    curl_close($curl); // Closes a cURL session and frees all resources. The cURL handle, $curl, is also deleted.
    
    if ($info['http_code'] != 200 && $info['http_code'] != 201 ) {
      echo "Received error: " . $info['http_code']. "\n";
      echo "Raw response:".$response."\n";
      die();
    }
    
  }

  // Convert the result from JSON format to a PHP array
  $jsonResponse = json_decode( $response );
  return $jsonResponse->access_token;

}

#This function gets dispute details
function get_dispute_details( $access_token, $disputeId ) {
 global $host, $access_token, $disputeId;
  $url = $host.'/v1/customer/disputes/'.$disputeId;
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // FALSE to stop cURL from verifying the peer's certificate.
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST,'TLSv1');
  curl_setopt($curl, CURLOPT_HEADER, false); // TRUE to include the header in the output.
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$access_token,
    'Accept: application/json',
    'Content-Type: application/json'
  ));

  $response = curl_exec( $curl );

  if (empty($response)) {
    // Some kind of an error happened
    die(curl_error($curl));
    curl_close($curl);
  } else {

    $info = curl_getinfo($curl);
    curl_close($curl); // close cURL handler
    
    if($info['http_code'] != 200 && $info['http_code'] != 201 ) {
      echo "Received error: " . $info['http_code']. "\n";
      echo "Raw response:".$response."\n";
      die();
    }

  }

  // Convert the result from JSON format to a PHP array
  $jsonResponse = json_decode($response);
  return $jsonResponse;

}

#This function gets dispute details
function accept_claim ($access_token, $disputeId ) {
 global $host, $access_token, $disputeId;
  $url = $host.'/v1/customer/disputes/'.$disputeId.'/accept-claim';
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // FALSE to stop cURL from verifying the peer's certificate.
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST,'TLSv1');
  curl_setopt($curl, CURLOPT_HEADER, false); // TRUE to include the header in the output.
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$access_token,
    'Accept: application/json',
    'Content-Type: application/json'
  ));

  $response = curl_exec( $curl );

  if (empty($response)) {
    // Some kind of an error happened
    die(curl_error($curl));
    curl_close($curl);
  } else {

    $info = curl_getinfo($curl);
    curl_close($curl); // close cURL handler
    
    if($info['http_code'] != 200 && $info['http_code'] != 201 ) {
      echo "Received error: " . $info['http_code']. "\n";
      echo "Raw response:".$response."\n";
      die();
    }

  }

  // Convert the result from JSON format to a PHP array
  $jsonResponse = json_decode($response);
  return $jsonResponse;

}




