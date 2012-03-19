<?php
/**
 * This function uses the Open Exchange Rates API to get the data, and the base data is in dollars.
 * @author Illution <support@illution.dk>
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @package Currency
 * @category Currentcy Conversion
 * @subpackage Open Exchange Rates
 * @version 1.1
 */
class OpenExchangeRates{

	/**
	 * The list of all currencies and their full names
	 * @var Object
	 * @since 1.0
	 * @access private
	 */
	public $Currentcies = NULL;

	/**
	 * This variable contains the currency values
	 * @var Object
	 * @since 1.0
	 * @access public
	 */
	public $Current_Currencies = NULL;

	/**
	 * The constructor
	 * @access public
	 * @since 1.0
	 */
	public function OpenExchangeRates(){
		define("CURRENCIES_API_URL","https://raw.github.com/currencybot/open-exchange-rates/master/currencies.json");
		define("LATEST_API_URL","http://openexchangerates.org/latest.json");
		define("HISTORY_API_URL","http://openexchangerates.org/historical/%DATE%.json");
		self::Get_Current_Currencies();
	}

	/**
	 * This function get the list of current currencies
	 * @access private
	 * @since 1.0
	 * @return Object The currencies that the api have available
	 */
	public function Get_Currencie_List(){
		$Raw = file_get_contents(CURRENCIES_API_URL);
		$this->Currentcies = json_decode($Raw);
		return json_decode($Raw);
	}

	/**
	 * This function gets the latest currentcy data
	 * @access public
	 * @since 1.1
	 * @return object The current currencies in an std class object
	 */
	public function Get_Current_Currencies(){
		$Raw = file_get_contents(LATEST_API_URL);
		$this->Current_Currencies = json_decode($Raw);
		return json_decode($Raw);
	}

	/**
	 * This function get the currrent rate for the $Currency, with a base in dollars
	 * @param string $Currency The currency to get data for
	 * @example
	 * @since 1.0
	 * @access public
	 * Get_Rate("DKK");
	 */
	public function Get_Rate($Currency = NULL){
		if(!is_null($Currency)){
			if(isset($this->Current_Currencies->rates->{$Currency}) && $this->Current_Currencies->rates->{$Currency} != NULL){
				return $this->Current_Currencies->rates->{$Currency};
			} else {
				return false;
			}

		} else {
			return false;
		}
	}

	/**
	 * This function gets currency data from a specific date
	 * @param date $Date      The date you wish to find data from in format YYYY-MM-DD
	 * @param string $Currentcy The currency to get data for in three digit format 
	 * @access public
	 * @since 1.1
	 * @example
	 * Get_Historical("2011-01-01","DKK");
	 * @return Boolean||string The currency data on the specific date
	 */
	public function Get_Historical($Date = NULL,$Currentcy = NULL){
		if(!is_null($Date) && !is_null($Currentcy)){
			$Data = self::Get_Historical_Data($Date);
			if(!is_null($Data) && !empty($Data) && $Data != false){
				return $Data->rates->{$Currentcy};
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * This function gets historical data from the api and returns it
	 * @param String $Date     A date in format YYYY-MM-DD
	 * @return Object||Boolean The JSON decode object or false if the operation fails
	 * @since 1.0
	 * @access public
	 * @example
	 * Get_Historical_Data("2011-10-18","USD");
	 */
	public function Get_Historical_Data($Date = NULL){
		if(!is_null($Date)){
			if(gettype($Date) == "integer"){
				$Date = date("Y-m-d",$Date);
			}
			if(file_exists(str_replace("%DATE%", $Date, HISTORY_API_URL))){
				$Raw = file_get_contents(str_replace("%DATE%", $Date, HISTORY_API_URL));
				if($Raw != NULL){
					return json_decode($Raw);
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
?>