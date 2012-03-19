<?php
/**
 * @name		shr.im Helper
 * @author		Bo Thomsen
 * @company		Illution
 * @package		Social Helpers
 * @version 	1.1
 * @url			http://illution.dk
 * @license		MIT License
 * @date		22/12-2011
 * @category	Helpers
 * @package 	Url Shorteners
 * @subpackage	shr.im
 */
class shr_im{
	
	## User Variables ##
	
	/**
	* This variable stores the shr.im Api key which can be set by the 'Set_Api_Key' function
	* @access private
	* @var string
	*/
	private $API_Key = NULL;
	
	/**
	* This variable stores the Nickname of the api user for shr.im
	* @access private
	* @var string
	*/
	private $Nickname = NULL;
	
	## Internals ##
	
	/**
	* This variable is an internal variable used to store the url to the shr.im api
	* @access private
	* @var string
	*/
	private $API_Url = 'http://shr.im/api/1.0/';
	
	/**
	* This variable is used for error correction when setting paramters, this array stores the available parameters.
	* @access private
	* @var array
	*/
	private $Parameters = array();
	
	/** 
	* The Constructor calls the Set_Parameters function
	* @access public
	*/
	public function shr_im(){
		self::Set_Parameters();
	}
	
	/**
	* An internal function to set the $Parameters used for the api
	* @access private
	*/
	private function Set_Parameters(){
		$this->Parameters = array(
			'alias' => 'alias',
			'domain' => 'domain',
			'user' => 'user',
			'api_user' => 'api_user',
			'api_key' => 'api_key',
			'url_src' => 'url_src',
			'url_min' => 'url_min',
			'is_private' => 'is_private'
		);
	}
	
	/**
	* A function to post a new Url to shr.im and get the return of the API
	* @param {String}	$Url_Src - The url to add
	* @param {String}	$Url_Min - An unique identifier on shr.im of your link
	* @param {String}	$Is_Private - An {Optional} parameter if the url is private (1) or public (0)
	* @param {String}	$Format - The format of the returned (json,xml,text,array)
	* @return {Array}|{String}|{Object} 	- The returned data from the API as text,array, SimpleXML element or json
	* @access public
	*/
	public function Post($Url_Src = NULL,$Url_Min = NULL,$Is_Private = NULL,$Format = 'array'){
		if(!is_null($Url_Src)){
			$Extra_Url = '';
			$Other = array();
			if(!is_null($Is_Private)){
					$Other[$this->Parameters['is_private']] = $Is_Private;
			}
			$Other[$this->Parameters['url_src']] = $Url_Src;
			if(strpos($Url_Src,'http://') == false){
						$Url_Src = 'http://'.$Url_Src;
			}
			if(!is_null($Url_Min)){
					$Other[$this->Parameters['url_min']] = $Url_Min;
			}
			foreach($Other as $Name => $Value){
					$Extra_Url .= '&'.$Name.'='.$Value;
			}
			if($Format == 'array'){
				$Request_Url = $this->API_Url.'post.json?';
				$Request_Url .= self::Auth_Data();
				$Data = file_get_contents($Request_Url.$Extra_Url);
				$Return = json_decode($Data,TRUE);
				return $Return[0];
			}
			else{
				switch($Format){
					case "json":
						$Request_Url = $this->API_Url.'post.json?';
						$Request_Url .= self::Auth_Data();
						$Data = file_get_contents($Request_Url.$Extra_Url);
						return $Data;
					break;
					
					case "text":
						$Request_Url = $this->API_Url.'post.text?';
						$Request_Url .= self::Auth_Data();
						$Data = file_get_contents($Request_Url.$Extra_Url);
						return $Data;
					break;	
					
					case "xml":
						$Request_Url = $this->API_Url.'post.xml?';
						$Request_Url .= self::Auth_Data();
						$Data = file_get_contents($Request_Url.$Extra_Url);
						$XML = simplexml_load_string($Data);
						return $XML->url;
					break;
				}
			}	
		}
	}
	
	/**
	* This function returns a 98% unique identifier of with the specified Length.
	* The result is a random shuffle of the url mixed with a character string
	* @param {Int}	$Length - The length of the random unique identifier
	* @param {String}	$Link - The input url		
	* @return {String}	- Returns a random string
	* @access public
	*/
	public function Generate_Unique_Identifier($Length,$Link){
		$Characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz".$Link;        
    	$String = str_shuffle(str_repeat($Characters, rand(1, $Length))); 
   		return substr($String, rand(0,strlen($String)-$Length), $Length); 
	}
	
