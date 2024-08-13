<?php
error_reporting(E_ALL);  // Report all types of errors
ini_set('display_errors', 1);
session_start();
require 'validator.php';
require 'cleanInput.php';
require 'helperFunction/login.php';
require 'salt.php';

define('API_LOGIN_URL', 'http://192.164.177.170/BVNPORTAL_API/auth/login.php');

if (isset($_SESSION['login'])) {
    if ($_SESSION['login'] === 'yes') {
        header("Location: tableDatabaseCount.php");
        exit;
    }
}
if (isset($_GET['session'])) {
    switch ($_GET['session']) {
        case 'timeout': {
                $timeout = 'Your session was timed out due to 300 seconds of inactivity.';
                break;
            }

        default:
            # code...
            break;
    }
    # code...
}
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = cleanInput($_POST['email']); //$_POST['email']
    $password = cleanInput($_POST['password']); //$_POST['password']
    $response = callLoginApi($email, $password);

    if ($response['status'] == 200 && $response['message'] == 'Success') {
        $_SESSION['login'] = 'yes';
        $_SESSION['email'] = $email;
        $_SESSION['app'] = 'BVN_PORTAL';
        header("Location:  tableDatabaseCount.php");
    } elseif($response['status'] == 400 ){
        $errors['empass'] = $response['message'];
    }
}

function callLoginApi($email, $password) {
    $postData = json_encode(array('email' => $email, 'password' => $password));

    $ch = curl_init(API_LOGIN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode == 200) {
        $responseData = json_decode($response, true);
        return $responseData;
    }
    return false;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>BVN PORTAL | Signin</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/palette.css">
    </head>
    <body id="landing">
        <?php
        include 'header.php'
        ?>
        <?php
        include 'showMessage.php';
        if (isset($errors)) {
            if (count($errors) !== 0) {
                $errMessage = 'Invalid Email/Password';

                showMessageError($errMessage);
            }
        }
        if (isset($success)) {
            $successMessage = '';
            foreach ($success as $key => $value) {
                $successMessage .= "$value ";
            }
            showMessageSuccess($successMessage);
        }
        if (isset($timeout)) {
            showMessageTimeout($timeout);
        }
        ?>
        <div class="container box">
            <div class="row d-flex justify-content-center">
                <form class="dark-primary-color rounded p-5" method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <p class="h2 form-header db-text">BVN PORTAL</p>
                    </div>
                    <div class="form-group">
                        <label class="db-text" for="email">Email: </label>
                        <input id="email" class="form-control border-dark" type="email" name="email" required />
                    </div>
                    <div class="form-group">
                        <label class="db-text" for="password">Password: </label>
                        <input id="password" class="form-control border-dark" type="password" name="password" required/>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" onclick="hideMessage()" class="btn btn-black ml-5" name="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
                    function hideMessage(){
                    let alertList = document.getElementsByClassName('alert');
                            for (elm of alertList){
                    elm.classList.add('d-none');
                    }
                    }

        </script>
    </body>
</html>
