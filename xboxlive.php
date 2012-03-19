<?php
/**
 * @name		Xbox Leaders, Xbox Live API Helper
 * @author		Bo Thomsen <bo@illution.dk>
 * @company		Illution
 * @version 	1.0
 * @link		http://illution.dk
 * @license		MIT License
 * @date		19/12-2011
 * @category	Xbox Live API
 * @package 	Data API's
 * @subpackage	Xbox Live Data API
 */

class XboxLive{

	/* User Data */
	private $Gamer_Tag = NULL; //The variable storing the Gamer Tag
	private $Format = NULL; //The output format
	
	/* Internals */
	private $Api_Url = 'http://api.xboxleaders.com/v2/'; //The API url
	private $Formats = array('xml','json'); //The avalable formats for the API
	
	/** 
	* The constructor
	* @access public
	*/
	public function XboxLive(){
		
	}
	
	/** 
	* This functions set the internal GamerTag variable
	* @param {String}	$GamerTag - The Xbox Live gamertag of the users you wish to get the data of
	* @return {Boolean]	- Returns true if this function succeded
	* @access public
	*/
	public function Set_Gamer_Tag($GamerTag = NULL){
		if(!is_null($GamerTag)){
			$this->Gamer_Tag = $GamerTag;
			return true;
		}
		else{
			return false;	
		}
	}
	
	/**
	* This function is used to set the output format
	* @param {String}	$Format - The requested return format
	* @return {Boolean}	- Returns true if succeded and false if failed
	* @access public
	*/
	public function Set_Format($Format = NULL){
		if(!is_null($Format)){
			if(in_array($Format,$this->Formats)){
				$this->Format = $Format;
				return true;	
			}
		}
		else{
			return false;	
		}
	}

	/** 
	* This function is optinal and is only needed if you wan't to overrule the existing api url
	* @param {String}	$Api_Url - The new url to the API
	* @return {Boolean}	- Returns a boolean succeded or failed
	* @access public
	*/	
	public function Set_Api_Url($Api_Url = NULL){
		if(!is_null($Api_Url)){
			$this->Api_Url = $Api_Url;
			return true;
		}
		else{
			return false;	
		}
	}
	/**
	* This function returns the user data of the specified gamer in the specified format
	* @param {String}	$Format - The return format of the data 'xml' or json
	* @param {String}	$GamerTag - The Xbox Live gamer tag of the user
	* @param {Boolean}	$ToArray - Normally set to true if set to false, then the data will be left as raw json data
	* @return {Array} or {Object}	- Returns a json object if the format is set to json and ToArray is false else it returns a Array of the user data or if format is xml an 	
	* Simple XML object is returned
	* @access public
	*/
	public function Get_Data($Format = NULL,$GamerTag = NULL,$ToArray = true){
		self::Set_Gamer_Tag($GamerTag);
		if(self::Set_Format($Format)&&!is_null($this->Gamer_Tag)){
			$Url = $this->Api_Url.'?format='.$this->Format.'&gamertag='.$this->Gamer_Tag;
			$User_Data = file_get_contents($Url);
			if($Format == 'json'){
				return json_decode($User_Data,$ToArray);
			}
			else{
				return simplexml_load_string($User_Data);	 
			}
		}
		else{
			return false;	
		}
	}
	
	/** 
	* This function returns the box art off the xbox game specified as a decimal in $TID
	* @param {Int}	$TID - The game id of the xbox game, taken from the xbox market place
	* @param {String}	$Size - The size of the box art 'small' or 'large'
	* @return {Boolean}|{String}	- This function returns false if failed and the url to the box art if succeded, but be aware this function doesn't check if the box art 
	* exists.
	* @access public
	*/
	public function GetBoxArt($TID = NULL,$Size = 'large'){
		if(!is_null($TID)){
			$GameHex = dechex($TID);
			switch($Size){
				case "large":
					return 'http://tiles.xbox.com/consoleAssets/'.$GameHex.'/en-US/largeboxart.jpg';
				break;
				
				case "small":
					return 'http://tiles.xbox.com/consoleAssets/'.$GameHex.'/en-US/smallboxart.jpg';
				break;
			}
		}
		else{
			return false;	
		}
	}
}
?>