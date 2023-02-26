<?php
require 'cekLogin.php';
if (isset($_GET['idp'])) {
    $idp = $_GET['idp'];

    $getnamapelanggan = mysqli_query($conn, "SELECT * from pesanan p, pelanggan pl where p.idpelanggan=pl.idpelanggan and p.idpesanan = '$idp'");
    $np = mysqli_fetch_array($getnamapelanggan);
    $namapel = $np['namapelanggan'];
} else {
    header('location:index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="description" content="" />
   <meta name="author" content="" />
   <title>Print Pesanan</title>
   <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
   <link href="css/styles.css" rel="stylesheet" />
   <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
   <style>
   @media print {
      #btnprint {
         display: none;
      }
   }
   </style>
</head>

<body>

   <div class="card">
      <div class="text-center">
         <div class="card-header">
            ====== NOTA PEMBELIAN ======
         </div>
         <div class="card-body">
            <h5 class="card-title">Nama Toko</h5>
            <p class="card-text">alamat toko ada dimana </p>
         </div>
      </div>
      <div class="row">
         <div class="col-2">Nama</div>
         <div class="col-6">: <?= $np['namapelanggan']; ?></div>
         <div class="col-4">Tanggal :</div>
      </div>
      <div class="row">
         <div class="col-2">No Telp</div>
         <div class="col-6">: <?= $np['notelp']; ?></div>
         <div class="col-4"><?= $np['tanggal']; ?></div>
      </div>
      <div class="row">
         <div class="col-2">Alamat </div>
         <div class="col-10">: <?= $np['alamat']; ?></div>
      </div>
      <hr>
      <table class="table">
         <thead>
            <tr>
               <th>No</th>
               <th>Nama Produk</th>
               <th>Harga Satuan</th>
               <th>Jumlah</th>
               <th>Sub Total</th>
            </tr>
         </thead>
         <tbody>
            <?php
                $get = mysqli_query($conn, "SELECT * FROM detailpesanan dp, produk p WHERE dp.idproduk = p.idproduk and dp.idpesanan='$idp'");
                $i = 1;
                $allsub = 0;
                while ($p = mysqli_fetch_array($get)) {
                    $idpr = $p['idproduk'];
                    $iddp = $p['iddetailpesanan'];
                    $idp = $p['idpesanan'];
                    $qty = $p['qty'];
                    $namaproduk = $p['namaproduk'];
                    $deskripsi = $p['deskripsi'];
                    $harga = $p['harga'];
                    $subtotal = $qty * $harga;
                    $allsub += $subtotal;

            ?>
            <tr>
               <td><?=$i++;?></td>
               <td><?=$namaproduk;?> - <?=$deskripsi;?></td>
               <td>Rp.<?=number_format($harga);?></td>
               <td><?=number_format($qty);?> pcs</td>
               <td>Rp.<?=number_format($subtotal);?></td>
            </tr>

            <?php }; ?>
         </tbody>

         <tfoot>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th>Rp.<?=number_format($allsub);?></th>
         </tfoot>
      </table>

   </div>
   <div class="card-footer text-muted text-center">
      <button class="btn btn-danger mx-3" onclick="history.back()" id="btnprint">Go Back</button>
      <button class="btn btn-primary mx-3" onclick="window.print()" id="btnprint">Go Print</button>
   </div>
   </div>

























   <script>
   window.print();
   </script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
   </script>
   <script src="js/scripts.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
   <script src="assets/demo/chart-area-demo.js"></script>
   <script src="assets/demo/chart-bar-demo.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
   <script src="js/datatables-simple-demo.js"></script>
</body>

</html>