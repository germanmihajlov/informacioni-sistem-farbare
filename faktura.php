<?php
require_once('konekcija.php');

if (!isset($_GET['id'])) {
    die("Neispravan poziv fakture.");
}

$id_fakture = (int)$_GET['id'];

// FAKTURA + VEZANA PORUDŽBINA
$faktura = mysqli_fetch_assoc(mysqli_query($db,
    "SELECT f.id_fakture,
            f.datum,
            f.nacin_placanja,
            p.id_porudzbine,
            k.naziv_firme,
            z.email
     FROM faktura f
     JOIN porudzbina p ON f.id_porudzbine = p.id_porudzbine
     JOIN kupac k ON p.id_kupca = k.id_kupca
     JOIN zaposleni z ON p.id_zaposlenog = z.id_zaposlenog
     WHERE f.id_fakture = $id_fakture"
));

if (!$faktura) {
    die("Za ovu porudžbinu ne postoji faktura.");
}

$id_porudzbine = $faktura['id_porudzbine'];

// STAVKE
$stavke = mysqli_query($db,
    "SELECT pr.naziv,
            s.kolicina,
            s.cena,
            (s.kolicina * s.cena) AS ukupno
     FROM stavka_porudzbine s
     JOIN proizvod pr ON s.id_proizvoda = pr.id_proizvoda
     WHERE s.id_porudzbine = $id_porudzbine"
);

//  UKUPNO
$suma = mysqli_fetch_assoc(mysqli_query($db,
    "SELECT SUM(kolicina * cena) AS ukupno
     FROM stavka_porudzbine
     WHERE id_porudzbine = $id_porudzbine"
));
?>

<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Faktura</title>
<style>
body { font-family: Arial; background:#f2f2f2; }
.box { background:white; width:70%; margin:30px auto; padding:20px; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th, td { border:1px solid #000; padding:8px; text-align:center; }
h2 { margin-bottom:10px; }
</style>
</head>
<body>

<div class="box">

<h2>FAKTURA</h2>

<p><strong>Broj fakture:</strong> <?= $faktura['id_fakture'] ?></p>
<p><strong>Broj porudžbine:</strong> <?= $id_porudzbine ?></p>
<p><strong>Datum:</strong> <?= $faktura['datum'] ?></p>
<p><strong>Kupac:</strong> <?= $faktura['naziv_firme'] ?></p>
<p><strong>Zaposleni:</strong> <?= $faktura['email'] ?></p>
<p><strong>Način plaćanja:</strong> <?= $faktura['nacin_placanja'] ?></p>

<table>
<tr>
    <th>Proizvod</th>
    <th>Količina</th>
    <th>Cena</th>
    <th>Ukupno</th>
</tr>

<?php while ($s = mysqli_fetch_assoc($stavke)): ?>
<tr>
    <td><?= $s['naziv'] ?></td>
    <td><?= $s['kolicina'] ?></td>
    <td><?= $s['cena'] ?> RSD</td>
    <td></td>
</tr>
<?php endwhile; ?>

<tr>
    <th colspan="3">UKUPNO</th>
    <th><?= $suma['ukupno'] ?> RSD</th>
</tr>
</table>

</div>

<hr>

<div style="text-align:center; margin-top:20px;">
    <button onclick="window.print()" style="
        padding:10px 20px;
        font-size:16px;
        cursor:pointer;
    ">
        Štampaj fakturu
    </button>
</div>
<div style="text-align:center; margin-top:15px;">
    <a href="porudzbina.php">
        <button style="
            padding:10px 20px;
            font-size:16px;
            cursor:pointer;
        ">
            Nazad na porudžbine
        </button>
    </a>
</div>


</body>
</html>
