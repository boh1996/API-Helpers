<?php
/**
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @author Illution <support@illution.dk>
 * @name {:Google Calculator API}
 * @company Illution
 * @package Google API's
 * @category API Helpers
 * @subpackage Google Calculator API
 * @version 1.0
 */
class Google_Calculator{
	/**
	 * This function converts from one currency to another via google calculator
	 * @param Number $Amount The amount to convert
	 * @param String $From   The curency to convert from 
	 * @param String $To     The currentcy to convert to
	 * @since 1.0
	 * @return String the result
	 * @access public
	 * @example
	 * Currency(100,"USD","DKK");
	 * @return Boolean||String [The result or false if error
	 */
	public function Currency($Amount = "",$From = "",$To = ""){
		if($Amount != ""&& $To != "" && $From != ""){
			$Amount = urlencode($Amount);
			$To = urlencode($To);
			$From = urlencode($From);
			$Url = "http://www.google.com/ig/calculator?hl=en&q=$Amount$From=?$To";
			$Curl = curl_init();
			curl_setopt ($Curl, CURLOPT_URL, $Url);
		    curl_setopt ($Curl, CURLOPT_RETURNTRANSFER, 1);
		    $Raw = curl_exec($Curl);
		    $Data = explode('"', $Raw);
		    $Return = explode(' ', $Data[3]);
		    if($Data[1] != "" && $Data[2] != ""){
				return $Return[0];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * This function calculate a calculation via Google Calculator API
	 * @param String $Calculation The calculation to calculate with functions like sqrt()
	 * @access public
	 * @since 1.0
	 * @return String The result of the calculation
	 * @example
	 * Calculate("5*9+(sqrt 10)^3=");
	 */
	public function Calculate($Calculation){
		$Url = "http://www.google.com/ig/calculator?hl=en&q=".urlencode($Calculation);
	    $Raw = file_get_contents($Url);
	    $Data = explode('"', $Raw);
	   	if($Data[1] != "" && $Data[2] != ""){
	   		$Data = explode(' ', $Data[3]);
			return $Data[0];;
		} else {
			return false;
		}
	}
}

$Calc = new Google_Calculator();
var_dump($Calc->Currency("llama","llama","llama"));
?>