	/**
	* This function assemblies the auth data for the url and return it
	* @return {String}	- This function returns the auth part of the url
	* @access private
	*/
	private function Auth_Data(){
		return $this->Parameters['api_user'].'='.$this->Nickname.'&'.$this->Parameters['api_key'].'='.$this->API_Key;
	}
	
	/** 
	* A function to post a new Url to shr.im and get the returned shortened Url
	* @param {String}	$Url_Src - The url to add
	* @return {String}	- The shortened url as a string
	* @access public
	*/
	public function Create_New_Url($Url_Src = NULL,$Url_Min = NULL,$Is_Private = NULL){
		return self::Post($Url_Src,$Url_Min,$Is_Private,'text');
	}
	
	/**
	* This function performs a edit operation on the shr.im api, with the credidentials of this class.
	* The edit will be perfomed on the unique identifier and will if the return is TRUE be changed to the new url specified in $Url.
	* @param {String}	$Url - The new url of the link
	* @param {String}	$Identifier - The unique identifier of the url you want's to change the url of
	* @return {Boolean}	- Returns a TRUE if the operation succeded and FALSE if the input os wrong or the operation failed
	* @access public
	*/
	public function Edit($Url = NULL,$Identifier = NULL){
		if(!is_null($Url) && !is_null($Identifier)){
			$Url = $this->API_Url.'edit.json?'.self::Auth_Data().'&'.$this->Parameters['url_min'].'='.$Identifier.'&'.$this->Parameters['url_src'].'='.$Url;
			$Response = file_get_contents($Url);
			return ($Response == 'OK')? TRUE : FALSE;
		}
		else{
			return FALSE;	
		}
	}
	
	/**
	* This function performs a delete request on the Identifier
	* @param {String}	$Identifier
	* @param {String}	$Format - Available formats are ('array','xml','json') - But the return will for now just be a boolean
	* @return {Boolean}	- Normally the api would return OK or Error but this function return a boolean value
	* @access publix
	*/
	public function Delete($Identifier = NULL,$Format = 'array'){
		if(!is_null($Identifier)){
			$Response = '';
			$ExtraUrl = self::Auth_Data().'&'.$this->Parameters['url_min'].'='.$Identifier;
			switch($Format){
				case "array":
					$Url = $this->API_Url.'delete.json?';
					$Data = file_get_contents($Url.$ExtraUrl);
					$Response = $Data;
				break;
	
				case "xml":
					$Url = $this->API_Url.'delete.xml?';
					$Data = file_get_contents($Url.$ExtraUrl);
					$Response = $Data;
				break;
				
				case "json":
					$Url = $this->API_Url.'delete.json?';
					$Data = file_get_contents($Url.$ExtraUrl);
					$Response = $Data;
				break;	
			}
			if($Response == 'OK'){
				return TRUE;
			}
			else{
				return FALSE;	
			}
		}
		else{
			return FALSE;	
		}
	}
	
	/**
	* This function gets data from $Url and return it in different formats.
	* @param {String}	$Url - The url which the data should be accessed from.
	* @param {String}	$Format - The requested format of the reponse available values are ('NULL','jsonobject','xml','array','json')
	* @return {String}|{Boolean}|{Object}|{Array}	- If you choose 'jsonobject' as format a std class with the decode json data will be returned.
	* If you choose 'array'	a json decode array will be returned. if you choose 'xml' a SimpleXML object will be returned and if you choose NULL a boolean will be returned.
	* If you choose 'json' the raw json data will be returned.
	* @access private
	*/
	private function Get_Data($Url = NULL,$Format = NULL){
		if(!is_null($Url)){
			$Response = file_get_contents($Url);
			if(!is_null($Format) && $Response != 'Error'){
				switch($Format){
					case "array":
						return json_decode($Response,TRUE);
					break;
					
					case "json":
						return $Response;
					break;
					
					case "xml":
						$XML = simplexml_load_string($Response);
						return $XML;
					break; 
					
					case "jsonobject":
						return json_decode($Response);
					break;
					case "xmlclean":
						return $Response;
					break;
				}
			}
			else{
				if($Response == 'OK'){
					return TRUE;
				}
				else{
					return FALSE;	
				}
			}
		}
		else{
			return FALSE;
		}
	}
	
