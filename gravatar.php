<?php
/**
 * @package		Gravatar API Helper
 * @author Bo Thomsen <bo@illution.dk>
 * @version 	1.1
 * @link 		http://illution.dk
 * @license		MIT License
 */
class Gravatar{
	
	/**
	 * This array will contain the parameters for the api
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $Parameters = NULL;

	/**
	 * This array will contain the paramters for requesting a profile image
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $Profile_Parameters = NULL;
	
	/**
	 * The constructor
	 * @since 1.0
	 * @access public
	 */
	public function Gravatar(){
		
	}
	
	/**
	 * This function generates a hash based on the email of the user
	 * @return string The generated md5 hash
	 * @param string $Email The email to generate the hash based on
	 * @access private
	 * @since 1.0
	 */
	private function EmailHash($Email){
		$Return = trim($Email);
		$Return = strtolower($Return);
		return md5($Return);
	}
	
	/**
	 * This function is used to set the parameters for the API
	 * @param string   $s        The size paramter can be set like this 256x212 or just 256
	 * @param boolean   $imagetag This parameter is used if the output is QR, then if this is true,
	 * the QR is wrapped in image tags
	 * @param string $callback A JSONP callback for the api
	 * @param string]   $type     The return type used in the XML data api properties are 'object' or 'string'
	 * @since 1.0
	 * @access public
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
	/**
	 * [Profile description]
	 * @param [type] $Email      [description]
	 * @param string $Format     [description]
	 * @param [type] $Parameters [description]
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
	
	/**
	 * This function returns the url to the user's profile image
	 * @param string $Hash The generated hash
	 * @see EmailHash
	 * @return string
	 * @since 1.0
	 * @access private
	 */
	private function Profile_Url($Hash){
		return 'http://www.gravatar.com/'.$Hash;
	}
	

	/**
	 * This function returns the api data as json
	 * @param string $Hash The hash of the user's email
	 * @return string The json string from the api
	 * @access private
	 * @since 1.0
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
	
	/**
	 * This funtion performs the xml request, to access the users
	 * information and return id as xml
	 * @param STRING $Hash The generated hash based on the users email
	 * @return string|object The return can be as a SimpleXML object of the Profile_Parameter
	 * type is set as object or if it's set as string a xml string is returned
	 * @access private
	 * @since 1.0
	 */
	private function XML($Hash){
		$file = file_get_contents( 'http://www.gravatar.com/'.$Hash.'.xml' );
		$Type = 'String';
		if(!is_null($this->Profile_Parameters)){
			if(array_key_exists('type',$this->Profile_Parameters)){
				switch($this->Profile_Parameters){
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
	 
	/**
	 * Thus function returns thr url to the VCF file from the api
	 * @param string $Hash The user hash
	 * @return string The url to the VCF file
	 * @since 1.0
	 * @access private
	 */
	private function VCF($Hash){
		$Url = 'http://www.gravatar.com/'.$Hash.'.vcf';
		return $Url;
	}
	
	/**
	 * This function returns the data as a PHP std Object,
	 * unserialized from the api
	 * @param string $Hash The user/email hash
	 * @return object|array The unserialized object or array
	 */
	private function PHP($Hash){
		$str = file_get_contents( 'http://www.gravatar.com/'.$Hash.'.php' );
		$profile = unserialize( $str );
		return $profile;
	}
	
	/**
	 * This function creates the url to the QR code, containing the api data
	 * @param string $Hash The hash generated based on the email of the user
	 * @return string The url to the QR code image
	 * @since 1.0
	 * @access private
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

	/**
	 * This functon inserts the $Yrl to an image tag, with the specified attributes
	 * @param string $Url             The generated image url
	 * @param string $ImageParameters The html image attributes
	 * @return string The html element as a string
	 * @access private
	 * @since 1.0
	 */
	private function Image_Tag($Url,$ImageParameters = NULL){
		if(!is_null($ImageParameters)){
			return '<img src="'.$Url.'" '.$ImageParameters.' />';
		}
		else{
			return '<img src="'.$Url.'" />';
		}
	}
	
	/**
	 * This function assemblies the request url with the specified parameters
	 * @param string $FileFormat The wished fileformat of the requested file
	 * @return string The assemblied request url
	 * @access private
	 * @since 1.0
	 */
	private function Generate_Parameters($FileFormat = 'json'){
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
	/**
	 * [Image description]
	 * @param [type] $Email           [description]
	 * @param string $FileFormat      [description]
	 * @param [type] $Secure          [description]
	 * @param [type] $Parameters      [description]
	 * @param [type] $ImageParameters [description]
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
	
	/**
	 * This function returns the url to the image using normal or secure gravatar
	 * @param string $Email      The users email
	 * @param string $FileFormat The response file format
	 * @param boolean $Secure     If the secure gravatar is going to be used
	 * @param array $Parameters The specified parameters
	 * @since 1.0
	 * @access public
	 * @return string The image url
	 */
	public function Image_Link($Email = NULL,$FileFormat = '',$Secure = NULL,$Parameters = NULL){
		if(!is_null($Email)){
			$Hash = self::EmailHash($Email);
			if(isset($Parameters)){
				$this->Parameters = $Parameters;
			}
			if((isset($this->Parameters['d']) && $this->Parameters['d'] != '404') || !isset($this->Parameters['d'])){
				$String = self::Generate_Parameters($FileFormat);
				if(is_null($Secure) && $Secure !== true){
					return 'http://www.gravatar.com/avatar/'.$Hash.$String;
				}
				else{
					return 'https://secure.gravatar.com/avatar/'.$Hash.$String;
				}
			}
		}
	}

	/**
	 * This function function is used to set the $Parameters array easily
	 * @param string $d The default image provider
	 * @param boolean $f If the image is going to be force the default provider
	 * @param string $r The rating options, available options are (g,ph,r,x)
	 * @param string $s The size of the image, it can be deffined like this 200 or 200x256
	 * @since 1.0
	 * @access public
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
	
	/**
	 * This function the available default image providers/generators, this is also descriped in the
	 * documentation.
	 * @access public
	 * @since 1.0
	 * @return array
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