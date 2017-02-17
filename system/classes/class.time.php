<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

class time
{
    public $timers = array();
    public $stats = array();
    
    function __stats($stat = '', $cur)
    {
        if ($stat != '')
        {
            if (!isset($this->stats[$stat]))
            {
                $this->stats[$stat] = array("cnt" => 0,
                                            "avg" => 0,
                                            "tot" => 0,
                                            "max" => 0,
                                            "min" => 0,
                                            "cur" => $t);
                
            }
            
            $this->stats[$stat]['cur'] = $cur;
            
            if (!isset($this->stats[$stat]['min']) OR $this->stats[$stat]['min'] > $this->stats[$stat]['cur'])
            {
                $this->stats[$stat]['min'] = $this->stats[$stat]['cur'];
            }
            if (!isset($this->stats[$stat]['max']) OR $this->stats[$stat]['max'] < $this->stats[$stat]['cur'])
            {
                $this->stats[$stat]['max'] = $this->stats[$stat]['cur'];
            }
            $this->stats[$stat]['tot'] = isset($this->stats[$stat]['tot']) ? $this->stats[$stat]['tot'] + $cur : $this->stats[$stat]['cur'];
            $this->stats[$stat]['cnt'] = isset($this->stats[$stat]['cnt']) ? ($this->stats[$stat]['cnt'] + 1) : 1;
            $this->stats[$stat]['avg'] = ceil($this->stats[$stat]['tot']  / $this->stats[$stat]['cnt']);
        }
    }
    
    function __time($stat = '', $type = 0)
    {
        list($usec, $sec) = explode(" ", microtime());
        $t = ((float)$usec + (float)$sec);
        
        if ($stat != '')
        {
            if (!isset($this->timers[$stat]))
            {
                $this->timers[$stat] = array("cnt" => 0,
                                             "avg" => 0,
                                             "tot" => 0,
                                             "max" => 0,
                                             "min" => 0,
                                             "cur" => $t);
                
            }
            
            $this->timers[$stat]['cur'] = ($type == 0) ? $t : round(($t - $this->timers[$stat]['cur']), 3);
            
            if ($type == 1)
            {
                if (!isset($this->timers[$stat]['min']) OR $this->timers[$stat]['min'] > $this->timers[$stat]['cur'])
                {
                    $this->timers[$stat]['min'] = $this->timers[$stat]['cur'];
                }
                if (!isset($this->timers[$stat]['max']) OR $this->timers[$stat]['max'] < $this->timers[$stat]['cur'])
                {
                    $this->timers[$stat]['max'] = $this->timers[$stat]['cur'];
                }
                $this->timers[$stat]['tot'] = isset($this->timers[$stat]['tot']) ? round($this->timers[$stat]['tot'] + $this->timers[$stat]['cur'], 3) : $this->timers[$stat]['cur'];
                $this->timers[$stat]['cnt'] = isset($this->timers[$stat]['cnt']) ? ($this->timers[$stat]['cnt'] + 1) : 1;
                $this->timers[$stat]['avg'] = round($this->timers[$stat]['tot']  / $this->timers[$stat]['cnt'], 3);
            }
        }
        
        return $t;
    }
    
    
}
?>