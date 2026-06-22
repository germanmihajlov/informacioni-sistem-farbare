<?php
require_once('konekcija.php');
session_start();

if (!isset($_SESSION['id_zaposlenog'])) {
    header("Location: login.php");
    exit;
}

$id_zaposlenog = $_SESSION['id_zaposlenog'];
$message = "";

/* ================== START NABAVKE ================== */
if (isset($_POST['start_nabavka'])) {
    $id_dobavljaca = (int)$_POST['id_dobavljaca'];

    mysqli_query($db,
        "INSERT INTO nabavka (id_dobavljaca, id_zaposlenog, datum, status)
         VALUES ($id_dobavljaca, $id_zaposlenog, CURDATE(), 'u toku')"
    );

    $_SESSION['id_nabavke'] = mysqli_insert_id($db);
}

/* ================== BRISANJE STAVKE (DOK JE U TOKU) ================== */
if (isset($_POST['delete_item']) && isset($_SESSION['id_nabavke'])) {
    $id_nabavke = $_SESSION['id_nabavke'];
    $id_stavke = (int)$_POST['id_stavke'];

    // dozvoljeno samo ako je nabavka u toku
    $status = mysqli_fetch_assoc(mysqli_query($db,
        "SELECT status FROM nabavka WHERE id_nabavke = $id_nabavke"
    ))['status'];

    if ($status === 'u toku') {
        mysqli_query($db,
            "DELETE FROM stavka_nabavke
             WHERE id_stavke = $id_stavke"
        );
    }
}

/* ================== DODAVANJE STAVKE ================== */
if (isset($_POST['add_item']) && isset($_SESSION['id_nabavke'])) {
    $id_nabavke = $_SESSION['id_nabavke'];
    $id_proizvoda = (int)$_POST['id_proizvoda'];
    $kolicina = (int)$_POST['kolicina'];
    $nabavna_cena = (float)$_POST['nabavna_cena'];

    if ($kolicina > 0 && $nabavna_cena > 0) {
        mysqli_query($db,
            "INSERT INTO stavka_nabavke
             (id_nabavke, id_proizvoda, kolicina, nabavna_cena)
             VALUES ($id_nabavke, $id_proizvoda, $kolicina, $nabavna_cena)"
        );
    } else {
        $message = "Neispravni podaci za stavku.";
    }
}

/* ================== ZAVRŠETAK NABAVKE (TRANSAKCIJA) ================== */
if (isset($_POST['finish_nabavka']) && isset($_SESSION['id_nabavke'])) {
    $id_nabavke = $_SESSION['id_nabavke'];

    $status = mysqli_fetch_assoc(mysqli_query($db,
        "SELECT status FROM nabavka WHERE id_nabavke = $id_nabavke"
    ))['status'];

    if ($status !== 'u toku') {
        $message = "Ova nabavka je već završena.";
    } else {

        mysqli_begin_transaction($db);

        try {
            $stavke = mysqli_query($db,
                "SELECT id_proizvoda, kolicina
                 FROM stavka_nabavke
                 WHERE id_nabavke = $id_nabavke"
            );

            while ($s = mysqli_fetch_assoc($stavke)) {
                mysqli_query($db,
                    "UPDATE proizvod
                     SET kolicina_na_lageru = kolicina_na_lageru + {$s['kolicina']}
                     WHERE id_proizvoda = {$s['id_proizvoda']}"
                );
            }

            mysqli_query($db,
                "UPDATE nabavka
                 SET status = 'završena'
                 WHERE id_nabavke = $id_nabavke"
            );

            mysqli_commit($db);
            unset($_SESSION['id_nabavke']);

        } catch (Exception $e) {
            mysqli_rollback($db);
            $message = "Greška prilikom završetka nabavke.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Nabavka</title>
<style>
body { font-family: Arial; background:#f2f2f2; }
header, nav { text-align:center; padding:10px; color:white; }
header { background:#34495e; }
nav { background:#2c3e50; }
nav a { color:white; margin:0 10px; text-decoration:none; font-weight:bold; }
.box { background:white; width:80%; margin:20px auto; padding:20px; border-radius:8px; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border:1px solid #aaa; padding:8px; text-align:center; }
button { padding:6px 10px; cursor:pointer; }
.message { color:red; text-align:center; }
</style>
</head>
<body>

<header><h1>Nabavka proizvoda</h1></header>

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

<?php if (!isset($_SESSION['id_nabavke'])): ?>

<form method="post">
    Dobavljač:
    <select name="id_dobavljaca" required>
        <?php
        $d = mysqli_query($db, "SELECT id_dobavljaca, naziv FROM dobavljac");
        while ($r = mysqli_fetch_assoc($d)) {
            echo "<option value='{$r['id_dobavljaca']}'>{$r['naziv']}</option>";
        }
        ?>
    </select>
    <button name="start_nabavka">Započni nabavku</button>
</form>

<?php else: ?>

<form method="post">
    Proizvod:
    <select name="id_proizvoda">
        <?php
        $p = mysqli_query($db, "SELECT id_proizvoda, naziv FROM proizvod");
        while ($r = mysqli_fetch_assoc($p)) {
            echo "<option value='{$r['id_proizvoda']}'>{$r['naziv']}</option>";
        }
        ?>
    </select>

    Količina:
    <input type="number" name="kolicina" min="1" required>

    Nabavna cena:
    <input type="number" step="0.01" name="nabavna_cena" required>

    <button name="add_item">Dodaj stavku</button>
</form>

<p class="message"><?= $message ?></p>

<table>
<tr><th>Proizvod</th><th>Količina</th><th>Nabavna cena</th><th></th></tr>
<?php
$id_nabavke = $_SESSION['id_nabavke'];
$s = mysqli_query($db,
    "SELECT s.id_stavke, p.naziv, s.kolicina, s.nabavna_cena
     FROM stavka_nabavke s
     JOIN proizvod p ON s.id_proizvoda = p.id_proizvoda
     WHERE s.id_nabavke = $id_nabavke"
);
while ($r = mysqli_fetch_assoc($s)) {
    echo "<tr>
        <td>{$r['naziv']}</td>
        <td>{$r['kolicina']}</td>
        <td>{$r['nabavna_cena']}</td>
        <td>
            <form method='post'>
                <input type='hidden' name='id_stavke' value='{$r['id_stavke']}'>
                <button name='delete_item'>X</button>
            </form>
        </td>
    </tr>";
}
?>
</table>

<form method="post">
    <button name="finish_nabavka">Završi nabavku</button>
</form>

<?php endif; ?>

</div>
</body>
</html>
