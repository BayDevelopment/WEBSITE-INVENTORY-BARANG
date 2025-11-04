<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            background-color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: "Poppins", sans-serif;
        }

        .login-container {
            min-height: 100vh;
        }

        /* Gambar kiri full tanpa celah */
        .login-image {
            background: url("<?= base_url('assets/img/login-logo.jpg') ?>") no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        /* Kartu login modern */
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .login-card .card-header {
            background-color: transparent;
            border-bottom: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0056b3, #007bff);
        }

        /* Responsif: sembunyikan gambar di tablet/mobile */
        @media (max-width: 991.98px) {
            .login-image {
                display: none;
            }

            .login-right {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid login-container">
        <div class="row">
            <!-- Gambar kiri full -->
            <div class="col-lg-8 login-image"></div>

            <!-- Form login kanan -->
            <div class="col-lg-4 bg-white login-right d-flex align-items-center justify-content-center">
                <div class="card login-card w-100 mx-4 mx-sm-5 my-5 shadow-blue">
                    <div class="card-header text-center bg-transparent border-0">
                        <h3 class="fw-bold my-3 text-primary">Selamat Datang 👋</h3>
                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                            Silakan masuk untuk mengelola data dan memantau sistem Inventory Barang Anda dengan mudah dan aman.
                        </p>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="inputEmail" placeholder="name@example.com">
                                <label for="inputEmail">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="inputPassword" placeholder="Password">
                                <label for="inputPassword">Password</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-login text-white px-4 py-2 rounded-pill py-lg-2">Masuk</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center bg-transparent border-0 mt-3">
                        <small>Developed by <a href="#" class="text-decoration-none">Bayudev</a></small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>