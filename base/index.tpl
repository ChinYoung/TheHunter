        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-body">

                        <?php include(BASE_PATH . "-status.tpl"); ?>

                        <ul class="nav nav-tabs no-margin myTabBeauty">
                            <li class="active"><a data-toggle="tab" href="#realtime">Readtime</a></li>
                            <li><a data-toggle="tab" href="#overall">Overall</a></li>
                        </ul>
                        <div class="tab-content" id="myTabContent" style="height: 800px;">
                            <div id="realtime" class="tab-pane fade active in">

                                <div class="widget-body">
                                <div class="row-fluid">
                                    <div class="span6">
                                        <div class="plain-header"><h4 class="title">Projects overall</h4></div>
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <div class="widget less-bottom-margin widget-border widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-info" id="projects">Loading...</h4>
                                                        <p>Total projects</p>
                                                        <div class="type"><span class="fs1 arrow text-info" aria-hidden="true" data-icon="&#xe048;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <div class="widget less-bottom-margin widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-warning" id="projects_running">Loading...</h4>
                                                        <p>Running</p>
                                                        <div class="type"><span class="fs1 arrow text-warning" aria-hidden="true" data-icon="&#xe077;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-fluid">
                                            <div class="span6">
                                                <div class="widget widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-warning" id="links_processing">Loading...</h4>
                                                        <p>Processing</p>
                                                        <div class="type"><span class="fs1 arrow text-warning" aria-hidden="true" data-icon="&#xe077;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <div class="widget widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-error" id="projects_canceled">Loading...</h4>
                                                        <p>Cancelled</p>
                                                        <div class="type"><span class="fs1 arrow text-error" aria-hidden="true" data-icon="&#xe0fa;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>      
                                        </div>
                                    </div>
                                
                                    <div class="span6">
                                        <div class="plain-header"><h4 class="title">Overal statistics</h4></div>
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <div class="widget less-bottom-margin widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-info" id="links_unique">Loading...</h4>
                                                        <p>Total unique urls</p>
                                                        <div class="type"><span class="fs1 arrow text-info" aria-hidden="true" data-icon="&#xe15e;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <div class="widget less-bottom-margin widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-success" id="links_processed">Loading...</h4>
                                                        <p>Total urls processed</p>
                                                        <div class="type"><span class="fs1 arrow text-success" aria-hidden="true" data-icon="&#xe15e;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-fluid">
                                            <div class="span6">
                                                <div class="widget widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-warning" id="links_failed">Loading...</h4>
                                                        <p>Total failed urls</p>
                                                        <div class="type"><span class="fs1 arrow text-warning" aria-hidden="true" data-icon="&#xe0fa;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span6">
                                                <div class="widget widget-border">
                                                    <div class="widget-body">
                                                        <div class="current-stats"><h4 class="text-success" id="emails_unique">Loading...</h4>
                                                        <p>Total email addresses</p>
                                                        <div class="type"><span class="fs1 arrow text-success" aria-hidden="true" data-icon="&#xe162;"></span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="widget">
                                            <div class="widget-header"><div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe07d;"></span> Latest Updates in <strong id="timer"></strong> secs</div></div>
                                            <div class="widget-body">
                                                <div id="scrollbar-three">
                                                    <div class="scrollbar">
                                                        <div class="track">
                                                            <div class="thumb">
                                                                <div class="end"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="viewport">
                                                        <div class="overview">
                                                            <ul class="imp-messages" id="imp-messages"></ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
    <script type="text/javascript">

    var formatTime = function(unixTimestamp) 
    {
        var dt = new Date(unixTimestamp * 1000);

        var hours = dt.getHours();
        var minutes = dt.getMinutes();
        var seconds = dt.getSeconds();

        // the above dt.get...() functions return a single digit
        // so I prepend the zero here when needed
        if (hours < 10) 
         hours = '0' + hours;

        if (minutes < 10) 
         minutes = '0' + minutes;

        if (seconds < 10) 
         seconds = '0' + seconds;

        return hours + ":" + minutes + ":" + seconds;
    }       

    time        = 0;
    var timer   = 10;
    var counter = 1;

    function getUpdates()
    {
        counter -= 1;
        document.getElementById('timer').innerHTML = counter; 

        if (counter == 0)
        {
            // Ajax call: get entries, add to entries div
            $.ajax({
                type: "GET",
                url: "api/processlatest?time=" + time,
                cache: false,
                success: function(xml) 
                {
                    time = $(xml).find("status").attr("time");
                    
                    $(xml).find("line").each(function()
                    {
                        t = formatTime($(this).attr("time"));
                        d = new Date($(this).attr("time")*1000);
                        dt = d.getDate() + '-' + d.getDay();
                        $('#imp-messages').prepend('<li><div class="message-date"><h3 class="date text-info">' + dt + '</h3><p class="month">' + t + '</p></div><div class="message-wrapper"><h4 class="message-heading">' + $(this).text() + '</h4><p class="message"></p></div></li>');
                        
                        if ($('#imp-messages li').length > 20) 
                        {
                            $('#imp-messages li:last-child').remove();
                        }
                    });
                }
            });
            counter = timer +1;
        }
    }

    function getProjectUpdate()
    {
        // Ajax call: get entries, add to entries div
        $.ajax({
            type: "GET",
            url: "api/projectstats",
            cache: false,
            success: function(xml) 
            {
                $(xml).find("stat").each(function()
                {
                    id = '#' + $(this).attr("id");
                    if ($(id).text() != $(this).text())
                    {
                        $(id).text($(this).text());
                        $(id).effect("highlight", {}, 3000);
                    }
                });
            }
        });    
    }

    setInterval(getProjectUpdate, 5000);
    setInterval(getUpdates, 1000);

    </script>

                                </div>
                            </div>
                            <div id="overall" class="tab-pane fade in">

                                <div class="widget-body">
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="widget">
                                            <div class="widget-header"><div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe1cd;"></span> Overal daily Progress</div></div>
                                            <div class="widget-body"><div id="chart_processed"></div></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="widget">
                                            <div class="widget-header"><div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe1cd;"></span> Overal daily Lnks</div></div>
                                            <div class="widget-body"><div id="chart_links"></div></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="widget">
                                            <div class="widget-header"><div class="title"><span class="fs1" aria-hidden="true" data-icon="&#xe1cd;"></span> Overal daily Emails</div></div>
                                            <div class="widget-body"><div id="chart_emails"></div></div>
                                        </div>
                                    </div>
                                </div>
                                </div>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script src="layout/js/custom-graphs.js"></script>

    <script>
    function drawData(divId, data)
    {
        var options = {
            width: '1000',
            pointSize: 7,
            lineWidth: 1,
            height: '150',
            backgroundColor: 'transparent',
            colors: ['#3eb157', '#3660aa', '#d14836', '#dba26b', '#666666', '#f26645'],
            tooltip: { textStyle: { color: '#666666', fontSize: 11 }, showColorCode: true },
            legend: { textStyle: { color: 'black', fontSize: 12 } },
            chartArea: { left: 80, top: 10, height: "70%" }
        };
        
        var chart = new google.visualization.AreaChart(document.getElementById(divId));
        chart.draw(data, options);
    }
    
