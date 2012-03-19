<?php
/**
 * This helper get the fav icon of a url, using the google service to do so.
 * @author Illution <support@illution.dk>
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
 * @package Google API's
 * @category Fav Icon
 * @subpackage Google Fav Icon Service
 * @version 1.0
 */
class Google_FavIcon_Service{

	/**
	 * The url to the Google Service
	 * @var string
	 */
	private $Url = "http://www.google.com/s2/u/0/favicons?domain={domain}";

	/**
	 * The supported protocols that should be removed from the url
	 * @var array
	 * @access private
	 * @since 1.0
	 */
	private $Protocols = array("https://","http://","HTTP://","HTTPS://","ftp://","FTP://","FTPS://","ftps://");

	/**
	 * This function is the constructor
	 * @since 1.0
	 * @access public
	 */
	public function Google_FavIcon_Service(){
	}

	/**
	 * This function returns an image url to the fav icon
	 * @param string $Url The url to get the fav icon of
	 * @return string The image url to the favicon
	 * @access public
	 * @since 1.0
	 */
	public function FavIcon($Url = NULL){
		if(!is_null($Url)){
			foreach ($this->Protocols as $Protocol) {
				$Url = str_replace($Protocol, "", $Url);
			}
			$RequestUrl = str_replace("{domain}", $Url , $this->Url);
			return $RequestUrl;
		}
	}
}

?>