<?php
session_start();
// koneksi db
$conn = mysqli_connect('localhost', 'root', '', 'kasir');

// login
if (isset($_POST['login'])) {
    // initiate variable
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' and password='$password'");
    $hitung = mysqli_num_rows($check);

    if ($hitung > 0) {
        // jika data ditemukan
        // berhasil login
        $_SESSION['login'] = 'True';
        header('location:index.php');
    } else {
        // data tidak ada
        echo '
        <script>alert("Usaername atau Password Salah");
        window.location.href="login.php"
        </script>';
    }
}

// tambah barang
if (isset($_POST['tambahbarang'])) {
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $hargabeli = $_POST['hargabeli'];
    $hargajual = $_POST['hargajual'];

    $insert = mysqli_query($conn, "INSERT INTO produk (namaproduk,deskripsi,beli,harga,stock) values ('$namaproduk','$deskripsi','$hargabeli','$hargajual','$stock')");

    if ($insert) {
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Barang Gagal ditambahkan");
        window.location.href="stock.php"
        </script>';

    }
}

// tambah pelanggan
if (isset($_POST['tambahpelanggan'])) {
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($conn, "INSERT INTO pelanggan (namapelanggan,notelp,alamat) values ('$namapelanggan','$notelp','$alamat')");

    if ($insert) {
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Data Pelanggan Gagal ditambahkan");
        window.location.href="pelanggan.php"
        </script>';

    }
}

// tambah pesanan
if (isset($_POST['tambahpesanan'])) {
    $idpelanggan = $_POST['idpelanggan'];

    $insert = mysqli_query($conn, "INSERT INTO pesanan (idpelanggan) values ('$idpelanggan')");

    if ($insert) {
        header('location:index.php');
    } else {
        echo '
        <script>alert("Data Pesanan Gagal ditambahkan");
        window.location.href="index.php"
        </script>';

    }
}

// tambah produk
if (isset($_POST['tambahproduk'])) {
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp'];
    $qty = $_POST['qty'];

    // hitung stock barang
    $hitung1 = mysqli_query($conn, "SELECT * FROM produk where idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stockawal = $hitung2['stock']; //stock sekarang

    if ($stockawal >= $qty) {
        // kurangi stock dulu
        $selisih = $stockawal - $qty;

        // stok cukup
        $insert = mysqli_query($conn, "INSERT INTO detailpesanan (idpesanan, idproduk, qty) values ('$idp','$idproduk','$qty')");
        $update = mysqli_query($conn, "UPDATE produk set stock='$selisih' where idproduk='$idproduk'");

        if ($insert && $update) {
            header('location:view.php?idp=' . $idp);
        } else {
            echo '
        <script>alert("Data Pesanan Gagal ditambahkan");
        window.location.href="view.php?idp=' . $idp . '"
        </script>';

        }
    } else {
        // stock kurang
        echo '
        <script>alert("Stock Barang tidak mencukupi");
        window.location.href="view.php?idp=' . $idp . '"
        </script>';

    }
}

// menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];

    // cek stock awal
    $find3 = mysqli_query($conn, "SELECT * FROM produk WHERE idproduk='$idproduk'");
    $find4 = mysqli_fetch_array($find3);
    $stockawal = $find4['stock'];

    // hitung
    $newstock = $stockawal + $qty;

    $insert = mysqli_query($conn, "INSERT INTO masuk (idproduk, qty) values ('$idproduk','$qty')");
    $update = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idproduk'");

    if ($insert && $update) {
        header('location:masuk.php');
    } else {
        echo '
        <script>alert("Barang Baru Gagal dtambahkan");
        window.location.href="masuk.php"
        </script>';

    }
}

