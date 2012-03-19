<?php
/**
 * @name		Gravatar API Helper
 * @author		Bo Thomsen
 * @company		Illution
 * @version 	1.1
 * @url			http://illution.dk
 * @license		MIT License
 * @date		03/12-2011
 */
/**
 * @tobedone	Make a Helper for reading the data
 */
class Gravatar{
	
	/* Variables */
	private $Parameters = NULL; //The parameters array
	private $Profile_Parameters = NULL; //The array storing parameters for profile data requests
	
	/* 
	| The contructor
	*/
	public function Gravatar(){
		
	}
	
	/* A function to generate a md5 hash of the Input email
	|
	| @param {String}	$Email - The Input Email
	| @return {String} - A md5 string of the Email
	*/
	private function EmailHash($Email){
		$Return = trim($Email);
		$Return = strtolower($Return);
		return md5($Return);
	}
	
	/* A function to easyli set the Parameters for the Profile API
	|
	| @param {String}	$s - Size parameter used in the QR generator
	| @param {boolean}	$imagetag - Image Tag parameter used in the QR generator
	| @param {String}	$callback - Callback funtion used in the JSON data API
	| @param {String}	$type - The return type used in the XML data api properties are 'object' or 'string'
	*/
	public function Profile_Parameters($s = NULL,$imagetag = NULL,$callback = NULL,$type = NULL){
		if(!is_null($s)){
			$this->Profile_Parameters['s'] = $s;
		}
		if(!is_null($callback)){
			$this->Profile_Parameters['callback'] = $callback;
		}
		if(!is_null($imagetag)){
			$this->Profile_Parameters['imagetag'] = $imagetag;
		}
		if(!is_null($type)){
			if(($type == 'string') || ($type == 'object')){
				$this->Profile_Parameters['type'] = $type;
			}
			else{
				$this->Profile_Parameters['type'] = 'string';
			}
		}
	}
	
	/* The function to return Gravatar profile information the standard format is a php array
	| 
	| @param {String}	Email - The users email
	| @param {String}	$Format - The requested format following values are available {Optional}
	|	-json - JSON Formated string is returned
	|		-callback - The callback javascript function
	|	-array - An php array is returned
	|	-php - serialized data is returned
	|	-xml - XML data is returned as string the parameters is as follows
	|		-type - String or Object/Simple XML object standard set to string {Optional}
	|			-object
	|			-string
	|	-qr - A QR code/link to it is returned the parameters for qr is
	|		-s - The size of the image
	´		-imagetag - A boolean value if i true the a image tag will be applied
	|	-vcf - A link to a vCard file is returned
	|	-profile - Only The Profile URL
	| @param {Array}	$Parameters - The parameters array can be generated with Profile_Parameters function {Optional}
	| @return {String} or {Object} or {Array}	$Return - The return string or Object
	*/
	public function Profile($Email = NULL,$Format = 'array',$Parameters = NULL){
		if(!is_null($Email)){
			$Hash = self::EmailHash($Email);
			if(!is_null($Parameters)){
				$this->Profile_Parameters = $Parameters;
			}
			switch($Format){
				//JSON data
				case "json":
					return self::JSON($Hash);
				break;
				
				//The Profile URL
				case "profile":
					return self::Profile_Url($Hash);
				break;
				
				//Array data is returned
				case "php":
					return self::PHP($Hash);
				break;
				
				//Array data is returned
				case "array":
					return self::PHP($Hash);
				break;
	
				//XML as a string is returned or an object
				case "xml":
					return self::XML($Hash);
				break;
				
				//A link to a QR code is returned
				case "qr":
					return self::QR($Hash);
				break;
				
				//A vCard/VCF file is returned
				case "vcf":
					return self::VCF($Hash);
				break;
				
				//Default set to PHP
				default:
					return self::PHP($Hash);
				break;	
			}
		}
	}
	
	/* A function to generate a profile url
	|
	| @param {String}	$Hash - The hashed Email
	| @return {String}	- Returns the link to the users profile
	*/
	private function Profile_Url($Hash){
		return 'http://www.gravatar.com/'.$Hash;
	}
	
