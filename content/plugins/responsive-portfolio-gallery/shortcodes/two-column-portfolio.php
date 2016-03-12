<?php

new Two_Column_Portfolio_Shortcode();
// Base class 'Responsive_Portfolio_Gallery_Shortcodes' defined in 'shortcodes/shortcodes.php'.
class Two_Column_Portfolio_Shortcode extends Responsive_Portfolio_Gallery_Shortcodes {
	var $shortcode = '2-column-responsive-portfolio';
	
	/* Contents of this function will be executed by the [2-column-responsive-portfolio] shortcode. */
	function shortcode(){
		/* Calls the content() function with $columns = 2 */
		$content = $this->content(2);
		return $content;
	}
}

