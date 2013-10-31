<?php

// show negative messages
if ($login->errors) {
    foreach ($login->errors as $error) {
        echo $error;    
    }
}

// show positive messages
if ($login->messages) {
    foreach ($login->messages as $message) {
        echo $message;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <!-- Mobile view properties & enable iOS Web App-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <title>OvR Trip Lists</title>

    <!-- favicon and apple-touch-icon -->
    <link rel="apple-touch-icon" href="../../assets/images/touch-icon-iphone.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/images/touch-icon-ipad.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="../../assets/images/touch-icon-iphone-retina.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="../../assets/images/touch-icon-ipad-retina.png" />
    <link rel="icon" type="image/png" href="http://ovrride.com/favicon.ico">

    <!-- Include compiled and minified stylesheets -->
    <link rel="stylesheet" href="../../assets/stylesheets/all.css">
    <!-- Include tablesorter styles -->
    <link rel="stylesheet" href="../../assets/tablesorter/css/theme.bootstrap.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">OvR Trip Lists</h3>
            </div>
            <div class="panel-body">
              <form accept-charset="UTF-8" role="form" method="post" action="index.php" name="loginform">
                <fieldset>
                  <div class="form-group">
                    <input id="login_input_username" class="form-control login_input" placeholder="Username" name="user_name" type="text" required />
                  </div>
                  <div class="form-group">
                    <input id="login_input_password" class="form-control login_input" placeholder="Password" name="user_password" type="password" autocomplete="off" required />
                  </div>
                  <input class="btn btn-lg btn-primary btn-block" type="submit" name="login" value="Log in">
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Include concatenated and minified javascripts -->
    <script src="../../assets/javascripts/all.min.js"></script>
  </body>
</html>