<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

class parselinks
{
    private $urls = array();
    public $baseUrl = '';
    public $maxDepth = '';
    public $ignoreParts = array('mailto', 'javascript');
    public $ignoreExtensions = array('css',
                                     'js',
                                     'zip','gzip','rar','7z','lha','cab','tar','gz','tar',
                                     'gif','png','jpg','bmp','psd','tiff','tga','pcx','psd',
                                     'avi','mov','swf','xvid','ra','qt','rm','mp2',
                                     'mp3','mp4','mid','wav',
                                     'pdf','rtf','doc','docx',
                                     'xml','rss');
    public $currentUrl = '';
    
    function parse($document)
    {
        if (strlen($document) < 10)
        {
            return false;
        }

        // startup / clean array's
        $links = array();
        $this->urls = array();
        
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($document); // , LIBXML_NOERROR
        $errors = libxml_get_errors();
        libxml_clear_errors();

        if (count($errors) == 0)
        {
            // loop each found a attribute
            foreach($doc->getElementsByTagName('a') AS $link) 
            {
                // check for href and add it to an array
                $links[] = trim($link->getAttribute('href'));
            }
        }
        else
        {
            // parse with regex
            $matches = array();
            preg_match_all('/href="([^\s"]+)/i', $document, $matches);
            foreach ($matches[1] AS $null => $url)
            {
                $links[] = $url;
            }
            unset($matches);
            unset($url);
        }
        
        if (count($links) > 0)
        {
            $checkUrl = new checkurl();
            
            foreach ($links AS $null => $href_url)
            {
                if (!isset($this->urls[$href_url]) && substr($href_url, 0, 1) != "#")
                {
                    $href_url = $checkUrl->check($this->currentUrl, 
                                                 $href_url);

                    // check if this link is in current domain.
                    if (strstr($href_url, $this->baseUrl))
                    {
                        $href_url = str_replace('amp;', '', $href_url);
                        $continue = true;

                        // check max depth of LINK
                        if ($this->maxDepth != '' && substr($href_url, 0, strlen($this->maxDepth)) != $this->maxDepth)
                        {
                            // for example if i only want links from https://www.google.nl/store
                            // http://www.google.nl/ isn't allowed but
                            // http://www.google.nl/store/apps is!
                            $continue = false;
                        }
                            
                        // check if url doesn't have parts that should be ignored!
                        if ($continue == true && is_array($this->ignoreParts) && !empty($this->ignoreParts))
                        {
                            foreach ($this->ignoreParts AS $null => $ignore)
                            {
                                if (strpos(strtolower($href_url), strtolower($ignore)))
                                {
                                    $continue = false;
                                    break;
                                }
                            }
                        }
                        
                        // last check before trusting the url.
                        if (!filter_var($href_url, FILTER_VALIDATE_URL))
                        {
                            $continue = false;
                        }

                        // last check, if extension match wrong extension.
                        $p = parse_url($href_url);
                        $ext = isset($p['path']) ? substr(strstr($p['path'], '.'), 1) : '';
                        if (strlen($ext) > 0 && array_search($ext, $this->ignoreExtensions))
                        {
                            $continue = false;
                        }
                        
                        $href_url = str_replace(array("\r\n","\r","\n"), "", $href_url);

                        // may i continue?
                        if ($continue == true)
                        {
                            $this->urls[$href_url] = true;
                        }
                    }
                }
            }
        }
        
        unset($document);
        unset($doc);
            
        libxml_use_internal_errors(false);

        return $this->urls;
    }    
}