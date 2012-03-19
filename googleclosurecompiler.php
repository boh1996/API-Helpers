<?php
/**
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @author Illution <support@illution.dk>
 * @company Illution
 * @package Google API's
 * @category API Helpers
 * @subpackage Google Closure Compiler
 * @version 1.0
 */
class Google_Closure_Compiler{
	/**
	 * [Build description]
	 * @param [type] $Content           The content to compile or a ulr to the content
	 * @param string $Compilation_Level The compilation level, the options is in Compilation_Levels
	 * @param string $Output_Info       The ouput info type see the values in Output_Info
	 * @param string $Output_Format     The output format "text","json" or "xml"
	 * @see Compilation_Levels
	 * @see Output_Info
	 * @see Output_Format
	 * @since 1.0
	 * @access public
	 */
	public function Build($Content,$Compilation_Level = "SIMPLE_OPTIMIZATIONS",$Output_Info = "compiled_code",$Output_Format = "text"){
		$Url = "http://closure-compiler.appspot.com/compile";
		if(is_null($Compilation_Level)){
			$Compilation_Level = "SIMPLE_OPTIMIZATIONS";
		}
		if(is_null($Output_Info)){
		$Output_Info = "compiled_code";
		}
		if(is_null($Output_Format)){
			$Output_Format = "text";
		}
		if(self::Is_Url($Content)){
			$Input_Type = "code_url";
		} else {
			$Input_Type = "js_code";
			$Content = urlencode($Content);
		}
		$Fields = array(
			$Input_Type => $Content,
			"compilation_level" => $Compilation_Level,
			"output_info" => $Output_Info,
			"output_format" => $Output_Format
		);
		$Fields_String = "";
		foreach($Fields as $Key => $Value) 
		{ 
			$Fields_String .= $Key.'='.$Value.'&'; 
		}
		rtrim($Fields_String,'&');

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL,$Url);
		curl_setopt($ch,CURLOPT_POST,count($Fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$Fields_String);

		$Result = curl_exec($ch);

		curl_close($ch);
		return $Result;
	}

	/**
	 * This function returns the values for Compilation_Level
	 * @return Array The compilation level values
	 * @since  1.0
	 * @access public
	 */
	public function Compilation_Levels(){
		return array(
			"WHITESPACE_ONLY",
			"SIMPLE_OPTIMIZATIONS",
			"ADVANCED_OPTIMIZATIONS"
		);
	}

	/**
	 * This function returns the values for Output_Info
	 * @return Array The values for Output_Info
	 */
	public function Output_Info(){
		return array(
				"compiled_code",
				"warnings",
				"errors",
				"statistics"
			);
	}

	/**
	 * This function returns the values for Output_Format
	 * @access public
	 * @since 1.0
	 * @return Array The possible values for Output_Format
	 */
	public function Output_Format(){
		return array(
				"xml",
				"text",
				"json"
			);
	}

	/**
	 * This function tests if the input is a url
	 * @param String $Url The url to test
	 * @return Boolean If the input is a url true is returned
	 * @access private
	 * @since 1.0
	 */
	private function Is_Url($Url){
		$Matches = NULL;
		$Result = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $Url, $Matches);
		if ($Result == 1) {
			return true;
		}
		else {
			return false;
		}
	}
}	
?>