	/**
	* This function performs a by_user api request and return the response in a 'std Class','JSON',Array or a 'SimpleXML Object' format.
	* A more detailed format description of formats is done below, and if the request fails or the input is wrong a boolean value of FALSE is returned.
	* @param {String}	$User - The user to get data from
	* @param {String}	$Format - The response formats are:
	* <code>	
	*	- 'xml' -> A SimpleXML object
	* 	- 'json' -> The raw json string
	*	- 'jsonobject' -> A std class json object
	*	- 'array' -> An array with the json decoded data
	* </code>
	* If the data is empty or the input is wrong the response will be a boolean value of FALSE;
	* @return {Array}|{String}{Object}|{Boolean}	- he return will be a json string, an array, simple xml object or a json std class with the result and if the operations -
	* fails then a boolean value of FALSE will be returned.
	* @access public
	*/
	public function By_User($User = NULL,$Format = 'array'){
		if(!is_null($User)){
			$Url = $this->API_Url;
			switch($Format){
				case "array":
					$Url .= 'by_user.json?'.self::Auth_Data().'&'.$this->Parameters['user'].'='.$User;
				break;
				
				case "json":
					$Url .= 'by_user.json?'.self::Auth_Data().'&'.$this->Parameters['user'].'='.$User;
				break;
				
				case "xml":
					$Url .= 'by_user.xml?'.self::Auth_Data().'&'.$this->Parameters['user'].'='.$User;
				break; 
				case "jsonobject":
					$Url .= 'by_user.json?'.self::Auth_Data().'&'.$this->Parameters['user'].'='.$User;
				break;
			}
			$Data = self::Get_Data($Url,$Format);
			if($Format == 'xml' && $Data != 'Error'){
				foreach($Data->url as $Object){
					$Object->description = $Object->description[0];	
				}
			}
			return (!is_null($Data))? $Data : FALSE;
		}
		else{
			return FALSE;	
		}
	}
	
	/**
	* This function perform a By_Domain search in the shr.im api and return the output in the specified format.
	* There is no error handling in this function, the only error checking is done on the input.
	* @param {String}	$Domain - The requested domain for the search
	* @param {String}	$Format - The requested format, the formats are:
	* <code>	
	*	- 'xml' -> A SimpleXML object
	* 	- 'json' -> The raw json string
	*	- 'jsonobject' -> A std class json object
	*	- 'array' -> An array with the json decoded data
	* </code>
	* @return {Object}|{String}|{Array}|{Boolean}	- The return format is specified in $Format and returned in the equivalent php format etc SimpleXML Object or std Class -
	* for json. The return will only be Boolean if the input is empty
	* @access 
	*/
	public function By_Domain($Domain = NULL,$Format = 'array'){
		if(!is_null($Domain)){
			$Url = $this->API_Url;
			$ExtraUrl = self::Auth_Data().'&'.$this->Parameters['domain'].'='.$Domain;
			switch($Format){
				case "array":
					$FormatUrl = 'by_domain.json?';
				break;
				
				case "json":
					$FormatUrl = 'by_domain.json?';
				break;
				
				case "xml":
					$FormatUrl = 'by_domain.xml?';
				break; 
				case "jsonobject":
					$FormatUrl = 'by_domain.json?';
				break;
			}
			$RequestUrl = $Url.$FormatUrl.$ExtraUrl;
			$Data = self::Get_Data($RequestUrl,$Format);
			if($Format == 'xml' && $Data != 'Error'){
				foreach($Data->url as $Object){
					$Object->description = $Object->description[0];	
				}
			}
			return (!is_null($Data))? $Data : FALSE;
		}
		else{
			return FALSE;	
		}
	}
	
	/**
	* This function returns more specific information of the link taken from 'view' of the api.
	* @param {String}	$Identifer - The alias of the url
	* @param {String}	$Format - The response format, the available formats are:
	* <code>	
	*	- 'xml' -> A SimpleXML object
	* 	- 'json' -> The raw json string
	*	- 'jsonobject' -> A std class json object
	*	- 'array' -> An array with the json decoded data
	* </code>
	* @todo - Do a re-test, on the api, there comes an Error
	* @return {Object}|{String}|{Array}|{Boolean}	- The return format is specified in $Format and returned in the equivalent php format etc SimpleXML Object or std Class -
	* for json. The return will only be Boolean if the input is empty. Be aware if an input error occurs then the response format is changed to boolean value.
	* @access public
	*/
	public function View($Identifier = NULL,$Format = 'array'){
		if(!is_null($Identifier)){
			$Url = $this->API_Url;
			$ExtraUrl = self::Auth_Data().'&'.$this->Parameters['alias'].'='.$Identifier;
			switch($Format){
				case "array":
					$FormatUrl = 'view.json?';
				break;
				
				case "json":
					$FormatUrl = 'view.json?';
				break;
				
				case "xml":
					$FormatUrl = 'view.xml?';
				break; 
				case "jsonobject":
					$FormatUrl = 'view.json?';
				break;
			}
			$RequestUrl = $Url.$FormatUrl.$ExtraUrl;
			$Data = self::Get_Data($RequestUrl,$Format);
			return ($Data != 'Error')? $Data : FALSE;
		}
		else{
			return FALSE;	
		}
	}
	
