<?php
/**
 * This function uses cURL to get data from a url
 * @param  string $url      The url to get data from
 * @param  string $postdata The post string to send
 * @param array $headers An optional field for headers
 * @return object|boolean
 * @since 1.0
 * @access private
 */
function webRequest ( $url, $postdata = NULL, $get = false, $headers = NULL ) {

	//If the operation is get include the get string
	if(!is_null($postdata)){
		if($get && strpos($url, "?") === false){
			$url .= "?".$postdata;
		} else {
			$url .= $postdata;
		}
	}
	$ch = curl_init($url);

	//If it's a post operation set the post data
	if(!$get && !is_null($postdata)){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	}

	//If headers are set include them
	if(!is_null($headers) && is_array($headers) && count($headers) > 0){
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
	}

	//Set user agent and other cURL options
	curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($ch,CURLOPT_USERAGENT,'BF3StatsAPI/0.1');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//Execute and get content
	$raw = curl_exec($ch);
	$statuscode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close($ch);

	//If XML convert using SimpleXML else use json decode
	if(strpos($raw, "<?xml") === false && substr($raw, 0, 1) != "<" ){
		$data = json_decode($raw);
	} else {
		$data = simplexml_load_string($raw);
		return $data;
	}

	//If the conversion was a failure return the raw data else return FALSE
	if(is_null($data) && count($data) > 0){
		return $raw;
	} else if(is_null($data)){
		return FALSE;
	}

	//If the operation was a succes return the data else return false
	if($statuscode == 200){
		return $data;
	} else {
		return FALSE;
	}
}

/**
 * This function removes a given number of lines
 * @param  string  $str   The string to chop in
 * @param  integer $lines The number of lines to chop
 * @return string
 */
function str_chop_lines ( $str, $lines = 4 ) {
    return implode("\n", array_slice(explode("\n", $str), $lines));
}

/**
 * This function creates a random string
 * @param  integer $length The length of the string
 * @return string
 * @since 1.1
 */
function getRandomString ( $length = 6 ) {
    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ+-*#&@!?";
    $validCharNumber = strlen($validCharacters);
 
    $result = "";
 
    for ($i = 0; $i < $length; $i++) {
        $index = mt_rand(0, $validCharNumber - 1);
        $result .= $validCharacters[$index];
    }
 
    return $result;
}

/**
 * This function checks if the cURL extension is loaded
 * @return boolean
 * @since 1.0
 * @access private
 */
function isCurlInstalled () {
	if  (in_array  ('curl', get_loaded_extensions())) {
		return true;
	}
	else{
		return false;
	}
}

/**
 * This function used file_get_contens to get the data from a url
 * @param  string $url       The url to get data from
 * @param  string $getstring The get string with an appended ?
 * @return object|boolean
 * @since 1.0
 * @access private
 */
function alternativeRequest ( $url, $getstring = NULL ) {
	if(!is_null($getstring)){
		$url = $url.$getstring;
	}
	$data = file_get_contents($url);
	$data = json_decode($data);
	if(!is_null($data)){
		return $data;
	} else {
		return FALSE;
	}
}

/**
 * This function uses a simple request signing system
 * @param  array $data   The request data
 * @param  string $secret The privete key to sign with
 * @return string
 */
function signRequest ( $data, $secret ) {
	$urlbase64 = array('+'=>'-','/'=>'_','='=>'');
	return strtr(base64_encode(hash_hmac('sha256',$data,$secret,true)),$urlbase64);
}

/**
 * This function creates a signedRequest params array
 * @param  array $data  The input data
 * @param  string $secret The api secret key
 * @param string &$string A string to store the string version of the request
 * @return array
 */
function crateSignedParams ( $data, $secret, &$string ) {
	$urlbase64 = array('+' => '-','/' => '_','=' => '');
	$data = strtr(base64_encode(json_encode($data)),$urlbase64);

	$return = array();
	$return['data'] = $data;
	$return['sig'] = strtr(base64_encode(hash_hmac('sha256',$data,$secret,true)),$urlbase64);
	$string = "data=".$data."&sig=".strtr(base64_encode(hash_hmac('sha256',$data,$secret,true)),$urlbase64);
	return $return;
}

/**
 * This function implodes a assoc array
 * @param  string $delemiter The delemiter between the key and value
 * @param string $element_delemiter The delemiter the different elements 
 * @param  array $array     The array to implode
 * @return string
 * @since 1.0
 */
function assoc_implode ( $delemiter = "&", $element_delemiter = "=", $array = NULL ) {
	if(!is_null($array) && !is_null($delemiter) && !is_null($element_delemiter)){
		$return = "";
		foreach ($array as $key => $value) {
			$return .= $key . $delemiter . $value.$element_delemiter;
		}
		$return = rtrim($return,$element_delemiter);
		return $return;
	} else {
		return "";
	}
}

/**
 * This function does a binary 
 * hmac_sha1
 * @param  string $key  The hmac password
 * @param  string $data The data to encrypt
 * @return string
 * @since 1.0
 */
function hmacsha1 ( $key, $data ) {
	    $blocksize = 64;
	    $hashfunc = 'sha1';
	    if (strlen($key)>$blocksize)
	        $key = pack('H*', $hashfunc( $key ));
	    $key = str_pad($key, $blocksize, chr(0x00));
	    $ipad = str_repeat(chr( 0x36 ), $blocksize );
	    $opad = str_repeat(chr( 0x5c ), $blocksize );
	    $hmac = pack(
	                'H*',$hashfunc(
	                    ($key ^ $opad).pack(
	                        'H*', $hashfunc(
	                            ($key ^ $ipad).$data
	                        )
	                    )
	                )
	            );
	    return $hmac;
}
/**
 * This function encodes the url with the right encoding
 * @param  string|array $input The string to encode
 * @return string|array
 */
function urlencode_rfc3986 ( $input ) {
    if (is_array( $input )) {
        return array_map(array($this, '_urlencode_rfc3986'), $input);
    }
    else if (is_scalar( $input )) {
        return str_replace('+',' ',str_replace('%7E', '~', rawurlencode($input)));
    } else{
        return '';
    }
}

/**
 * This function generates a auth header signature
 * @param  string $url    The url to send too
 * @param  string $verb   The HTTP verb
 * @param  string $secret The secret key
 * @return string
 * @since 1.1
 * @access private
 */
function signature ( $url, $verb, $secret ){
	$string = $verb + "\n" + date("r")+ "\n" + $url + "\n";
	base64_encode( hmacsha1(urlencode_rfc3986( $secret ), $string));
}
?>