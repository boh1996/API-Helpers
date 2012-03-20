<?php
/**
 * This wrapper is used to access the masterbranch api
 * @package Masterbranch
 * @category Social API's
 * @version 1.0
 * @author Illution <support@illution.dk>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
class Masterbranch{

	/**
	 * The api key for masterbranch
	 * @var string
	 * @access private
	 * @since 1.0
	 */
	private $_api_key = NULL;

	/**
	 * The masterbranch api url
	 * @var string
	 * @access private
	 * @since 1.0
	 */
	private $_api_url = "https://masterbranch.com/api/1.0/user/show?email={USER_EMAIL}&authtoken={USER_API_TOKEN}";

	/**
	 * This is the constructor it can be used to set the api key
	 * @param string $api_key The masterbranch api key
	 * @access public
	 * @since 1.0
	 */
	public function Masterbranch($api_key = NULL){
		if(!is_null($api_key)){
			self::set_api_key($api_key);
		}
	}

	/**
	 * This function gets the user information from the api and returns at as a std object
	 * @param  string $user_email The email of the user to get information of
	 * @param boolean $alternative If this flag is set to true, then file_get_contents
	 * is used instead of cURL
	 * @return boolean|object FALSE if failed and an object containing the data if success 	
	 * @access public
	 * @since 1.0
	 */
	public function get_user_information($user_email = NULL,$alternative = false){
		if(!is_null($user_email) && !is_null($this->_api_key) && !is_null($this->_api_url)){
			$request_url = str_replace("{USER_EMAIL}", $user_email, str_replace("{USER_API_TOKEN}", $this->_api_key, $this->_api_url));
			if($alternative){
				return json_decode(self::_webRequestFile($request_url));
			} else {
				return json_decode(self::_webRequestCurl($request_url));
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function makes a curl request, to $url
	 * @param  string $url The request url
	 * @return boolean|string
	 * @access private
	 * @since 1.0
	 */
	private function _webRequestCurl($url = NULL){
		if(!is_null($url)){
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($curl, CURLOPT_TIMEOUT, 10)

			$raw = curl_exec($curl);
			if(!is_null($raw) && $raw != ""){
				return $raw;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function uses file get contents to get the content of a file
	 * @param  string $url The url to get the file contents of
	 * @return boolean|string
	 * @access private
	 * @since 1.0
	 */
	private function _webRequestFile($url = NULL){
		if(!is_null($url)){
			$raw = file_get_contents($url);
			if(!is_null($raw) && $raw != ""){
				return $raw;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Use this function to set the masterbranch api key
	 * @param string $api_key The masterbranch api key
	 * @return boolean The result of this function, if it was a success TRUE is returned
	 * @access public
	 * @since 1.0
	 */
	public function set_api_key($api_key = NULL){
		if(!is_null($api_key)){
			$this->_api_key = $api_key;
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>