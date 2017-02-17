         <div class="row-fluid">
            <div class="span12">
              <div class="widget">
                <div class="widget-header">
                  <div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe023;"></span> Project "<?php echo $project['name']; ?>"</div>
                </div>
                <div class="widget-body">

<?php include(BASE_PATH . "-status.tpl"); ?>

                  <form class="form-horizontal no-margin">
                    <div class="control-group">
                      <label class="control-label" for="name">Project title</label>
                      <div class="controls"><input class="span8" id="name" value="<?php echo $project['name'];?>" type="text" placeholder="Project title"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="urle">Url</label>
                      <div class="controls"><input class="span8" id="url" type="text" value="<?php echo $project['url'];?>" placeholder="http://www.yourwebsite.com/"></div>
                    </div>
                    <div class="control-group">
                      <label class="control-label">Status</label>
                      <div class="controls"><?php echo ($project['status'] != 1) ? 'Not running' : 'Running'; ?></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Options</label>
                        <div class="controls">
<?php if ($project['status'] != 1 && $project['status'] < 4) { ?>
                                    <a href="projects/edit/<?php echo $project['id'];?>" class="btn btn-small" data-original-title="">Edit</a>
                                    <a href="projects/action/start/<?php echo $project['id'];?>" class="btn btn-success btn-small" data-original-title="">Start</a>
                                    <a href="projects/action/reset/<?php echo $project['id'];?>" onclick="javascript:return confirm('Are you shure to reset this project, all gathered information will be removed!');"  class="btn btn-warning btn-small btn-primary"  data-original-title="">Reset</a>
                                    <a href="projects/action/delete/<?php echo $project['id'];?>" onclick="javascript:return confirm('Are you shure to delete this project, all information will be removed from the database!');"  class="btn btn-danger btn-small btn-primary" data-original-title=""><i class="icon-remove icon-white"></i> Delete</a>
<?php } ?>
<?php if ($project['status'] == 1) { ?>
                                    <a href="projects/action/stop/<?php echo $project['id'];?>" class="btn btn-warning btn-small btn-primary" data-original-title="">Stop</a>
<?php } ?>                                    
                        </div>
                    </div>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
         </div>

            <div class="span12">
              <div class="plain-header">
                <h4 class="title">
                  Project statistics / links
                </h4>
              </div>
              <div class="row-fluid">
                <div class="span6">
                  <div class="widget less-bottom-margin widget-border">
                    <div class="widget-body">
                      <a href="projects/urllist/<?php echo $project['id'];?>">
                      <div class="current-stats">
                        <h4 class="text-info"><?php echo number_format(getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_unique' AND `project_id` = " . $project['id'], 'res'), 0, ",", "."); ?> / <?php echo number_format(getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_parsed' AND `project_id` = " . $project['id'], 'res'), 0, ",", ".") ?></h4>
                        <p>Total unique urls / total found</p>
                        <div class="type"><span class="fs1 arrow text-info" aria-hidden="true" data-icon="&#xe15e;"></span></div>
                      </div>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="span6">
                  <div class="widget less-bottom-margin widget-border">
                    <div class="widget-body">
                      <a href="projects/urllist/<?php echo $project['id'];?>/search/processed">
                      <div class="current-stats">
                        <h4 class="text-success"><?php echo number_format(getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_processed' AND `project_id` = " . $project['id'], 'res'), 0, ",", "."); ?></h4>
                        <p>Total urls proccesed</p>
                        <div class="type"><span class="fs1 arrow text-success" aria-hidden="true" data-icon="&#xe15e;"></span></div>
                      </div>
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row-fluid">
                <div class="span6">
                  <div class="widget widget-border">
                    <div class="widget-body">
                      <a href="projects/urllist/<?php echo $project['id'];?>/search/failed">
                      <div class="current-stats">
                        <h4 class="text-warning"><?php echo number_format(getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_failed' AND `project_id` = " . $project['id'], 'res'), 0, ",", "."); ?></h4>
                        <p>Total failed urls</p>
                        <div class="type"><span class="fs1 arrow text-warning" aria-hidden="true" data-icon="&#xe0fa;"></span></div>
                      </div>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="span6">
                  <div class="widget widget-border">
                    <div class="widget-body">
                      <a href="projects/emaillist/<?php echo $project['id'];?>">
                      <div class="current-stats">
                        <h4 class="text-success"><?php echo number_format(getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'emails_unique' AND `project_id` = " . $project['id'], 'res'), 0, ",", "."); ?></h4>
                        <p>Total email addresses</p>
                        <div class="type"><span class="fs1 arrow text-success" aria-hidden="true" data-icon="&#xe162;"></span></div>
                      </div>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
         
