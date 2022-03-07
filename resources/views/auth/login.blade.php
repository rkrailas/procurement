
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>P2P System (NISSAN) </title>

  <!-- Google Font: Source Sans Pro -->
  {{-- <link rel="stylesheet" href="{{ asset('backend/plugins/google/google-font.css') }}"> --}}
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css') }}" >
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('backend/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-danger">
    <div class="card-header text-center">
      <div class="h1"><b>Sign In</b></div>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to Nissan Procurement System</p>

      <form action="{{ route('login') }}" method="POST" autocomplete="off">
        @csrf
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        {{-- <div class="input-group mb-3">
          <select class="form-control" name="company">
            <option value="">Select Company</option>
            <option value="4112">4112</option>
            <option value="2650">2650</option>
            <option value="3860">3860</option>
            <option value="2641">2641</option>
          </select>
        </div> --}}
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-danger btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
        <div class="row">
          <div class="col-12">
            @error('username')
            <span class="text-danger"> {{ $message }} </span>
            @enderror
            @error('password')
              <span class="text-danger"> {{ $message }} </span>
            @enderror
            @error('company')
              <span class="text-danger"> {{ $message }} </span>
            @enderror
          </div>
        </div>
      </form>

    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('backend/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
