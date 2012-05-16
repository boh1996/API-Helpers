<?php
include "request_helpers.php";
/**
 * @package		Battlefield Bad Company 2 API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://bfbcs.com/api
 */
class BFBC2{

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * The url to the Battlefield Bad Company 2 stats API
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $_api_url = "http://api.bfbcs.com/api/";

	/**
	 * The current platform to look in
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $platform = "pc";

	/**
	 * An array of the fields to request
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	public $fields = array("all");

	/**
	 * The available values for the field parameter
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_fields = array(
		"all",
		"general",
		"kits",
		"teams",
		"weapons",
		"vehicles",
		"gadgets",
		"specializations",
		"insiginias",
		"pins",
		"achievements",
		"dogtags",
		"grimg",
		"basic",
		"online",
		"progress",
		"misc",
		"raw"
	);

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function __construct(){
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function is used to set the platform
	 * @param  string $platform The platform to use
	 * @since 1.0
	 * @access public
	 */
	public function platform($platform){
		if(in_array($platform, array("pc","ps3","360"))){
			$this->platform = $platform;
		}
	}

	/**
	 * This function is used to set the fields parameter
	 * @param  string|array $fields The field(s) to add
	 * @since 1.0
	 * @access public
	 */
	public function fields($fields = NULL){
		if(!is_null($fields)){
			if(is_array($fields)){
				if(!is_null($this->fields) && is_array($this->fields)){
					$this->fields = array_merge($this->fields,$fields);
				} else {
					$this->fields = $fields;
				}
			} else {
				$this->fields = array($fields);
			}
		} else {
			$this->fields = array("all");
		}
	}

	/**
	 * This function fetches a list of data based on the request player(s) from the API
	 * @param  string|array $players The player(s) to request
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function players($players){
		if(!is_null($players) && is_array($this->fields)){
			if(is_array($players)){
				$players = implode(",", $players);
			}
			$url = $this->_api_url.$this->platform;
			$request_string = "players=".$players."&fields=".implode(",", $this->fields);
			$data = ($this->_webRequest)? webRequest($url,$request_string ) : alternativeRequest($url,"?".$request_string );
			if(!isset($data->error)){
				return $data;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function fetches the global stats for that platform
	 * @return object
	 * @since 1.0
	 * @access public
	 */
	public function globalStats(){
		$url = $this->_api_url.$this->platform;
		$request_string = "fields=".implode(",", $this->fields);
		$data = ($this->_webRequest)? webRequest($url,$request_string ) : alternativeRequest($url,"?".$request_string );
		return $data;
	}

	/**
	 * This function searches for a player using the API
	 * @param  string $player The player to search for
	 * @return boolean|object
	 * @since 1.0
	 * @access public
	 */
	public function search($player){
		if(!is_null($player)){
			$url = $this->_api_url.$this->platform;
			$request_string = "search=".$player."&fields=".implode(",", $this->fields);
			$data = ($this->_webRequest)? webRequest($url,$request_string ) : alternativeRequest($url,"?".$request_string );
			if(!isset($data->error)){
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