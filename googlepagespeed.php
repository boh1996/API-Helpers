<?php
/**
 * This package is an api helper for the Google Page Speed service,
 * it is requestion the data based on your request and return it.
 * @author Illution <support@illution.dk>
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @package Google API's
 * @category Page Speed
 * @subpackage Page Speed
 * @version 1.0
 */
class GooglePageSpeed{

	/**
	 * The Google API Key
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $Api_Key = NULL;

	/**
	 * The Google API Url
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $Api_Url = "https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url={url}&key={key}";

	/**
	 * This function is the constructor, it sets the API key variable
	 * @param string $Api_Key The Google API key
	 * @access public
	 * @since 1.0
	 */
	public function GooglePageSpeed($Api_Key = NULL){
		self::Api_Key($Api_Key);
	}

	/**
	 * This function uses the page speed api to get the data and return it
	 * @param string $Url The url to analyze
	 * @return Boolean||Object This function returns a standard class if succes or a boolean if failed
	 * @access public
	 * @since 1.0
	 */
	public function PageSpeed($Url){
		if(!is_null($this->Api_Key)){
			$RequestUrl = str_replace("{key}", $this->Api_Key, $this->Api_Url);
			$RequestUrl = str_replace("{url}", $Url, $RequestUrl);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $RequestUrl);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$Raw = curl_exec($curl);
			curl_close($curl);
			$Data = json_decode($Raw);
			if(!isset($Data->error)){
				return $Data;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * This function sets the Api_Key variable
	 * @param string $Api_Key The Google API key
	 * @access public
	 * @since 1.0
	 */
	public function Api_Key($Api_Key = NULL){
		if(!is_null($Api_Key)){
			$this->Api_Key = $Api_Key;
		}
	}
}
?>