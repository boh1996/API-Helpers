<?php
/**
 * @name		Open Search Description Document Generator
 * @author		Bo Thomsen
 * @company		Illution
 * @version 	1.0
 * @url			http://illution.dk
 * @license		MIT License
 * @date		30/11-2011
 */

class OpenSearchDocument{
	
	/* Description Features */
	private $SyndicateRights = array('open','limited','private','closed');
	private $ImageElements = array('Height','Width','Type','Image');
	private $RelValues = array('results','suggestions','self','collection');
	private $LocalRole = array('request','example','related','correction','subset','superset');
	private $StandardSpecifications = array('http://a9.com/-/spec/opensearch/1.1/');
	private $UrlTemplate = array('Template','Type','Rel','IndexOffset');
	
	/* Document */
	private $Documents = array();
	private $Document = '';
	private $Output = '';
	
	/* Open Search Description */
	public $Format = 'OpenSearchDescription';
	public $OSDDSpecifications = array('http://a9.com/-/spec/opensearch/1.1/');
	public $XMLVersion = '1.0';
	public $XMLEncoding = 'UTF-8';
	public $ShortName = '';
	public $LongName = '';
	public $Description = '';
	public $Tags = array();
	public $Contacts = array();
	public $Urls = array();
	public $Images = array();
	public $Queries = array();
	public $Developers = array();
	public $Attributions = array();
	public $SyndicationRight = '';
	public $AdultContent = '';
	public $Language = '';
	public $OutputEncoding = 'UTF-8';
	public $InputEncoding = 'UTF-8';
	
	/* A function to set the internal variables from a Open Search Class
	|
	| @param {Object} $OpenSearch - The OpenSearch Class
	*/
	private function SetOpenDocument($OpenSearch = NULL){
		if(count($OpenSearch->OSDDSpecifications)>1){
			$this->OSDDSpecifications = $OpenSearch->OSDDSpecifications;
		}
		if($OpenSearch->XMLVersion != ''){
			$this->XMLVersion = $OpenSearch->XMLVersion;
		}
		if($OpenSearch->XMLEncoding != ''){
			$this->XMLEncoding = $OpenSearch->XMLEncoding;
		}
		if($OpenSearch->ShortName != ''){
			$this->ShortName = $OpenSearch->ShortName;
		}
		if($OpenSearch->LongName != ''){
			$this->LongName = $OpenSearch->LongName;
		}
		if($OpenSearch->Description != ''){
			$this->Description = $OpenSearch->Description;
		}
		if(count($OpenSearch->Tags) >0){
			$this->Tags = $OpenSearch->Tags;
		}
		if(count($OpenSearch->Contacts)>0){
			$this->Contacts = $OpenSearch->Contacts;
		}
		if(count($OpenSearch->Urls)>0){
			$this->Urls = $OpenSearch->Urls;
		}
		if(count($OpenSearch->Images)>0){
			$this->Images = $OpenSearch->Images;
		}
		if(count($OpenSearch->Query)>0){
			$this->Queries = $OpenSearch->Query;
		}
		if(count($OpenSearch->Developers)>0){
			$this->Developers = $OpenSearch->Developers;
		}
		if(count($OpenSearch->Attributions)>0){
			$this->Attributions = $OpenSearch->Attributions;
		}
		if($OpenSearch->SyndicationRight != ''){
			$this->SyndicationRight = $OpenSearch->SyndicationRight;
		}
		if($OpenSearch->AdultContent != ''){
			$this->AdultContent = $OpenSearch->AdultContent;
		}
		if($OpenSearch->Language != ''){
			$this->Language = $OpenSearch->Language;
		}
		if($OpenSearch->OutputEncoding != ''){
			$this->OutputEncoding = $OpenSearch->OutputEncoding;
		}
		if($OpenSearch->InputEncoding != ''){
			$this->InputEncoding = $OpenSearch->InputEncoding;
		}
	}
	
	/*
	| @return {String} - Returns the "Open Serach Description Document" as a string
	*/
	public function Document(){
		return $this->Output;	
	}

	/* The contructor
	| @param {Object} $OpenSearch - If you wish to use the Open Search Class to set the internal variables{Optional}
	*/
	public function OpenSearchDocument($OpenSearch = NULL){
		if(!is_null($OpenSearch)){
			self::SetOpenDocument($OpenSearch);
		}
		
		header('Content-Type: text/xml');
		
		/* XML Header */
		$this->Documents[] = '<?xml version="'.$this->XMLVersion.'" encoding="'.$this->XMLEncoding.'"?>';
		
		/* Header */
		$this->Documents[] = self::Generate_Specification();
		
		/* Content */
		self::Generate_Content();
		
		/* Footer */
		$this->Documents[] = self::Generate_Specification_Bottom();
		
		/* Generate Document String */
		$this->Output = self::Generate_Document();
	}
	
