<?php
require_once('konekcija.php');
session_start();

// PROMENA STATUSA REKLAMACIJE
if (isset($_POST['update_status'])) {
    $id_reklamacije = (int)$_POST['id_reklamacije'];
    $novi_status = mysqli_real_escape_string($db, $_POST['status']);

    // dozvoljeni statusi
    $dozvoljeni = ['u obradi', 'odobrena', 'odbijena'];

    if (in_array($novi_status, $dozvoljeni)) {

        $trenutno = mysqli_fetch_assoc(mysqli_query($db,
            "SELECT status FROM reklamacija WHERE id_reklamacije = $id_reklamacije"
        ));

        if ($trenutno && $trenutno['status'] === 'u obradi') {
            mysqli_query($db,
                "UPDATE reklamacija
                 SET status = '$novi_status'
                 WHERE id_reklamacije = $id_reklamacije"
            );
        }
    }
}

$message = "";

// DODAVANJE REKLAMACIJE
if (isset($_POST['dodaj_reklamaciju'])) {
    $id_porudzbine = (int)$_POST['id_porudzbine'];
    $opis = mysqli_real_escape_string($db, $_POST['opis']);

    mysqli_query($db,
        "INSERT INTO reklamacija (id_porudzbine, opis, status, datum)
         VALUES ($id_porudzbine, '$opis', 'u obradi', CURDATE())"
    );

    $message = "Reklamacija je uspešno evidentirana.";
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Reklamacije</title>
<style>
body { font-family: Arial; background:#f2f2f2; }
header, nav { text-align:center; padding:10px; color:white; }
header { background:#34495e; }
nav { background:#2c3e50; }
nav a { color:white; margin:0 10px; text-decoration:none; font-weight:bold; }
.box { background:white; width:70%; margin:20px auto; padding:20px; border-radius:8px; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th, td { border:1px solid #aaa; padding:8px; text-align:center; }
textarea { width:95%; height:80px; }
button { padding:6px 12px; cursor:pointer; }
.message { text-align:center; color:green; font-weight:bold; }
</style>
</head>

<body>

<header><h1>Reklamacije</h1></header>

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

<div class="box">

<h2>Nova reklamacija</h2>

<form method="post">
    Porudžbina:
    <select name="id_porudzbine" required>
        <?php
        $porudzbine = mysqli_query($db,
            "SELECT id_porudzbine
             FROM porudzbina
             WHERE status = 'završena'"
        );

        while ($p = mysqli_fetch_assoc($porudzbine)) {
            echo "<option value='{$p['id_porudzbine']}'>
                    Porudžbina #{$p['id_porudzbine']}
                  </option>";
        }
        ?>
    </select>
    <br><br>

    Opis reklamacije:<br>
    <textarea name="opis" required></textarea><br><br>

    <button name="dodaj_reklamaciju">Sačuvaj reklamaciju</button>
</form>

<p class="message"><?= $message ?></p>

<hr>

<h2>Lista reklamacija</h2>

<table>
<tr>
    <th>ID</th>
    <th>Porudžbina</th>
    <th>Opis</th>
    <th>Status</th>
    <th>Akcija</th>
</tr>

<?php
$reklamacije = mysqli_query($db,
    "SELECT id_reklamacije, id_porudzbine, opis, status
     FROM reklamacija"
);

while ($r = mysqli_fetch_assoc($reklamacije)):
?>
<tr>
    <td><?= $r['id_reklamacije'] ?></td>
    <td><?= $r['id_porudzbine'] ?></td>
    <td><?= $r['opis'] ?></td>
    <td><?= $r['status'] ?></td>
    <td>
        <?php if ($r['status'] === 'u obradi'): ?>
        <form method="post">
            <input type="hidden" name="id_reklamacije" value="<?= $r['id_reklamacije'] ?>">

            <select name="status">
                <option value="odobrena">odobrena</option>
                <option value="odbijena">odbijena</option>
            </select>

            <button name="update_status">Sačuvaj</button>
        </form>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

</div>
</body>
</html>
