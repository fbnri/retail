<?php

include "koneksi.php";
include 'config.php';

if (!isset($_SESSION['name'])) {
  header('Location: login.php');
  exit;
}

$sql_roles = "SELECT id, role_name FROM roles";
$result_roles = mysqli_query($link, $sql_roles);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $role_id = $_POST['role_id'];

  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  $sql = "INSERT INTO users (name, username, password, role_id) VALUES (?, ?, ?, ?)";
  $stmt = $link->prepare($sql);
  $stmt->bind_param("sssi", $name, $username, $hashed_password, $role_id);

  session_start();
  if ($stmt->execute()) {
    $_SESSION['message'] = "User berhasil ditambahkan!";
    header("Location: setting_user.php");
    exit;
  } else {
    $_SESSION['message'] = "Terjadi kesalahan: " . $stmt->error;
    header("Location: setting_user.php");
    exit;
  }
}

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
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="css/daterangepicker.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  </head>
  <body class="vertical  light  ">
    <div class="wrapper">
      <?php include "navbar.php" ?>
      <?php include "sidebar.php" ?>
      <main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <h2 class="page-title">Tambah Pengguna</h2>
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <form action="" method="post">
                        <div class="form-group mb-3">
                          <label for="product_name">Name</label>
                          <input type="text" name="name" id="name" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                          <label for="product_name">Username</label>
                          <input type="text" name="username" id="username" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                          <label for="product_name">Password</label>
                          <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" autocomplete="off" required>
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                              <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                          </div>
                        </div>
                        <div class="form-group mb-3">
                          <label for="example-select">Role</label>
                          <select class="form-control" name="role_id" id="example-select">
                            <?php
                            while ($data = $result_roles->fetch_assoc()) {
                              echo "<option value='" . $data['id'] . "'>" . $data['role_name'] . "</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <div>
                          <a href="setting_user.php"><button type="button" class="btn mb-2 btn-secondary">Batal</button></a>
                          <button type="submit" name="add" class="btn mb-2 btn-primary">Ok</button>
                        </div>
                      </form>
                    </div> <!-- /.col -->
                  </div>
                </div>  
              </div> <!-- / .card -->
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
      function formatPrice(input) {
        let value = input.value.replace(/[^\d]/g, '');  // Hapus semua karakter selain angka
        
        // Menambahkan titik sebagai pemisah ribuan
        let formattedValue = '';
        let counter = 0;
        
        // Loop untuk menambahkan titik setiap 3 digit dari belakang
        for (let i = value.length - 1; i >= 0; i--) {
          counter++;
          formattedValue = value[i] + formattedValue;
          if (counter % 3 === 0 && i !== 0) {
            formattedValue = '.' + formattedValue;
          }
        }
        
        // Memperbarui input dengan nilai yang sudah diformat
        input.value = formattedValue;
      }
    </script>
    <script>
      const togglePassword = document.querySelector("#togglePassword");
      const passwordField = document.querySelector("#password");
      const eyeIcon = document.querySelector("#eyeIcon");

      togglePassword.addEventListener("click", () => {
        // Toggle between password and text type
        const type = passwordField.type === "password" ? "text" : "password";
        passwordField.type = type;

        // Toggle icon class
        eyeIcon.classList.toggle("bi-eye");
        eyeIcon.classList.toggle("bi-eye-slash");
      });
    </script>
  </body>
</html>