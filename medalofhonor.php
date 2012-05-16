<?php
include "request_helpers.php";
/**
 * @package		MOH Stats API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 */
class MedalOfHonor{

	/**
	 * The Medal of Honor Tier 1 stats, api url
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $_api_url = "http://api.mohstats.com/api/";

	/**
	 * An array containing the players to request from the api
	 * @var array
	 * @since 1.0
	 * @access public
	 */
	public $players = NULL;

	/**
	 * An array containing the profile fields to request
	 * @var array
	 * @since 1.0
	 * @access public
	 */
	public $fields = NULL;

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * An array containing the available api platforms
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_platforms = array(
		"pc",
		"ps3",
		"360"
	);

	/**
	 * The platform to request the data for
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $platform = "pc";

	/**
	 * An array containig all the available stats fields
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_fields = array(
		"all",
		"basic",
		"general",
		"rankings",
		"kits",
		"teams",
		"gamemodes",
		"medals",
		"ribbons",
		"achievements",
		"weapons",
		"descs",
		"imgs"
	);

	/**
	 * This function sets the platform if the platform is available
	 * @param  string $platform The platform to use
	 * @since 1.0
	 * @access public
	 */
	public function platform($platform){
		$platform = strtolower($platform);
		if(in_array($platform, $this->_platforms)){
			$this->platform = $platform;
		}
	}

	/**
	 * This functíon is the class constructor it checks if cURL is installed
	 * @since 1.0
	 * @access public
	 */
	public function MedalOfHonor(){
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This functíon is used to add fields to the fields array,
	 * if not input is defined all the fields are added
	 * @param  array|string $fields The field(s) to add
	 * @since 1.0
	 * @access public
	 */
	public function fields($fields = NULL){
		if(!is_null($fields)){
			if(!is_array($fields)){
				$fields = array($fields);
			}
			if(!is_null($this->fields) && is_array($this->fields)){
				$this->fields = array_merge($this->fields,$fields);
			} else {
				$this->fields = $fields;
			}
		} else {
			$this->fields = $this->_fields;
		}
	}

	/**
	 * This function is used to set the players array
	 * @param  array|string $players The player or players to add
	 * @since 1.0
	 * @access public
	 */
	public function players($players){
		if(!is_array($players)){
			$players = array($players);
		}
		if(!is_null($this->players) && is_array($this->players)){
			$this->players = array_merge($this->players,$players);
		} else {
			$this->players = $players;
		}
	}

	/**
	 * This function uses to MOH stats API to get the data about some players
	 * @since 1.0
	 * @access public
	 * @return object|boolean
	 */
	public function getPlayers(){
		if(!is_null($this->players) && !is_null($this->fields)){
			$url = $this->_api_url.$this->platform;
			$request_string = 'players='.implode(",", $this->players).'&fields='.implode("", $this->fields);
			if($this->_webRequest){
				$data = webRequest($url,$request_string);
			} else {
				$request_string = "?".$request_string; 
				$data = alternativeRequest($url,$request_string);
			}
			return $data;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function returns the API stats of the API
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function globalStats(){
		$url = $this->_api_url = $url = $this->_api_url.$this->platform."?globalstats";
		if($this->_webRequest){
			$data = webRequest($url);
		} else {
			$data = alternativeRequest($url);
		}		
		return $data;
	}

	/**
	 * This function searches for a player, using the API
	 * @param  string $player The player name to search for
	 * @return object|boolean
	 * @since 1.0
	 * @access public
	 */
	public function search($player = NULL){
		if(!is_null($player)){
			$url = $this->_api_url.$this->platform;
			$request_string = 'search='.$player;
			if($this->_webRequest){
				$data = swebRequest($url,$request_string);
			} else {
				$request_string = "?".$request_string; 
				$data = alternativeRequest($url,$request_string);
			}
			return $data;
		} else {
			return FALSE;
		}
	}
}
?>