	/* A function get and return a JSON string a parameter [callback] is available to set the javascript callback function
	|
	| @param {String}	$Hash - The hashed Email
	| @return {String} - Returns a JSON data string
	*/
	private function JSON($Hash){
		$Url = 'http://www.gravatar.com/'.$Hash.'.json';
		if(!is_null($this->Profile_Parameters)){
			if(array_key_exists('callback',$this->Profile_Parameters)){
				$Url .= '?callback='.$this->Profile_Parameters['callback'];
			}	
		}
		return $Url;
	}
	
	/* A function to get and return the xml data from Gravatar
	|
	| @param {String}	$Hash - The Hashed Email
	| @return {String} or {Object} - Returns the XML data as string or an SimpleXML object
	*/
	private function XML($Hash){
		$file = file_get_contents( 'http://www.gravatar.com/'.$Hash.'.xml' );
		$Type = 'String';
		if(!is_null($this->Profile_Parameters)){
			if(array_key_exists('type',$this->Profile_Parameters)){
				switch($this->Profile_Parameters){
					
					//String
					case "string":
						$Type = 'String';
					break;
					
					//Object
					case "object":
						$Type = 'Object';
					break;
					
					//Default set to String
					default:
						$Type = 'String';
					break;
				}
			}
		}
		if($Type == 'Object'){
			return $xml = simplexml_load_string( $file );
		}
		else{
			return $file;	
		}
	}
	 
	/* A function to return url to the vCard file
	|
	| @param {String}	$Hash - The Hashed Email
	| @return {String} - Returns a string to the vCard file for the users
	*/
	private function VCF($Hash){
		$Url = 'http://www.gravatar.com/'.$Hash.'.vcf';
		return $Url;
	}
	
	/* A function to return an array
	|
	| @param {String}	$Hash - The Hashed Email
	| @return {Array} - An array containing the user data
	*/
	private function PHP($Hash){
		$str = file_get_contents( 'http://www.gravatar.com/'.$Hash.'.php' );
		$profile = unserialize( $str );
		return $profile;
	}
	
	/* A function  to return a url to a QR for the user and a size parameter [s] is possible
	|
	| @param {String}	$Hash - The Hashed Email
	| @return {String} - A url to the QR code
	*/
	private function QR($Hash){
		$Url = 'http://www.gravatar.com/'.$Hash.'.qr';
		if(!is_null($this->Profile_Parameters) && array_key_exists('s',$this->Profile_Parameters)){
			$Url .= '?s='.$this->Profile_Parameters['s'];
		}
		if(!is_null($this->Profile_Parameters) && array_key_exists('imagetag',$this->Profile_Parameters)){
			if($this->Profile_Parameters['imagetag'] == 'true'){
				$Return = '<img src="'.$Url.'"/>';
			}
			else{
				$Return = $Url;	
			}
		}
		else{
			$Return = $Url;	
		}
		return $Return;
	}
	
	/* A function to generate an image tag an optional with the following image parameters
	|
	| @param {String} $Url - The requested url for the image, the with the calculated md5 hash
	| @param {String} $ImageParameters - An optinal parameter for Image Parameters
	| @return {String} - Image tag with the input as src
	*/
	private function Image_Tag($Url,$ImageParameters = NULL){
		if(!is_null($ImageParameters)){
			return '<img src="'.$Url.'" '.$ImageParameters.' />';
		}
		else{
			return '<img src="'.$Url.'" />';
		}
	}
	
	/* A function to assembly the parameters string 
	|
	| @param {String}	$FileFormat - The wished Fileformat if the standard is not wanted
	| @return {String}	$String - The assembled string of parameters
	*/
	private function Generate_Parameters($FileFormat = ''){
		$String = '';
		if($FileFormat != ''){
			$String .= $FileFormat;
		}
		if(isset($this->Parameters)){
			$String .= '?';	
			$Parameters_String = '';
			if(array_key_exists('d',$this->Parameters)){
				$Parameters_String .= '&d='.urlencode($this->Parameters['d']);
			}
			if(array_key_exists('f',$this->Parameters)){
				$Parameters_String .= '&f='.$this->Parameters['f'];
			}
			if(array_key_exists('r',$this->Parameters)){
				$Parameters_String .= '&r='.$this->Parameters['r'];
			}
			if(array_key_exists('s',$this->Parameters)){
				$Parameters_String .= '&s='.$this->Parameters['s'];
			}
			$String .= ltrim($Parameters_String,'&');
		}	
		$String = rtrim($String,'&');
		return $String;
	}
	
