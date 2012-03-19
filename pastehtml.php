<?php
/**
 * This helper, can make a new paste at the PasteHTML site
 * @package HTML Online
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @subpackage PasteHTML
 * @category Paste Sites
 * @version 1.0
 * @author Illution <support@illution.dk>
 */ 
class PasteHTML{

	/**
	 * This property contains the pastehtml api url with some tags to replace
	 * @var string
	 * @access private
	 * @since 1.0
	 * @internal This is the api url for pastehtml
	 */
	private $Api_Url = "http://pastehtml.com/upload/create?input_type={input_type}&result={result}&txt={txt}";

	/**
	 * This array contains the available input types
	 * @var array
	 * @since 1.0
	 * @access private
	 * @internal This is the available input types, it's only used for error correction
	 */
	private $Input_Types = array("html","txt","mrk");

	/**
	 * This is the available inputs in the Result parameter
	 * @var array
	 * @since 1.0
	 * @access private
	 * @internal This is only used for error correction
	 */
	private $Results = array("address","redirect");

	/**
	 * This is the constructor it's only there because it needs to
	 * @since 1.0
	 * @access public
	 */
	public function PasteHTML(){}

	/**
	 * This function make a request url and contact the pasteHTML api with your input
	 * @param string $Txt        The page content
	 * @param string $Input_Type The input type "txt","html" or "mrk"
	 * @param string $Result     The result type "address" which gives you the url to the pasteHTML result page or
	 * "redirect" which makes a api url that when clicked redirect the user to the pasteHTML result page
	 * @since 1.0
	 * @access public
	 * @example
	 * <iframe src="<?php echo $PasteHTML->Create('Hey im llama','txt','address'); ?>" ></iframe>
	 * @example
	 * echo $PasteHTML->Create("<html><head></head></html>","html","address");
	 * @example
	 * echo $PasteHTML->Create("<html><head></head></html>","mrk","address");
	 * @example
	 * echo $PasteHTML->Create("<html><head></head></html>");
	 * @example
	 * <a src="<?php echo $PasteHTML->Create('<html><head></head></html>','html','redirect'); ?>"></a>
	 */
	public function Create($Txt = NULL,$Input_Type = "html",$Result = "address"){
		if(!is_null($Txt)){
			if(!in_array($Input_Type, $this->Input_Types)){
				$Input_Type = "html";
			}
			if(is_null($Input_Type)){
				$Input_Type = "html";
			}
			if(is_null($Result)){
				$Result = "address";
			}
			if(!in_array($Result, $this->Results)){
				$Result = "address";
			}
			$Request_Url = str_replace("{input_type}", $Input_Type, str_replace("{result}", $Result, str_replace("{txt}", urlencode($Txt), $this->Api_Url)));
			if($Result == "address"){
				return file_get_contents($Request_Url);
			} else if($Result == "redirect") {
				return $Request_Url;
			}
		} else {
			return false;
		}
	}
}
?>