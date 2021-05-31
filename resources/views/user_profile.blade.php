<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Dorepha & Maesta') }} - Porfile </title>
    @include('layouts/header')
</head>

<body class="open-menu-1">

@include('layouts/menu')

<!-- the-table-content-1 -->
<div class="the-table-content-1 my-0 d-block overflow-auto pb-5 mb-5">
    <!-- container -->
    <div class="container-fluid">
        <!-- Profile -->

        <div class="row div_container_user justify-content-center">
            <div class="col-sm-6">
                <input type="hidden" value="{{\Session::has('success')}}" id="msg_success">
                @if (\Session::has('success'))
                    <div class="alert alert-success msg_success">
                        <ul>
                            <p>{!! \Session::get('success') !!}</p>
                        </ul>
                    </div>
                @endif
                <div class="h4 mb-3">Your Profile</div>
                <form method="post" action="/update_user_profile" id="user_edit_form_profile">
                    @csrf
                    <div class="form-row">
                        <input type="hidden" name="role" id="inlineRadio1" value="user">
                        <div class="form-group col-12">
                            <input type="hidden" name="user_id" value="{{ $data->id }}">
                            <label for="exampleInputName">Name</label>
                            <input type="text" class="form-control @error('name')is-invalid @enderror" name="name"
                                   pattern="[\sa-zA-Z]{3,20}"
                                   title="Your name must be more than 3 characters, should contain characters"
                                   id="user_name" value="{{ $data->name }}" required placeholder="Enter Name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" class="form-control" name="email" id="email"
                                   value="{{$data->email}}" required
                                   placeholder="Enter email" readonly>
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputPassword1">Password</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control @error('password')is-invalid @enderror"
                                       name="password" id="pass" value="@error('password'){{ old('password') }} @enderror"
                                       pattern="^(?=.*?[a-z])(?=.*?[0-9]).{7,}$"
                                       title="Must contain at least one number and lowercase letter, and at least 7 or more characters"
                                       placeholder="********">
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
                        <div class="col-12">
                            <button type="submit" id="submit_form" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Profile // -->
        </div>
        <!-- container // -->
    </div>
    <!-- the-table-content-1 // -->
</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
@include('layouts/footer')
<script>
    $(document).ready(function () {
        var url = window.location.pathname;
        if (url == '/profile') {
            $('.sidenav .dropdown-btn').removeClass('active');
            $('.sidenav a.input_active').removeClass('active');
            $('.sidenav a.profile_active').addClass('active');
        }
    });
</script>
</body>

</html>
