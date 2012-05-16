<?php
/**
 * This function uses cURL to get data from a url
 * @param  string $url      The url to get data from
 * @param  string $postdata The post string to send
 * @return object|boolean
 * @since 1.0
 * @access private
 */
function webRequest($url,$postdata = NULL){
	$ch = curl_init($url);
	if(!is_null($postdata)){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	}
	curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($ch,CURLOPT_USERAGENT,'BF3StatsAPI/0.1');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$raw = curl_exec($ch);
	$statuscode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close($ch);
	$data = json_decode($raw);
	if(is_null($data)){
		return $raw;
	}
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
function str_chop_lines($str, $lines = 4) {
    return implode("\n", array_slice(explode("\n", $str), $lines));
}

/**
 * This function creates a random string
 * @param  integer $length The length of the string
 * @return string
 * @since 1.1
 */
function getRandomString($length = 6) {
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
function isCurlInstalled() {
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
function alternativeRequest($url,$getstring = NULL){
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
function signRequest($data,$secret){
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
function crateSignedParams($data,$secret,&$string){
	$urlbase64 = array('+' => '-','/' => '_','=' => '');
	$data = strtr(base64_encode(json_encode($data)),$urlbase64);

	$return = array();
	$return['data'] = $data;
	$return['sig'] = strtr(base64_encode(hash_hmac('sha256',$data,$secret,true)),$urlbase64);
	$string = "data=".$data."&sig=".strtr(base64_encode(hash_hmac('sha256',$data,$secret,true)),$urlbase64);
	return $return;
}
?>