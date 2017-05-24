<?php
session_start();
$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
try{
   /* if ($_POST["captcha"] == $_SESSION["captcha_code"]) {*/
        $name = trim($_POST['name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $message = "<p class='error'>Required Parameter is missing</p>";
        } else {
            if($password != $confirmPassword) {
                $message = "<p class='error'>Please verify Password and Confirm Password.</p>";
            } else {

                include "database_access.php";
                if (!$connection) {
                    $message = "<p class='error'>Connection Failed.</p>";
                } else {

                    if( !preg_match('^(?=.*\d)(?=.*?[a-zA-Z])(?=.*?[\W_]).{6,10}$^', $password) || strlen( $password) < 6) {
                        $message = "<p class='error'>Password length should be 6-20 characters and contain at-least one digit, upper or lowercase letter and at-least one special character.</p>";
                    } else {
                        $repeat = '';
                        $query = "select * from users where username = '$username' or email = '$email'";
                        $users = $connection->query($query);

                        foreach ($users as $user) {
                            if($user['email'] == $email) {
                                $repeat = 'email';
                            } else if ($user['username'] == $username) {
                                $repeat = 'username';
                            }
                        }

                        if(!$repeat) {
                            include "functions.php";
                            $salt = generateRandomSalt();
                            $hashedPassword = encrypt_decrypt('encrypt', $password, $salt);
                            $hashedSalt = encrypt_decrypt('encrypt', $salt);
                            $insertQuery = "INSERT INTO users (name, username, email, password, salt) VALUES  (:name, :username, :email, :password, :salt)";
                            $statement = $connection->prepare($insertQuery);
                            $result = $statement->execute(['name' => $name, 'username' => $username, 'email' => $email, 'password' => $hashedPassword, 'salt' => $hashedSalt]);
                            if (!$result) {
                                $message = "<p class='error'>Error in User Sign up</p>";
                            } else {
                                echo '<script>window.location = "login.php";</script>';
                                exit();
                            }
                        } else {
                            $message = "<p class='error'>Username or Email should be unique. Please enter unique ".$repeat."</p>";
                        }
                    }
                }
            }
        }
  /*  } else {
        $message = "<p class='error'>Enter Correct Captcha Code.</p>";
    }*/
}catch (Exception $e) {
    $message = "<p class='error'>Error : " . $e->getMessage() . "</p>";
}
}
include "master.php" ?>

<script>
        function sendContact() {
            var valid;
            valid = validateContact();
            if (valid) {
                $('#signup-form').submit();
            }
        }

        function validateContact() {
            var valid = true;
            $(".demoInputBox").css('background-color', '');
            $("#signup-status").html('');

            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();
            var password_length = password.length;
            var inputs = ['username', 'name', 'email', 'password', 'confirm_password'];
            for (var i = 0 ; i < inputs.length ; i++ ) {
                if (!$("#"+inputs[i]).val()) {
                    $("#"+inputs[i]).css('background-color', '#FFFFDF');
                    $("#signup-status").html('<p class="error">Required Parameter is missing.</p>');
                    valid = false;
                }
            }
            if(valid) {
                if(confirm_password != password) {
                    $("#confirm_password").css('background-color', '#FFFFDF');
                    $("#password").css('background-color', '#FFFFDF');
                    $("#signup-status").html('<p class="error">Please verify Password and Confirm Password.</p>');
                    valid = false;
                } else if (password_length < 6 || password_length > 20) {
                    $("#password").css('background-color', '#FFFFDF');
                    $('#password-error').html('Password should be atleast 6 character');
                    $("#signup-status").html('<p class="error">Password length should be 6-20 characters.</p>');
                    valid = false;
                }
            }
            return valid;
        }
    </script>

<div class="login">
    <div class="box-header">
        <h3 class="login-heading">Sign Up</h3>
    </div>

    <div class="login-body">
        <form id="signup-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" accept-charset="UTF-8" class="form-horizontal form-login">
            <div class="form-group ">
                <div id="signup-status" class="col-sm-12">
                    <?php echo $message; ?>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="name">
                        Name
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="name" class="form-control" placeholder="Name" name="name" type="text" value="" required>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="username">
                        Username
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="username" class="form-control" placeholder="Username" name="username" type="text" value="" required>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="email">
                        Email
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="email" class="form-control" placeholder="Email" name="email" type="email" value="" required>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="password">
                        Password
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="password" class="form-control" placeholder="Password" name="password" type="password" value="" required>
                    <span class="error-message" id="password-error"> </span>

                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="confirm_password">
                        Confirm Password
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="confirm_password" class="form-control" placeholder="Confirm Password" name="confirm_password" type="password" value="" required>
                </div>
            </div>

            <!--<div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="captcha">
                        Captcha
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="captcha" class="form-control" placeholder="Captcha" name="captcha" type="password" value="" required>
                </div>
            </div>-->

            <?php /*include "captcha.php" */?>

            <div class="form-group" style="margin-bottom: 40px;">
                <div class="col-sm-12">
                    <input class="btn btn-global btn-global-thin text-uppercase" type="button" onclick="sendContact()" value="Sign Up">
                </div>
            </div>
        </form>
    </div>
</div>
    <?php include "footer.php" ?>
