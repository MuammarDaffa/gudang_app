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

        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('prod_').'.'.$ext;
            $target = __DIR__ . '/../uploads/' . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
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

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9fafb;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        table img {
            border-radius: 6px;
        }
        a.btn-link {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn btn-secondary mb-3">&larr; Kembali ke Dashboard</a>

    <div class="card p-4">
        <h3 class="text-center text-primary mb-4">📦 Kelola Produk</h3>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'create_form'): ?>
            <h5 class="mb-3">Tambah Produk Baru</h5>
            <form method="post" action="products.php?action=create" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label>Harga</label>
                    <input name="price" type="number" step="0.01" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Stok</label>
                    <input name="stock" type="number" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Gambar Produk</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="products.php" class="btn btn-outline-secondary">Batal</a>
            </form>

        <?php elseif ($action === 'edit' && isset($row)): ?>
            <h5 class="mb-3">Edit Produk</h5>
            <form method="post" action="products.php?action=edit&id=<?= $row['id'] ?>" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Harga</label>
                    <input name="price" type="number" step="0.01" value="<?= $row['price'] ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Stok</label>
                    <input name="stock" type="number" value="<?= $row['stock'] ?>" class="form-control">
                </div>
                <?php if ($row['image']): ?>
                    <p>Gambar Saat Ini:</p>
                    <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" class="mb-3" style="max-width:120px">
                <?php endif; ?>
                <div class="mb-3">
                    <label>Ganti Gambar</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-warning">✏️ Update</button>
                <a href="products.php" class="btn btn-outline-secondary">Batal</a>
            </form>

        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Daftar Produk</h5>
                <a href="products.php?action=create_form" class="btn btn-success">➕ Tambah Produk</a>
            </div>
            <table class="table table-striped table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td>Rp <?= number_format($p['price'],2,',','.') ?></td>
                            <td><?= $p['stock'] ?></td>
                            <td>
                                <?php if($p['image']): ?>
                                    <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" style="max-width:80px">
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="products.php?action=delete&id=<?= $p['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