	/**
	* This function returns the most popular links if shr.im in the specified format
	* @param {String}	$Format - The return format the available formats are:
	* <code>	
	*	- 'xml' -> A SimpleXML object
	* 	- 'json' -> The raw json string
	*	- 'jsonobject' -> A std class json object
	*	- 'array' -> An array with the json decoded data
	* </code>
	* @return {Boolean}|{Array}|{Object}|{String}	- The return is in the equivalent php format of the specified $Format. Boolean occurs if the input is NULL. 
	* @access public
	*/
	public function Popular($Format = 'array'){
		$Formats = array(
			'array' => 'json',
			'json' => 'json',
			'xml' => 'xml',
			'jsonobject' => 'json'
		);
		if(array_key_exists($Format,$Formats)){
			$Url = $this->API_Url.'popular.'.$Formats[$Format].'?'.self::Auth_Data();
			$Data = self::Get_Data($Url,$Format);
			return ($Data != 'Error')? $Data : FALSE;
		}
		else{
			return FALSE;
		}
	}
	
	/**
	* This function gets your newest urls on shr.im, and return the in the specified return $Format.
	* @param {String}	$Format - The return format, the available formats are:
	* <code>	
	*	- 'xml' -> A SimpleXML object
	* 	- 'json' -> The raw json string
	*	- 'jsonobject' -> A std class json object
	*	- 'array' -> An array with the json decoded data
	* </code>
	* @return {Boolean}|{Array}|{Object}|{String}	- The return is in the equivalent php format of the specified $Format. Boolean occurs if the input is NULL. 
	* @access public
	*/
	public function Home_Timeline($Format = 'array'){
		$Formats = array(
			'array' => 'json',
			'json' => 'json',
			'xml' => 'xml',
			'jsonobject' => 'json'
		);
		if(array_key_exists($Format,$Formats)){
			$Url = $this->API_Url.'home_timeline.'.$Formats[$Format].'?'.self::Auth_Data();
			$Data = self::Get_Data($Url,$Format);
			return ($Data != 'Error')? $Data : FALSE;
		}
		else{
			return FALSE;
		}
	}
	
	/**
	* This function gets the newest urls on shr.im, and return them in the specified $Format.
	* @param {String}	$Format - The return format, the available formats are:
	* <code>	
	*	- 'xml' -> A SimpleXML object
	* 	- 'json' -> The raw json string
	*	- 'jsonobject' -> A std class json object
	*	- 'array' -> An array with the json decoded data
	* </code>
	* @return {Boolean}|{Array}|{Object}|{String}	- The return is in the equivalent php format of the specified $Format. Boolean occurs if the input is NULL. 
	* @access public
	*/
	public function Public_Timeline($Format = 'array'){
		$Formats = array(
			'array' => 'json',
			'json' => 'json',
			'xml' => 'xml',
			'jsonobject' => 'json'
		);
		if(array_key_exists($Format,$Formats)){
			$Url = $this->API_Url.'public_timeline.'.$Formats[$Format].'?'.self::Auth_Data();
			$Data = self::Get_Data($Url,$Format);
			return ($Data != 'Error')? $Data : FALSE;
		}
		else{
			return FALSE;
		}
	}
	
	/** 
	* The function to set the shr.im credidentials
	* @param {String}	$Nickname - The shr.im nickname
	* @param {String}	$Key - The shr.im API key
	* @access public
	*/
	public function Set_Credidentials($Nickname = NULL,$Key = NULL){
		if(!is_null($Nickname)){
			$this->Nickname = $Nickname;
		}
		if(!is_null($Key)){
			$this->API_Key = $Key;
		}
	}
	
	/** 
	* A function to set the api key seperately
	* @param {String}	$Key - The shr.im API key
	* @access public
	*/
	public function Set_Api_Key($Key = NULL){
		if(!is_null($Key)){
			$this->API_Key = $Key;
		}
	}
	
	/**
	* A function to set the nickname seperately
	* @param {String}	$Nickname - The shr.im Nickname
	*´@access public
	*/
	public function Set_Nick_Name($Nickname = NULL){
		if(!is_null($Nickname)){
			$this->Nickname = $Nickname;	
		}
	}
}
?>