	private function Generate_Document(){
		$String = '';
		//print_r($this->Documents);
		foreach($this->Documents as $Element){
			$String .= $Element;
		}
		return $String;
	}
	
	private function Generate_Specification_Bottom(){
		if($this->Format == 'OpenSearchDescription'){
			return '</OpenSearchDescription>';	
		}
	}
	
	private function Generate_Content(){
		/* Short Name */
		if(self::ShortName() != ''){
			$this->Documents[] = self::ShortName();
		}
		
		/* Long Name */
		if(self::LongName() != ''){
			$this->Documents[] = self::LongName();
		}
		
		/* Description */
		if(self::Description() != ''){
			$this->Documents[] = self::Description();
		}
		
		/* Tags */
		if(self::Tags() != ''){
			$this->Documents[] = self::Tags();
		}
		
		/* Contact */
		if(self::Contact() != ''){
			$this->Documents[] = self::Contact();
		}
		
		/* Language */
		if(self::Language() != ''){
			$this->Documents[] = self::Language();
		}
		
		/* Syndicate Right */
		if(self::SyndicateRight() != ''){
			$this->Documents[] = self::SyndicateRight();
		}
		
		/* Adult Content */
		if(self::AdultContent() != ''){
			$this->Documents[] = self::AdultContent();
		}
		
		/* Attribution */
		if(self::Attribution() != ''){
			$this->Documents[] = self::Attribution();
		}
		
		/* Image */
		if(self::Image() != ''){
			$this->Documents[] = self::Image();
		}

		/* Developer */
		if(self::Developer() != ''){
			$this->Documents[] = self::Developer();
		}
		
		/* Url */
		if(self::Url() != ''){
			$this->Documents[] = self::Url();
		}
		
		/* Query */
		if(self::Query() != ''){
			$this->Documents[] = self::Query();
		}
		
		/* Input Encoding */
		if(self::InputEncoding() != ''){
			$this->Documents[] = self::InputEncoding();
		}
		
		/* Output Encoding */
		if(self::OutputEncoding() != ''){
			$this->Documents[] = self::OutputEncoding();
		}
	}
	
	private function Generate_Specification(){
		$Header = '';
		if($this->Format == 'OpenSearchDescription'){
			$Header = '<OpenSearchDescription ';
			$Footer = '';
			foreach($this->OSDDSpecifications as $Specification){
				if($Specification == 'http://a9.com/-/spec/opensearch/1.1/'){
					$Footer .= 'xmlns="'.$Specification.'"';
				}
				else{
					$Footer .= ' '.$Specification;
				}
			}
			$Header .= ' '.$Footer;
			$Header .= '>';
		}
		return $Header;
	}
	
	private function Image(){
		if(count($this->Images)>0){
			$Images = array();
			foreach($this->Images as $Image){
				if(isset($Image['Image'])){
					$String = '<Image';
					if(isset($Image['Height'])){
						$String .= ' height="'.$Image['Height'].'"';
					}
					if(isset($Image['Width'])){
						$String .= ' width="'.$Image['Width'].'"';
					}
					if(isset($Image['Type'])){
						$String .= ' type="'.$Image['Type'].'"';
					}
					$String .= '>';
					$String .= $Image['Image'];
					$String .= '</Image>';	
					$Images[] = $String;
				}
			}
			$Return = '';
			foreach($Images as $ImageElement){
				$Return .= $ImageElement;
			}
			return $Return;
		}
		else{
			return '';	
		}	
	}
	
	private function Url(){
		if(isset($this->Urls)){
			$Urls = array();
			foreach($this->Urls as $Url){
				if(isset($Url['Template'])){
					$String = '<Url';
					if(isset($Url['Type'])){
						$String .= ' type="'.$Url['Type'].'"' ;
					}
					if(isset($Url['Rel'])){
						$String .= ' rel="'.$Url['Rel'].'"' ;
					}
					if(isset($Url['IndexOffset'])){
						$String .= ' indexOffset="'.$Url['IndexOffset'].'"' ;
					}
					if(isset($Url['Template'])){
						$String .= ' template="'.$Url['Template'].'"' ;
					}
					$String .= '/>';
					$Urls[] = $String;
				}
			}
			$Return = '';
			foreach($Urls as $UrlElement){
				$Return .= $UrlElement;
			}
			return $Return;
		}
		else{
			return '';	
		}		
	}
	
