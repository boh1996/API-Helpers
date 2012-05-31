<?php
/**
 * This wrapper, requests data from stats counter and outouts it in the correct format
 * @author Illution <support@illution.dk>
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @package Analytics
 * @category Analytics API's
 * @subpackage StatsCounter Global Stats
 * @version 1.0
 * @link http://http://gs.statcounter.com/ The StatsCounter Global Stats
*/
class Globals_Stats{

	/**
	 * The StatsCounter Global Stats API Endpoint
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $api_url = "http://gs.statcounter.com/chart.php";

	/**
	 * The stat type code, used to select what stat to get
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $stat_type_hidden = NULL;

	/**
	 * The region code
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $region_hidden = "ww";

	/**
	 * The current sort option,
	 * options are "date" or "key" or "key_date"
	 * date: makes an element for each date and then the different elements
	 * key: makes an element for each key and then puts all the values in there and create a ordered date item
	 * key_date: makes an element for each key, and in there for each date
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $sort_by = "date";

	/**
	 * The amount of time between the registrations to use,
	 * "weekly", "daily" etc
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $granularity = NULL;

	/**
	 * The stat to retrieve in readable text
	 * @var stríng
	 * @since 1.0
	 * @access public
	 */
	public $stat_type = NULL;

	/**
	 * The selected region to get the data from,
	 * in readable text
	 * @var string
	 * @since 1.0
	 * @access public
	 */
	public $region = "Worldwide";

	/**
	 * A conversion table from class properties to url parameters
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_parameters = array(
		"region" =>  "region",
		"region_hidden" => "region_hidden",
		"granularity" => "granularity", 
		"stat_type" => "statType",
		"stat_type_hidden" => "statType_hidden",
		"from_week_year" => "fromWeekYear",
		"to_week_year" => "toWeekYear",
		"from_year" => "fromYear",
		"to_year" => "toYear",
		"from_quater_year" => "fromQuarterYear",
		"to_quarter_year" => "toQuarterYear",
		"from_month_year" => "fromMonthYear",
		"to_month_year" => "toMonthYear",
		"from_day" => "fromDay",
		"to_day" => "toDay"
	);

	/**
	 * This is a loopup table for the stat types
	 * the format is stat_type_hidden => stat_type
	 * @var array
	 * @since 1.0
	 * @access private
	 */
	private $_stats_types = array(
		"browser" => "Browser",
		"browser_version" => "Browser Version",
		"browser_version_partially_combined" => "Browser Version (Partially Combined)",
		"mobile_browser" => "BMobile Browser",
		"os" => "BOperating System",
		"mobile_os" => "BMobile OS",
		"search_engine" => "BSearch Engine",
		"mobile_search_engine | Mobile Search",
		"mobile_vs_desktop" => "BMobile vs. Desktop",
		"resolution" => "BScreen Resolution",
		"mobile_resolution" => "BMobile Screen Resolution",
		"social_media" => "BSocial Media",
		"digg_vs_reddit" => "BDigg vs Reddit",
		"mobile_vendor" => "BMobile Vendor (Beta)"
	);

	/**
	 * This function is used to indentify what parameters are used for each "function"
	 * @since 1.0
	 * @access private
	 * @var array
	 */
	private $_functions = array(
		"weekly" => array("from_year","from_week","to_year","to_week"),
		"daily" => array("from_year","from_month","from_day","to_year","to_month","to_day"),
		"yearly" => array("from_year", "to_year"),
		"quarterly" => array("from_year","from_quater","to_year","to_quarter"),
		"monthly" => array("from_year","from_month","to_year","to_month")
	);

	/**
	 * This array is used to describe the request parameters for a function
	 * @var array
	 */
	private $_formats = array(
		"weekly" => array("from_week_year" => "{from_year}-{from_week}","to_week_year" => "{to_year}-{to_week}"),
		"daily" => array("from_month_year" => "{from_year}-{from_month}","toMonthYear" => "{to_year}-{to_month}"),
		"quarterly" => array("from_quarter_year" => "{from_year}-{from_quater}","to_quarter_year" => "{to_year}-{to_quarter}"),
		"monthly" => array("","from_month_year" => "{from_year}-{from_month}","to_month_year" => "{to_year}-{to_month}")
	);

