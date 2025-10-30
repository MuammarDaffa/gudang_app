<?php
require_once '../functions/auth.php';
requireLogin();
require_once '../config/database.php';

$action = $_GET['action'] ?? 'list';

// ====== CREATE ======
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    // handling image upload (optional)
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('prod_').'.'.$ext;
        $target = __DIR__ . '/../uploads/' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdis", $name, $desc, $price, $stock, $imageName);
$stmt->execute();
header('Location: products.php?msg=created');
exit;

}

// ====== UPDATE ======
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];

        // ambil file lama jika perlu hapus
        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('prod_').'.'.$ext;
            $target = __DIR__ . '/../uploads/' . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            // optional: hapus file lama dari server
            $old = $conn->query("SELECT image FROM products WHERE id=$id")->fetch_assoc();
            if ($old && $old['image']) {
                @unlink(__DIR__ . '/../uploads/' . $old['image']);
            }
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
            $stmt->bind_param("ssdisi", $name, $desc, $price, $stock, $imageName, $id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
            $stmt->bind_param("ssdii", $name, $desc, $price, $stock, $id);
        }
        $stmt->execute();
        header('Location: products.php?msg=updated');
        exit;
    } else {
        $row = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
    }
}

// ====== DELETE ======
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // hapus file gambar jika ada
    $old = $conn->query("SELECT image FROM products WHERE id=$id")->fetch_assoc();
    if ($old && $old['image']) {
        @unlink(__DIR__ . '/../uploads/' . $old['image']);
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: products.php?msg=deleted');
    exit;
}

// ====== LIST ======
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

?>
<!doctype html>
<html>
<head><title>Kelola Produk</title></head>
<body>
  <a href="dashboard.php"><< Kembali</a>
  <h2>Daftar Produk</h2>
  <?php if(isset($_GET['msg'])): ?>
    <p><b><?= htmlspecialchars($_GET['msg']) ?></b></p>
  <?php endif; ?>

  <p><a href="products.php?action=create_form">Tambah Produk</a></p>

  <?php if ($action === 'create_form'): ?>
    <h3>Tambah Produk</h3>
    <form method="post" action="products.php?action=create" enctype="multipart/form-data">
      <input name="name" required placeholder="Nama produk"><br>
      <textarea name="description" placeholder="Deskripsi"></textarea><br>
      <input name="price" type="number" step="0.01" placeholder="Harga"><br>
      <input name="stock" type="number" placeholder="Stok"><br>
      <input type="file" name="image" accept="image/*"><br>
      <button type="submit">Simpan</button>
    </form>
  <?php elseif ($action === 'edit' && isset($row)): ?>
    <h3>Edit Produk</h3>
    <form method="post" action="products.php?action=edit&id=<?= $row['id'] ?>" enctype="multipart/form-data">
      <input name="name" required value="<?= htmlspecialchars($row['name']) ?>"><br>
      <textarea name="description"><?= htmlspecialchars($row['description']) ?></textarea><br>
      <input name="price" type="number" step="0.01" value="<?= $row['price'] ?>"><br>
      <input name="stock" type="number" value="<?= $row['stock'] ?>"><br>
      <?php if ($row['image']): ?>
        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="" style="max-width:120px"><br>
      <?php endif; ?>
      <input type="file" name="image" accept="image/*"><br>
      <button type="submit">Update</button>
    </form>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <tr><th>ID</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Gambar</th><th>Aksi</th></tr>
      <?php while($p = $products->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= number_format($p['price'],2) ?></td>
          <td><?= $p['stock'] ?></td>
          <td>
            <?php if($p['image']): ?>
              <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" style="max-width:80px">
            <?php endif; ?>
          </td>
          <td>
            <a href="products.php?action=edit&id=<?= $p['id'] ?>">Edit</a> |
            <a href="products.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</body>
</html>
