<?php

class Helper {
	
	/**
	 * 
	 */
	public static function br2nl($string){
    	return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}
	
	/**
	 * 
	 */
	public static function log( $string ){
		$nl = "\n";
		
		$out  = "[" . date("d.m.Y H:i:s") . "]";
		$out .= " " . $string;
		$out .= $nl;
		
		echo $out;
	}
	
	public static function removeUmlaute( $string ){

    	$from = array("Ä", "ä", "Ü", "ü" ,"Ö", "ö", "ß", "$" );
		$to = array("Ae", "ae", "Ue", "ue", "Oe", "oe", "ss", "(USD)");

		return str_replace($from, $to, $string);

	}
	
	public static function strip_signature( $string ){
		
		$content = explode("\n",$string);
		$out = ""; $found = false;
		
		foreach( $content as $line ){
			if( trim($line) == '--' ) $found = true;
			if( $found != true ) $out .= $line . "\n"; 		
		}
		// var_dump( $content );
		return $out;
		
	} 
	
	public static function removeEmptyLines( $string ){
		
		$content = explode("\n",$string);
		$out = ""; $found = false;
		
		for($i=0;$i<(count($content)-1);$i++){
			$line = $content[$i];
			$next_line = $content[$i+1];
			
			if( !(trim($line) == '' && trim($next_line) == '') ) $out .= $line . "\n"; 		
		}
		// var_dump( $content );
		return $out;
		
	} 	

}