<?php
include "request_helpers.php";
/**
 * @package		Diablo 3 Class Calculator Data API Wrapper
 * @author 		Bo Thomsen <bo@illution.dk>
 * @version 	1.0
 * @link 		https://illution.dk
 * @license		MIT License
 * @see 		http://us.battle.net/d3/en/data/calculator/
 */
class Diablo3{

	/**
	 * The Diablo 3 stats API url
	 * @var string
	 * @since 1.0
	 * @access private
	 */
	private $_api_url = "";

	/**
	 * The url to the calculator data sheets
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	private $_calculator_url = "http://us.battle.net/d3/en/data/calculator/{class}";

	/**
	 * An array containing the available Diablo 3 classes
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_classes = array(
		"wizard",
		"witch-doctor",
		"demon-hunter",
		"monk",
		"barbarian"
	);

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
	public function __construct(){
		$this->_webRequest = isCurlInstalled();
	}

	/**
	 * This function checks if the class is available
	 * @param  string $class The class to test
	 * @return boolean
	 * @since 1.0
	 * @access private
	 */
	private function _class($class){
		return in_array($class, $this->_classes);
	}

	/**
	 * This function gets skill information on a Diablo 3 class
	 * @since 1.0
	 * @access public
	 * @param  string $class The class to get info of 
	 * @return boolean|object
	 */
	public function calculate($class){
		if(self::_class($class)){
			$url = str_replace("{class}", $class, $this->_calculator_url);
			$data = ($this->_webRequest)? webRequest($url) : alternativeRequest($url);
			if(!is_null($data) && $data !== false){
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