	/* The function to assembly the full image tag with parameters for Gravatar and from the Image Tag generator function
	|
	| @param {String}	$Email - The email of the requested user
	| @param {String}	$FileFormat - The requested file format is not enough{Optional}
	| @param {String}	$Secure - A parameter that if its true then secure mode will be turned{Optional}
	| @param {Array}	$Paramters - An array of wished parameters if need options are the same as the Parameters function{Optional}
	| @param {String}	$ImageParameters - A string of wished parameters for the image tag, like width an height an so {Optional}
	| @return {String} - Returns a url with the wished parameters secure or not
	*/
	public function Image($Email = NULL,$FileFormat = '',$Secure = NULL,$Parameters = NULL,$ImageParameters = NULL){
		if(!is_null($Email)){
			$Hash = self::EmailHash($Email);
			if(isset($Parameters)){
				$this->Parameters = $Parameters;
			}
			if((isset($this->Parameters['d']) && $this->Parameters['d'] != '404') || !isset($this->Parameters['d'])){
				$String = self::Generate_Parameters($FileFormat);
				if(is_null($Secure) && $Secure != 'true'){
					return self::Image_Tag('http://www.gravatar.com/avatar/'.$Hash.$String,$ImageParameters);
				}
				else{
					return self::Image_Tag('https://secure.gravatar.com/avatar/'.$Hash.$String,$ImageParameters);
				}
			}
		}
	}
	
	/* The function to generate only the url to the image on gravatarn the only diffrence from Image is that this function only generates a url string
	|
	| @param {String}	$Email - The email of the requested user
	| @param {String}	$FileFormat - The requested file format is not enough{Optional}
	| @param {String}	$Secure - A parameter that if its true then secure mode will be turned{Optional}
	| @param {Array}	$Paramters - An array of wished parameters if need options are the same as the Parameters function{Optional}
	| @return {String} - The wished url for the image on Gravatar secrure or not and with the wished parameters for gravatar
	*/
	public function Image_Link($Email = NULL,$FileFormat = '',$Secure = NULL,$Parameters = NULL){
		if(!is_null($Email)){
			$Hash = self::EmailHash($Email);
			if(isset($Parameters)){
				$this->Parameters = $Parameters;
			}
			if((isset($this->Parameters['d']) && $this->Parameters['d'] != '404') || !isset($this->Parameters['d'])){
				$String = self::Generate_Parameters($FileFormat);
				if(is_null($Secure) && $Secure != 'true'){
					return 'http://www.gravatar.com/avatar/'.$Hash.$String;
				}
				else{
					return 'https://secure.gravatar.com/avatar/'.$Hash.$String;
				}
			}
		}
	}

	/* A function to set the Parameters easily 
	|
	| @param {String}	$d - Default Image
	| @param {boolean}	$f - Force default
	| @param {String}	$r - Rating options are (g,pg,r,x)
	| @param {String}	$s - Size like 200
	*/
	public function Parameters($d = NULL,$f = NULL,$r = NULL,$s = NULL){
		if(!is_null($d)){
			$this->Parameters["d"] = $d;
		}
		if(!is_null($f)){
			$this->Parameters["f"] = $f;
		}
		if(!is_null($r)){
			$this->Parameters["r"] = $r;
		}
		if(!is_null($s)){
			if($s > 512){
				$s = '512';
			}
			if($s < 1){
				$s = '1';
			}
			$this->Parameters["s"] = $s;
		}
	}
	
	/* A function to return the default Image providers/Generators
	|
	| @return {String}	$Return - An array of the available ImageProfiders for the $d parameter
	*/
	public function DefaultImageProfiders(){
		$Return = array(
			'404',
			'mm',
			'identicon',
			'monsterid',
			'wavatar',
			'retro'
		);
		return $Return;	
	}
}
?>