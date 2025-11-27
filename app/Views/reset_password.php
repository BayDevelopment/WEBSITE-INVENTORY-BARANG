<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            background: linear-gradient(135deg, #eef2f7, #dfe7f2);
            font-family: 'Poppins', sans-serif;
        }

        .reset-card {
            width: 420px;
            border-radius: 18px;
            animation: fadeIn 0.4s ease-in-out;
        }

        .form-control {
            border-radius: 10px;
        }

        button {
            border-radius: 10px !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">

        <div class="card shadow-lg p-4 reset-card">

            <h4 class="text-center mb-3 fw-bold text-primary">Reset Password</h4>
            <p class="text-center text-muted mb-4" style="font-size: 14px;">
                Silakan masukkan kata sandi baru Anda.
            </p>

            <!-- Alert -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
            <?php endif; ?>

            <form action="<?= base_url('auth/forgot-password/reset') ?>" method="post">

                <!-- TOKEN -->
                <input type="hidden" name="token" value="<?= $token ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control" required minlength="6" placeholder="Masukkan password baru">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    Reset Password
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="<?= base_url('auth/login') ?>" class="text-decoration-none fw-semibold">
                    ← Kembali ke Login
                </a>
            </div>

        </div>
    </div>

</body>

</html>