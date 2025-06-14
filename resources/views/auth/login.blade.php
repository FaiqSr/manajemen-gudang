<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FINANCE | Log in</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/dist/css/adminlte.min.css">

    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/toastr/toastr.min.css">

    <link rel="icon" href="{{ asset('/public/adminlte') }}/dist/img/logo.png" type="image/png" sizes="16x16">


    <style type="text/css">
        body {
            background-color: #2e4585 !important
        }
    </style>

</head>

<body class="hold-transition login-page">
    <div class="login-box">

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h2"><b>FINANCE</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Masukkan username dan Password</p>
                <form action="{{ url('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input name="email" type="email" class="form-control" placeholder="Email" autocomplete="off"
                            value="admin@localhost.com" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input name="password" type="password" class="form-control" placeholder="Password"
                            value="123456">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i>
                                Sign In</button>
                        </div>

                    </div>
                </form>
            </div>

        </div>

    </div>


    <script src="{{ asset('/public/adminlte') }}/plugins/jquery/jquery.min.js"></script>

    <script src="{{ asset('/public/adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('/public/adminlte') }}/dist/js/adminlte.min.js"></script>

    <script src="{{ asset('/public/adminlte') }}/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="{{ asset('/public/adminlte') }}/plugins/toastr/toastr.min.js"></script>

    <script>
        function gagal() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'error',
                title: ' &nbsp; Username dan Password Tidak Sesuai. Silahkan Coba lagi'
            });
        }

        function akses() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'error',
                title: ' &nbsp; Akses gagal, silahkan login terlebih dahulu'
            });
        }

        function logout() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: ' &nbsp; Anda Telah Logout'
            });
        }
    </script>

    @if (session('gagal'))
        <script>
            gagal();
        </script>
    @endif

    @if (session('akses'))
        <script>
            akses();
        </script>
    @endif

    @if (session('logout'))
        <script>
            logout();
        </script>
    @endif

</body>

</html>
