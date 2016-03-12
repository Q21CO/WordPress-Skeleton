<?php

new Three_Column_Portfolio_Shortcode();
// Base class 'Responsive_Portfolio_Gallery_Shortcodes' defined in 'shortcodes/shortcodes.php'.
class Three_Column_Portfolio_Shortcode extends Responsive_Portfolio_Gallery_Shortcodes {
	var $shortcode = '3-column-responsive-portfolio';
	
	/* Contents of this function will be executed by the [3-column-responsive-portfolio] shortcode. */
	function shortcode(){
		$content = $this->content(3);
		return $content;
	}
	
}
