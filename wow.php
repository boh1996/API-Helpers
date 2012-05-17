<?php
include "request_helpers.php";
/**
 * @package		Wow API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://blizzard.github.com/api-wow-docs/
 * @todo 		Add Authetication header
 */
class Wow{

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * The region of Battle.net to use
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $region = "us";

	/**
	 * The Battle.net application public
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $application_public = NULL;

	/**
	 * The application secret key
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $application_secret = NULL;

	/**
	 * The available regions and their host
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_regions = array(
		"us" => "us.battle.net",
		"eu" => "eu.battle.net",
		"kr" => "kr.battle.net",
		"tw" => "tw.battle.net",
		"ch" => "www.battlenet.com.cn"
	);

	/**
	 * The locale to use
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $locale = "en_US";

	/**
	 * The available fields to select in the character fields selection
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_character_fields = array(
		"guild",
		"stats",
		"feed",
		"talents",
		"items",
		"reputation",
		"titles",
		"professions",
		"appearance",
		"companions",
		"mounts",
		"pets",
		"achievements",
		"progression",
		"pvp",
		"quests"
	);

	/**
	 * The available locales and their regions
	 * @since 1.0
	 * @access private
	 * @var array
	 */
	private $_locales = array(
		"us" => array("en_US","es_MX"),
		"eu" => array("en_GB","es_ES","fr_FR","ru_RU","de_DE"),
		"kr" => array("ko_KR"),
		"tw" => array("zh_TW"),
		"ch" => array("zh_CN")
	);

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function __construct () {
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function creats the request url
	 * @param  string $resource The request resource string
	 * @return string
	 * @since 1.0
	 * @access private
	 */
	private function _build_url ($resource,$params = NULL) {
		$url = $this->_regions[$this->region].$resource."?locale=".$this->locale;
		if (!is_null($params) && is_array($params) && count($params) > 0) {
			$url .= "&".assoc_implode("=","&",$params);
		}
		return $url;
	}

	/**
	 * This function sets the locale if it's valied
	 * @param  string $locale The locale to set
	 * @return boolean
	 * @since 1.0
	 * @access public
	 */
	public function locale ($locale) {
		if (!is_null($this->region) && in_array($locale, $this->_locales[$this->region])) {
			$this->locale = $locale;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function sets the region if it's a valied region
	 * @param  string $region The region to use
	 * @return boolean
	 * @since 1.0
	 * @access public
	 */
	public function region ($region) {
		if (in_array($region, $this->_regions)) {
			$this->region = $region;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function recieves achivement infomation using the API
	 * @param  integer $id The achivement id, 2144 etc
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function achivement ($id) {
		return self::_fetch("/api/wow/achievement/".$id);
	}

	/**
	 * This function ensures that the fields are correct
	 * @param  array $fields The selected fields
	 * @param  string|array $table  The name of the property that holds the available fields or an array of available fields
	 * @return array
	 * @since 1.0
	 * @access private
	 */
	private function _fields ($fields,$table) {
		if (!is_array($table)) {
			$available = $this->{$table};
		} else {
			$available = $table;
		}
		if (!is_array($table)) {
			return $fields;
		}
		foreach ($fields as $key => $field) {
			if(!in_array($field, $available)){
				unset($fields[$key]);
			}
		}
		return $fields;
	}

	/**
	 * This function fetches information on diffenrent characters
	 * @param  string $realm     The character realm
	 * @param  string $name 	 The character name
	 * @param  array $fields    An optional fields array, the available fields are shown in $this->_character_fields
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function character ($realm = NULL,$name = NULL,$fields = NULL) {
		if (!is_null($realm) && !is_null($user_name)) {
			$params = array();
			if (!is_null($fields) && is_array($fields)) {
				$params["fields"] = implode(",", self::_fields($fields,"_character_fields"));
			}
			return self::_fetch("/api/wow/achievement/".$realm."/".$name,$params);
		} else {
			return FALSE;
		}
	}

	/**
	 * This function fetches information of a guild
	 * @param  string $realm      The realm name
	 * @param  string $guild_name The guild name
	 * @param  array $fields     An optional fields arrray, available fields are "members","achievements","news"
	 * @return boolean|array
	 * @since 1.0
	 * @access public
	 */
	public function guild ($realm,$guild_name,$fields = NULL) {
		$params = array();
		if (!is_null($fields) && is_array($fields)) {
			$params["fields"] = implode(",", self::_fields($fields,array("members","achievements","news")));
		}
		return self::_fetch("/api/wow/achievement/".$realm."/".$guild_name,$params);
	}

	/**
	 * This function recives the status of all realms ore the choosen realms
	 * @param  array $realms An optional array of the realms to choose
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function realm ($realms = NULL) {
		$params = array();
		if (!is_null($realms) && is_array($realms)) {
			$params["realms"] = implode(",", $realms);
		}
		return self::_fetch("/api/wow/realm/status",$params);
	}

	/**
	 * This function recieves infomation on a recipe
	 * @param  integer $id The id of the recipe, 33994 etc
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function recipe ($id) {
		return self::_fetch("/api/wow/recipe/".$id);
	}

	/**
	 * This function fetches info on ongoing actions
	 * @param  string $realm The realm to search in
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function auctions ($realm) {
		return self::_fetch("/api/wow/auction/data/".$realm);
	}

	/**
	 * This function builds the url and check for errors
	 * @param  string $url    The url to fetch from
	 * @param  array $params Url parameters
	 * @return boolean|object
	 * @since 1.0
	 * @access private
	 */
	private function _fetch ($url,$params = NULL) {
		$url = self::_build_url($url,$params);
		$headers = array();
		if (!is_null($this->application_secret) && !is_null($this->application_public)) {
			$headers[] = "Authorization: BNET ".$this->application_public.":".signature($url,"GET",$this->application_secret);
		}
		$data = ($this->_webRequest)? webRequest($url,NULL,true,$headers) : alternativeRequest($url);
		if (!is_null($data) && $data !== false && (!isset($data->status))) {
			return $data;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function fetches info on a specific item
	 * @param  integer $id The id of the item to fetch info on, 38268 etc
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function item ($id) {
		return self::_fetch("/api/wow/item/".$id);
	}	

	/**
	 * This function fetches information on a specific item set
	 * @param  integer $id The item of the item set, 1060 etc
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function item_set ($id) {
		return self::_fetch("/api/wow/item/set/".$id);
	}

	/**
	 * This function fetches information on arenas'
	 * @param  string $realm     The realm to search in
	 * @param  string $team_size The team size to search for, teamsizes are "2v2", "3v3" and "5v5"
	 * @param  string $team_name The name of the team to search for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function arena_team ($realm,$team_size = NULL,$team_name) {
		return self::_fetch("/api/wow/arena/".$realm."/".$team_size."/".$team_name);
	}

	/**
	 * This function fetches Arena Team Ladder infomation
	 * @param  string $battlegroup The battlegroup to search in
	 * @param  string $team_size   The team size to search for, teamsizes are "2v2", "3v3" and "5v5"
	 * @param  array $params      An array of optional paramerters, parameters are 
	 * "page" to set the current result page, default is 1
	 * "size" to set how many results per page, default is 50
	 * "asc" if the results are going to be sorted in ascending order, true means yes and default is true
	 * @return boolean|object
	 */
	public function arena_ladder ($battlegroup,$team_size,$params = NULL) {
		return self::_fetch("/api/wow/pvp/arena/".$battlegroup."/".$team_size,$params);
	}

	/**
	 * This function fetches the rated battleground ladder info for the choosen region
	 * @param  [type] $params Some optional parameters @see arena_ladder
	 * @return boolean|object
	 * @see arena_ladder
	 * @since 1.0
	 * @access public
	 */
	public function rated_battleground_ladder ($params) {
		return self::_fetch("/api/wow/pvp/ratedbg/ladder",$params);
	}

	/**
	 * This function fetches info on a quest
	 * @param  integer $id The id of the quest to fetch info on, 13146 etc
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function quest ($id) {
		return self::_fetch("/api/wow/quest/".$id);
	}

	/**
	 * This function fetches battlegroup info for the selected region
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function battlegroups () {
		return self::_fetch("/api/wow/data/battlegroups/");
	}

	/**
	 * This function fetches a list of character races
	 * @since 1.0
	 * @access public
	 * @return boolean|object
	 */
	public function character_races () {
		return self::_fetch("/api/wow/data/character/races");
	}

	/**
	 * This function fetches a list of character classes
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function character_classes () {
		return self::_fetch("/api/wow/data/character/classes");
	}

	/**
	 * This function fetches a list of all the achievements optainable by the different characters.
	 * Category structure and achivement hirachy is also fetched
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function character_achievements () {
		return self::_fetch("/api/wow/data/character/achievements");
	}

	/**
	 * This function fetches a list of all the rewards earnable for a guild
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function guild_rewards () {
		return self::_fetch("/api/wow/data/guild/rewards");
	}

	/**
	 * This function fetches a list of all the available guild perks
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function guild_perks () {
		return self::_fetch("/api/wow/data/guild/perks");
	}

	/**
	 * This function fetches a list of optainable guild achivements
	 * @see character_achievements
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function guild_achievements () {
		return self::_fetch("/api/wow/data/guild/achievements");
	}

	/**
	 * This function fetches a list of all the item classes
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function item_classes () {
		return self::_fetch("/api/wow/data/item/classes");
	}
}
?>