// edit pesanan
if (isset($_POST['editpesanan'])) {
    $iddp = $_POST['iddp'];
    $idpr = $_POST['idpr'];
    $idp = $_POST['idp'];
    $qty = $_POST['qty'];

    // cek qty awal
    $find = mysqli_query($conn, "SELECT * FROM detailpesanan where iddetailpesanan='$iddp'");
    $find2 = mysqli_fetch_array($find);
    $qtyawal = $find2['qty'];

    // cek stock awal
    $find3 = mysqli_query($conn, "SELECT * FROM produk WHERE idproduk='$idpr'");
    $find4 = mysqli_fetch_array($find3);
    $stockawal = $find4['stock'];

    if ($qty >= $qtyawal) {
        // hitung selisih
        $selisih = $qty - $qtyawal;
        $newstock = $stockawal - $selisih;

        $update = mysqli_query($conn, "UPDATE detailpesanan set qty='$qty' where iddetailpesanan='$iddp'");
        $update2 = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idpr'");

        if ($update && $update2) {
            header('location:view.php?idp=' . $idp);

        } else {
            echo '
        <script>alert("Pesanan Barang Gagal dihapus");
        window.location.href="view.php?idp=' . $idp . '"
        </script>';

        }
    } else {
        $selisih = $qtyawal - $qty;
        $newstock = $stockawal + $selisih;

        $update = mysqli_query($conn, "UPDATE detailpesanan set qty='$qty' where iddetailpesanan='$iddp'");
        $update2 = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idpr'");

        if ($update && $update2) {
            header('location:view.php?idp=' . $idp);
        } else {
            echo '
        <script>alert("Pesanan Barang Gagal dihapus");
        window.location.href="view.php?idp=' . $idp . '"
        </script>';

        }

    }

}

