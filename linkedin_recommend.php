<?php
/**
 * @name		Linked In Recomend Widget PHP Creator
 * @author		Bo Thomsen
 * @company		Illution
 * @version 	1.0
 * @url			http://illution.dk
 * @license		MIT License
 * @date		11/12-2011
 */
/**
 *	This class is used to easy via PHP create LinkedIn Recommend Buttons on you site.
 *  You can set all the parameters on three ways by the available functions, via the variable scope and via the Generate_Button function.
 * If you wish to use more than one button per run then the easiest way is to use Generate_Button wich will by one function call return the wished button.
 */
/*
//Params are defined in this format @param {Type}{Access}	$Name - Description 
 */
 /*
 | @name LinkedIn_Recommend
 | @type class
 | @param {Array}{Private}	$_CounterTypes - A private array storing the available counter types
 | @param {String}{Public}	$InScriptSource - The script source for the Javascript file for linked in widgets
 | @param {String}{Public}	$InScriptType - The MIME type of the linked in widget file	
 | @param {String}{Public}	$ResourceType - The resource type, this is most used when different classes are combined but this value is normaly set to IN/RecommendProduct
 | @param {String}{Public}	$CompanyID - The Linked In id of the company you wish to make a reccomend button for one of their products	
 | @param {String}{Public}	$ProductID	- The Linked In id of the product
 | @param {String}{Public}	$CounterType - The type of counter (right,top,NULL) if the value is NULL there wouldn't be set any counter data wich means no counter	    
 */
class LinkedIn_Recommend{
	
	/* Variables */
	private $_CounterTypes = array('right','top'); //Array of available types of counters
	
	/* Script Variables */
	public $InScriptSource = 'http://platform.linkedin.com/in.js'; //The URL to the LinkedIn widget Javasript file
	public $InScriptType = 'text/javascript'; //The MIME type of the link script
	public $ResourceType = 'IN/RecommendProduct'; //The LinkedIn document type
	/* Data */
	public $CompanyID = NULL; //The LinkedIn id of the wished company
	public $ProductID = NULL; //The id of the product you wish to share
	public $CounterType = NULL; //The count type (top,right,NULL)
	
	/* The Constructor */
	public function LinkedIn_Recommend(){
		
	}
	
	/* A function to set the company Id of the widget
	| @param {String}	$Id - The wished company id for the widget
	| @access public 
	*/
	public function CompanyId($Id = NULL){
		if(!is_null($Id)){
			$this->CompanyID = $Id;
		}
	}
	
	/* A funtion to set the wished porduct id for the widget
	| @param {String}	$Id - The product Id
	| @access public
	*/
	public function ProductId($Id = NULL){
		if(!is_null($Id)){
			$this->ProductID = $Id;
		}
	}
	
	/* A function to set the wished counter values are (right,top,NULL) the parameters is as standard set to NULL wich means no counter
	| @param {String}	$Type - The counter type values are {right,top,NULL}
	| @access public
	*/
	public function CounterType($Type = NULL){
		if(!is_null($Type)){
			if(in_array($Type,$this->_CounterTypes)){
				$this->CounterType = $Type;
			}
		}
	}

	/* A function to set the source of the Linked In widget .js file this is optional and is intented to use if you use your own CDN
	| @param {String}	$Source - The url to the widget file
	| @access public
	*/	
	public function LinkedInScriptSource($Source = NULL){
		if(!is_null($Source)){
			$this->InScriptSource = $Source;
		}
	}

	/* A function to set the MIME type of the Linked In widget file
	| @param {String}	$Type - The MIME type in lowercase
	| @access public
	*/	
	public function LinkedInScriptType($Type = NULL){
		if(!is_null($Type)){
			$this->InScriptType = $Type;
		}
	}

	/* A function to set the resource tyoe etc IN/RecommendProduct, or called the type of widget
	| @param {String}	$Type - The resource type
	| @access public
	*/	
	public function LinkedInResourceType($Type = NULL){
		if(!is_null($Type)){
			$this->ResourceType = $Type;
		}
	}
	
	/* The function to start the normal generation
	| @param {Boolean}	$Header - If this parameter is set to false then you manually need to generate or insert the header. The generation can be done by Generate_Header()
	| @access public
	| @return {String} - The finished generated button with our without the header
	*/	
	public function Generate($Header = true){
		if(!is_null($this->ProductID) && !is_null($this->CompanyID)){
			$String = ''; //Set the container to empty
			if($Header){
				$String .= self::Generate_Header(); //Generate the header of the user has requested it
			}
			$String .= '<script type="'.$this->ResourceType.'" '; // Begins script tag
			$String .= 'data-company="'.$this->CompanyID.'" '; // Set the company taken from CompanyID
			$String .= 'data-product="'.$this->ProductID.'" '; // Sets the Product ID taken from ProductID
			if(!is_null($this->CounterType)){
				$String .= 'data-counter="'.$this->CounterType.'"';
			}
			$String .= '></script>'; //Ends script tag
			return $String;
		}
	}
	
	/* The function to generate the header only if you wish to generate more then one button per page this is usefull
	| @access public
	| @return {String} - The header
	*/
	public function Generate_Header(){
		return '<script src="'.$this->InScriptSource.'" type="'.$this->InScriptType.'"></script>';
	}
	
	/* The function to generate a button where the data is set by variables, this way is most of the time used if you wish to generate more than one button per run.
	| @param {String}	$CompanyId - The id of the company on Linked In
	| @param {String}	$ProductId - The Id of the product you wish to use on the reccomend button
	| @param {Boolean}	$Header - If this parameter is set to false then you manually need to generate or insert the header, wich is done by Generate_Header() {Optional}
	| @param {String}	$CounterType - The counter type, values are (right,top,NULL) where NULL means no counter {Optional}
	| @param {String}	$Source - The url to the widget file {Optional}
	| @param {String}	$ScriptType - The MIME type in lowercase {Optional}
	| @param {String}	$ResourceType - The LinkedIn resource type {Optional}
	| @access public
	| @return {String} - The generated button with or without header
	*/	
	public function Generate_Button($CompanyId = NULL,$ProductId = NULL,$Header = true,$CounterType = NULL,$ScriptSource = NULL,$ScriptType = NULL,$ResourceType = NULL){
		self::CompanyId($CompanyId);
		self::ProductId($ProductId);
		self::CounterType($CounterType);
		self::LinkedInScriptSource($ScriptSource);
		self::LinkedInScriptType($ScriptType);
		self::LinkedInResourceType($ResourceType);
		return self::Generate($Header);
	}
}
?>