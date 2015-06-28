<?php
require_once('PasswordHashClass.php');
require_once('db.php');
require_once('Mandrill.php');

if ( isset($_POST['user_name']) && isset($_POST['user_password']) && isset($_POST['user_email']) && isset($_POST['Register'])) {
    $db = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME .';charset=utf8', DB_USER, DB_PASS);
    $userCheck = $db->prepare('SELECT * FROM ovr_lists_login WHERE user_name = :user');
    $userCheck->execute(array('user' => $_POST['user_name']));
    $emailCheck = $db->prepare('SELECT * FROM ovr_lists_login WHERE user_email = :email');
    $emailCheck->execute(array('email' => $_POST['user_email']));
    if ( $userCheck->rowCount() > 0 ) { # No duplicate user names
        echo "Sorry, that username is already in use";
    } else if ( $emailCheck->rowCount() > 0 ) { # No duplicate emails
        echo "Sorry, that email is already in use";
    } else { # Carry on with registration
        $passwordHash = PasswordHash::create_hash($_POST['user_password']);
        $addUser = $db->prepare("INSERT INTO ovr_lists_login (user_name,user_password_hash,user_email)
                                VALUES(:user_name, :user_password_hash, :user_email)");
        $addUser->execute(array('user_name' => $_POST['user_name'], 
                                'user_password_hash' => $passwordHash, 
                                'user_email' => $_POST['user_email']
                                ));
        $userId = $db->lastInsertId();
        $activation_hash_string = $userId . $_POST['user_name'] . $_POST['user_email'] . $passwordHash;
        $activation = urlencode(hash_hmac('sha256', $activation_hash_string, $passwordHash));
        $mandrillAPI = new Mandrill(getenv('MANDRILL_API'));
        $textBody = "Lists account request for " . $_POST['user_name'] . ", " . $_POST['user_email'];
        $textBody = <<<AAA
            Lists account request\n
            User: {$_POST['user_name']}\n
            Email: {$_POST['user_email']}\n
            \n
            Activation Link: https://{$_SERVER['SERVER_NAME']}/login/activate.php?user={$userId}&key={$activation}\n
AAA;
$htmlBody = <<<BBB
    Lists account request<br />
    User: {$_POST['user_name']}<br />
    Email: {$_POST['user_email']}<br />
    \n
    Activation Link: <a href="https://{$_SERVER['SERVER_NAME']}/login/activate.php?user={$userId}&key={$activation}">https://{$_SERVER['SERVER_NAME']}/login/activate.php?user={$userId}&key={$activation}</a><br />
BBB;
        $message = new stdClass();
        $message->html = $bodyHTML;
        $message->text = $textBody;
        $message->subject = "Lists: User Account";
        $message->from_email = "devops@ovrride.com";
        $message->from_name  = "OvR";
        $message->to = array(array("email" => "devops@ovrride.com"));
        $message->track_opens = true;
        $response = $mandrillAPI->messages->send($message);
    }
}

?>
<html lang="en">
  <head>
    <title>OvR Trip Lists</title>
    <!-- Mobile view properties & enable iOS Web App-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="OvR Lists">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link href="../css/application.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container login-window">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title logo">
                <img src="../images/logo.jpg">
              </h3>
            </div>
            <div class="panel-body">
                <h4>User Registration</h4>
                <p>All registrations need to be activated by an OvRride Administrator. You will recieve an email when your account is activated.</p>
              <form accept-charset="UTF-8" role="form" method="post" action="register.php" name="loginform">
                <fieldset>
                  <div class="form-group">
                    <input id="username" class="form-control login_input" placeholder="Username" name="user_name" type="text" required />
                  </div>
                  <div class="form-group">
                    <input id="password" class="form-control login_input" placeholder="Password" name="user_password" type="password" autocomplete="off" required />
                  </div>
                  <div class="form-group">
                      <input id="email" class="form-control login_input" placeholder="Email" name="user_email" type="text" required />
                  </div>
                  <input class="btn btn-lg btn-primary btn-block" type="submit" name="Register" value="Register">
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