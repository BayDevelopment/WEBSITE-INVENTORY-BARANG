<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            background: linear-gradient(135deg, #0056b3, #007bff);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .login-image {
            flex: 1;
            background: url("<?= base_url('assets/img/login-logo.jpg') ?>") center/cover no-repeat;
            min-height: 100%;
            display: block;
        }

        .login-form {
            flex: 1;
            padding: 3rem;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-form h3 {
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }

        .login-form p {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-floating input {
            border-radius: 12px;
            border: 1px solid #d1d1d1;
            padding: 1rem;
            font-size: 0.95rem;
        }

        .form-floating input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .invalid-feedback {
            font-size: 0.85rem;
        }

        .btn-login {
            background: linear-gradient(135deg, #0d6efd, #007bff);
            border: none;
            border-radius: 50px;
            padding: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #007bff, #0d6efd);
            transform: translateY(-2px);
        }

        .footer-text {
            /* position: absolute; */
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
        }

        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 90%;
            }

            .login-image {
                height: 180px;
            }

            .login-form {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>

    <!-- SweetAlert -->
    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            Swal.fire({
                toast: true,
                icon: 'error',
                title: '<?= session()->getFlashdata('error'); ?>',
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        </script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            Swal.fire({
                toast: true,
                icon: 'success',
                title: '<?= session()->getFlashdata('success'); ?>',
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        </script>
    <?php endif; ?>

    <div class="login-wrapper">
        <div class="login-image"></div>

        <div class="login-form">
            <h3>Selamat Datang 👋</h3>
            <p>Masuk untuk mengelola sistem Inventory Barang Anda dengan aman dan efisien.</p>

            <form action="<?= site_url('auth/login') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <!-- Username -->
                <div class="form-floating mb-3">
                    <input type="text"
                        name="username"
                        id="inputUsername"
                        class="form-control <?= ($validation->hasError('username') ? 'is-invalid' : '') ?>"
                        placeholder="Username"
                        value="<?= old('username') ?>">
                    <label for="inputUsername"><i class="bi bi-person-fill me-2"></i>Username</label>
                    <div class="invalid-feedback">
                        <?= $validation->getError('username') ?>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-floating mb-4">
                    <input type="password"
                        name="password"
                        id="inputPassword"
                        class="form-control <?= ($validation->hasError('password') ? 'is-invalid' : '') ?>"
                        placeholder="Password">
                    <label for="inputPassword"><i class="bi bi-lock-fill me-2"></i>Password</label>
                    <div class="invalid-feedback">
                        <?= $validation->getError('password') ?>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </div>
            </form>

            <div class="footer-text mt-4">
                <small>© <?= date('Y') ?> Inventory System — Built with 💙 by <strong>BayuDev</strong></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const submitBtn = form.querySelector("button[type='submit']");
            const inputs = form.querySelectorAll("input");

            form.addEventListener("submit", function() {
                // Ubah tombol menjadi loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Loading...`;

                // Jadikan input readonly agar tetap terkirim
                inputs.forEach(input => {
                    input.setAttribute("readonly", true);
                });
            });
        });
    </script>

</body>

</html>