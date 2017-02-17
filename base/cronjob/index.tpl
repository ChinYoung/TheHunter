         <div class="row-fluid">
            <div class="span12">
              <div class="widget">
                <div class="widget-header">
                  <div class="title">
                    <span class="fs1" aria-hidden="true" data-icon="&#xe023;"></span> Cronjob properties
                  </div>
                </div>
                <div class="widget-body">

<?php include(BASE_PATH . "-status.tpl"); ?>
                
                  <form class="form-horizontal no-margin" method="post">
                    <div class="control-group">
                      <label class="control-label" for="run_active">Cronjob running?</label>
                      <div class="controls"><input type="radio" id="run_active" name="run_active" value="1" <?php echo getPost('run_active', '1', true);?> />Yes 
                                            <input type="radio" id="run_active" name="run_active" value="0" <?php echo getPost('run_active', '0', true);?> /> No</div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="run_time">Total runtime</label>
                      <div class="controls"><input class="span1" id="run_time" name="run_time" onkeyup="toTimeDisplay(this.value * 1000000, 'run_time_secs')" value="<?php echo getPost('run_time');?>" type="text" placeholder="Running time in seconds"> <div style="float: right;" id="run_time_secs"></div> in seconds </div>
                      
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="run_projects">Simultaneous projects #</label>
                      <div class="controls"><input class="span1" id="run_projects" name="run_projects" value="<?php echo getPost('run_projects');?>" type="text" placeholder="# of projects running per round"> # projects running per round</div>
                    </div>
                    <hr>
                    <div class="control-group">
                      <label class="control-label" for="run_debug">Project debug?</label>
                      <div class="controls"><input type="radio" id="run_debug" name="run_debug" value="1" <?php echo getPost('run_debug', '1', true);?> />Yes 
                                            <input type="radio" id="run_debug" name="run_debug" value="0" <?php echo getPost('run_debug', '0', true);?> /> No (display debug information into cronjob logs)</div>
                    </div>
                    <hr>
                    <div class="control-group">
                      <label class="control-label" for="curl_timeout">Curl timeout</label>
                      <div class="controls"><input class="span1" id="curl_timeout" name="curl_timeout" onkeyup="toTimeDisplay(this.value * 1000000, 'curl_timeout_secs')" value="<?php echo getPost('curl_timeout');?>" type="text" placeholder="Timeout in seconds"> <div style="float: right" id="curl_timeout_secs"></div>in seconds (lower gives more errors, but is faster!)</div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="curl_pauze">Curl pauze</label>
                      <div class="controls"><input class="span2" id="curl_pauze" name="curl_pauze" onkeyup="toTimeDisplay(this.value, 'curl_pauze_secs')" value="<?php echo getPost('curl_pauze');?>" type="text" placeholder="Timeout in micro-seconds"> <div style="float: right" id="curl_pauze_secs"></div> Curl wait for response pauze </div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="run_failed_max">Fail retries</label>
                      <div class="controls"><input class="span1" id="run_failed_max" name="run_failed_max" value="<?php echo getPost('run_failed_max');?>" type="text" placeholder="# fails"> # of retries before fail</div>
                    </div>
                    <hr>
                    <div class="control-group">
                      <label class="control-label" for="run_pauze">Pauze per round</label>
                      <div class="controls"><input class="span2" id="run_pauze" name="run_pauze" onkeyup="toTimeDisplay(this.value, 'run_pauze_secs')" value="<?php echo getPost('run_pauze');?>" type="text" placeholder="Timeout in micro-seconds"> <div style="float: right" id="run_pauze_secs"></div> (in microseconds, to give DB rest, 1 000 000 = 1 second)</div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="run_pauze_timeout">Cronjob pauze timer</label>
                      <div class="controls"><input class="span2" id="run_pauze_timeout" name="run_pauze_timeout" onkeyup="toTimeDisplay(this.value, 'run_pauze_timeout_secs')" value="<?php echo getPost('run_pauze_timeout');?>" type="text" placeholder="Timeout in micro-seconds"> <div style="float: right;" id="run_pauze_timeout_secs"></div> Pauze for x seconds and next round</div>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-info pull-right">Update</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>

<script type="text/javascript">
function toTimeDisplay(raw, divOut)
{
    function addZ(n) {
        return (n<10? '0':'') + n;
    }
    s = raw
    var ms = s % 1000000;
    s = (s - ms) / 1000000;
    var secs = s % 60;
    s = (s - secs) / 60;
    var mins = s % 60;
    if (ms > 0)
    {
        ms = ' and ' + Math.round(Math.round(ms / 1000)) + ' microseconds';
    }
    else
    {
        ms = '';
    }


    $('#' + divOut).html(addZ(mins) + ' minutes, ' + addZ(secs) + ' seconds' + ms);
}

$.each(['curl_pauze', 'run_pauze', 'run_pauze_timeout'], function(index, value) {
    toTimeDisplay($('#' + value).val(), value + "_secs");
});
$.each(['curl_timeout', 'run_time'], function(index, value) {
    toTimeDisplay($('#' + value).val() * 1000000, value + "_secs");
  
});

</script>