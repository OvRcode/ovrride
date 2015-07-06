<?php
require_once('PasswordHashClass.php');
require_once('db.php');
require_once('Mandrill.php');

$db = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME .';charset=utf8', DB_USER, DB_PASS);

if ( isset($_POST['key']) && isset($_POST['user_id']) ) {
    $hashCheck = $db->prepare("SELECT * FROM ovr_lists_login WHERE user_id = :id");
    $hashCheck->bindParam("id", $_POST['user_id'], PDO::PARAM_STR);
    $hashCheck->execute();
    if ( $hashCheck->rowCount() !== 1 ) {
        echo "Something went wrong, please try again.";
    } else {
        $user = $hashCheck->fetch(PDO::FETCH_ASSOC);
        $hash_string = $user['user_id'] . $user['user_name'] . $user['user_email'] . $user['user_password_hash'];
        $reset_hash = urlencode( hash_hmac('sha256', $hash_string, $user['user_password_hash']) );

        if ( PasswordHash::slow_equals($_POST['key'], $reset_hash) == 1 ) {
            $hashed_password = PasswordHash::create_hash($_POST['user_password']);
            $passwordReset = $db->prepare("UPDATE ovr_lists_login SET user_password_hash = :password_hash, activated = '1' WHERE user_id = :id");
            if ( $passwordReset->execute(array("password_hash" => $hashed_password, "id" => $_POST['user_id'])) ) {
                echo "Password has been reset go to <a href='https://{$_SERVER['SERVER_NAME']}/login/login.php'>https://{$_SERVER['SERVER_NAME']}/login/login.php</a> to login";
            } else {
                echo "Password failed to update.";
            }
        }
    }
}
else if ( isset($_POST['Reset']) && $_POST['user_name'] && isset($_POST['user_email'])) {
    $userCheck = $db->prepare("SELECT * FROM ovr_lists_login WHERE user_name = :user AND user_email = :email");
    $userCheck->bindParam("user", $_POST['user_name'], PDO::PARAM_STR);
    $userCheck->bindParam("email", $_POST['user_email'], PDO::PARAM_STR);
    $userCheck->execute();
    if ( $userCheck->rowCount() === 0 ) {
        # No Matching user account, let's not leak too much info here
        echo "Something went wrong, please try again.";
    } else {
        # User exists, generating reset email
        $user = $userCheck->fetch(PDO::FETCH_ASSOC);
        $reset_hash_string = $user['user_id'] . $user['user_name'] . $user['user_email'] . $user['user_password_hash'];
        $reset_key = urlencode( hash_hmac('sha256', $reset_hash_string, $user['user_password_hash']) );
        
        $mandrillAPI = new Mandrill(getenv('MANDRILL_API'));
        $textBody = <<<AAA
            A password reset request was made for your account. If you did not make this request then ignore this email.\n
        If you did request a reset copy the following address into your browser to reset your password.
            Reset Link: https://{$_SERVER['SERVER_NAME']}/login/reset.php?id={$user['user_id']}&key={$reset_key}\n
AAA;
        $bodyHTML = <<<BBB
            <p>A password reset request was made for your account. If you did not make this request then ignore this email.
        If you did request a reset copy the following address into your browser to reset your password.</p>
            Reset Link: <a href="https://{$_SERVER['SERVER_NAME']}/login/reset.php?id={$user['user_id']}&key={$reset_key}">         
                            https://{$_SERVER['SERVER_NAME']}/login/reset.php?id={$user['user_id']}&key={$reset_key}
                        </a>
BBB;
        $message = new stdClass();
        $message->html = $bodyHTML;
        $message->text = $textBody;
        $message->subject = "OvR Lists: Password Reset";
        $message->from_email = "devops@ovrride.com";
        $message->from_name  = "OvR";
        $message->to = array(array("email" => $user['user_email']));
        $message->track_opens = true;
        $response = $mandrillAPI->messages->send($message);
        echo "An email is on its way, check your inbox";
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
                <h4>Password Reset</h4>
                <?php if ( !isset($_GET['key']) && !isset($_GET['id']) ) : ?>
                    <p>Enter email and username, a password reset link will be emailed to you.</p>
                <?php else : ?>
                    <p>Enter your new password</p>
                <?php endif; ?>
              <form accept-charset="UTF-8" role="form" method="post" action="reset.php" name="loginform">
                <fieldset>
                  <div class="form-group">
                    <input id="username" class="form-control login_input" placeholder="Username" name="user_name" type="text" required />
                  </div>
                  <div class="form-group">
                      <input id="email" class="form-control login_input" placeholder="Email" name="user_email" type="text" required />
                  </div>
                  <?php if ( isset($_GET['key']) && isset($_GET['id']) ) : ?>
                      <input id="key" type="hidden" name="key" value="<?php echo $_GET['key']?>">
                      <input id="id" type="hidden" name="user_id" value="<?php echo $_GET['id']?>">
                      <div class="form-group">
                          <input id="password" class="form-control login_input" name="user_password" type="password" placeholder="New Password" required />
                      </div>
                  <?php endif; ?>
                  <input class="btn btn-lg btn-primary btn-block" type="submit" name="Reset" value="Reset">
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