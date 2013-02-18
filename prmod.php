<?php
/**
 * This class is used to get data from PR Spy
 * @author Illution <support@illution.dk>
 * @license https://illution.dk/copyright Copyrights Illution © 2012, All rights reserved
 * @package Project Reality Mod
 * @category Project Reality Mod
 * @subpackage PR Spy
 * @link http://www.realitymod.com/prspy/ Project Reality BF 2 Spy
 * @link http://www.realitymod.com/pra2spy/ Project Reality ARMA 2 Spy
 * @version 1.0
 * @author Illution <support@illution.dk>
 */
class PRMod{

	/**
	 * The url to the document containing the current PR BF 2 servers, with players
	 * @var string
	 * @access private
	 * @static
	 * @since 1.0
	 */
	private $PR_BF2_SERVERS = "http://www.realitymod.com/prspy/currentservers.jsonp";

	/**
	 * The url to the document containing the list of online PR BF 2 players online
	 * @var string
	 * @access private
	 * @static
	 * @since 1.0
	 */
	private $PR_BF2_PLAYERS = "http://www.realitymod.com/prspy/currentplayers.jsonp";

	/**
	 * The url to the PR ARMA 2 document containing, the current servers, with players online
	 * @var string
	 * @access private
	 * @static
	 * @since 1.0
	 */
	private $PR_ARMA2_SERVERS = "http://www.realitymod.com/pra2spy/currentserversarma2.jsonp";

	/**
	 * The url to the document containing the current PR ARMA 2 online players
	 * @var string
	 * @access private
	 * @since 1.0
	 * @static
	 */
	private $PR_ARMA2_PLAYERS = "http://www.realitymod.com/pra2spy/currentplayersarma2.jsonp";

	/**
	 * The class constructor
	 * @since 1.0
	 * @access public
	 */
	public function __construct(){}

	/**
	 * This function gets the current servers which has players on, depending on which game is selected
	 * @param string $Game The PR game to get the current servers for values are "BF2" or "ARMA2"
	 * @param boolean $Assoc If assoc is true the data is converted to an associative array
	 * @return array|object|boolean FALSE is returned if the operation fails else is an object returned if Assoc is false,
	 * and an array is returned if Assoc is true
	 * @access public
	 * @since 1.0
	 */
	public function GetCurrentServers($Game = "BF2",$Assoc = false){
		if($Game == "BF2"){
			$Raw = file_get_contents($this->PR_BF2_SERVERS);
		} elseif($Game == "ARMA2"){
			$Raw = file_get_contents($this->PR_ARMA2_SERVERS);
		}
		$Data = self::jsonp_decode($Raw,$Assoc,true);
		if($Data !== false && $Data != ""){
			return $Data;
		} else {
			return false;
		}
	}

	/**
	 * This function gets the current players online, depending on which game is selected
	 * @param string $Game The PR game to get the current players for values are "BF2" or "ARMA2"
	 * @param boolean $Assoc If assoc is true the data is converted to an associative array
	 * @return array|object|boolean FALSE is returned if the operation fails else is an object returned if Assoc is false,
	 * and an array is returned if Assoc is true
	 * @access public
	 * @since 1.0
	 */
	public function GetCurrentPlayers($Game = "BF2",$Assoc = false){
		if($Game == "BF2"){
			$Raw = file_get_contents($this->PR_BF2_PLAYERS);
		} elseif($Game == "ARMA2"){
			$Raw = file_get_contents($this->PR_ARMA2_PLAYERS);
		}
		$Data = self::jsonp_decode($Raw,$Assoc,true);
		if($Data !== false && $Data != ""){
			return $Data;
		} else {
			return false;
		}
	}

	/**
	 * This function converts a JSONP string to a std object
	 * @param  string  $Jsonp The JSONP string to decode
	 * @param  boolean $Assoc If this flag is set to true, then the data will be converted to an array
	 * @param boolean $Strict If this parameter is true, then some known errors is corrected
	 * @return array||object|boolean
	 * @since 1.0
	 * @access private
	 * @link http://codepad.org/eJXeaXIO The JSONP decode function
	 */
	private function jsonp_decode($Jsonp = NULL, $Assoc = false,$Strict = false) {
		if(!is_null($Jsonp)){
	    	if ($Jsonp[0] !== '[' && $Jsonp[0] !== '{') {
	       		$Jsonp = substr($Jsonp, strpos($Jsonp, '('));
	    	}
	    	$Data = trim($Jsonp,'();');
	    	if($Strict){
	    	$Data = str_replace('],
	],
	"', ']],"', $Data);
	    	$Data = str_replace('",
 },', '"},', $Data);
	    	$Data = str_replace('",
 }
}', '"}}', $Data);
	    	}
    		return json_decode($Data, $Assoc);
    	} else {
    		return false;
    	}
	}
}
?>