	/**
	 * This function is used to easily set the stat types variables
	 * @param  string $stat_type The lower cased stat type("Stat Type Hidden") value
	 * @since 1.0
	 * @access public
	 */
	public function stat_type ( $stat_type ) {
		if(array_key_exists($stat_type, $this->_stats_types)){
			$this->stat_type = $this->_stats_types[$stat_type];
			$this->stat_type_hidden = $stat_type;
		}
	}

	/**
	 * This function us used to set the region,
	 * if the file "global_stats_countries.json" exists
	 * then it's used as a error correction table
	 * @param  string $region_hidden The region to use
	 * @param string $region An optional readable name of the region,
	 * only use this if you don't use the json file
	 * @since 1.0
	 * @access public
	 */
	public function region ( $region_hidden, $region = NULL ) {
		if (file_exists("global_stats_countries.json") !== false) {
			$table = json_decode(file_get_contents("global_stats_countries.json"));
			if (array_key_exists($region_hidden, $table)) {
				$this->region_hidden = $region_hidden;
				$this->region = $table[$region_hidden];
			}
		} else {
			$this->region_hidden = $region_hidden;
			$this->region = $region;
		}
	}

	/**
	 * This function checks if a file exists at the web
	 * @param  string $path The path to the file to check
	 * @return boolean
	 * @since 1.0
	 * @access private
	 * @author Spark <spark@limao.com.br>
	 */
	private function _server_file_exists( $path ){
    	return (@fopen($path,"r") == true);
	}

	/**
	 * The Class Constructor
	 * @since 1.0
	 * @access public
	 */
	public function __construct () {}

	/**
	 * This function is used to detect the granularity
	 * @param  string $function The name of the called function
	 * @since 1.0
	 * @access private
	 */
	private function _granularity ( $function ) {
		switch ($function) {
			case 'daily':
				$this->granularity = "daily";
				break;
			
			case 'weekly':
				$this->granularity = "weekly";
				break;

			case 'monthly':
				$this->granularity = "monthly";
				break;

			case 'quarterly':
				$this->granularity = "quarterly";
				break;

			case 'yearly':
				$this->granularity = "yearly";
				break;
		}
	}

	/**
	 * This function is called when someone tries to call any of the available operations:
	 * The operations and there paramters:
	 * @example
	 * weekly("from_year","from_week","to_year","to_week");
	 * daily("from_year","from_month","from_day","to_year","to_month","to_day");
	 * yearly("from_year", "to_year");
	 * quarterly("from_year","from_quater","to_year","to_quarter");
	 * monthly("from_year","from_month","to_year","to_month");
	 * @param  string $name      The function that is beeing called
	 * @param  array $arguments The function argunments
	 * @since 1.0
	 * @access public
	 * @return array|boolean
	 */
	public function __call ( $name, $arguments ) {
		if(array_key_exists($name, $this->_functions)){
			if(count($arguments) == count($this->_functions[$name])){
				self::_granularity($name);
				foreach ($this->_functions[$name] as $key => $parameter) {
					if(isset($arguments[$key])){
						$arguments[$key] = self::_validate_input($parameter, $arguments[$key]);
					}
				}
				return self::_do_function($name,$arguments);
			} else {
				trigger_error("Missing parameters in ".$name,E_USER_NOTICE);
			}
		} else {
			trigger_error($name." is not an existing function",E_USER_NOTICE);
		}
	}

	/**
	 * This function is used to validate the input parameters
	 * @param  string $parameter The name of the parameter to validate
	 * @param  string|integer $input     The input to validate
	 * @return string|integer
	 * @since 1.0
	 * @access private
	 */
	private function _validate_input ( $parameter, $input ) {
		if (strpos($parameter, "day") !== false) {
			$input = self::_validate_day($input);
		} else if (strpos($parameter, "month") !== false) {
			$input = self::_validate_month($input);
		} else if (strpos($parameter, "year") !== false) {
			$input = self::_validate_year($input);
		} else if (strpos($parameter, "quarter") !== false) {
			$input = self::_validate_quarter($input);
		} else if (strpos($parameter, "week") !== false) {
			$input = self::_validate_week($input);
		}
		return $input;
	}

