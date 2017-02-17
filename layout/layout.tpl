<!DOCTYPE html>
<!--[if lt IE 7]>
    <html class="lt-ie9 lt-ie8 lt-ie7" lang="en">
<![endif]-->

<!--[if IE 7]>
    <html class="lt-ie9 lt-ie8" lang="en">
<![endif]-->

<!--[if IE 8]>
    <html class="lt-ie9" lang="en">
<![endif]-->

<!--[if gt IE 8]>
    <!-->
    <html lang="en">
    <!--
<![endif]-->

<head>
    <base href="<?php echo $configCls->get("application/site_url"); ?>">
    <meta charset="utf-8">
    <title>2RiP.US Email Bulk Ripper for Mass Email Marketing #2rip</title>
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
    <meta name="author" content="2rip.us">
    <meta name="description" content="All in One email marketing software, email extractor, email harvester, email verifier">
    <meta name="keywords" content="email marketingsoftware, email extractor, mass email software, email harvester, email verifier, bulk mail software ">

    <script src="layout/js/html5-trunk.js"></script>
    <link href="layout/icomoon/style.css" rel="stylesheet">
    <!--[if lte IE 7]>
    <script src="layout/css/icomoon-font/lte-ie7.js"></script>
    <![endif]-->

    <!-- bootstrap css -->
    <link href="layout/css/main.css" rel="stylesheet">
    <link href="layout/css/fullcalendar.css" rel="stylesheet">

    <link rel="stylesheet" href="layout/css/jquery-ui.css" />
    <script src="layout/js/jquery-1.9.1.js"></script>
    <script src="layout/js/jquery-ui.js"></script>

    <script src="layout/js/bootstrap.js"></script>
    <script src="layout/js/jquery-ui-1.8.23.custom.min.js"></script>
    <script src="layout/js/tiny-scrollbar.js"></script>
    <script src="layout/js/custom-index.js"></script>
</head>
<body>
    <header>
        <a href="index-2.html" class="logo">2RiP.US Email Bulk Ripper for Mass Email Marketing</a>
        <div id="mini-nav">
            <ul class="hidden-phone">
                <li><a href="projects">Running projects <span id="projects_running_header">0</span></a></li>
                <li><a href="logoff">Logout</a></li>
            </ul>
        </div>
    </header>

    <div class="container-fluid">
        <div id="mainnav" class="hidden-phone hidden-tablet">
            <ul>
                <li <?php if ($_GET['arg'] == '') { ?> class="active" <?php } ?>>
                    <?php if ($_GET['arg'] == '') { ?> <span class="current-arrow"></span> <?php } ?>
                    <a href="./"><div class="icon"><span class="fs1" aria-hidden="true" data-icon="&#xe0a1;"></span></div>Dashboard</a>
                </li>
                <li<?php if (substr($_GET['arg'], 0, 8) == "projects") { ?> class="active" <?php } ?>>
                    <?php if (substr($_GET['arg'], 0, 8) == "projects") { ?> <span class="current-arrow"></span> <?php } ?>
                    <a href="projects"><div class="icon"><span class="fs1" aria-hidden="true" data-icon="&#xe1ce;"></span></div>Projects</a>
                </li>
                <li<?php if (substr($_GET['arg'], 0, 8) == "cronjob") { ?> class="active" <?php } ?>>
                    <?php if (substr($_GET['arg'], 0, 8) == "cronjob") { ?> <span class="current-arrow"></span> <?php } ?>
                    <a href="cronjob"><div class="icon"><span class="fs1" aria-hidden="true" data-icon="&#xe0b1;"></span></div>Cronjob</a>
                </li>
                <li<?php if (substr($_GET['arg'], 0, 11) == "cronlog") { ?> class="active" <?php } ?>>
                    <?php if (substr($_GET['arg'], 0, 11) == "cronlog") { ?> <span class="current-arrow"></span> <?php } ?>
                    <a href="cronlog"><div class="icon"><span class="fs1" aria-hidden="true" data-icon="&#xe04b;"></span></div>Cronjob Log</a>
                </li>
            </ul>
        </div>
      
        <div class="dashboard-wrapper">
            <div class="main-container">
                <div class="navbar hidden-desktop">
                    <div class="navbar-inner">
                        <div class="container">
                            <a data-target=".navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </a>
                            <div class="nav-collapse collapse navbar-responsive-collapse">
                            <ul class="nav">
                                <li><a href="./">Dashboard</a></li>
                                <li><a href="projects">Projects</a></li>
                                <li><a href="personal">Personal</a></li>
                                <li><a href="contact">Contact</a></li>
<?php if ($_SESSION['auth']['status'] > 3) { ?>                                
                                <li><a href="users">Users</a></li>
<?php } ?>                                
<?php if ($_SESSION['auth']['status'] > 4) { ?>                                
                                <li><a href="cronjob">Cronjob settings</a></li>
                                <li><a href="cronlog">Cronlog</a></li>
<?php } ?>                                
                                <li><a href="logoff">Logoff</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <ul class="breadcrumb-beauty">
                        <li><a href="./"><span class="fs1" aria-hidden="true" data-icon="&#xe002;"></span> Dashboard for "<?php echo $_SESSION['auth']['username'];?>"</a></li>
<?php 
if (isset($dashboard) && is_array($dashboard) && count($dashboard) > 1) 
{ 
    foreach ($dashboard AS $url => $title)
    {
?>

                        <li><a href="<?php echo $url;?>"><?php echo $title;?></a></li>
<?php 
    }
}
?>
                    </ul>
                </div>
            </div>

            <br>

<?php echo $content;?>

            </div>
        </div><!-- dashboard-container -->
    </div><!-- container-fluid -->
    
    <footer>
        <p class="copyright">&copy; Email Bulk Ripper (c) <?php echo date("Y"); ?></p>
    </footer>

    <!-- Tiny Scrollbar JS -->
    <script src="layout/js/tiny-scrollbar.js"></script>
<script type="text/javascript">
function getProjectsRunning()
{
    // Ajax call: get entries, add to entries div
    $.ajax({
        type: "GET",
        url: "api/processrunning",
        cache: false,
        success: function(xml) 
        {
            $(xml).find("stat").each(function()
            {
                id = '#' + $(this).attr("id") + "_header";
                if ($(id).text() != $(this).text())
                {
                    $(id).text($(this).text());
                    $(id).effect("highlight", {}, 3000);
                }
            });
        }
    });
}

getProjectsRunning();
setInterval(getProjectsRunning, 10000);
</script>
</body>
</html>