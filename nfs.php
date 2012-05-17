<?php
include "request_helpers.php";
/**
 * @package		Need for Speed World Stats API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://world.needforspeed.com/SpeedAPI/doc/
 */
class NFS{

	/**
	 * The url to the Need For Speed API
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $_api_url = "http://world.needforspeed.com/SpeedAPI/ws/";

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * The language the data is returned as
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $locale = "en_US";

	/**
	 * The response object format
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $format = "json";

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function __construct(){
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function checks if the servers are up and running,
	 * the data can be returned as a bool or as a Simple XML Object or a JSON Object
	 * @param  boolean $return Set this to true and a Simple XML Object or a JSON Object is returned
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function server_status($return = false){
		$url = $this->_api_url."game/nfsw/server/status";
		$request_string = "locale=".$this->locale."&output=".$this->format;
		$data = ($this->_webRequest)? webRequest($url,$request_string,true) : alternativeRequest($url,"?".$request_string);
		if(!is_null($data) && $data !== false){
			if($return == true){
				return $data;
			} else {
				return ((isset($data->status) && $data->status == "UP") || (isset($data->worldServerStatus->status) && $data->worldServerStatus->status == "UP"))? true : false;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function returns the Need For Speed World RSS feed
	 * @param  string  $tag          An optional tag that all entities shall be in
	 * @param  integer $first_result An optional first result id
	 * @param  integer $max_results  An optional number of max result
	 * @since 1.0
	 * @access public
	 * @return boolean|object
	 */
	public function rss($tag = NULL,$first_result = 0,$max_results = 10){
		$url = $this->_api_url."cmsbridge/news/rss/".$this->locale;
		if(!is_null($tag)){
			$url .= "/tag/".$tag;
		}
		$request_string = "firstResult=".$first_result."&maxResults=".$max_results;
		$data = ($this->_webRequest)? webRequest($url,$request_string,true) : alternativeRequest($url,"?".$request_string);
		if(!is_null($data) && !isset($data->errorCode)){
			return $data;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function returns data from the leaderboards from a specific event
	 * @param  integer $event_id         The id of the event
	 * @param  integer $event_type       The event type "PVP = 1","PVE = 2", possible values are "1" or "2"
	 * @param  integer $leaderboard_type The leadervoard type Overall = "1", Daily = "2", Weekly = "3", Monthly = "4"
	 * @param  string|array $driver_names     An array of the requested drivers or a string with the name(s) seperated by commas
	 * @param  integer $record_filter    Used with Daily,Weekly and Monthly leaderboards, for daily is 0 current date and 6 means 6 days past current date, for weekly is the values a valid week number and for monthly is it a valid month number
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function leaderboard($event_id,$event_type,$leaderboard_type,$driver_names,$record_filter = NULL){
		if(is_array($driver_names)){
			$driver_names = implode(",", $driver_names);
		} else if(is_null($driver_names)){
			return FALSE;
		}
		$url = $this->_api_url."game/nfsw/leaderboards";
		$request_string = "lt=".$leaderboard_type."&output=".$this->format."&eid=".$event_id."&et=".$event_type."&dn=".$driver_names;
		$data = ($this->_webRequest)? webRequest($url,$request_string) : alternativeRequest($url,"?".$request_string);
		if(!is_null($data) && !isset($data->errorCode)){
			return $data;
		} else {
			return FALSE;
		}
	}

	/**
	 * This function returns the leaderboard events
	 * @since 1.0
	 * @access public
	 * @return object
	 */
	public function leaderboard_events(){
		$url = $this->_api_url."game/nfsw/leaderboards/events";
		$request_string = "output=".$this->format;
		$data = ($this->_webRequest)? webRequest($url,$request_string) : alternativeRequest($url,"?".$request_string);
		return $data;
	}	

	/**
	 * This function gets a list of all the badges a driver owns
	 * @param  string $driver_name The name of the driver
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function driver_badges($driver_name){
		return self::_driver($driver_name,"badges");
	}

	/**
	 * This function returns specs on the car that the driver has selected
	 * @param  string $driver_name The name of the driver
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function driver_current_car($driver_name){
		return self::_driver($driver_name,"car");
	}

	/**
	 * This function gets a list of all the cars that driver has
	 * @param  string $driver_name the name of the driver to look for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function driver_cars($driver_name){
		return self::_driver($driver_name,"cars");
	}

	/**
	 * This function gets the drivers profile
	 * @param  string $driver_name The name of the driver
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function driver_profile($driver_name){
		return self::_driver($driver_name,"profile");
	}

	/**
	 * This function gets a list of all the drivers profiles
	 * @param  string $driver_name The name of the driver
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function driver_profiles($driver_name){
		return self::_driver($driver_name,"profiles");
	}

	/**
	 * This function gets the last time where the user logged in
	 * @param  string $driver_name The name of the user
	 * @since 1.0
	 * @access public
	 * @return boolean|object
	 */
	public function driver_last_login($driver_name){
		return self::_driver($driver_name,"lastLogin");
	}

	/**
	 * This function is the basic for the driver API operations
	 * @param  string $driver_name The name of the driver
	 * @param  string $operation   The driver endpoint "lastLogin" etc
	 * @return boolean|object
	 * @since 1.0
	 * @access private
	 */
	private function _driver($driver_name,$operation){
		$url = $this->_api_url."game/nfsw/driver/".$driver_name."/".$operation;
		$request_string = "output=".$this->format;
		$data = ($this->_webRequest)? webRequest($url,$request_string,true) : alternativeRequest($url,"?".$request_string);
		if(!is_null($data) && !isset($data->errorCode)){
			return $data;
		} else {
			return FALSE;
		}	
	}

	/**
	 * This function uses the API to get the current stats of the player
	 * @param  string $driver_name The name of the player to search for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function driver_stats($driver_name){
		return self::_driver($driver_name,"stats");
	}
}
?>