// hapus pesanan
if (isset($_POST['hapuspesanan'])) {
    $iddp = $_POST['iddp'];
    $idpr = $_POST['idpr'];
    $idp = $_POST['idp'];

    // cek qty
    $cek1 = mysqli_query($conn, "SELECT * FROM detailpesanan where iddetailpesanan='$iddp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtyawal = $cek2['qty'];

    // cek stock
    $cek3 = mysqli_query($conn, "SELECT * FROM produk where idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stockawal = $cek4['stock'];

    $hitung = $stockawal + $qtyawal;

    $update = mysqli_query($conn, "UPDATE produk set stock='$hitung' where idproduk='$idpr'");
    $hapus = mysqli_query($conn, "delete from detailpesanan where idproduk='$idpr' and iddetailpesanan='$iddp'");

    if ($update && $hapus) {
        header('location:view.php?idp=' . $idp . '');
    } else {
        echo '
        <script>alert("Pesanan Barang Gagal dihapus");
        window.location.href="view.php?idp=' . $idp . '"
        </script>';
    }
}

// edit barang
if (isset($_POST['editbarang'])) {
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp'];

    $update = mysqli_query($conn, "UPDATE produk set namaproduk='$namaproduk', deskripsi='$deskripsi', harga='$harga' WHERE idproduk='$idp'");

    if ($update) {
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Barang Gagal diupdate");
        window.location.href="stock.php"
        </script>';

    }
}

// hapus barang stock
if (isset($_POST['hapusbarang'])) {
    $idp = $_POST['idp'];

    $delete = mysqli_query($conn, "delete from produk where idproduk='$idp'");

    if ($delete) {
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Barang Gagal dihapus");
        window.location.href="stock.php"
        </script>';

    }
}

// update pelanggan
if (isset($_POST['editpelanggan'])) {
    $idpl = $_POST['idpl'];
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $update = mysqli_query($conn, "UPDATE pelanggan set namapelanggan='$namapelanggan', notelp='$notelp', alamat='$alamat' where idpelanggan='$idpl'");

    if ($update) {
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Pelanggan Gagal diupdate");
        window.location.href="pelanggan.php"
        </script>';

    }
}

// hapus pelanggan
if (isset($_POST['hapuspelanggan'])) {
    $idpl = $_POST['idpl'];

    $delete = mysqli_query($conn, "delete from pelanggan where idpelanggan='$idpl'");

    if ($delete) {
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Pelanggan Gagal dihapus");
        window.location.href="pelanggan.php"
        </script>';
    }

}

// edit barang masuk
if (isset($_POST['editbarangmasuk'])) {
    $idm = $_POST['idm'];
    $idp = $_POST['idp'];
    $qty = $_POST['qty'];

    // cek qty awal
    $find = mysqli_query($conn, "SELECT * FROM masuk where idmasuk='$idm'");
    $find2 = mysqli_fetch_array($find);
    $qtyawal = $find2['qty'];

    // cek stock awal
    $find3 = mysqli_query($conn, "SELECT * FROM produk WHERE idproduk='$idp'");
    $find4 = mysqli_fetch_array($find3);
    $stockawal = $find4['stock'];

    if ($qty >= $qtyawal) {
        // hitung selisih
        $selisih = $qty - $qtyawal;
        $newstock = $stockawal + $selisih;

        $update = mysqli_query($conn, "UPDATE masuk set qty='$qty' where idmasuk='$idm'");
        $update2 = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idp'");

        if ($update && $update2) {
            header('location:masuk.php');
        } else {
            echo '
        <script>alert("Barang Masuk Gagal diupdate");
        window.location.href="masuk.php"
        </script>';
        }
    } else {
        $selisih = $qtyawal - $qty;
        $newstock = $stockawal - $selisih;

        $update = mysqli_query($conn, "UPDATE masuk set qty='$qty' where idmasuk='$idm'");
        $update2 = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idp'");

        if ($update && $update2) {
            header('location:masuk.php');
        } else {
            echo '
        <script>alert("Barang Masuk Gagal diupdate");
        window.location.href="masuk.php"
        </script>';
        }

    }

}

// hapus barang masuk
if (isset($_POST['hapusbarangmasuk'])) {
    $idm = $_POST['idm'];
    $idp = $_POST['idp'];

    // cek qty awal
    $find = mysqli_query($conn, "SELECT * FROM masuk where idmasuk='$idm'");
    $find2 = mysqli_fetch_array($find);
    $qtyawal = $find2['qty'];

// cek stock awal
    $find3 = mysqli_query($conn, "SELECT * FROM produk WHERE idproduk='$idp'");
    $find4 = mysqli_fetch_array($find3);
    $stockawal = $find4['stock'];

// hitung selisih
    $newstock = $stockawal - $qtyawal;

    $update = mysqli_query($conn, "DELETE from masuk where idmasuk='$idm'");
    $update2 = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idp'");

    if ($update && $update2) {
        header('location:masuk.php');
    } else {
        echo '
        <script>alert("Barang Masuk Gagal dihapus");
        window.location.href="masuk.php"
        </script>';
    }

}

// hapus pemesan
if (isset($_POST['hapuspemesan'])) {
    $idps = $_POST['idps'];

    $cekdata = mysqli_query($conn, "SELECT * FROM detailpesanan where idpesanan='$idps'");

    while ($dt = mysqli_fetch_array($cekdata)) {
        // balikin stock
        $qty = $dt['qty'];
        $idproduk = $dt['idproduk'];
        $iddp = $dt['iddetailpesanan'];
        // cek stock awal
        $find3 = mysqli_query($conn, "SELECT * FROM produk WHERE idproduk='$idproduk'");
        $find4 = mysqli_fetch_array($find3);
        $stockawal = $find4['stock'];

        $newstock = $stockawal + $qty;

        $update = mysqli_query($conn, "UPDATE produk set stock='$newstock' where idproduk='$idproduk'");

        // hapus data
        $delete = mysqli_query($conn, "DELETE from detailpesanan where iddetailpesanan='$iddp'");

    }
    $deleteall = mysqli_query($conn, "DELETE from pesanan where idpesanan='$idps'");

    if ($update && $delete && $deleteall) {
        header('location:index.php');
    } else {
        echo '
        <script>alert("Pesanan Gagal dihapus");
        window.location.href="index.php"
        </script>';
    }

}

// fungsi cetak
if(isset($_POST['cetakpesanan'])){
    $idp = $_POST['idp'];
    header('location:cetak.php?idp='.$idp);
}