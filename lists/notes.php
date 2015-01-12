<?php
/**
 * OvR Lists - The main template file for OvR Lists
 *
 *
 * @package OvR Lists
 * @since Version 0.0.1
 */

# Report all PHP errors
# For Development use only
# error_reporting(E_ALL|E_STRICT);
# ini_set('display_errors','On');

session_regenerate_id();
session_start();

# Start Session with a 1 day persistent session lifetime
$cookieLifetime = 60 * 60 * 24 * 1;
setcookie(session_name(),session_id(),time()+$cookieLifetime);

# Session Validation - Is User logged in?
# else redirect to login page
if (!(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] != ''))
  header ("Location: /login/index.php");

# get version from file
$version = file_get_contents('lists.version');
?>
<!DOCTYPE html>
<html lang="en"  manifest="/manifest.appcache">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="OvR Lists">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- favicon and apple-touch-icon --> 
    <link rel="icon" type="image/png" href="https://ovrride.com/favicon.ico">
    <link rel="apple-touch-icon" href="images/ios/iconset/Icon-60@2x.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="images/ios/iconset/Icon-60@3x.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="images/ios/iconset/Icon-76.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="images/ios/iconset/Icon-76@2x.png" />
    <link rel="apple-touch-icon" sizes="58x58" href="images/ios/iconset/Icon-Small@2x.png" />

    <title>OvR Trip Lists</title>

    <link href="css/application.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <button class="btn btn-black" id="status"><i class="fa fa-signal"></i> Online</button>
                </li>
                <li>
                    <button type="button" class="btn btn-warning" id="btn-hide">
                        <i class="fa fa-arrow-left"></i>&nbsp;Hide Menu
                    </button>
                </li>
                <li>
                    <button class="btn btn-primary" id="btn-settings">
                      <i class="fa fa-sliders"></i>&nbsp;Settings
                    </button>
                </li>
                <li>
                    <button type="button" class="btn btn-primary" id="btn-list">
                      <i class="fa fa-list"></i>&nbsp;List
                    </button>
                </li>
                <li>
                    <button type="button" class="btn btn-primary" id="btn-summary">
                        <i class="fa fa-table"></i>&nbsp;Summary
                    </button>
                </li>
                <li>
                    <button type="button" class="btn btn-primary disabled" id="btn-notes">
                        <i class="fa fa-pencil-square-o"></i>&nbsp;Notes
                    </button>
                </li>
                <li>
                    <button type="button" class="btn btn-primary" id="btn-message">
                        <i class="fa fa-exclamation-triangle"></i>&nbsp;Message
                    </button>
                </li>
                <li>
                    <button type="button" class="btn btn-primary" id="btn-admin">
                        <i class="fa fa-tachometer"></i>&nbsp;Admin
                    </button>
                </li>
                <li>
                    <button type="button" class="btn btn-danger" id="btn-logout">
                        <i class="fa fa-power-off"></i>&nbsp;Log Out
                    </button>
                </li>
                <li>
                    <span class="version">OvR Lists <?php echo $version; ?></span>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper container">
          <nav class="navbar navbar-default navbar-static-top ovr" role="navigation">
            <div class="container-fluid">
                <button class="btn btn-link navbar-brand" id="brand">OvR Trip Lists</button>
                <button class="btn btn-default" id="menu-toggle"><i class="fa fa-cogs"></i>&nbsp;Menu</button>
            </div>
          </nav>
            <div class="container-fluid pad" id="content">
                <!-- content here -->
                <h2>Notes</h2>
                <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <textarea rows="8" class="form-control" id="newNote" placeholder="Add note here"></textarea>
                    </div>
                </div>
                <div class="row notes">
                    <div class="col-xs-12">
                        <button class="btn btn-success" id="saveNote">
                            <i class="fa fa-floppy-o"></i>&nbsp;Save Note
                        </button>
                        <button class="btn btn-info" id="refreshNotes">
                            <i class="fa fa-refresh"></i>&nbsp;Refresh Notes
                        </button>
                        <button class="btn btn-warning" id="bus" value="show">
                            <i class="fa fa-bus"></i>&nbsp;This Bus Only
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="notesContent"></div>
                    </div>
                </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->
    <script src="js/notes.min.js"></script>
</body>

</html>
