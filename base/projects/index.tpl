
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget">
                        <div class="widget-header">
                            <div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe14a;"></span>Listing projects</div>
                        </div>
                        <div class="widget-body">

<?php include(BASE_PATH . "-status.tpl"); ?>

                        <a class="btn" href="projects/new">Create new project</a><br /><br />
                        

                        <table class="table table-condensed table-striped table-bordered table-hover no-margin">
                            <thead>
                            <tr>
                                <th style="width:45%">Name</th>
                                <th style="width:20%">Status</th>
                                <th style="width:5%"># Urls</th>
                                <th style="width:5%"># Failed</th>
                                <th style="width:5%"># Emails</th>
                                <th style="width:20%;">Options</th>
                            </tr>
                            </thead>
                            <tbody>
<?php
$dbCls->query("SELECT * 
               FROM `project` 
               WHERE `user_id` = ?
               ORDER BY `project`.`status` = 0, 
                        `project`.`status`",
             array($_SESSION['userID']));
if ($dbCls->rows() > 0)
{
    foreach ($dbCls->fetch() AS $list)
    {
?>

                            <tr>
                                <td><?php echo $list['name']; ?><br />Url: <i><?php echo $list['url']; ?></i></td>
                                <td><span class="badge badge-info"><?php echo $projectStatusArray[$list['status']]; ?></span></td>
                                <td><?php echo number_format(getStat($dbCls, $list['id'], 'links_unique'), 0, ",", "."); ?></td>
                                <td><?php echo number_format(getStat($dbCls, $list['id'], 'links_failed'), 0, ",", "."); ?></td>
                                <td><?php echo number_format(getStat($dbCls, $list['id'], 'emails_unique'), 0, ",", "."); ?></td>
                                <td>
                                    <a href="projects/open/<?php echo $list['id'];?>" class="btn btn-info btn-small btn-primary " data-toggle="modal" data-original-title="">Open</a>
<?php if ($list['status'] != 1 && $list['status'] < 4) { ?>
                                    <a href="projects/action/start/<?php echo $list['id'];?>" class="btn btn-success btn-small" data-original-title="">Start</a>
<?php } ?>
<?php if ($list['status'] == 1) { ?>
                                    <a href="projects/action/stop/<?php echo $list['id'];?>" class="btn btn-warning btn-small btn-primary" data-original-title="">Stop</a>
<?php } ?>                                    
                                </td>
                            </tr>
<?php 
    }
?>
                            <tr>
                                <td colspan=5>&nbsp;</td>
                                <td>
                                    <a href="projects/endreset" class="btn btn-warning2 btn-small btn-primary" onclick="javascript:return confirm('Are you shure to end and reset ALL PROJECTS, all gathered information will be removed!');" data-toggle="modal" data-original-title="">End / Reset all projects</a>
                                </td>
                            </tr>
<?php    
}
else
{
?>
                            <tr>
                                <td colspan=7>You should create a new project! coz there's none here.... ;)</td>
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