<?php
$projects = array();
$dbCls->query("SELECT id FROM project WHERE user_id = ?",
              array($_SESSION['userID']));
if ($dbCls->rows() > 0)
{
    foreach ($dbCls->fetch() AS $p)
    {
        $projects[$p['id']] = $p['id'];
    }
}

if (count($projects) > 0)
{
?>

    var data1 = google.visualization.arrayToDataTable([
<?php $data = getD($dbCls, $projects, array('links_parsed', 'links_unique')); ?>
        ['Date', '# Parsed', '# Unique'],
    <?php foreach ($data AS $date => $list) {?>
        ['<?php echo $date; ?>',<?php echo $list['links_parsed'];?>,<?php echo $list['links_unique'];?>],
    <?php } ?> ]);

    var data = google.visualization.arrayToDataTable([
<?php $data = getD($dbCls, $projects, array('links_failed', 'links_processed')); ?>
        ['Date', '# Failed', '# Processed'],
    <?php foreach ($data AS $date => $list) {?>
        ['<?php echo $date; ?>',<?php echo $list['links_failed'];?>,<?php echo $list['links_processed'];?>],
    <?php } ?> ]);

    var data2 = google.visualization.arrayToDataTable([
<?php $data = getD($dbCls, $projects, array('emails_parsed', 'emails_unique')); ?>
        ['Date', '# Parsed', '# Unique'],
    <?php foreach ($data AS $date => $list) {?>
        ['<?php echo $date; ?>',<?php echo $list['emails_parsed'];?>,<?php echo $list['emails_unique'];?>],
    <?php } ?> ]);

    $(document).ready(function() { 
        drawData('chart_processed', data);
        drawData('chart_links', data1);
        drawData('chart_emails', data2);
    });
    </script>
<?php } ?>              
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
