<?php
include "request_helpers.php";
/**
 * @package		Crysis 2 API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://c2stats.com/api/
 */
class Crysis2{

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * The url to the Crysis 2 Stats API
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	private $_api_url = "http://c2stats.com/api/";

	/**
	 * The platform to look in,
	 * at the moment only pc is available
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $platform = "pc";

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function Crysis2(){
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function fetches information on the choosen players
	 * @param  string|array $players A list of players or just a player name
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function getList($players = NULL){
		if(!is_null($players)){
			if(is_array($players)){
				$players = implode(",", $players);
			}
			$url = $this->_api_url.$this->platform;
			$request_string = "request=getlist&players=".$players;
			$data = ($this->_webRequest)? webRequest($url,$request_string) : alternativeRequest($url,"?".$request_string);
			if(!is_null($data) && $data !== false && count($data->players) > 0){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}
?>