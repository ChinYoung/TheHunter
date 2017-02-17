<?php
$emaillist = array();
$emailCount = 0;
$dbCls->query("SELECT * 
               FROM `email`
               WHERE `project_id` = ? " . $searchSql . "
               LIMIT " . $startPosition . ", " . $emails_per_page,
              array($project['id']));
if ($dbCls->rows() > 0) 
{
    $emailCount = $dbCls->rows();
    $emaillist = $dbCls->fetch();
}
?>

            <div class="row-fluid">
                <div class="span12">

                    <div class="widget">
                        <div class="widget-header">
                            <div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe14a;"></span>Listing email addresses from project "<?php echo $project['name'];?>"</div>
                        </div>
                        <div class="widget-body">
                        
<?php include(BASE_PATH . "-status.tpl"); ?>

                            <div class="span8">
                            
                                <div class="row-fluid">

                                    <div class="span4">
                                        <div class="widget">
                                            <div class="widget-header">
                                                <div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe14c;"></span> Download list of addresses</div>
                                            </div>
                                            <div class="widget-body">
                                                <div class="btn-group">
                                                    <a class="btn" href="javascript:void(-1);" data-original-title="">Download options</a>
                                                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#" data-original-title=""><span class="caret"></span></a>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="projects/emaillist/<?php echo $project['id'];?>/csv" data-original-title="">As CSV file</a></li>
                                                        <li><a href="projects/emaillist/<?php echo $project['id'];?>/txt" data-original-title="">As Text file</a></li>
                                                        <li><a href="projects/emaillist/<?php echo $project['id'];?>/zip" data-original-title="">As ZIP file</a></li>
                                                        <li><a href="projects/emaillist/<?php echo $project['id'];?>/print" data-original-title="">Send to printer</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="span8">
                                        <div class="widget">
                                            <div class="widget-header">
                                                <div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe14c;"></span> Update current list of email addresses (text file only)</div>
                                            </div>
                                            <div class="widget-body">
                                            <div class="input-append">
                                                <form method="post" style="margin: 0; padding; 0;">
                                                    <input class="span6" id="uploadfile" name="uploadfile" type="file" style="width: 315px;">
                                                    <input class="btn" type="submit" name="action[bounced]" value="As Bounced" />
                                                    <input class="btn" type="submit" name="action[send]" value="As Send" />
                                                </form>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <br style="clear: both;" />
                                <div class="span6">
                                    <a class="btn" href="projects/emaillist/<?php echo $project['id'];?>">All</a> 
                                    <a class="btn btn-warning2" href="projects/emaillist/<?php echo $project['id'];?>/search/bounced">Bounced</a> 
                                    <a class="btn btn-info" href="projects/emaillist/<?php echo $project['id'];?>/search/send">Send</a><br /><br />
                                </div>

                                </div>
                                
                                
                            </div>
                                                        
                            
<?php if ($totalRows > $emails_per_page) { ?>

                            <div class="widget">
                                <div class="widget-header">
                                    <div class="title">
                                        <span class="fs1" aria-hidden="true" data-icon="&#xe0b6;"></span> Check all email addresses, choose a page below here (showing: <?php echo $emailCount; ?> from <?php echo $startPosition ;?> to <?php echo $startPosition + $emailCount; ?> of total <?php echo $totalRows;?>)
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

$pCls->pointers(array("first"   => $configCls->get("application/site_url") . "/projects/emaillist" . $a,
                      "link"    => $configCls->get("application/site_url") . "/projects/emaillist" . $a,
                      "query"   => "?page=%s"));


$pCls->totalItems   = $totalRows;
$pCls->itemsPerPage = $emails_per_page; // aantal items per pagina.
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
                                <th style="width:70%">Email</th>
                                <th style="width:10%">Status</th>
                                <th style="width:10%">Validated</th>
                                <th style="width:10%">Found</th>
                            </tr>
                            </thead>
                            <tbody>
<?php
if (count($emaillist)> 0) 
{
    foreach ($emaillist AS $list)
    {
?>

                            <tr>
                                <td><?php echo $list['email']; ?></td>
                                <td><?php echo $emailStatusArray[$list['status']]; ?></td>
                                <td><?php echo $list['processed']; ?></td>
                                <td><?php echo $list['times'];?></td>
                                </td>
                            </tr>
<?php 
    }
}
else
{
?>
                            <tr>
                                <td colspan=7>Sorry, nothing found here.</td>
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

<script type="text/javascript">
$(document).ready(function(){
        $('input:file').change(function(){
                if ($(this).val()) {
                    $('input:submit').attr('disabled',false);
                    // or, as has been pointed out elsewhere:
                    // $('input:submit').removeAttr('disabled'); 
                } 
            }
            );
    });
</script>