<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

class config
{
    public $config = array();
    
    public function __construct($config_file)
    {
        if (file_exists($config_file)) {
            $this->config = parse_ini_file($config_file, true);
            return true;
        } else {
            return false;
        }
    }
    
    public function get($in)
    {
        $x = explode("/", $in);
        
        $var = $this->config;
        $val = false;
        foreach ($x AS $up) {
            if (isset($var[$up])) {
                $var = $var[$up];
            }
        }
        
        return $var;
    }
	
	function write($filename)
	{
		if (is_writable($filename)) {
			$res = array();
			foreach ($this->config AS $key => $val) {
				if (is_array($val)) {
					$res[] = "[$key]";
					foreach($val AS $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
				}
				else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
			}
			file_put_contents($filename, implode("\r\n", $res));
			
			return true;
		}
		
		return false;
	}
}
?>