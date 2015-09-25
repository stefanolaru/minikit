<?php 

class MinikitShortcodes extends Minikit {

	function __construct() {
	
		// foundation column
		add_shortcode('column', array($this, 'column'));
		
		// foundation row
		add_shortcode('row', array($this, 'row'));
		
		// add url shortcode, to be used in content area to retrieve site_url()
		add_shortcode('url', array($this, 'get_url'));
		
	}
	
	function column($atts,$content=null) {
		$count = !empty($atts['count'])?$atts['count']:6;
		$class = !empty($atts['class'])?' '.trim($atts['class']):'';
		$equalizer = !empty($atts['equalizer'])?'  data-equalizer-watch':'';
		//	
		$html = '<div class="small-12 medium-'.$count.' columns'.$class.'"'.$equalizer.'>';
		if(!empty($atts['wrap'])) {
			$html .= '<div class="'.$atts['wrap'].'"><div class="img">';
		}
		$html .= do_shortcode($content);
		if(!empty($atts['wrap'])) {
			$html .= '</div></div>';
		}
		$html .= '</div>';
		
		return $html;
	}
	
	function row($atts,$content=null) {
		$class = !empty($atts['class'])?' '.trim($atts['class']):'';
		$equalizer = !empty($atts['equalizer'])?'  data-equalizer':'';
		//	
		$html = '<div class="row'.$class.'"'.$equalizer.'>';
		$html .= do_shortcode($content);
		$html .= '</div>';
		return $html;
	}
	
	function get_url() {
		return site_url();
	}
	
}

new MinikitShortcodes;