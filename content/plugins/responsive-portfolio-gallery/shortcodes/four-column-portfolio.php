<?php

new Four_Column_Portfolio_Shortcode();
// Base class 'Responsive_Portfolio_Gallery_Shortcodes' defined in 'shortcodes/shortcodes.php'.
class Four_Column_Portfolio_Shortcode extends Responsive_Portfolio_Gallery_Shortcodes {
	var $shortcode = '4-column-responsive-portfolio';
	
	/* Contents of this function will be executed by the [4-column-responsive-portfolio] shortcode. */
	function shortcode(){
		$content = $this->content(4);
		return $content;
	}
	
}
