<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

class cache
{
    public $cacheTime = 600; // 10 minutes;
    private $cachePath = '';
    private $cacheFile = '';
    
    public function __construct($path, $filename)
    {
        if (!is_dir($path))
        {
            if (!@mk_dir($path))
            {
                die("path bestaat niet");
            }
        }
        
        $filename = str_replace("/", 
                                "-", 
                                $filename);
        
        if (strlen($filename) == 0 OR
            (file_exists($path . "/" . $filename) &&
             !is_readable($path . "/" . $filename)))
        {
            die("cache bestand is niet benaderbaar");
        }
        
        $this->cacheFile = $path . "/" . $filename;
        $this->cachePath = $path;
    }
    
    public function status($checkTime = 0, $checkVar = '')
    {
        if (file_exists($this->cacheFile) &&
            is_readable($this->cacheFile))
        {
            if ($checkVar != '')
            {
                // check for variable in cache
                $f = fopen($this->cacheFile, "r");
                if ($f)
                {
                    $line = fgets($f); 
                    fclose($f);
                    
                    if (strlen($checkVar) >= strlen($line) &&
                        substr($line, 0, strlen($checkVar)) == $checkVar)
                    {
                        return false;
                    }
                }
            }
            elseif ($checkTime == 0)
            {
                // return if cache needs to be reset.
                // use current time.
                return (time() > (filemtime($this->cacheFile) + $this->cacheTime)) ? true : false;
            }
            else
            {
                return ($checkTime > filemtime($this->cacheFile)) ? true : false;
            }
        }

        return true;
    }
    
    public function getCache()
    {
        // read cache file
        if (file_exists($this->cacheFile) &&
            is_readable($this->cacheFile))
        {
            // read file into buffer
            $buf = file_get_contents($this->cacheFile);
            $x = strpos($buf, "\r\n");
            // return data
            return unserialize(substr($buf, stripos($buf, "\r\n")+2, strlen($buf)));
        }
    }
    
    public function setCache($data, $setFileTime = 0, $checkVar = '')
    {
        // save cache
        if (!file_exists($this->cacheFile) OR
            is_readable($this->cacheFile))
        {
            // try to open file
            $fp = fopen($this->cacheFile, "w+");

            // if file is opend and locked...
            if ($fp &&
                flock($fp, 
                      LOCK_EX)) 
            {
                // clean file
                ftruncate($fp, 
                          0);
                // check line
                fwrite($fp, 
                       $checkVar . "\r\n");
                // data
                fwrite($fp, 
                       serialize($data));
                // release lock
                flock($fp, 
                      LOCK_UN);
                // close file
                fclose($fp);
            }
            
            // if time should be altered, alter it
            if ($setFileTime != 0)
            {
                // touch the file
                touch($this->cacheFile,
                      $setFileTime);
            }
        }        
    }
}

?>