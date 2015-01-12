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
  <head>
    <title>OvR Trip Lists</title>
    <!-- Mobile view properties & enable iOS Web App-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="OvR Lists">
    <!-- favicon and apple-touch-icon --> 
    <link rel="icon" type="image/png" href="https://ovrride.com/favicon.ico">
    <link rel="apple-touch-icon" href="../../images/ios/iconset/Icon-60@2x.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../../images/ios/iconset/Icon-60@3x.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="../../images/ios/iconset/Icon-76.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="../../images/ios/iconset/Icon-76@2x.png" />
    <link rel="apple-touch-icon" sizes="58x58" href="../../images/ios/iconset/Icon-Small@2x.png" />
    <!-- Apple Splash Screens -->
    <!-- iPhone -->
    <link href="../../images/startup-320x460.png"
      media="(device-width: 320px) and (device-height: 480px)
        and (-webkit-device-pixel-ratio: 1)"
      rel="apple-touch-startup-image" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link href="../../css/application.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container login-window">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title logo">
                <img src="../../images/logo.jpg">
              </h3>
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
    <script src="../../js/uncompressed/vendor.js"></script>
  </body>
</html>