<?php
require_once('konekcija.php');
session_start();
if (!isset($_SESSION['id_zaposlenog'])) {
    header("Location: login.php");
    exit;
}
$id_zaposlenog = $_SESSION['id_zaposlenog'];

$GOTOVINSKI_KUPAC_ID = 5;
$message = "";

// START PORUDŽBINE
if (isset($_POST['start_order'])) {
    mysqli_query($db,
        "INSERT INTO porudzbina (id_kupca, id_zaposlenog, datum, status)
         VALUES ($GOTOVINSKI_KUPAC_ID, $id_zaposlenog, CURDATE(), 'u toku')"
    );
    $_SESSION['id_porudzbine'] = mysqli_insert_id($db);
}

// PROMENA KUPCA
if (isset($_POST['set_kupac']) && isset($_SESSION['id_porudzbine'])) {
    $id_porudzbine = $_SESSION['id_porudzbine'];
    $id_kupca = (int)$_POST['id_kupca'];

    mysqli_query($db,
        "UPDATE porudzbina
         SET id_kupca = $id_kupca
         WHERE id_porudzbine = $id_porudzbine"
    );
}

// DODAVANJE STAVKE
if (isset($_POST['add_item']) && isset($_SESSION['id_porudzbine'])) {
    $id_porudzbine = $_SESSION['id_porudzbine'];
    $id_proizvoda = (int)$_POST['id_proizvoda'];
    $kolicina = (int)$_POST['kolicina'];

    $p = mysqli_fetch_assoc(mysqli_query($db,
        "SELECT cena, kolicina_na_lageru
         FROM proizvod WHERE id_proizvoda = $id_proizvoda"
    ));

    if ($kolicina > 0 && $kolicina <= $p['kolicina_na_lageru']) {
        mysqli_query($db,
            "INSERT INTO stavka_porudzbine
             (id_porudzbine, id_proizvoda, kolicina, cena)
             VALUES ($id_porudzbine, $id_proizvoda, $kolicina, {$p['cena']})"
        );
    } else {
        $message = "Neispravna količina.";
    }
}

// Zavrsetak porudzbine

if (isset($_POST['finish_order']) && isset($_SESSION['id_porudzbine'])) {
    $id = $_SESSION['id_porudzbine'];

    mysqli_begin_transaction($db);

    try {
        // skidanje lagera
        $stavke = mysqli_query($db,
            "SELECT id_proizvoda, kolicina
             FROM stavka_porudzbine
             WHERE id_porudzbine = $id"
        );

        while ($s = mysqli_fetch_assoc($stavke)) {
            mysqli_query($db,
                "UPDATE proizvod
                 SET kolicina_na_lageru = kolicina_na_lageru - {$s['kolicina']}
                 WHERE id_proizvoda = {$s['id_proizvoda']}"
            );
        }

        // završi porudžbinu
        mysqli_query($db,
            "UPDATE porudzbina
             SET status = 'završena'
             WHERE id_porudzbine = $id"
        );

        // kupac
        $kupac = mysqli_fetch_assoc(mysqli_query($db,
            "SELECT id_kupca FROM porudzbina WHERE id_porudzbine = $id"
        ));

        // faktura
        if ($kupac['id_kupca'] != $GOTOVINSKI_KUPAC_ID) {
            mysqli_query($db,
                "INSERT INTO faktura (datum, id_porudzbine, nacin_placanja)
                 VALUES (CURDATE(), $id, 'faktura')"
            );
        }

        mysqli_commit($db);

        $_SESSION['zavrsena_porudzbina'] = $id;
        unset($_SESSION['id_porudzbine']);

    } catch (Exception $e) {
        mysqli_rollback($db);
        $message = "Greška prilikom završetka porudžbine.";
    }
}




?>

