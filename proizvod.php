<?php
require_once('konekcija.php');
session_start();

if (!isset($_SESSION['id_zaposlenog'])) {
    header("Location: login.php");
    exit;
}
$id_zaposlenog = $_SESSION['id_zaposlenog'];

$message = "";

// POST ZAHTEV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $naziv = mysqli_real_escape_string($db, $_POST['naziv'] ?? '');
    $cena = (float) ($_POST['cena'] ?? 0);
    $kolicina = (int) ($_POST['kolicina'] ?? 0);
    $id_kategorije = (int) ($_POST['id_kategorije'] ?? 0);
    $id_proizvoda = (int) ($_POST['id_proizvoda'] ?? 0);
    $tip = mysqli_real_escape_string($db, $_POST['tip'] ?? '');
    $boja = mysqli_real_escape_string($db, $_POST['boja'] ?? '');
    $zapremina = (float)($_POST['zapremina'] ?? 0);


    if ($cena < 0 || $kolicina < 0) {
        $message = "Cena i količina ne mogu biti negativne.";
    } else {

        // DODAVANJE NOVOG PROIZVODA
        if (isset($_POST['create'])) {
            $query = "INSERT INTO proizvod (naziv, cena, kolicina_na_lageru, id_kategorije, tip, boja, zapremina)
                      VALUES ('$naziv', $cena, $kolicina, $id_kategorije, $tip, $boja, $zapremina)";
            if (mysqli_query($db, $query)) {
                $message = "Proizvod je dodat.";
            } else {
                $message = mysqli_error($db);
            }
        }

        // IZMENA
        if (isset($_POST['update'])) {
            $query = "UPDATE proizvod SET
                        naziv='$naziv',
                        cena=$cena,
                        id_kategorije=$id_kategorije,
                        tip=$tip,
                        boja=$boja,
                        zapremina=$zapremina
                      WHERE id_proizvoda=$id_proizvoda";
            if (mysqli_query($db, $query)) {
                $message = "Proizvod je izmenjen.";
            } else {
                $message = mysqli_error($db);
            }
        }

        
    }
}

// PODACI ZA IZMENU
$edit_proizvod = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = mysqli_query($db, "SELECT * FROM proizvod WHERE id_proizvoda=$id");
    $edit_proizvod = mysqli_fetch_assoc($res);
}

// KATEGORIJE
$kategorije = mysqli_query($db, "SELECT * FROM kategorija_proizvoda");
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Proizvodi</title>

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
    <h1>Farbara – Proizvodi</h1>
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


// DODAVANJE
<div class="box">
    <h2>Dodaj proizvod</h2>
    <form method="post">
        Naziv:
        <input type="text" name="naziv" required>

        Cena:
        <input type="number" name="cena" required>

        Količina:
        <input type="number" name="kolicina" required>

        Kategorija:
        <select name="id_kategorije" required>
            <option value="">-- Izaberi --</option>
            <?php while ($k = mysqli_fetch_assoc($kategorije)): ?>
                <option value="<?= $k['id_kategorije'] ?>">
                    <?= $k['naziv_kategorije'] ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        Tip:
        <input type="text" name="tip"><br>

        Boja:
        <input type="text" name="boja"><br>

        Zapremina:
        <input type="number" step="0.5" name="zapremina"><br>


        <br><br>
        <button type="submit" name="create">Dodaj</button>
    </form>
</div>

// IZMENA
<?php if ($edit_proizvod): ?>
<div class="box">
    <h2>Izmena proizvoda</h2>
    <form method="post">
        <input type="hidden" name="id_proizvoda" value="<?= $edit_proizvod['id_proizvoda'] ?>">

        Naziv:
        <input type="text" name="naziv" value="<?= $edit_proizvod['naziv'] ?>" required>

        Cena:
        <input type="number" name="cena" value="<?= $edit_proizvod['cena'] ?>" required>


        Kategorija:
        <select name="id_kategorije" required>
            <?php
            mysqli_data_seek($kategorije, 0);
            while ($k = mysqli_fetch_assoc($kategorije)):
            ?>
                <option value="<?= $k['id_kategorije'] ?>"
                    <?= $k['id_kategorije'] == $edit_proizvod['id_kategorije'] ? 'selected' : '' ?>>
                    <?= $k['naziv_kategorije'] ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        Tip:
        <input type="text" name="tip"><br>

        Boja:
        <input type="text" name="boja"><br>

        Zapremina :
        <input type="number" step="0.01" name="zapremina"><br>


        <br><br>
        <button type="submit" name="update">Sačuvaj izmene</button>
    </form>
</div>
<?php endif; ?>

<div class="message"><?= $message ?></div>

// TABELA
<table>
<tr>
    <th>ID</th>
    <th>Naziv</th>
    <th>Cena</th>
    <th>Lager</th>
    <th>Kategorija</th>
    <th>Akcija</th>
</tr>

<?php
$result = mysqli_query($db, "
    SELECT p.*, k.naziv_kategorije
    FROM proizvod p
    LEFT JOIN kategorija_proizvoda k ON p.id_kategorije = k.id_kategorije
");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$row['id_proizvoda']}</td>
        <td>{$row['naziv']}</td>
        <td>{$row['cena']}</td>
        <td>{$row['kolicina_na_lageru']}</td>
        <td>{$row['naziv_kategorije']}</td>
        <td>
            <form method='get' class='inline'>
                <input type='hidden' name='edit' value='{$row['id_proizvoda']}'>
                <button type='submit'>Izmeni</button>
            </form>
            
        </td>
    </tr>";
}
?>
</table>

</body>
</html>
