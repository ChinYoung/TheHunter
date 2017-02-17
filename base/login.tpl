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
    <meta charset="utf-8">
    <title>Admin login</title>
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
    <script src="layout/js/html5-trunk.js"></script>
    <link href="layout/icomoon/style.css" rel="stylesheet">
    <link href="layout/css/main.css" rel="stylesheet">
    <!--[if lte IE 7]>
      <script src="layout/css/icomoon-font/lte-ie7.js"></script>
    <![endif]-->

</script>
</head>
<body>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span4 offset4">
            
<?php include(BASE_PATH . "-status.tpl"); ?>

                <div class="signin">
                    <h1 class="center-align-text">Login</h1>
                    <form action="" class="signin-wrapper" method="post">
                    <div class="content">
                        <input class="input input-block-level" placeholder="Email" type="email" name="email" value="">
                        <input class="input input-block-level" placeholder="Password" name="password" type="password">
                    </div>
                    <div class="actions">
                        <input class="btn btn-info pull-right" type="submit" value="Login">
                        <span class="checkbox-wrapper">
                            <a href="#" class="pull-left"><strong>Lost password?</strong> Read the manual</a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="layout/js/jquery.min.js"></script>
    <script src="layout/js/bootstrap.js"></script>
    
  </body>
</html>
<?php exit; ?>