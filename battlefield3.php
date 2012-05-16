<?php
include "request_helpers.php";
/**
 * @package		Battlefield 3 API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://bf3stats.com/api
 */
class Battlefield3{

	/**
	 * The BF3 stats API url
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $_api_url = "http://api.bf3stats.com/";

	/**
	 * The available platforms
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
	 * This array contains the available profile fields
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_options = array(
		"scores",
		"global",
		"nextranks",
		"rank",
		"imgInfo",
		"lastseen",
		"urls",
		"keys",
		"raw",
		"nozero",
		"coop",
		"coopInfo",
		"coopMissions",
		"gamemodes",
		"gamemodesInfo",
		"weapons",
		"weaponsName",
		"weaponsInfo",
		"weaponsOnlyUsed",
		"weaponsUnlocks",
		"weaponsRanking",
		"weaponsStars",
		"equipment",
		"equipmentName",
		"equipmentInfo",
		"equipmentRanking",
		"equipmentOnlyUsed",
		"specializations",
		"specializationsName",
		"specializationsInfo",
		"teams",
		"kits",
		"kitsName",
		"kitsInfo",
		"kitsStars",
		"kitsUnlocks",
		"vehicles",
		"vehiclesName",
		"vehiclesInfo",
		"vehiclesRanking",
		"vehiclesOnlyUsed",
		"vehCats",
		"vehCatsStars",
		"vehCatsUnlocks",
		"vehCatsGroup",
		"vehCatsInfo",
		"awards",
		"awardsName",
		"awardsInfo",
		"awardsAwarded",
		"ranking",
		"rankingInfo",
		"assignments",
		"assignmentsInfo",
		"assignmentsName",
		"clear",
		"index",
		"all",
		"noinfo",
		"nounlocks"
	);

	/**
	 * The selected profile fields
	 * @var array
	 * @since 1.0
	 * @access public
	 */
	public $options = NULL;

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * The current platform to use
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $platform = "pc";

	/**
	 * The API key/identity, it's needed for some api calls
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $api_key = NULL;

	/**
	 * The api secret key, it's needed for some request
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $api_secret = NULL;

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function Battlefield3(){
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function uses the api to get information of the specified players
	 * @param  array|string $players The players to get information of
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function playerList($players = NULL){
		if(is_null($players)){
			return FALSE;
		}
		if(is_array($players)){
			$player_string = "players=".implode(",", $players);
		} else {
			$player_string = "players=".$players;
		}
		$url = $this->_api_url.$this->platform."/playerlist/";
		$request_string = "players".$player_string;
		if(is_array($this->options)){
			$request_string .= "&opt=".implode(",", $this->options);
		}
		$data = ($this->_webRequest)? webRequest($url,$player_string) : alternativeRequest($url,"?".$player_string);
		if(count($data->list) > 0){
			return $data;
		} else{
			return FALSE;
		}
	}
	
	/**
	 * This function is used to add extra options to the array
	 * @param  array $options The options to add
	 * @since 1.0
	 * @access public
	 */
	public function options($options = NULL){
		if(!is_null($options)){
			if(!is_null($this->options)){
				$this->options = array_merge(array("clear"),$options,$this->options);
			} else {
				$this->options = array_merge(array("clear"),$options);
			}
		} else {
			$this->options = array("clear","all");
		}
	}

	/**
	 * This function uses the API to get information on a specific player
	 * @param  string $player The name of the player to search for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function player($player = NULL){
		if(!is_null($player)){
			$url = $this->_api_url.$this->platform."/player/";
			$request_string = "player=".$player;
			$data = ($this->_webRequest)? webRequest($url,$request_string) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found")){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function is used to get information on a server, found by its 32 chars long id
	 * @param  string $id The server id string
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function server($id = NULL){
		if(!is_null($id)){
			$url = $this->_api_url.$this->platform."/server/";
			$request_string = "id=".$id;
			$data = ($this->_webRequest)? webRequest($url,$request_string) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found")){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function is used to get a players dog tags
	 * lost and won
	 * @param  string $player The name of the player
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function dogTags($player = NULL){
		if(!is_null($player)){
			$url = $this->_api_url.$this->platform."/dogtags/";
			$request_string = "player=".$player;
			$data = ($this->_webRequest)? webRequest($url,$request_string) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found" || $data->status == "dogtags")){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function gets the online status of the Stats API
	 * @return object
	 * @since 1.0
	 * @access public
	 */
	public function onlineStats(){
		$url = $this->_api_url."global"."/onlinestats/";
		$data = ($this->_webRequest)? webRequest($url) : alternativeRequest($url);
		return $data;
	}

	/**
	 * This function updates the API's information on a player
	 * @param  string $player The name of the player
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function playerUpdate($player = NULL){
		if(!is_null($player) && !is_null($this->api_key) && !is_null($this->api_secret)){
			$url = $this->_api_url.$this->platform."/playerupdate/";
			$params = array(
				"time" => time(),
				"ident" => $this->api_key,
				"player" => $player
			);
			$request = crateSignedParams($params,$this->api_secret,$request_string);
			$data = ($this->_webRequest)? webRequest($url,$request) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found" || $data->status == "exists")){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}	
	}

	/**
	 * This function checks if a player exists, and if not the basic player info is gotten from EA
	 * @param  string $player The name of the player
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function playerLookup($player = NULL){
		if(!is_null($player) && !is_null($this->api_key) && !is_null($this->api_secret)){
			$url = $this->_api_url.$this->platform."/playerlookup/";
			$params = array(
				"time" => time(),
				"ident" => $this->api_key,
				"player" => $player
			);
			$request = crateSignedParams($params,$this->api_secret,$request_string);
			$data = ($this->_webRequest)? webRequest($url,$request) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found" || $data->status == "exists")){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function generates a new API key
	 * @param  string $newKey An optional key
	 * @param  string $name   An optional name of the key
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function setupKey($newKey = NULL,$name = NULL){
		if(!is_null($this->api_key) && !is_null($this->api_secret)){
			$url = $this->_api_url."global"."/setupkey/";
			$params = array(
				"time" => time(),
				"ident" => $this->api_key
			);
			if(!is_null($newKey)){
				$params["clientident"] = $newKey;
			}
			if(!is_null($name)){
				$params["name"] = $name;
			}
			$request = crateSignedParams($params,$this->api_secret,$request_string);
			$data = ($this->_webRequest)? webRequest($url,$request) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found" || $data->status == "exists")){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function gets the information about about a API key
	 * @param  string $key The key to search for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function getKey($key){
		if(!is_null($key) && !is_null($this->api_key) && !is_null($this->api_secret)){
			$url = $this->_api_url."global"."/getkey/";
			$params = array(
				"time" => time(),
				"ident" => $this->api_key,
				"clientident" => $key
			);
			$request = crateSignedParams($params,$this->api_secret,$request_string);
			$data = ($this->_webRequest)? webRequest($url,$request) : alternativeRequest($url,"?".$request_string);
			if(isset($data->status) && ($data->status == "ok" || $data->status == "found" || $data->status == "exists")){
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