<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Porudžbine</title>
<style>
body { font-family: Arial; background:#f2f2f2; }
header, nav { text-align:center; padding:10px; color:white; }
header { background:#34495e; }
nav { background:#2c3e50; }
nav a { color:white; margin:0 10px; text-decoration:none; font-weight:bold; }
.box { background:white; width:80%; margin:20px auto; padding:20px; border-radius:8px; }
table { width:100%; border-collapse:collapse; }
th, td { border:1px solid #aaa; padding:8px; text-align:center; }
button { padding:6px 10px; cursor:pointer; }
.message { color:red; text-align:center; }
</style>
</head>

<body>

<header><h1>Porudžbine</h1></header>

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

<?php
// REZIME
if (isset($_SESSION['zavrsena_porudzbina'])):
$id = $_SESSION['zavrsena_porudzbina'];

$kupac = mysqli_fetch_assoc(mysqli_query($db,
    "SELECT k.naziv_firme
     FROM porudzbina p JOIN kupac k ON p.id_kupca = k.id_kupca
     WHERE p.id_porudzbine = $id"
));

$stavke = mysqli_query($db,
    "SELECT p.naziv, s.kolicina, s.cena
     FROM stavka_porudzbine s
     JOIN proizvod p ON s.id_proizvoda = p.id_proizvoda
     WHERE s.id_porudzbine = $id"
);

$ukupno = mysqli_fetch_assoc(mysqli_query($db,
    "SELECT SUM(kolicina * cena) AS suma
     FROM stavka_porudzbine
     WHERE id_porudzbine = $id"
));

$zaposleni = mysqli_fetch_assoc(mysqli_query($db,
    "SELECT z.email
            FROM porudzbina p
            JOIN zaposleni z ON p.id_zaposlenog = z.id_zaposlenog
            WHERE p.id_porudzbine = $id"
            
));

?>

<h2>Rezime porudžbine</h2>
<p><strong>Kupac:</strong> <?= $kupac['naziv_firme'] ?></p>
<p><strong>Zaposleni:</strong> <?= $zaposleni['email'] ?></p>

<table>
<tr><th>Proizvod</th><th>Količina</th><th>Cena</th></tr>
<?php while ($r = mysqli_fetch_assoc($stavke)): ?>
<tr>
    <td><?= $r['naziv'] ?></td>
    <td><?= $r['kolicina'] ?></td>
    <td><?= $r['cena'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<p><strong>Ukupno:</strong> <?= $ukupno['suma'] ?> RSD</p>


<?php
if ($kupac['naziv_firme'] === 'Gotovinski kupac') {
    echo "<p><em>Fiskalni račun (placeholder)</em></p>";
} else {
    $f = mysqli_fetch_assoc(mysqli_query($db,
        "SELECT id_fakture FROM faktura WHERE id_porudzbine = $id"
    ));

    if ($f) {
        echo "<p>
                <a href='faktura.php?id={$f['id_fakture']}'>
                    Pogledaj fakturu
                </a>
              </p>";
    } else {
        echo "<p><em>Faktura nije generisana</em></p>";
    }
}
?>


<form method="post">
    <button name="start_order">Nova porudžbina</button>
</form>

<?php unset($_SESSION['zavrsena_porudzbina']); ?>

<?php elseif (!isset($_SESSION['id_porudzbine'])): ?>

<form method="post">
    <button name="start_order">Započni porudžbinu</button>
</form>

<?php else:
//  PORUDŽBINA U TOKU

$id_porudzbine = $_SESSION['id_porudzbine'];
$trenutni_kupac = mysqli_fetch_assoc(mysqli_query($db,
    "SELECT id_kupca FROM porudzbina WHERE id_porudzbine = $id_porudzbine"
))['id_kupca'];
?>

<form method="post">
    Kupac:
    <select name="id_kupca" onchange="this.form.submit()">
        <?php
        $kupci = mysqli_query($db,
            "SELECT id_kupca, naziv_firme FROM kupac"
        );
        while ($k = mysqli_fetch_assoc($kupci)) {
            $selected = ($k['id_kupca'] == $trenutni_kupac) ? 'selected' : '';
            echo "<option value='{$k['id_kupca']}' $selected>
                    {$k['naziv_firme']}
                  </option>";
        }
        ?>
    </select>
    <input type="hidden" name="set_kupac">
</form>

<hr>

<form method="post">
    <select name="id_proizvoda">
        <?php
        $p = mysqli_query($db,
            "SELECT id_proizvoda, naziv
             FROM proizvod WHERE kolicina_na_lageru > 0"
        );
        while ($r = mysqli_fetch_assoc($p)) {
            echo "<option value='{$r['id_proizvoda']}'>{$r['naziv']}</option>";
        }
        ?>
    </select>
    <input type="number" name="kolicina" min="1" required>
    <button name="add_item">Dodaj</button>
</form>

<p class="message"><?= $message ?></p>

<table>
<tr><th>Proizvod</th><th>Količina</th><th>Cena</th><th></th></tr>
<?php
$s = mysqli_query($db,
    "SELECT s.id_stavke, p.naziv, s.kolicina, s.cena
     FROM stavka_porudzbine s
     JOIN proizvod p ON s.id_proizvoda = p.id_proizvoda
     WHERE s.id_porudzbine = $id_porudzbine"
);
while ($r = mysqli_fetch_assoc($s)) {
    echo "<tr>
        <td>{$r['naziv']}</td>
        <td>{$r['kolicina']}</td>
        <td>{$r['cena']}</td>
        <td>
            <form method='post'>
                <input type='hidden' name='id_stavke' value='{$r['id_stavke']}'>
                
            </form>
        </td>
    </tr>";
}
?>
</table>

<form method="post">
    <button name="finish_order">Završi porudžbinu</button>
</form>

<?php endif; ?>

</div>
</body>
</html>