	/**
	 * This function gets the file contents and returns it
	 * @param  string $function  The function/operation to perform
	 * @param  array $arguments The function arguments
	 * @return boolean|array
	 * @since 1.0
	 * @access private
	 */
	private function _do_function ( $function, $arguments ) {
		$params = array();
		foreach ($this->_functions[$function] as $key => $parameter) {
			if(isset($arguments[$key])){
				$params[$parameter] = self::_validate_input($parameter, $arguments[$key]);
			}
		}
		$params = self::_convert_parameters($params, $function);
		$url = self::_build_url($params);
		if (self::_server_file_exists($url)) {
			$raw = file_get_contents($url);
			if(self::_number_of_lines($raw) < 2){
				return FALSE;
			} else {
				return self::_explode_response($raw);
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * This function explodes the CSV ordered data, and
	 * sort it in the requested format
	 * @param  string $response The raw CSV date from the API
	 * @return array
	 * @since 1.0
	 * @access private
	 */
	private function _explode_response ( $response ) {
		$return = array();
		$raw = explode("\n", $response);
		$header = str_replace('"', '', $raw[0]);
		$header = explode(",", $header);
		unset($header[0]);
		unset($raw[0]);
		unset($raw[count($raw)-1]);
		foreach ($raw as $string) {
			$row = explode(",", $string);
			$date = $row[0];
			unset($row[0]);
			switch ($this->sort_by) {
				case 'key_date':
					if($date != ""){
						foreach ($row as $key => $value) {
							if (array_key_exists($key, $header) && $value != "") {
								$return[$header[$key]][$date] = $value;
							}
						}
					}
				break;
				
				case 'key':
					if($date != ""){
						$return["time"][] = $date;
						foreach ($row as $key => $value) {
							if (array_key_exists($key, $header) && $value != "") {
								$return[$header[$key]][] = $value;
							}
						}
					}
				break;

				default:
					if($date != ""){
						$return[$date] = array();
						foreach ($row as $key => $value) {
							if (array_key_exists($key, $header) && $value != "") {
								$return[$date][$header[$key]] = $value;
							}
						}
					}
				break;
			}
	
		}
		print_r($return);
		return $return;
	}

	/**
	 * This function checks if a format descriptor is available for this parameter
	 * @param  string $function  The function to check in
	 * @param  string $parameter The parameter to check for
	 * @return boolean
	 * @since 1.0
	 * @access private
	 */
	private function _format_parameter_exists ( $function, $parameter) {
		$return = FALSE;
		foreach ($this->_formats[$function] as $variable => $format) {
			if(strpos($format, $parameter) !== false){
				$return = TRUE;
			}
		}
		return $return;
	}

	/**
	 * This function is used to replace the format description 
	 * names with the correct values
	 * @param  array $params The array of parameters to check in
	 * @param  string $string The string to replace in
	 * @return string
	 * @since 1.0
	 * @access private
	 */
	private function _replace ( $params, $string ) {
		foreach ($params as $key => $value) {
			if(strpos($string, $key) !== false){
				$string = str_replace("{".$key."}", $value, $string);
			}
		}
		return $string;
	}

	/**
	 * This function is used to change the class variables to the correct format and name
	 * before using it in the build url system
	 * @param  array $params   The array of parameters to use
	 * @param  string $function The name of the called function
	 * @return array
	 * @since 1.0
	 * @access private
	 */
	private function _convert_parameters ( $params, $function ) {
		$return = array();
		if(array_key_exists($function, $this->_formats)){
			$input_parameters = $params;
			$params = array_merge($params, $this->_formats[$function]);
			foreach ($params as $parameter => $value) {
				if (array_key_exists($parameter, $this->_formats[$function])) {
					if (array_key_exists($parameter, $this->_parameters)) {
						$return[$this->_parameters[$parameter]] = self::_replace($input_parameters, $this->_formats[$function][$parameter]);
					} else {
						$return[$parameter] = $value;
					}
				} else if (!self::_format_parameter_exists($function, $parameter)) {
					if (array_key_exists($parameter, $this->_parameters)) {
						$return[$this->_parameters[$parameter]] = $value;
					} else {
						$return[$parameter] = $value;
					}
				}
			}
		} else {
			foreach ($params as $parameter => $value) {
				if (array_key_exists($parameter, $this->_parameters)) {
					$return[$this->_parameters[$parameter]] = $value;
				} else {
					$return[$parameter] = $value;
				}
			}
		}
		return $return;
	}

	/**
	 * This function counts how many newlines in a string
	 * @param  string $string The string to check
	 * @return integer
	 * @since 1.0
	 * @access private
	 * @author marek_mar
	 */
	private function _number_of_lines ( $string ) {
		$num_lines = substr_count($string, "\n") + 1;
		return $num_lines;
	}

	/**
	 * This function validates the length of a month
	 * @param  integer $month The month to validate
	 * @return string|integer The validated input
	 * @since 1.0
	 * @access private
	 */
	private function _validate_month ( $month ) {
		if (strlen($month) < 2) {
			$month = "0".$month;
		}
		if ((int)$month > 12) {
			$month = 12;
		} else if ((int)$month < 1) {
			$month = 1;
		}
		return $month;
	}

	/**
	 * This function validates the length of a day
	 * @since 1.0
	 * @access private
	 * @param  integer $day The day to validate
	 * @return string|integer The validated input
	 */
	private function _validate_day ( $day ) {
		if (strlen($day) < 2) {
			$day = "0".$day;
		}
		if ((int)$day > 31) {
			$day = 31;
		} else if ((int)$day < 1) {
			$day = 1;
		}
		return $day;
	}

	/**
	 * This function is used to validate week inputs
	 * @param  strong|integer $week The week to validate
	 * @return string|integer The validated input
	 * @since 1.0
	 * @access private
	 */
	private function _validate_week ( $week ) {
		if (strlen($week) < 2) {
			$week = "0".$week;
		}
		if ((int)$week > 53) {
			$week = 52;
		} else if ((int)$week < 1) {
			$week = 1;
		}
		return $week;
	}

	/**
	 * This function validates the length of a year
	 * @param  integer $year The year to validate
	 * @return string|integer The validated input
	 * @since 1.0
	 * @access private
	 */
	private function _validate_year ( $year ) {
		if (strlen($year) == 2) {
			$year = "20".$year;
		} else if (strlen($year) == 3){
			$year = "2".$year;
		}
		return $year;
	}

	/**
	 * This function is used to validate quarter values
	 * @param  integer $quarter The quarter to validate
	 * @return string|integer The validated input
	 * @since 1.0
	 * @access private
	 */
	private function _validate_quarter ( $quarter ) {
		if ((int)$quarter  > 4) {
			$quarter  = 4;
		} else if ((int)$quarter  < 1) {
			$quarter  = 1;
		}
		return $quarter;
	}

	/**
	 * This function builds the parameter array
	 * @param  array $check The parameters to check
	 * @return array
	 * @since 1.0
	 * @access private
	 */
	private function _check_parameters ( $check ) {
		$params = array();
		foreach ($check as $parameter) {
			if (property_exists($this, $parameter) && !is_null($this->{$parameter})) {
				$params[$this->_parameters[$parameter]] = $this->{$parameter};
			}
		}
		return $params;
	}

	/**
	 * This function builds up the request url
	 * @param  array $extra The extra parameters
	 * @return string
	 * @since 1.0
	 * @access private
	 */
	private function _build_url (  $extra = NULL ) {
		$url = $this->api_url."?";
		$params = self::_check_parameters(array("region","region_hidden","stat_type","stat_type_hidden","granularity"));
		if(!is_null($extra)){
			$params = array_merge($params,$extra);
		}
		$param_url = "";
		if(count($params) > 0){
			$param_url .= self::_assoc_implode("=","&",$params);
			$param_url .= "&csv=1";
		} else {
			$param_url .= "csv=1";
		}
		$url .= self::_encode($param_url);
		return $url;
	}

	/**
	 * This function encodes a url
	 * @param  string $string The string to encode
	 * @return string
	 * @since 1.0
	 * @access private
	 */
	private function _encode ( $string ) {	
		return str_replace(" ", "%20", $string);
	}

	/**
	 * This function implodes a assoc array
	 * @param  string $delemiter The delemiter between the key and value
	 * @param string $element_delemiter The delemiter the different elements 
	 * @param  array $array     The array to implode
	 * @return string
	 * @since 1.0
	 */
	private function _assoc_implode ($delemiter = "&", $element_delemiter = "=", $array = NULL) {
		if(!is_null($array) && !is_null($delemiter) && !is_null($element_delemiter)){
			$return = "";
			foreach ($array as $key => $value) {
				$return .= $key . $delemiter . $value.$element_delemiter;
			}
			$return = rtrim($return,$element_delemiter);
			return $return;
		} else {
			return "";
		}
	}
}
?>