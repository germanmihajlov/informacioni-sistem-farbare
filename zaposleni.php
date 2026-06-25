<?php
require_once('konekcija.php');
session_start();

if (!isset($_SESSION['id_zaposlenog'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// OBRADA POST ZAHTEVA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ime_prezime_z = mysqli_real_escape_string($db, $_POST['ime_prezime_z'] ?? '');
    $uloga = mysqli_real_escape_string($db, $_POST['uloga'] ?? '');
    $email = mysqli_real_escape_string($db, $_POST['email'] ?? '');
    $id_zaposlenog_form = isset($_POST['id_zaposlenog']) ? (int)$_POST['id_zaposlenog'] : 0;

    // DODAVANJE ZAPOSLENOG
    if (isset($_POST['create'])) {
        $query = "INSERT INTO zaposleni (ime_prezime_z, uloga, email, status)
                  VALUES ('$ime_prezime_z', '$uloga', '$email', 'aktivan')";
        if (mysqli_query($db, $query)) {
            $message = "Zaposleni je uspešno dodat.";
        } else {
            $message = "Greška pri dodavanju: " . mysqli_error($db);
        }
    }

    // IZMENA ZAPOSLENOG
    if (isset($_POST['update'])) {
        $query = "UPDATE zaposleni SET
                    ime_prezime_z = '$ime_prezime_z',
                    uloga = '$uloga',
                    email = '$email'
                  WHERE id_zaposlenog = $id_zaposlenog_form";
        if (mysqli_query($db, $query)) {
            $message = "Podaci o zaposlenom su ažurirani.";
        } else {
            $message = "Greška pri ažuriranju: " . mysqli_error($db);
        }
    }

    // DEAKTIVACIJA (SOFT DELETE)
    if (isset($_POST['deactivate'])) {
        $query = "UPDATE zaposleni
                  SET status = 'neaktivan'
                  WHERE id_zaposlenog = $id_zaposlenog_form";
        if (mysqli_query($db, $query)) {
            $message = "Zaposleni je deaktiviran.";
        } else {
            $message = "Greška pri deaktivaciji: " . mysqli_error($db);
        }
    }
}

// GET ZA IZMENU
$edit_zaposleni = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $result = mysqli_query($db, "SELECT * FROM zaposleni WHERE id_zaposlenog = $id");
    $edit_zaposleni = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Farbara – Zaposleni</title>
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
        form.inline { display:inline; }
    </style>
</head>
<body>

<header>
    <h1>Farbara – Upravljanje zaposlenima</h1>
</header>

<nav>
    <a href="kupac.php">Kupci</a>
    <a href="zaposleni.php">Zaposleni</a>
    <a href="proizvod.php">Proizvodi</a>
    <a href="porudzbina.php">Porudžbine</a>
    <a href="nabavka.php">Nabavka</a>
    <a href="reklamacija.php">Reklamacije</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="box">
    <h2>Dodaj novog zaposlenog</h2>
    <form method="post">
        Ime i prezime:<br>
        <input type="text" name="ime_prezime_z" required><br>
        Pozicija:<br>
        <input type="text" name="uloga"><br>
        Email:<br>
        <input type="email" name="email"><br><br>
        <button type="submit" name="create">Dodaj zaposlenog</button>
    </form>
</div>

<?php if ($edit_zaposleni): ?>
<div class="box">
    <h2>Izmena zaposlenog</h2>
    <form method="post">
        <input type="hidden" name="id_zaposlenog" value="<?= $edit_zaposleni['id_zaposlenog'] ?>">
        Ime i prezime:<br>
        <input type="text" name="ime_prezime_z" value="<?= $edit_zaposleni['ime_prezime_z'] ?>" required><br>
        Pozicija:<br>
        <input type="text" name="uloga" value="<?= $edit_zaposleni['uloga'] ?>"><br>
        Email:<br>
        <input type="email" name="email" value="<?= $edit_zaposleni['email'] ?>"><br><br>
        <button type="submit" name="update">Sačuvaj izmene</button>
    </form>
</div>
<?php endif; ?>

<div class="message"><?= $message ?></div>

<?php
$result = mysqli_query($db, "SELECT * FROM zaposleni WHERE status = 'aktivan'");
if ($result && mysqli_num_rows($result) > 0):
?>
<table>
<tr>
    <th>ID</th>
    <th>Ime i prezime</th>
    <th>Pozicija</th>
    <th>Email</th>
    <th>Akcija</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id_zaposlenog'] ?></td>
    <td><?= $row['ime_prezime_z'] ?></td>
    <td><?= $row['uloga'] ?></td>
    <td><?= $row['email'] ?></td>
    <td>
        <form method="get" class="inline">
            <input type="hidden" name="edit" value="<?= $row['id_zaposlenog'] ?>">
            <button type="submit">Izmeni</button>
        </form>
        |
        <form method="post" class="inline">
            <input type="hidden" name="id_zaposlenog" value="<?= $row['id_zaposlenog'] ?>">
            <button type="submit" name="deactivate"
              onclick="return confirm('Deaktivirati zaposlenog?')">
              Deaktiviraj
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>

</body>
</html>
