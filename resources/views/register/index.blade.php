<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Forecasting | Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href={{ asset('assets/css/sb-admin-2.min.css') }} rel="stylesheet">

    <!-- Favicon -->
<link rel="icon" href="{{ asset('logo.arida.png') }}" type="image/png">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-5">

                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                        </div>
                            <form action="/register" method="POST" class="user">
                                @csrf
                                <div class="form-group">
                                        <input type="text" id="name" name="name" placeholder="Name" required class="form-control form-control-user">
                                    </div>
                                    <div class="form-group">
                                    <div class=" mb-sm-0">
                                        <input type="text" id="username" name="username" placeholder="Username" required class="form-control form-control-user">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email"  id="email" name="email"placeholder="Email Address" required class="form-control form-control-user">
                                </div>
                                <div class="form-group">
    <select name="role" class="form-control form-control-user" required>
        <option value="">-- Pilih Role --</option>
        <option value="ppic">PPIC</option>
        <option value="manajer">Manajer Produksi</option>
    </select>
</div>
                                <div class="form-group">
                                    <div class=" mb-sm-0">
                                        <input type="password" id="password" name="password"placeholder="Password" required class="form-control form-control-user">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.html">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('Login') }}">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
