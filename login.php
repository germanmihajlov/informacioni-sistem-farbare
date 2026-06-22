<?php
require_once('konekcija.php');
session_start();

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $lozinka = mysqli_real_escape_string($db, $_POST['lozinka']);

    $q = mysqli_query($db,
    "SELECT id_zaposlenog
     FROM zaposleni
     WHERE email = '$email' AND lozinka = '$lozinka'"
);

if (mysqli_num_rows($q) === 1) {
    $z = mysqli_fetch_assoc($q);

    $_SESSION['id_zaposlenog'] = $z['id_zaposlenog'];

    header("Location: porudzbina.php");
    exit;
}

    } else {
        $error = "Pogrešan email ili lozinka.";
    }

?>

<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
body {
    font-family: Arial;
    background:#f2f2f2;
}
.box {
    width:300px;
    margin:100px auto;
    background:white;
    padding:20px;
    border-radius:8px;
    text-align:center;
}
input {
    width:100%;
    padding:8px;
    margin:8px 0;
}
button {
    padding:8px 15px;
}
.error {
    color:red;
}
</style>
</head>

<body>

<div class="box">
    <h2>Prijava</h2>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="lozinka" placeholder="Lozinka" required>
        <button name="login">Uloguj se</button>
    </form>

    <p class="error"><?= $error ?></p>
</div>

</body>
</html>
