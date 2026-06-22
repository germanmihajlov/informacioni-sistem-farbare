<?php
require_once('konekcija.php');
session_start();

if (!isset($_SESSION['id_zaposlenog'])) {
    header("Location: login.php");
    exit;
}
$id_zaposlenog = $_SESSION['id_zaposlenog'];

$message = "";

// ================== DODAVANJE KUPCA ==================
if (isset($_POST['create'])) {
    $naziv_firme = mysqli_real_escape_string($db, $_POST['naziv_firme']);
    $adresa = mysqli_real_escape_string($db, $_POST['adresa']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $telefon = mysqli_real_escape_string($db, $_POST['telefon']);

    $query = "INSERT INTO kupac (naziv_firme, adresa, email, telefon)
              VALUES ('$naziv_firme', '$adresa', '$email', '$telefon')";

    if (mysqli_query($db, $query)) {
        $message = "Kupac je uspešno dodat.";
    } else {
        $message = "Greška: " . mysqli_error($db);
    }
}

// ================== IZMENA KUPCA ==================
if (isset($_POST['update'])) {
    $id_kupca = $_POST['id_kupca'];
    $naziv_firme = mysqli_real_escape_string($db, $_POST['naziv_firme']);
    $adresa = mysqli_real_escape_string($db, $_POST['adresa']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $telefon = mysqli_real_escape_string($db, $_POST['telefon']);

    $query = "UPDATE kupac SET
                naziv_firme='$naziv_firme',
                adresa='$adresa',
                email='$email',
                telefon='$telefon'
              WHERE id_kupca=$id_kupca";

    if (mysqli_query($db, $query)) {
        $message = "Podaci o kupcu su ažurirani.";
    } else {
        $message = "Greška: " . mysqli_error($db);
    }
}

// ================== UČITAVANJE KUPCA ZA IZMENU ==================
$edit_kupac = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($db, "SELECT * FROM kupac WHERE id_kupca=$id");
    $edit_kupac = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Farbara – Kupci</title>
    <style>
        body { font-family: Arial; background:#f2f2f2;}
        header, nav { text-align:center; color:white; padding:10px; }
        header { background:#34495e; }
        nav { background:#2c3e50; }
        nav a { color:white; margin:0 10px; text-decoration:none; font-weight:bold; }
        nav a:hover { text-decoration:underline; }

        .box { background:white; width:60%; margin:20px auto; padding:20px; border-radius:8px; }
        input { width:90%; padding:8px; margin:5px 0; }
        button { padding:8px 12px; cursor:pointer; }

        table { width:90%; margin:20px auto; border-collapse:collapse; }
        th, td { border:1px solid #aaa; padding:8px; text-align:center; }
        .message { text-align:center; color:green; font-weight:bold; }

        form.inline { display:inline; margin:0; }
    </style>
</head>
<body>

<header>
    <h1>Evidencija stalnih kupaca</h1>
</header>

<nav>
<?php if (isset($_SESSION['id_zaposlenog'])): ?>
    <a href="kupac.php">Kupci</a>
    <a href="zaposleni.php">Zaposleni</a>
    <a href="proizvod.php">Proizvodi</a>
    <a href="porudzbina.php">Porudžbine</a>
    <a href="nabavka.php">Nabavka</a>
    <a href="reklamacija.php">Reklamacije</a>
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="login.php">Login</a>
<?php endif; ?>
</nav>


<!-- ================== FORMA ZA DODAVANJE ================== -->
<div class="box">
    <h2>Dodaj novog kupca (firmu)</h2>
    <form method="post">
        Naziv firme:<br>
        <input type="text" name="naziv_firme" required><br>

        Adresa:<br>
        <input type="text" name="adresa"><br>

        Email:<br>
        <input type="email" name="email"><br>

        Telefon:<br>
        <input type="text" name="telefon"><br><br>

        <button type="submit" name="create">Dodaj kupca</button>
    </form>
</div>

<!-- ================== FORMA ZA IZMENU ================== -->
<?php if ($edit_kupac): ?>
<div class="box">
    <h2>Izmena kupca</h2>
    <form method="post">
        <input type="hidden" name="id_kupca" value="<?= $edit_kupac['id_kupca'] ?>">

        Naziv firme:<br>
        <input type="text" name="naziv_firme" value="<?= $edit_kupac['naziv_firme'] ?>"><br>

        Adresa:<br>
        <input type="text" name="adresa" value="<?= $edit_kupac['adresa'] ?>"><br>

        Email:<br>
        <input type="email" name="email" value="<?= $edit_kupac['email'] ?>"><br>

        Telefon:<br>
        <input type="text" name="telefon" value="<?= $edit_kupac['telefon'] ?>"><br><br>

        <button type="submit" name="update">Sačuvaj izmene</button>
    </form>
</div>
<?php endif; ?>

<!-- ================== TABELA KUPACA ================== -->
<?php
$result = mysqli_query($db, "SELECT * FROM kupac");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Naziv firme</th><th>Email</th><th>Akcija</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id_kupca']}</td>
                <td>{$row['naziv_firme']}</td>
                <td>{$row['email']}</td>
                <td>
                    <form method='get' action='kupac.php' style='display:inline;'>
                    <input type='hidden' name='edit' value='{$row['id_kupca']}'>
                    <button type='submit'>Izmeni</button>
                    </form>

                </td>

              </tr>";
    }
    echo "</table>";
}
?>

<p style="text-align:center;color:green;"><b><?= $message ?></b></p>

</body>
</html>
