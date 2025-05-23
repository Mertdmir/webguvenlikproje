<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Basit ürün listesi (id => [isim, fiyat])
$products = [
    1 => ['Retro Kamera', 1200],
    2 => ['Mini Yazıcı', 900],
    3 => ['Bluetooth Kulaklık', 450],
];

// Sepete ürün ekleme (demo, sadece session'da tutuyoruz)
if (isset($_POST['add_to_cart'])) {
    $pid = (int)$_POST['product_id'];
    if (isset($products[$pid])) {
        $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
    }
}

// Sepet toplam fiyat hesaplama
$cart_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $cart_total += $products[$pid][1] * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Alışveriş Sayfası - Hoşgeldin <?=htmlspecialchars($_SESSION['user'])?></title>
<style>
  body {
    background: #f4faff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0; padding: 0;
    color: #222;
  }
  header {
    background: #007acc;
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  header h1 {
    margin: 0; font-weight: 600;
  }
  header a.logout {
    color: white;
    text-decoration: none;
    font-weight: 600;
    background: #005a9e;
    padding: 8px 15px;
    border-radius: 4px;
    transition: background-color 0.3s;
  }
  header a.logout:hover {
    background: #003f6b;
  }
  main {
    max-width: 900px;
    margin: 30px auto;
    background: white;
    padding: 20px 30px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgb(0 0 0 / 0.1);
  }
  h2 {
    border-bottom: 2px solid #007acc;
    padding-bottom: 8px;
    margin-bottom: 20px;
  }
  .products {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
  }
  .product {
    background: #e6f2ff;
    border-radius: 8px;
    padding: 15px;
    width: 200px;
    box-sizing: border-box;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .product h3 {
    margin: 0 0 10px 0;
    color: #005a9e;
  }
  .product p.price {
    font-weight: 700;
    color: #007acc;
    margin-bottom: 15px;
  }
  .product form button {
    background: #007acc;
    border: none;
    color: white;
    padding: 8px 0;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.25s;
  }
  .product form button:hover {
    background: #005a9e;
  }
  .cart {
    margin-top: 30px;
    border-top: 2px solid #007acc;
    padding-top: 15px;
  }
  .cart h3 {
    margin-bottom: 15px;
  }
  .cart ul {
    list-style: none;
    padding: 0;
  }
  .cart li {
    margin-bottom: 6px;
  }
  .xxe-section {
    margin-top: 40px;
    border-top: 2px solid #007acc;
    padding-top: 15px;
  }
  input[type=file] {
    margin-top: 10px;
  }
  input[type=submit] {
    margin-top: 10px;
    background: #007acc;
    border: none;
    padding: 8px 20px;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
  }
  input[type=submit]:hover {
    background: #005a9e;
  }
  pre {
    background: #f0f4f8;
    padding: 10px;
    margin-top: 10px;
    border-radius: 4px;
    overflow-x: auto;
  }
</style>
</head>
<body>

<header>
  <h1>Hoşgeldin, <?=htmlspecialchars($_SESSION['user'])?></h1>
  <a href="logout.php" class="logout">Çıkış Yap</a>
</header>

<main>
  <section class="products">
    <h2>Ürünler</h2>
    <?php foreach ($products as $id => $p): ?>
      <div class="product">
        <h3><?=htmlspecialchars($p[0])?></h3>
        <p class="price"><?=number_format($p[1], 2)?> TL</p>
        <form method="POST">
          <input type="hidden" name="product_id" value="<?=$id?>">
          <button type="submit" name="add_to_cart">Sepete Ekle</button>
        </form>
      </div>
    <?php endforeach; ?>
  </section>

  <section class="cart">
    <h3>Sepetiniz</h3>
    <?php if (!empty($_SESSION['cart'])): ?>
      <ul>
        <?php foreach ($_SESSION['cart'] as $pid => $qty): ?>
          <li><?=htmlspecialchars($products[$pid][0])?> × <?=$qty?> = <?=number_format($products[$pid][1]*$qty,2)?> TL</li>
        <?php endforeach; ?>
      </ul>
      <strong>Toplam: <?=number_format($cart_total,2)?> TL</strong>
    <?php else: ?>
      <p>Sepetiniz boş.</p>
    <?php endif; ?>
  </section>

  <section class="xxe-section">
    <h2>XML Dosyası Yükle (XXE Testi)</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="xmlfile" required>
      <input type="submit" name="upload" value="Gönder">
    </form>
    <?php
    if (isset($_POST['upload'])) {
        libxml_disable_entity_loader(false);
        $xml = file_get_contents($_FILES['xmlfile']['tmp_name']);
        $doc = new DOMDocument();
        $doc->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD); // XXE Açığı
        echo "<pre>" . htmlspecialchars($doc->textContent) . "</pre>";
    }
    ?>
  </section>
</main>

</body>
</html>
