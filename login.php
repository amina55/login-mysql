<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    echo '<script>window.location = "welcome.php";</script>';
    exit();
}
$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
try{
    if ($_POST["captcha"] == $_SESSION["captcha_code"]) {
        $userName = trim($_POST['username']);
        $password = trim($_POST['password']);
        if (empty($userName) || empty($password)) {
            $message = "<p class='error'>Required Parameter is missing</p>";
        } else {
            include "database_access.php";

            if (!$connection) {
                $message = "<p class='error'>Connection Failed.</p>";
            } else {
                $stmt = $connection->prepare('SELECT id, username, password, salt FROM users WHERE username = :username');
                $stmt->execute(['username' => $userName]);
                $user = $stmt->fetch();
                if (empty($user)) {
                    $message = "<p class='error'>User Name is incorrect</p>";
                } else {
                    include "functions.php";
                    $decryptSalt = encrypt_decrypt('decrypt', $user['salt']);
                    $decryptPassword = encrypt_decrypt('decrypt', $user['password'], $decryptSalt);

                    if($password == $decryptPassword) {
                        $salt = generateRandomSalt();
                        $hashedPassword = encrypt_decrypt('encrypt', $password, $salt);
                        $hashedSalt = encrypt_decrypt('encrypt', $salt);
                        $updateQuery = "Update users SET salt = :salt, password = :password where id = :id";
                        $statement = $connection->prepare($updateQuery);
                        $statement->execute(['salt' => $hashedSalt, 'password' => $hashedPassword, 'id' => $user['id']]);
                        $_SESSION['logged_in'] = $userName;
                        echo '<script>window.location = "welcome.php";</script>';
                        exit();
                    } else {
                        $message = "<p class='error'>Your given password is incorrect</p>";
                    }

                }
            }
        }
    } else {
        $message = "<p class='error'>Enter Correct Captcha Code.</p>";
    }
}catch (Exception $e) {
    $message = "<p class='error'>Error : " . $e->getMessage() . "</p>";
}
}
    include "master.php";
?>
 <script>
        function sendContact() {
            var valid;
            valid = validateContact();
            if (valid) {
                $('#login-form').submit();
            }
        }

        function validateContact() {
            var valid = true;
            $(".demoInputBox").css('background-color', 'white');
            var inputs = ['username', 'password', 'captcha'];
            $("#login-status").html('');
            for (var i = 0; i < inputs.length ; i++) {
                if (!$("#" + inputs[i]).val()) {
                    $("#" + inputs[i]).css('background-color', '#FFFFDF');
                    valid = false;
                    $("#login-status").html('<p class="error">Required Parameter is missing.</p>');
                }
            }
            return valid;
        }
    </script>

<div class="login">
    <div class="box-header">
        <h3 class="login-heading">Log In</h3>
    </div>

    <div class="login-body">
        <form id="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="form-horizontal form-login">
            <div class="form-group ">
                <div id="login-status" class="col-sm-12">
                    <?php echo $message; ?>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="username">
                        Username
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="username" class="form-control" placeholder="Username" name="username" type="text" value="" required>
                    <span class="error-message"></span>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="password">
                        Password
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="password" class="form-control" placeholder="Password" name="password" type="password" value="" required>
                    <span class="error-message"></span>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-sm-12">
                    <label class="control-label mb10" for="captcha">
                        Captcha
                        <em class="required-asterik">*</em>
                    </label>
                    <input id="captcha" class="form-control" placeholder="Captcha" name="captcha" type="password" value="" required>
                </div>
            </div>

            <?php include "captcha.php" ?>
            <div class="form-group" style="margin-bottom: 40px;">
                <div class="col-sm-12">
                    <input type="button" class="btn btn-global btn-global-thin text-uppercase" onclick="sendContact()" value="Log In">
                </div>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php" ?>
