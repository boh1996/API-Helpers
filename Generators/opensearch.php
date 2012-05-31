<?php
/**
 * @name		Open Search Description Document
 * @author		Bo Thomsen
 * @company		Illution
 * @version 	1.0
 * @url			http://illution.dk
 * @license		MIT License
 * @date		30/11-2011
 */

	class OpenSearch{
		
		/* Description Features */
		private $SyndicateRights = array('open','limited','private','closed'); //The available properties in SyndicateRights
		private $ImageElements = array('Height','Width','Type','Image'); //The available image properties
		private $RelValues = array('results','suggestions','self','collection'); //The available url rel values
		private $LocalRole = array('request','example','related','correction','subset','superset'); // The avaiable values in the role property
		private $StandardSpecifications = array('http://a9.com/-/spec/opensearch/1.1/'); // A array of specifications as standard set to the normal OSSD specification
		private $UrlTemplate = array('Template','Type','Rel','IndexOffset'); // The available properties for the url element
		private $MIMES = 
			array(// A list of helper mime types
				'application/rss+xml','application/json','application/opensearchdescription+xml','text/html','application/x-suggestions+json','application/atom+xml'
			); 
		
		/* Settings */
		private $Details = array();
		
		/* Responses */
		public $RSS = ''; // Will be a placeholder for the RSS class
		public $Atom = ''; //Will be a placeholder for the Atom class
		public $HTML = ''; //HTML response
		public $XML = ''; //XML Response
		public $JSON = ''; //JSON Response
		
		/* 	Open Search Description	*/
		public $OSDDSpecifications = array('http://a9.com/-/spec/opensearch/1.1/'); //The wished specifications on the document in an array.
		public $XMLVersion = '1.0';
		public $XMLEncoding = 'UTF-8';
		public $ShortName = '';
		public $LongName = '';
		public $Description = '';
		public $Tags = array();
		public $Contacts = array();
		public $Urls = array();
		public $Images = array();
		public $Query = array();
		public $Developers = array();
		public $Attributions = array();
		public $SyndicationRight = '';
		public $AdultContent = '';
		public $Language = '';
		public $OutputEncoding = 'UTF-8';
		public $InputEncoding = 'UTF-8';
		
		/* Experimental */
		private $Replace = array();
		private $XMLParser = '';
		
		/*
		| A function to generate the opensearch document
		|	@access {public}
		|	@param {String} $XMLVersion - The XML Version
		|	@param {String} $XMLEncoding - The XML Encoding standard set to UTF-8
		|	@param {String} $OSSDSpecification - The url to the Wished specification
		|	@param {String} $OutputENcoding - The Output Encoding as standard set to UTF-8
		|	@param {String} $InputEncoding - The Input Encoding as standard set to UTF-8
		*/
		public function OpenSearch($XMLVersion = NULL,$XMLEncoding = NULL,$OSDDSpecification = NULL,$OutputEncoding = NULL,$InputEncoding = NULL){
			if(is_null($XMLVersion)){
				$this->XMLVersion = $XMLVersion;
			}
			if(!is_null($XMLEncoding)){
				$this->XMLEncoding = $XMLEncoding;
			}
			if(!is_null($OSDDSpecification)){
				$this->OSDDSpecifications[] = $OSDDSpecification;
			}
			if(!is_null($OutputEncoding)){
				$this->OutputEncoding;
			}
			if(!is_null($InputEncoding)){
				$this->InputEncoding = $InputEncoding;
			}
			self::SetSettings();
		}

		/*	
		| A function to set the needed settings only ment to have a more cleaner code
		*/
		private function SetSettings(){
		$this->Details = array(
				'Specification' => 'OSDDSpecifications',
				'XMLVersion' => 'XMLVersion',
				'XMLEncoding' => 'XMLEncoding',
				'ShortName' => 'ShortName',
				'LongName' => 'LongName',
				'Description' => 'Description',
				'Tags' => 'Tags',
				'Contacts' => 'Contacts',
				'Urls' => 'Urls',
				'Images' => 'Images',
				'Query' => 'Query',
				'Developers' => 'Developers',
				'Attributions' => 'Attributions',
				'SyndicationRight' => 'SyndicationRight',
				'AdultContent' => 'AdultContent',
				'Language' => 'Language',
				'OutputEncoding' => 'OutputEncoding',
				'InputEncoding' => 'InputEncoding'
			);
		}

		/*	
		| A function to add a replace rule to the class
		|	@param {String} $Replace - The Thing to replace in {} curly brackets
		|	@param {String} $With - The thing you want to replace it with
		*/
		public function addReplaceRule($Replace = NULL,$With = NULL){
			if(!is_null($Replace) && !is_null($With)){
				$this->Replace[$Replace] = $With;
			}
		}
		
		/* A function to add a query in the specific array format to the Query array
		| @param {Array}	$Query - A query with the childrens set specific as defined in the create query function
		*/
		public function addQuery($Query){
			$this->Query[] = $Query;
		}
		
		/* A function to add a query to the Query array, this is the easy way
		| @param {String}	$Role - 
		| @param {String}	$SearchTerms - 
		| @param {String}	$Specification - {Optional}
		| @param {String}	$Count - {Optional}	
		| @param {String}	$StartIndex - {Optional}
		| @param {String}	$StartPage - {Optional} 
		| @param {String}	$TotalResults - {Optional}
		| @param {String}	$Title - {Optional}
		| @param {String}	$Language - {Optional}
		| @param {String}	$InputEncoding - {Optional}
		| @param {String}	$OutputEncoding - {Optional}
		| @param {String}	$Custom - {Optional}
		*/
		public function createQuery(
			$Role = NULL,
			$SearchTerms = NULL,
			$Specification = NULL,
			$Count = NULL,
			$StartIndex = NULL,
			$StartPage = NULL,
			$TotalResults = NULL,
			$Title = NULL,
			$Language = NULL,
			$InputEncoding = NULL,
			$OutputEncoding = NULL,
			$Custom	= NULL
		){
			$Query = array();
			if(!is_null($Role)){
				$Query['Role'] = $Role;
			}
			if(!is_null($SearchTerms)){
				$Query['SearchTerms'] = $SearchTerms;
			}
			if(!is_null($Specification)){
				$Query['Specification'] = $Specification;
			}
			if(!is_null($Count)){
				$Query['Count'] = $Count;
			}
			if(!is_null($StartIndex)){
				$Query['StartIndex'] = $StartIndex;
			}
			if(!is_null($StartPage)){
				$Query['StartPage'] = $StartPage;
			}
			if(!is_null($TotalResults)){
				$Query['TotalResults'] = $TotalResults;
			}
			if(!is_null($Title)){
				$Query['Title'] = $Title;
			}
			if(!is_null($Language)){
				$Query['Language'] = $Language;
			}
			if(!is_null($InputEncoding)){
				$Query['InputEncoding'] = $InputEncoding;
			}
			if(!is_null($OutputEncoding)){
				$Query['OutputEncoding'] = $OutputEncoding;
			}
			if(!is_null($Custom)){
				$Query['Custom'] = $Custom;
			}
			$this->Query[] = $Query;
		}
		
		/*
		|
		| @param {String} $Specification - A link to the Atom Specification
		*/
		public function addAtomSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:atom="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] =  ' xmlns:atom="http://www.w3.org/2005/Atom"';
			}
		}

		/*
		|
		| @param {String} $Specification - A link to the Geo Specification
		*/
		public function addGeoSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:geo="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] =  ' xmlns:geo="http://a9.com/-/opensearch/extensions/geo/1.0/"';
			}
		}
		
		/*
		|
		| @param {String} $Specification - A link to the Refrerer Specification
		*/
		public function addRefrererSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:referrer="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] =  ' xmlns:referrer="http://a9.com/-/opensearch/extensions/referrer/1.0/"';
			}
		}
		
		/*
		|
		| @param {String} $Specification - A link to the Time Specification
		*/
		public function addTimeSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:time="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] =  ' xmlns:time="http://a9.com/-/opensearch/extensions/time/1.0/"';
			}
		}
		
		/*
		|
		| @param {String} $Specification - A link to the Parameters Specification
		*/
		public function addParametersSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:parameters="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] =  ' xmlns:parameters="http://a9.com/-/spec/opensearch/extensions/parameters/1.0/"';
			}
		}
		
		/*
		|
		| @param {String} $Specification - A link to the Relevance Specification
		*/
		public function addRelevanceSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:relevance="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] =  ' xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/"';
			}
		}		
		
		/*
		|
		| @param {String} $Specification - A link to the Open Search Specification
		*/
		public function addOpenSearchSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = ' xmlns:opensearch="'.$Specification.'"';
			}
			else{
				$this->OSDDSpecifications[] = ' xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"';
			}
		}

		/*	
		| A function to add a new specification link to the array
		|	@param {String} $Specification - The link to the specification
		*/
		public function addSpecification($Specification = NULL){
			if(!is_null($Specification)){
				$this->OSDDSpecifications[] = $Specification;
			}
		}
		
		/*	
		| 
		|	@param {String} $Key
		|	@param {String} $Array
		|	@return {boolean} $Reutnr
		*/
		private function Key_Exists_And_Is_Set($Key,$Array){
			$Return = false;
			if(array_key_exists($Key,$Array)){
				if(isset($Array[$Key])){
					$Return = true;
				}
			}
			return $Return;
		}
		
		/*	
		| A function to set all the Details by Array
		|	@param {Array} $Details
		*/
		public function setDetails($Details = NULL){
			if(!is_null($Details)){
				foreach($this->Details as $Key => $Variable){
					if(self::Key_Exists_And_Is_Set($Key,$Details)){
						$this->$Variable = $Details[$Key];
					}
				}
			}
		}
		
		/*
		|
		|	@param {String} $Description
		*/
		public function setDescription($Description = NULL){
			if(!is_null($Description)){
				$this->Description = $Description;
			}
		}
		
		/*	
		|
		|	@param {String} $LongName
		*/
		public function setLongName($LongName = NULL){
			if(!is_null($LongName)){
				$this->LongName = $LongName;
			}
		}
		
		/* Set the short name property in ShortName	
		|
		|	@param {String} $ShortName
		*/
		public function setShortName($ShortName = NULL){
			if(!is_null($ShortName)){
				$this->ShortName = $ShortName;
			}
		}
		
		/* Add aa Contact to Contacts
		|
		|	@param {String} $Contact
		*/
		public function addContact($Contact = NULL){
			if(!is_null($Contact)){
				$this->Contacts[] = $Contact;
			}
		}
		
		/*	Set Syndicate Rights property in SyndicateRights
		|
		|	@param {String} $SyndicateRight
		*/
		public function setSyndicateRight($SyndicateRight = NULL){
			if(!is_null($SyndicateRight)){
				if(in_array($SyndicateRight,$this->SyndicateRights)){
					$this->SyndicationRight = $SyndicateRight;
				}
			}
		}
		
		/* Add an image to the Images	
		|
		|	@param {String} $Image
		*/
		public function addImage($Image = NULL,$Height = NULL,$Width = NULL,$Type = NULL){
			$ImageObject = array();
			if(!is_null($Image)){
				$ImageObject['Image'] = $Image;
			}
			if(!is_null($Height)){
				$ImageObject['Height'] = $Height;
			}
			if(!is_null($Width)){
				$ImageObject['Width'] = $Width;
			}
			if(!is_null($Type)){
				$ImageObject['Type'] = $Type;
			}
			if(isset($ImageObject['Image'])){
				$this->Images[] = $ImageObject;
			}
		}
		
		/* Add a tag to Tags
		|
		|	@param {String} $Tag
		*/
		public function addTag($Tag = NULL){
			if(!is_null($Tag)){
				$this->Tags[] = $Tag;
			}
		}
		
		/*
		public function addStandardUrl($Url = NULL,$Format,$Type = NULL,$Template = NULL,$Rel = NULL,$IndexOffset = NULL){
			?q={searchTerms}
			pw={startPage?}
			start={startIndex?}
			format=rss
		}
		*/
		
		/* Add a url to Urls	
		|
		|	@param {String} $Type
		|	@param {String} $Template
		|	@param {String} $Rel
		|	@param {String} $IndexOffset
		*/
		public function addUrl($Type = NULL,$Template = NULL,$Rel = NULL,$IndexOffset = NULL){
			$URL = array();
			if(!is_null($Type)){
				$URL['Type'] = $Type;
			}
			if(!is_null($Template)){
				$URL['Template'] = $Template;
			}
			if(!is_null($Rel)){
				$URL['Rel'] = $Rel;
			}
			if(!is_null($IndexOffset)){
				$URL['IndexOffset'] = $IndexOffset;
			}
			if(isset($URL)){
				$this->Urls[] = $URL;
			}
		}
		
		/* Add an Attribution to Attributions	
		|
		|	@param {String} $Attribution
		*/		
		public function addAttribution($Attribution = NULL){
			if(!is_null($Attribution)){
				$this->Attributions[] = $Attribution;
			}
		}

		/* Set the language property in Language	
		|
		|	@param {String} $Language
		*/		
		public function setLanguage($Language = NULL){
			if(!is_null($Language)){
				$this->Language = $Language;
			}
		}
		
		/*	
		| A function to add a Developer as a string to the Developers Array
		|	@param {String} $Developer - The Developer information as string
		*/
		public function addDeveloper($Developer = NULL){
			if(!is_null($Developer)){
				$this->Developers[] = $Developer;
			}
		}
		
		/*	
		| A function to set the Adult Content string
		|	@param {String} $Developer - The adult information in string format
		*/
		public function setAdultContent($AdultContent){
			if(!is_null($AdultContent)){
				$this->AdultContent = $AdultContent;
			}
		}
	}
?>