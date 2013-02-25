<?php
include "libs/PQLite.php";
class NewOCR {

	/**
	 * The url to the image to OCR
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $imageUrl = null;

	/**
	 * The language wordbook to use to do OCR
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $language = null;

	/**
	 * An array to use to validate the language
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	public $languages = null;

	/**
	 * A parameter send to the API if the page layout should be analysed
	 * @since 1.0
	 * @access public
	 * @var boolean
	 */
	public $pageLayoutAnalysis = true;

	/**
	 * The NewOCR url
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	private $_apiUrl = "http://www.newocr.com/";

	/**
	 * The last returned result
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $lastResult = null;

	/**
	 * The constructor function
	 * @since 1.0
	 * @access public
	 */
	public function __construct () {
		$this->languages = array(
			"dan",
			"dan~cuneiform",
			"dan-frak",
			"eng",
			"eng~cuneiform",
			"afr",
			"sqi",
			"ara",
			"aze",
			"eus",
			"bel",
			"ben",
			"bul",
			"bul~cuneiform",
			"cat",
			"chr",
			"chi_sim",
			"chi_tra",
			"hrv",
			"hrv~cuneiform",
			"ces",
			"cze~cuneiform",
			"nld",
			"dut~cuneiform",
			"enm",
			"epo",
			"est",
			"est~cuneiform",
			"fin",
			"frk",
			"fra",
			"fra~cuneiform",
			"frm",
			"glg",
			"deu",
			"ger~cuneiform",
			"deu-frak",
			"ell",
			"grc",
			"heb",
			"heb-seg",
			"heb-ras",
			"hin",
			"hun",
			"hun~cuneiform",
			"isl",
			"ind",
			"ita",
			"ita~cuneiform",
			"ita_old",
			"jpn",
			"kan",
			"kor",
			"lat_lid",
			"lav",
			"lav~cuneiform",
			"lit",
			"lit~cuneiform",
			"mkd",
			"msa",
			"mal",
			"mlt",
			"nor",
			"pol",
			"pol~cuneiform",
			"por",
			"por~cuneiform",
			"ron",
			"rum~cuneiform",
			"rus",
			"rus~cuneiform",
			"ruseng~cuneiform",
			"srp",
			"srp~cuneiform",
			"slk",
			"slo~cuneiform",
			"slk-frak",
			"slv",
			"slv~cuneiform",
			"spa",
			"spa~cuneiform",
			"spa_old",
			"swa",
			"swe",
			"swe~cuneiform",
			"swe-frak",
			"tgl",
			"tam",
			"tel",
			"tha",
			"tur",
			"tur~cuneiform",
			"ukr",
			"ukr~cuneiform",
			"vie"
		);
	}

	/**
	 * This function recieves and filters the recieved result
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function analyse () {
		if (isset($this->language) && isset($this->imageUrl)) {
			$fields = array(
				"l" => $this->language,
				"norm" => 1,
				"ocr" => 1,
				"psm" => (int)$this->pageLayoutAnalysis,
				"url" => $this->imageUrl
			);
			$post = self::_post($this->_apiUrl,$fields);

			$pq = new PQLite($post);
			if ($post == "") return false;
			$this->lastResult = $pq->find("#ocr-result")->getInnerHTML();
			return $this->lastResult;
		} else {
			return false;
		}
 	}

 	/**
 	 * This function sends a POST request and returns the result
 	 * @since 1.0
 	 * @access private
 	 * @param  string $url    The url to post too
 	 * @param  array $fields The post fields to send
 	 * @return string 		  The result
 	 */
 	private function _post ($url, $fields) {
 		$ch = curl_init();

 		$fields_string = "";

 		foreach($fields as $key => $value) { 
 			$fields_string .= $key .'='. $value. '&'; 
 		}

		rtrim($fields_string, '&');

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		$result = curl_exec($ch);

		curl_close($ch);
		return $result;
 	}

	/**
	 * This function sets the language parameter
	 * @since 1.0
	 * @access public
	 * @param  string $language The language "string"
	 * @return boolean
	 */
	public function language ($language) {
		if (in_array($language, $this->languages)) {
			$this->language = $language;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This function sets the imageUrl parameter
	 * @since 1.0
	 * @access public
	 * @param  string $image The image url to set
	 * @return boolean
	 */
	public function image ($image) {
		if (self::isImage($image)) {
			$this->imageUrl = $image;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This function checks if a url is the url of an image
	 * @since 1.0
	 * @access public
	 * @param  string  $url The url to check
	 * @return boolean
	 */
	public function isImage ($url) {
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch);

		$type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		curl_close($ch);

		return (strpos($type, "image") !== false);
	}

	/**
	 * This function checks if the page contains an image tag
	 * @since 1.0
	 * @access public
	 * @param  string $url The page url to check at
	 * @param array $fileTypes The filetypes to check for
	 * @return boolean|string
	 */
	public function locateImage ($url,$fileTypes = array("png","jpg")) {
		foreach ($fileTypes as $fileType) {
			$imageUrl = self::_get($url.".".$fileType);
			if ($imageUrl !== false) {
				return $url.".".$fileType;
			}
		}
		
		$result = self::_get($url);

		if ($result == "" || $result === false) return false;

		$pq = new PQLite($result);
		$imageUrl = $pq->find("img")->getAttr("src");
		return ($imageUrl != "") ? $imageUrl : false;
	}

	/**
	 * This function performs a get request
	 * @since 1.0
	 * @access private
	 * @param  string $url The get url
	 * @return boolean|string
	 */
	private function _get ($url) {
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);

		curl_close($ch);

		return ($result != "" && $result !== false) ? $result : false;
	}
}
?>