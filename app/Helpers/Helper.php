<?php

if (!function_exists('build_nested_nodes')) {

    function build_nested_nodes($array, $field = 'name') {
        if (! is_array($array))
            $array = explode('.', $array);

    	$name = array_shift($array);

    	if (count($array)>0) {
	    	return array(
	    		$field => $name,
	    		'children' => array(build_nested_nodes($array))
	    	);    		
    	}
    	else
    		return array($field => $name);
    } 
}

if (!function_exists('remove_non_ascii_for_smsc_consumption')) {

    function remove_non_ascii_for_smsc_consumption($mobile) {
        return preg_replace("/[^A-Za-z0-9]/", '', $mobile);
    } 
}