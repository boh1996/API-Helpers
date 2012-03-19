<?php
/**
 * @name		Google URL Shortener API Helper
 * @author		Bo Thomsen
 * @company		Illution
 * @package		URL Shortener Helpers
 * @version 	1.2
 * @url			http://illution.dk
 * @license		MIT License
 * @date		16/12-2011
 */
class Google_Url_Shortener{

	/* User Data */
	private $Api_Key = NULL; //The storage for the Google Public API key
	private $Start_Token = NULL; //An experimental storage for later use
	
	/* Internals */
	private $Expand_Url = 'https://www.googleapis.com/urlshortener/v1/url'; //The API url for the expand short url api
	private $Short_Url = 'https://www.googleapis.com/urlshortener/v1/url'; //The shorten url API url
	private $Projections = array('FULL','ANALYTICS_CLICKS','ANALYTICS_TOP_STRINGS'); //The different Projektion Values in some cases isn't ANALYTICS_CLICKS available

	/* The constructor
	| @param {String}	$Api_Key - The Google Public API key
	| @access public
	*/
	public function Google_Url_Shortener($Api_Key = NULL){
		self::Set_Api_Key($Api_Key);
	}

	/* A function to set the API key
	| @param {String}	$Key - The Google Public API key
	| @access public
	*/
	public function Set_Api_Key($Key = NULL){
		if(!is_null($Api_Key)){
			$this->Api_Key = $Api_Key;
		}	
	}
	
	/* This function returns the expnaded url
	| @param {String}	$Short_Url - The goo.gl shortened url
	| @return {String}	- The expanded url
	| @access public
	*/
	public function Expand_url($Short_Url = NULL,$Projection = NULL){
		if(!is_null($Short_Url)){
			$String = $this->Expand_Url.'?shortUrl='.$Short_Url;
			if(!is_null($Projection) && in_array($Projection,$this->Projections)){
				$String .= '&projection='.$Projection;
			}
			$JSON = file_get_contents($String);
			$Data = json_decode($JSON,true);
			if(is_null($Projection)){
				return $Data['longUrl'];
			}
			else{
				return json_decode($JSON,true);
			}
		}
	}
	
	/* This function perform the API request and returns raw data in array or json or only and as standard the shortened url as String
	| @param {String}	$Long_Url - The url to be shortened
	| @param {String}	$Raw - If is set to 'true' the RAW json_decoded data will be returned and if its set to 'json' the raw json response will be returned
	| @return {Array} or {String}	- Array will be returned if the Raw is set to true else is this return a json string or a normal string with the shortened url
	| @access public
	*/
	public function Shorten_Url($Long_Url = NULL,$Raw = 'false'){
		if(!is_null($Long_Url)){
			if(!is_null($this->Api_Key)){
				$Url = $this->Short_Url.'?key='.$this->Api_Key;
			}
			else{
				$Url = $this->Short_Url;	
			}
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $Url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('longUrl' => $Long_Url)));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($curl);
			curl_close($curl);
			if($Raw == 'false'){
				$Data = json_decode($response,TRUE);
				return $Data['id'];
			}
			else{
				if($Raw == 'true'){
					return json_decode($response,TRUE);
				}
				else if($Raw == 'json'){
					return $response;
				}
			}
		}
	}
	
	/* This function returnes the full API response in array format
	| @param {String}	$Long_Url - The url that should be shortened
	| @return {Array}	- The RAW array response
	| @access public
	*/
	public function Get_Raw_Data($Long_Url = NULL){
		if(!is_null($Long_Url)){
			return self::Shorten_Url($Long_Url,'false');
		}
	}
	
	/* This function returnes the RAW json response from the Google shorten API
	| @param {String}	$Long_Url - The url that should be shortened
	| @return {Array}	- The RAW JSON response
	| @access public
	*/
	public function Get_Raw_Json($Long_Url = NULL){
		if(!is_null($Long_Url)){
			return self::Shorten_Url($Long_Url,'json');
		}
	}
}
?>