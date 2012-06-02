<?php
include "request_helpers.php";
/**
 * This API Wrapper is used to query the Unoffical Xbox Live API
 * @package		Unofficial Xbox Live API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @category Gaming
 * @package API Wrappers
 * @subpackage Xbox Live
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		https://xboxapi.com/documentation
 */
class XboxLive{
		
	/**
	 * The url to the Xbox API
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $api_url = "https://xboxapi.com/";

	/**
	 * When peforming a request, the api returns the api limit
	 * and this object will contain two properties:
	 * usage: The current usage
	 * limit: The maximum limit
	 * @var object
	 */
	public $limit = NULL;

	/**
	 * If this is true cURL is used else if file_get_contents used
	 * @since 1.0
	 * @access private
	 */
	private $_webRequest = FALSE;

	/**
	 * This function is the constructor
	 * @since 1.0
	 * @access public
	 */
	public function __construct () {
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function is used to get all games of a specific player
	 * @param  string $player The player to search for
	 * @return object|boolean
	 * @since 1.0
	 * @access public
	 */
	public function games ( $player ) {
		$url = $this->api_url."json/games/".urlencode($player);
		$object = self::_request($url);
		if(!is_object($object) || $object->Success != 1){
			return FALSE;
		}
		foreach ($object->Games as $key => &$game) {
			if(isset($game->Progress->LastPlayed)){
				$game->Progress->LastPlayed = str_replace("/Date(", "", str_replace(")/", "", $game->Progress->LastPlayed));
			}
		}
		return $object;
	}

	/**
	 * This function is used to get friends of a player
	 * @param  string $player The gamer tag of the player
	 * @return object|boolean
	 * @since 1.0
	 * @access public
	 */
	public function friends ( $player ) {
		$url = $this->api_url."/json/friends/".urlencode($player);
		$object = self::_request($url);
		if(!is_object($object) || ($object->Success != 1 && $object->Success != false)){
			return FALSE;
		}
		foreach ($object->Friends as $key => &$friend) {
			if(isset($friend->LastSeen)){
				$friend->LastSeen = str_replace("/Date(", "", str_replace(")/", "", $friend->LastSeen));
			}
		}
		return $object;
		
	}

	/**
	 * This function is used to get profile information of a player
	 * @param  string $player The players gamer tag
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function profile ( $player ) {
		$url = $this->api_url."/json/profile/".urlencode($player);
		$object = self::_request($url);
		if(!is_object($object) || ($object->Success != 1 && $object->Success != false)){
			return FALSE;
		}
		return $object;
	}

	/**
	 * This function is used to get acgievements for a game and what achievements a palyer has earned
	 * @param  string $player The players gamer tag
	 * @param  string $game   The game id
	 * @return boolean|object
	 */
	public function achievements ( $player, $game) {
		$url = $this->api_url."/json/achievements/".$game."/".urlencode($player);
		$object = self::_request($url);
		if(!is_object($object) || ($object->Success != 1 && $object->Success != false)){
			return FALSE;
		}
		if(isset($object->Game->Progress->LastPlayed)){
				$object->Game->Progress->LastPlayed = str_replace("/Date(", "", str_replace(")/", "", $object->Game->Progress->LastPlayed));
		}
		foreach ($object->Achievements as $key => &$achievement) {
			if(isset($achievement->EarnedOn)){
				$achievement->EarnedOn = str_replace("/Date(", "", str_replace(")/", "", $achievement->EarnedOn));
			}
		}
		return $object;
	}

	/**
	 * This function performs the request
	 * @since 1.0
	 * @access private
	 * @param  string $url The request url
	 * @return object|boolean
	 */
	private function _request ( $url ) {
		if($this->_webRequest) {
			$object  = webRequest($url);
		} else {
			$object = alternativeRequest($url);
		}
		if(!is_object($object)){
			return FALSE;
		}
		$limit = explode("/", $object->API_Limit);
		$this->limit = (object)array("usage" => $limit[0],"limit" => $limit[1]);
		return $object;
	}

	/**
	 * This function returns a url to the box art image of the requsted game
	 * @param  string $tid  The Xbox Game id
	 * @param  string $size The size of the image small or large
	 * @param boolean $data_url If this is set to true then a data url is returned
	 * @return string
	 * @access public
	 * @since 1.0
	 */
	public function get_box_art ($tid, $size = 'large', $data_url = false) {
		$game_hex = dechex($tid);
		$url = 'https://live.xbox.com/consoleAssets/'.$game_hex.'/en-US/'.$size.'boxart.jpg';
		if ($data_url) {
			$image = file_get_contents($url);
			return "data:image/jpeg;base64,".base64_encode($image);
		} else {
			return $url;
		}
	}
}
?>