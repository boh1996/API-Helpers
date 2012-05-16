<?php
	/**
	 * This helper is written for the placeholder.it service.
	 * The url pattern that is used is as this http://placehold.it/{size}x{size}/{color}/{textColor}&text={text}
	 * @author Illution <support@illution.dk>
	 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons 3.0
	 * @package Placeholders
	 * @category Image Placeholders
	 * @subpackage Placeholder.it
	 * @version 1.0
	 * @link http://placehold.it/ The placeholder service
	*/
	class PlaceholdIt {

		/**
		 * This is the size parameter it can be deffined like this 300x200 or just 300
		 * @var string
		 * @access private
		 * @since 1.0
		 */
		private $Size = NULL;

		/**
		 * The background color parameter can be deffined like this fff
		 * @var string
		 * @access private
		 * @since 1.0
		 */
		private $Color = NULL;

		/**
		 * This is the text color parameter, it can be deffined like this fff
		 * @var string
		 * @access private
		 * @since 1.0
		 */
		private $TextColor = NULL;

		/**
		 * This is the placeholder text
		 * @var string
		 * @access private
		 * @since 1.0
		 */
		private $Text = NULL;

		/**
		 * The url to the placeholder site
		 * @var string
		 * @access private
		 * @since 1.0
		 */
		private static $Url = "http://placehold.it/";


		/**
		 * This function is the constructor of you set the size parameter it will return the placeholder image
		 * else use the Placeholder() function to generate and the parameter functions to set the values
		 * @see Placeholder()
		 * @see Size()
		 * @see $Size
		 * @see $Color
		 * @see Color()
		 * @see $TextColor
		 * @see TextColor()
		 * @see $Text
		 * @see Text()
		 * @param string $Size      The image size
		 * @param string $Color     The background color
		 * @param string $TextColor The image text color
		 * @param string $Text      The placeholder text
		 * @return string The image url
		 * @access public
		 * @since 1.0
		 */
		public function PlaceholdIt($Size =NULL,$Color = NULL,$TextColor = NULL,$Text = NULL){

			if(!is_null($Color)){
				$this->Color = $Color;
			}

			if(!is_null($TextColor)){
				$this->TextColor = $TextColor;	
			}

			if(!is_null($Text)){
				$this->Text = $Text;	
			}

			if(!is_null($Size)){
				$this->Size = $Size;
				return self::Placeholder();	
			}
		}

		/**
		 * This function sets the $Size parameter
		 * @see $Size
		 * @param string $Size The size of the image in a format like this {size}x{size} or {size}
		 * @access public
		 * @since 1.0
		 */
		public function Size($Size = NULL){
			if(!is_null($Size)){
				$this->Size = $Size;	
			}
		}

		/**
		 * This function sets the $Color parameter
		 * @see $Color
		 * @param string $Color The placeholder background color
		 * @access public
		 * @since 1.0
		 */
		public function Color($Color = NULL){
			if(!is_null($Color)){
				$this->Color = $Color;
			}
		}

		/**
		 * This function sets the $TextColor parameter
		 * @see $TextColor
		 * @param string $TextColor The placeholder image text color
		 * @access public
		 * @since 1.0
		 */
		public function TextColor($TextColor = NULL){
			if(!is_null($TextColor)){
				$this->TextColor = $TextColor;
			}
		}

		/**
		 * This function sets the $Text parameter
		 * @see $Text
		 * @param string $Text The placeholder image text
		 * @access public
		 * @since 1.0
		 */
		public function Text($Text = NULL){
			if(!is_null($Text)){
				$this->Text = $Text;
			}
		}

		/**
		 * This function generates the link for the placeholder service
		 * @access public
		 * @since 1.0
		 * @return string The placeholder image url
		 */
		public function Placeholder(){
			if(!is_null($this->Size)){
				$Return = $Url.$this->Size;
				if(!is_null($this->Color)){
					$Return .= "/".str_replace("#", "", $this->Color);
				}

				if(!is_null($this->TextColor)){
					$Return .= "/".$this->TextColor;
				}

				if(!is_null($this->Text)){
					$Return .= "&text=".urlencode($this->Text);
				}

				return $Return;
			} else {
				return "http://placehold.it/350x150/";
			}
		}
	}	
?>