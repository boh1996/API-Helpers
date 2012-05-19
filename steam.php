<?php
include "request_helpers.php";
class Steam{

	/**
	 * If this is set to true cURL is used else is file_get_contens used
	 * @since 1.0
	 * @access private
	 * @var boolean
	 */
	private $_webRequest = true;

	/**
	 * This function is the constructor, it checks if cURL requests are available
	 * @since 1.0
	 * @access public
	 */
	public function __construct(){
		$this->_webRequest = isCurlInstalled();
	}
}
?>