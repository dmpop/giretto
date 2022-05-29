<?php
$config = include('config.php');
$pw_hash = password_hash($password, PASSWORD_DEFAULT);

if ($protect) {
    session_start();
}

if (isset($_POST['password']) && password_verify($_POST['password'], $pw_hash)) {
    $_SESSION["password"] = $pw_hash;
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="favicon.png" />
    <link rel="stylesheet" href="css/milligram.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <title><?php echo $title; ?></title>
</head>

<body>
    <div id="content">
        <div style="text-align: center; margin-bottom: 2em;">
            <img style="display: inline; height: 2.5em; border-radius: 0; vertical-align: middle;" src="favicon.svg" alt="logo" />
            <h1 style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px;"><?php echo $title ?></h1>
        </div>
        <form action="" method="POST">
            <label>Password:</label>
            <input style="width: 15em;" type="password" name="password"><br />
            <button type="submit" name="submit">Log in</button>
        </form>
        <hr style="margin-top: 2em; margin-bottom: 1.5em;">
        <?php echo $footer; ?>
    </div>
</body>

</html>