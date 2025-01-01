<?php

include "koneksi.php";
include 'config.php';

if (!isset($_SESSION['name'])) {
  header('Location: login.php');
  exit;
}

// Query untuk mengambil data transaksi
$sql = " SELECT transaction.id AS transaction_id,
                transaction.date AS transaction_date,
                transaction.time AS transaction_time,
                product_management.product_name AS product_name,
                product_management.price AS price,
                transaction_items.subtotal AS item_subtotal,
                transaction.total_price AS total_price,
                transaction_items.quantity AS quantity
        FROM transaction
        JOIN transaction_items ON transaction.id = transaction_items.transaction_id
        JOIN product_management ON transaction_items.product_id = product_management.id";

$result = mysqli_query($link, $sql);

?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>Tiny Dashboard - A Bootstrap Dashboard Template</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="css/daterangepicker.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
  </head>
  <body class="vertical  light">
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Penghapusan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin menghapus produk ini?
          </div>
          <div class="modal-footer">
            <form id="deleteForm" method="POST" action="delete_transaction.php">
              <input type="hidden" name="id" id="delete-id">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger" name="delete">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="wrapper">
      <?php include "navbar.php" ?>
      <?php include "sidebar.php" ?>
      <main role="main" class="main-content">
        <div class="container-fluid">
          <?php if (isset($_SESSION['message'])): ?>
            <div class="col-12 mb-4">
              <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
            <?php unset($_SESSION['message']); ?> <!-- Hapus pesan setelah ditampilkan -->
          <?php endif; ?>
          <div class="row justify-content-center">
            <div class="col-12">
              <h2 class="page-title">Riwayat Transaksi</h2>
              <a href="add_transaction.php">
                <button type="button" class="btn mb-2 btn-primary">+ Lakukan Transaksi</button>
              </a>
              <div class="row">
                <div class="col-md-12 my-4">
                  <div class="card shadow">
                    <div class="card-body">
                      <div class="toolbar">
                        <form class="form">
                          <div class="col-md-3 col-6">
                            <label for="search" class="sr-only">Search</label>
                            <input type="text" class="form-control" id="search1" value="" placeholder="Search" autocomplete="off">
                            <div id="search-results" class="search-results"></div>
                          </div>
                        </form>
                      </div>
                      <!-- table -->
                      <table class="table table-bordered table-hover mt-3">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Nama Produk</th>
                                <th>Jumlah Produk</th>
                                <th>Harga Satuan</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php 
                            $current_date = '';
                            $current_time = '';
                            $current_total_price = '';
                            $rowspan_date = 0;
                            $rowspan_time = 0;
                            $rowspan_total_price = 0;

                            // Simpan data dari query
                            $data_list = [];
                            while ($data = mysqli_fetch_assoc($result)) {
                              $data_list[] = $data;
                            }

                            foreach ($data_list as $key => $data) : 
                              // Hitung rowspan untuk tanggal
                              if ($data['transaction_date'] !== $current_date) {
                                $current_date = $data['transaction_date'];
                                $rowspan_date = count(array_filter($data_list, function($d) use ($current_date) {
                                  return $d['transaction_date'] === $current_date;
                                }));
                              } else {
                                $rowspan_date = 0;
                              }

                              // Hitung rowspan untuk jam
                              if ($data['transaction_time'] !== $current_time || $rowspan_date > 0) {
                                $current_time = $data['transaction_time'];
                                $rowspan_time = count(array_filter($data_list, function($d) use ($current_time, $current_date) {
                                  return $d['transaction_time'] === $current_time && $d['transaction_date'] === $current_date;
                                }));
                              } else {
                                $rowspan_time = 0;
                              }

                              // Hitung rowspan untuk total harga
                              if ($data['total_price'] !== $current_total_price || $rowspan_time > 0) {
                                $current_total_price = $data['total_price'];
                                $rowspan_total_price = count(array_filter($data_list, function($d) use ($current_total_price, $current_time, $current_date) {
                                  return $d['total_price'] === $current_total_price 
                                    && $d['transaction_time'] === $current_time 
                                    && $d['transaction_date'] === $current_date;
                                  }));
                                } else {
                                  $rowspan_total_price = 0;
                                }
                            ?>
                            <tr>
                              <?php if ($rowspan_date > 0) : ?>
                              <td rowspan="<?= $rowspan_date; ?>"><?= $data['transaction_date']; ?></td>
                              <?php endif; ?>
                              <?php if ($rowspan_time > 0) : ?>
                              <td rowspan="<?= $rowspan_time; ?>"><?= $data['transaction_time']; ?></td>
                              <?php endif; ?>
                              <td><?= $data['product_name']; ?></td>
                              <td><?= $data['quantity']; ?></td>
                              <td><?= number_format($data['price'], 0, ',', '.'); ?></td>
                              <!-- Merge hanya untuk data yang sama pada kolom Total Harga -->
                              <?php if ($rowspan_total_price > 0) : ?>
                              <td rowspan="<?= $rowspan_total_price; ?>">Rp <?= number_format($data['total_price'], 0, ',', '.'); ?></td>
                              <?php endif; ?>
                              <td>
                                <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item delete-btn" href="#" data-id="<?= $data['transaction_id']; ?>">Hapus</a>
                                </div>
                              </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                              <td colspan="7">Tidak ada data transaksi</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div> <!-- customized table -->
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->
        <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="list-group list-group-flush my-n3">
                  <div class="list-group-item bg-transparent">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="fe fe-box fe-24"></span>
                      </div>
                      <div class="col">
                        <small><strong>Package has uploaded successfull</strong></small>
                        <div class="my-0 text-muted small">Package is zipped and uploaded</div>
                        <small class="badge badge-pill badge-light text-muted">1m ago</small>
                      </div>
                    </div>
                  </div>
                  <div class="list-group-item bg-transparent">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="fe fe-download fe-24"></span>
                      </div>
                      <div class="col">
                        <small><strong>Widgets are updated successfull</strong></small>
                        <div class="my-0 text-muted small">Just create new layout Index, form, table</div>
                        <small class="badge badge-pill badge-light text-muted">2m ago</small>
                      </div>
                    </div>
                  </div>
                  <div class="list-group-item bg-transparent">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="fe fe-inbox fe-24"></span>
                      </div>
                      <div class="col">
                        <small><strong>Notifications have been sent</strong></small>
                        <div class="my-0 text-muted small">Fusce dapibus, tellus ac cursus commodo</div>
                        <small class="badge badge-pill badge-light text-muted">30m ago</small>
                      </div>
                    </div> <!-- / .row -->
                  </div>
                  <div class="list-group-item bg-transparent">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="fe fe-link fe-24"></span>
                      </div>
                      <div class="col">
                        <small><strong>Link was attached to menu</strong></small>
                        <div class="my-0 text-muted small">New layout has been attached to the menu</div>
                        <small class="badge badge-pill badge-light text-muted">1h ago</small>
                      </div>
                    </div>
                  </div> <!-- / .row -->
                </div> <!-- / .list-group -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Clear All</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade modal-shortcut modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">Shortcuts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body px-5">
                <div class="row align-items-center">
                  <div class="col-6 text-center">
                    <div class="squircle bg-success justify-content-center">
                      <i class="fe fe-cpu fe-32 align-self-center text-white"></i>
                    </div>
                    <p>Control area</p>
                  </div>
                  <div class="col-6 text-center">
                    <div class="squircle bg-primary justify-content-center">
                      <i class="fe fe-activity fe-32 align-self-center text-white"></i>
                    </div>
                    <p>Activity</p>
                  </div>
                </div>
                <div class="row align-items-center">
                  <div class="col-6 text-center">
                    <div class="squircle bg-primary justify-content-center">
                      <i class="fe fe-droplet fe-32 align-self-center text-white"></i>
                    </div>
                    <p>Droplet</p>
                  </div>
                  <div class="col-6 text-center">
                    <div class="squircle bg-primary justify-content-center">
                      <i class="fe fe-upload-cloud fe-32 align-self-center text-white"></i>
                    </div>
                    <p>Upload</p>
                  </div>
                </div>
                <div class="row align-items-center">
                  <div class="col-6 text-center">
                    <div class="squircle bg-primary justify-content-center">
                      <i class="fe fe-users fe-32 align-self-center text-white"></i>
                    </div>
                    <p>Users</p>
                  </div>
                  <div class="col-6 text-center">
                    <div class="squircle bg-primary justify-content-center">
                      <i class="fe fe-settings fe-32 align-self-center text-white"></i>
                    </div>
                    <p>Settings</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main> <!-- main -->
    </div> <!-- .wrapper -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src='js/daterangepicker.js'></script>
    <script src='js/jquery.stickOnScroll.js'></script>
    <script src="js/tinycolor-min.js"></script>
    <script src="js/config.js"></script>
    <script src='js/jquery.dataTables.min.js'></script>
    <script src='js/dataTables.bootstrap4.min.js'></script>
    <script>
      $('#dataTable-1').DataTable(
      {
        autoWidth: true,
        "lengthMenu": [
          [16, 32, 64, -1],
          [16, 32, 64, "All"]
        ]
      });
    </script>
    <script src="js/apps.js"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag()
      {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-56159088-1');
    </script>
    <script>
      // Tangkap event klik pada tombol hapus
      $(document).on('click', '.delete-btn', function() {
        var productId = $(this).data('id'); // Ambil ID produk dari tombol hapus
        $('#delete-id').val(productId); // Isi nilai ID ke input hidden di modal
        $('#confirmationModal').modal('show'); // Tampilkan modal konfirmasi
      });


      $(document).ready(function() {
        // Cek jika alert berhasil dimuat
        if($('#success-alert').length) {
          // Set waktu untuk menghilangkan alert setelah 2 detik
          setTimeout(function() {
            $('#success-alert').alert('close'); // Menutup alert
          }, 2000); // 2000 ms = 2 detik
        }
      });
    </script>
    <script>
      document.getElementById('search1').addEventListener('input', function () {
        let query = this.value;

        if (query.trim() !== "") {
          fetch(`/search?query=${query}`, { method: 'GET' })
            .then((response) => response.json())
            .then((data) => {
              const resultsDiv = document.getElementById('search-results');
              resultsDiv.innerHTML = '';

              data.forEach(item => {
                const resultItem = document.createElement('div');
                resultItem.textContent = item.name; // Contoh data
                resultsDiv.appendChild(resultItem);
              });
            })
            .catch((error) => console.error('Error:', error));
        }
      });
    </script>
    <script>
      document.getElementById('search1').addEventListener('input', function () {
        let query = this.value;

        // Pastikan query tidak kosong
        if (query.trim() !== "") {
          fetch(`/search?query=${query}`, {
            method: 'GET',
          })
            .then((response) => response.json())
            .then((data) => {
              console.log(data); // Tampilkan hasil pencarian di console
              // Tambahkan logika untuk menampilkan hasil di UI
            })
            .catch((error) => {
              console.error('Error:', error);
            });
        }
      });
    </script>
  </body>
</html>
