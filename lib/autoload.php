<?php
spl_autoload_register(function($classname) {
	$fn = ROOT."lib/class.{$classname}.php";
	if(file_exists($fn)) {
		require_once($fn);
	}else{
		$fn = ROOT."{$classname}.php";
		if(file_exists($fn)) {
			require_once($fn);
		}
	}
},true);