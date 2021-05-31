<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Dorepha & Maesta') }} - Add User </title>
    @include('layouts/header')
</head>

<body class="open-menu-1">

@include('layouts/menu')

<!-- the-table-content-1 -->
<div class="the-table-content-1 my-0 d-block overflow-auto pb-5 mb-5">
    <!-- container -->
    <div class="container-fluid">
        <!-- Add User Management -->

        <div class="row div_container_user justify-content-center">
            <div class="col-sm-6">
                <div class="h4 mb-3">Add New User</div>
                <form method="post" action="/store_user" id="user_form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="exampleInputName">Name</label>
                            <input type="text" class="form-control @error('name')is-invalid @enderror" name="name" pattern="[\sa-zA-Z]{3,20}"
                                   title="Your name must be more than 3 characters, should contain characters"
                                   id="user_name" value="{{ old('name') }}" required placeholder="Enter Name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" class="form-control @error('email')is-invalid @enderror" name="email" id="email"
                                   pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{1,63}$"
                                   value="{{ old('email') }}" required placeholder="Enter email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputPassword1">Password</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control @error('password')is-invalid @enderror"
                                       pattern="^(?=.*?[a-z])(?=.*?[0-9]).{7,}$"
                                       title="Must contain at least one number and lowercase letter, and at least 7 or more characters"
                                       name="password" id="pass" placeholder="********" value="@error('email'){{ old('password') }} @enderror" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" onclick="gen()" type="button">Generate</button>
                                </div>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    @if($message == 'The password format is invalid.')
                                        <strong>{{ $message }}</strong>
                                        <p id="passwordHelpBlock" class="form-text text-muted">
                                            Your password must be contain at least one number and lowercase letter, and at least 8 or more characters.
                                         </p>
                                    @else
                                        <strong>{{ $message }}</strong>
                                    @endif
                                  </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a href="/user_management">
                                <button type="button" class="btn btn-secondary w-100">Back</button>
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="submit" id="submit_form" class="btn btn-primary w-100">Submit</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- Add User Management // -->
    </div>
    <!-- container // -->
</div>
<!-- the-table-content-1 // -->


<!-- jQuery first, then Popper.js, then Bootstrap JS -->
@include('layouts/footer')
<script>
    $(document).ready(function () {
        var url = window.location.pathname;
        if (url == '/add_user') {
            $('.sidenav a.input_active').removeClass('active');
            $('.sidenav a.profile_active').removeClass('active');
            $('.sidenav .dropdown-btn').click();
        }
    });
</script>
</body>

</html>
