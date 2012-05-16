<?php
include "request_helpers.php";
/**
 * @package		Battlefield 2 API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://bf2s.com/api/
 */
class Battlefield2{

	/**
	 * The url to the BF2 stats API
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	private $_api_url = "http://bf2s.com/api/";

	/**
	 * The api key used, for the api request
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $api_key = NULL;

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function Battlefield2(){
		$this->_webRequest = isCurlInstalled();
		$this->api_key = getRandomString();
	}

	/**
	 * This function fetches API information for a specific
	 * @param  integer|string $player The BF2 player id of the player to search for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function player($player){
		$url = $this->_api_url.$this->api_key."/player/".$player;
		$data = ($this->_webRequest)? webRequest($url) : alternativeRequest($url);
		if(!is_object($data)){
			$data = str_chop_lines($data,1);
			$data = json_decode($data);
		}
		if(!isset($data->error) && !is_null($data) && $data !== false){
			return $data;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function fetches information from the leaderboard of one or more players
	 * @param  string|array $players The player(s) to fetach
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function leaderBoard($players){
		if(is_array($players)){
			$players = implode(",", $players);
		}
		$url = $this->_api_url.$this->api_key."/leaderboard/".$players;
		$data = ($this->_webRequest)? webRequest($url) : alternativeRequest($url);
		if(!is_object($data)){
			$data = str_chop_lines($data,1);
			$data = json_decode($data);
		}
		if(!isset($data->error) && !is_null($data) && $data !== false){
			return $data;
		} else {
			return FALSE;
		}
	}
}
?>