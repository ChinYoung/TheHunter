<?php
$urllist = array();
$urlCount = 0;
$dbCls->query("SELECT `spider`.*,
                      `s`.`url` AS refUrl
               FROM  `spider` 
               LEFT JOIN  `spider` s ON s.id =  `spider`.`ref_id` 
               WHERE `spider`.`project_id` = ? " . $where . "
               LIMIT " . $startPosition . ", " . $urls_per_page,
              array($project['id']));
if ($dbCls->rows() > 0)
{
    $urlCount = $dbCls->rows();
    $urllist = $dbCls->fetch();
}              
?>
            <br /><br />
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget">
                        <div class="widget-header">
                            <div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe14a;"></span>Listing good/in que urls from projects "<?php echo $project['name'];?>"</div>
                        </div>
                        <div class="widget-body">

<?php include(BASE_PATH . "-status.tpl"); ?>

                        <div class="span6">
                            <a class="btn" href="projects/urllist/<?php echo $project['id'];?>">All</a> 
                            <a class="btn btn-warning2" href="projects/urllist/<?php echo $project['id'];?>/search/failed">Failed</a> 
                            <a class="btn btn-success" href="projects/urllist/<?php echo $project['id'];?>">Processed</a>
                            <a class="btn btn-info" href="projects/urllist/<?php echo $project['id'];?>/search/queue">In queue</a><br /><br />
                        </div>

<?php 
if ($totalRows > $urls_per_page) 
{ 
?>
                        <div class="widget">
                            <div class="widget-header">
                                <div class="title">
                                    <span class="fs1" aria-hidden="true" data-icon="&#xe0b6;"></span> Check all links, choose a page below here (showing: <?php echo $urlCount; ?> from <?php echo $startPosition ;?> to <?php echo $startPosition + $urlCount; ?> of total <?php echo $totalRows;?>)
                                </div>
                            </div>
                            
                            <div class="widget-body">
                                <div class="pagination no-margin">
                                    <ul>
<?php
$pCls = new pages(); // class starten

$a = '';
for ($x = 0; $x < 4; $x++)
{
    if (isset($argument[$x]))
    {
        $a .= '/' . $argument[$x];
    }
}

$pCls->pointers(array("first"   => $configCls->get("application/site_url") . "/projects/urllist" . $a,
                      "link"    => $configCls->get("application/site_url") . "/projects/urllist" . $a,
                      "query"   => "?page=%s"));


$pCls->totalItems   = $totalRows;
$pCls->itemsPerPage = $urls_per_page; // aantal items per pagina.
$pCls->maxPages     = 20; // maximaal 10 pagina's in pages laten zien

echo $pCls->getPages((isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 0);
?>                                    
                                    </ul>
                                </div>
                            </div>
                        </div>
<?php } ?>
                        <table class="table table-condensed table-striped table-bordered table-hover no-margin">
                            <thead>
                            <tr>
                                <th style="width:40%">Url</th>
                                <th style="width:40%">From URL / Error code</th>
                                <th style="width:10%"># Links</th>
                                <th style="width:5%"># Emails</th>
                                <th style="width:5%"># Found</th>
                            </tr>
                            </thead>
                            <tbody>
<?php
if (count($urllist) > 0) 
{
    foreach ($urllist AS $list)
    {
?>

                            <tr>
                                <td><?php echo $list['url']; ?></td>
<?php if (isset($argument[2]) && $argument[2] == 'failed') { ?>
                                <td><strong>Error</strong>: <?php echo $list['failed_msg'];?></td>
<?php } else { ?>
                                <td><?php echo $list['refUrl']; ?></td>
<?php } ?>
                                <td><?php echo $list['links']; ?></td>
                                <td><?php echo $list['emails']; ?></td>
                                <td><?php echo $list['times']; ?></td>
                            </tr>
<?php 
    }
}
else
{
?>
                            <tr>
                                <td colspan=5>Sorry, there is nothing to find here.</td>
                            </tr>
<?php
}
?>
                            </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
