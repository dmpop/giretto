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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <head>

        <meta name="viewport" content="width=device-width">
        <link rel="shortcut icon" href="favicon.png" />
        <link rel="stylesheet" href="css/milligram.min.css">
        <link rel="stylesheet" href="css/styles.css">
        <title><?php echo $title; ?></title>
    </head>
</head>

<body>
    <div id="content">
        <h1><?php echo $title; ?></h1>
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