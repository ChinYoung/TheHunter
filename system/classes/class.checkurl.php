<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

class checkUrl
{
    function unparse_url($parsed_url) 
    { 
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
        $pass     = ($user || $pass) ? "$pass@" : ''; 
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
        return "$scheme$user$pass$host$port$path$query"; 
    } 

    function check($currentUrl, $foundUrl) 
    {
        $p = parse_url($currentUrl);
       
        $foundUrl = $this->unparse_url(parse_url($foundUrl));
        if (isset($p['path']) && $p['path'] == "/")
        {
            // remove last / from path.
            $currentUrl = substr($currentUrl, 0, -1);
        }
        
        if (preg_match('/^[a-z]+:\/\//', $foundUrl)) 
        {
            return $foundUrl;
        }

        $matches;
        preg_match('/^([a-z]+:\/\/)(.+)?/', $currentUrl, $matches);
        $iets    = $matches[1];
        $baseUrl = $matches[2];

        if(preg_match('/^\?/', $foundUrl)) 
        {
            $baseUrl = preg_replace('/\?.*$/', '', $baseUrl);
            return "$iets$baseUrl$foundUrl";
        }
        elseif(preg_match('/^\//', $foundUrl)) 
        {
            $baseUrl = preg_replace('/\/.+/', '', $baseUrl);
            $foundUrl = substr_replace($foundUrl, "" ,0, 1);
            return $this->check("$iets$baseUrl", $foundUrl);
        }
        elseif (preg_match('/^\.\//', $foundUrl)) 
        {
            $baseUrl = preg_replace('/\/$/', '', $baseUrl);
            $foundUrl = substr_replace($foundUrl, "" ,0, 2);
            return $this->check("$iets$baseUrl", $foundUrl);
        }
        elseif (preg_match('/^\.\.\//', $foundUrl)) 
        {
            $baseUrl = preg_replace('/\/\w+\/?$/', '', $baseUrl);
            $foundUrl = substr_replace($foundUrl, "" , 0, 3);
            return $this->check("$iets$baseUrl", $foundUrl);
        }
        else 
        {
            if (!$foundUrl) 
            {
                $url = $currentUrl;
            }
            else 
            {
                if($currentUrl = preg_replace('/\/[^\/]+\.(?:html?|php|cgi|txt|rss|doc|docx|pdf)(?:\?.+)?$/', '', $currentUrl)) 
                {
                    $url = "$currentUrl/$foundUrl";
                }
                else 
                {
                    $currentUrl = preg_replace('/\/?$/', '', $currentUrl);
                    $url = "$currentUrl/$foundUrl";
                }
            }

            $matches;
            if (preg_match('/(.+\/[^\.]+)(\.{1,2}\/.+)/', $url, $matches)) 
            {
                return $this->check($matches[1], $matches[2]);
            }
            
            return $url;
        }
    }
}
?>