	private function Query(){	
		if(count($this->Queries)>0){
			$Queries = array();
			foreach($this->Queries as $Query){
				$String = '<Query ';
				if(array_key_exists('Role',$Query)){
					$String .= 'role="'.$Query['Role'].'"';;
				}
				if(array_key_exists('SearchTerms',$Query)){
					$String .= ' searchTerms="'.$Query['SearchTerms'].'"';
				}
				if(array_key_exists('Specification',$Query)){
					$String .= ' xmlns:custom="'.$Query['Specification'].'"';
				}
				if(array_key_exists('Count',$Query)){
					$String .= ' count="'.$Query['Count'].'"';
				}
				if(array_key_exists('StartIndex',$Query)){
					$String .= ' startIndex="'.$Query['StartIndex'].'"';
				}
				if(array_key_exists('StartPage',$Query)){
					$String .= ' startPage="'.$Query['StartPage'].'"';
				}
				if(array_key_exists('TotalResults',$Query)){
					$String .= ' totalResults="'.$Query['TotalResults'].'"';
				}
				if(array_key_exists('Title',$Query)){
					$String .= ' title="'.$Query['Title'].'"';
				}
				if(array_key_exists('Language',$Query)){
					$String .= ' language="'.$Query['Language'].'"';
				}
				if(array_key_exists('InputEncoding',$Query)){
					$String .= ' inputEncoding="'.$Query['InputEncoding'].'"';
				}
				if(array_key_exists('OutputEncoding',$Query)){
					$String .= ' outputEncoding="'.$Query['OutputEncoding'].'"';
				}
				if(array_key_exists('Custom',$Query)){
					$String .= ' '.$Query['Custom'];
				}
				$String .= ' />';
				$Queries[] = $String;
			}
			$Return = '';
			foreach($Queries as $QueryElement){
				$Return .= $QueryElement;
			}
			return $Return;
		}
		else{
			return '';	
		}	
	}
	
	private function Developer(){
		if(count($this->Developers)>0){
			$String = implode(',',$this->Developers);
			return '<Developer>'.$String.'</Developer>';
		}
		else{
			return '';	
		}	
	}
	
	private function Attribution(){
		if(count($this->Attributions)>0){
			$String = implode(',',$this->Attributions);
			return '<Attribution>'.$String.'</Attribution>';
		}
		else{
			return '';	
		}	
	}
	
	private function AdultContent(){
		if($this->AdultContent != ''){
			return '<AdultContent>'.$this->AdultContent.'</AdultContent>';
		}
		else{
			return '';	
		}
	}
	
	private function SyndicateRight(){
		if($this->SyndicationRight != ''){
			return '<SyndicationRight>'.$this->SyndicationRight.'</SyndicationRight>';
		}
		else{
			return '';
		}
	}
	
	private function InputEncoding(){
		if($this->InputEncoding != ''){
			return '<InputEncoding>'.$this->InputEncoding.'</InputEncoding>';
		}
		else{
			return '';	
		}
	}
	
	private function OutputEncoding(){
		if($this->OutputEncoding != ''){
			return '<OutputEncoding>'.$this->OutputEncoding.'</OutputEncoding>';
		}
		else{
			return '';	
		}
	}
	
	private function Description(){
		if($this->Description != ''){
			return '<Description>'.$this->Description.'</Description>';
		}
		else{
			return '';	
		}
	}
	
	private function Tags(){
		if(count($this->Tags)>0){
			$String = '';
			foreach($this->Tags as $Tag){
				$String .= ' '.$Tag;
			}
			return '<Tags>'.ltrim($String).'</Tags>';
		}
		else{
			return '';	
		}
	}
	
	private function Language(){
		if($this->Language != ''){
			return '<Language>'.$this->Language.'</Language>';
		}
		else{
			return '';	
		}
	}
	
	private function Contact(){
		if(count($this->Contacts)>0){
			$String = implode(',',$this->Contacts);
			return '<Contact>'.$String.'</Contact>';
		}
		else{
			return '';	
		}
	}
	
	private function ShortName(){
		if($this->ShortName != ''){
			return '<ShortName>'.$this->ShortName.'</ShortName>';
		}
		else{
			return '';	
		}
	}
	
	/*
	|
	*/
	private function LongName(){
		if($this->LongName != ''){
			return '<LongName>'.$this->LongName.'</LongName>';
		}
		else{
			return '';	
		}
	}
}
?>