<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

class pages
{
    public $titleFirst      = "<<";
    public $titlePrevious   = "<";
    public $titleNext       = ">";
    public $titleLast       = ">>";
    public $titleCurrent    = "%s";
    public $titleCurrentCls = ' class="active"';

    public $linkStart = '<li%cls%><a href="';
    public $linkMid = '">';
    public $linkEnd = '</a></li> ';

    public $itemsPerPage = 0;
    public $maxPages = 5;
    public $totalItems = 0;

    function pointers($in)
    {
        $this->pointerFirst   = $in['first'];
        $this->pointerLink    = $in['link'];
        $this->pointerQuery   = $in['query'];
    }
    
    function init()
    {
        $this->returnStr = '';
        
        // start init
        $this->pages = ceil($this->totalItems / $this->itemsPerPage);

        // if current page is bigger then total pages?
        if ($this->currentPage > $this->pages)
        {
            // if current page is bugger then max pages, currentpage is maxpage
            $this->currentPage = $this->pages;
        }
        
        /* middle part */
        $this->rPage = floor(($this->maxPages -1) /2);
        
        // if page is between total pages
        if ($this->currentPage - $this->rPage > 0 && $this->currentPage + $this->rPage < $this->pages)
        {
            $this->first = $this->currentPage - $this->rPage;
            $this->last  = $this->currentPage + $this->rPage;
        }
        // if page is one of the first pages
        elseif ($this->currentPage - $this->rPage <= 0)
        {
            // from 0
            $this->first = 1;
            $this->last  = $this->maxPages;
        }
        // if page is one of the last pages
        else
        {
            // from maxpages
            $this->first = ($this->pages - $this->maxPages) +1;
            $this->last  = $this->pages;
        }

        // if last is bigger then total pages
        if ($this->last > $this->pages)
        {
            $this->last = $this->pages;
        }
        // if first is smaller then 1
        if ($this->first < 1)
        {
            $this->first = 1;
        }
    }
    
    function first()
    {
        /* show first, prevoius page */
        if ($this->currentPage > 2)
        {
            $this->returnStr .= $this->linkStart . $this->pointerFirst . $this->linkMid . $this->titleFirst . $this->linkEnd;
            $this->returnStr .= $this->linkStart .  $this->pointerLink . str_replace("%s", 
                                                                                     ($this->currentPage -1), 
                                                                                     $this->pointerQuery) . $this->linkMid . $this->titlePrevious . $this->linkEnd;
        }
        elseif ($this->currentPage > 1)
        {
            $this->returnStr .= $this->linkStart . $this->pointerFirst . $this->linkMid . $this->titleFirst . $this->linkEnd;
        }
    }
    
    function last()
    {
        /* end part */
        if (($this->pages - $this->currentPage) > 1)
        {
            $this->returnStr .= $this->linkStart . $this->pointerLink . str_replace("%s", 
                                                                                    $this->currentPage +1, 
                                                                                    $this->pointerQuery) . $this->linkMid . $this->titleNext . $this->linkEnd;
            $this->returnStr .= $this->linkStart . $this->pointerLink . str_replace("%s", 
                                                                                    $this->pages, 
                                                                                    $this->pointerQuery) .  $this->linkMid . $this->titleLast . $this->linkEnd;
        }
        elseif ($this->pages - $this->currentPage > 0)
        {
            $this->returnStr .= $this->linkStart . $this->pointerLink . str_replace("%s", 
                                                                                    $this->pages, 
                                                                                    $this->pointerQuery) .  $this->linkMid . $this->titleLast . $this->linkEnd;
        }
    }
    
    function middle()
    {
        /* middle pages */
        for ($x = $this->first; $x < ($this->last +1); $x++)
        {
            $buf = '';
            $cls = '';;
            if ($this->currentPage == $x)
            {
                $cls = $this->titleCurrentCls;
                $buf .= $this->linkStart . $this->pointerLink . str_replace("%s",   
                                                                            $x, 
                                                                            $this->pointerQuery) .  $this->linkMid . str_replace("%s", 
                                                                                                                                          $x, 
                                                                                                                                          $this->titleCurrent) . $this->linkEnd;

            }
            else
            {
                $buf  .= $this->linkStart . $this->pointerLink . str_replace("%s", 
                                                                             $x, 
                                                                             $this->pointerQuery) . $this->linkMid . $x . $this->linkEnd;
            }

            $this->returnStr .= str_replace("%cls%",
                                            $cls,
                                            $buf);
        }
    }
    
    function getPages($currentPage)
    {
        $this->currentPage = $currentPage;
        $this->init();
        
        $this->first();
        $this->middle();
        $this->last();
        
        return str_replace("%cls%", 
                           "", 
                           $this->returnStr);
    }
}

/*
$p = new pagePointers;
*/
/*
$p->pointers(array("first"   => "/pages.php",
                   "link"    => '/pages.php',
                   "query"   => '?from=%s',
                   "current" => '<strong style="color: red;">%s</strong>'));
*/


/*
// seo pointers
$p->pointers(array("first"   => "/pages.html",
                   "link"    => "/pages/",
                   "query"   => "from/%s.html"));


$p->totalItems   = rand(0, 250);
$p->itemsPerPage = rand(4, 10);
$p->maxPages     = rand(3, 10);

$p->titleFirst    = "eerste";
$p->titlePrevious = "vorige";
$p->titleNext     = "volgende";
$p->titleLast     = "laatste";
$p->titleCurrent  = "<strong style='color: red;'>%s</strong>";

echo "<h2> totalItems $p->totalItems itemsPerPage $p->itemsPerPage maxPages $p->maxPages</h2>";

for ($currentPage = 0; $currentPage < floor($p->totalItems / $p->itemsPerPage)+1; $currentPage++)
{
    echo $p->getPages($currentPage);
    echo '<hr>' . "\r\n\r\n\r\n";
}

/* uitleg class

stel je wilt door pagina's bladeren van bv profielen en je wilt 20 profielen per pagina laten zien;

$p = new pagePointers; // class starten

$p->totalItems   = mysql_result(mysql_query("SELECT count(uid) FROM profielen"), 0); //  max aantal profielen
$p->itemsPerPage = 20; // aantal items per pagina.
$p->maxPages     = 10; // maximaal 10 pagina's in pages laten zien

// wil je namen veranderen van de links?
$p->titleFirst    = "<<";
$p->titlePrevious = "<";
$p->titleNext     = ">";
$p->titleLast     = ">>";

// vanaf welke pagina?
$_from = 1
if (isset($_GET['from']) && is_numeric($_GET['from']))
{
    if (floor($_GET['from'] * $itemsPerPage) < $totalItems)
    {
        $_from = 1;
    }
}

// doe je query
$query = mysql_query("SELECT * FROM profielen LIMIT " . (($_from - 1) * $itemsPerPage) . "," . $itemsPerPage);

// loop je items

// laat nu aantal pagina's zien

echo "Pagina's: " . $p->getPages($_from);

*/
?>