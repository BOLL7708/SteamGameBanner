<?php

class Config
{
	private static $instance;
	private $settings;

	protected function __construct() 
	{
		// Loads local config if present, this to avoid pushing private data
		$files = ['config.local.php', 'config.php']; 
		foreach($files as $path)
		{
			if(file_exists($path)) {
				$this->settings = include($path);	
				break;
			}
		}
	}

	public static function get($key, $default=null)
	{
		if(self::$instance == null) self::$instance = new Config();
		$out = self::$instance->settings[$key] ?? $default;
		return $out;
	}

	public static function print($key, $default=null)
	{
		$out = self::get($key, $default);
		if(is_bool($out)) $out = $out ? 'true' : 'false';
		return $out;
	}
}

?>