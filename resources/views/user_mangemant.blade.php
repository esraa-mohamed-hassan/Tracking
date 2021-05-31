<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>{{ config('app.name', 'Dorepha & Maesta') }} - User Management</title>
    @include('layouts/header')
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        .table th,
        .table td {
            padding: 0.5rem;
            text-align: center;
        }

        .the-all-buttons-1 {
            padding-bottom: 3rem !important;
            background: none !important;
            margin-top: 2%;
        }

        td.user_td {
            text-align: center;
            display: flex;
            padding-left: 25%;
        }

        table.table td button.delete {
            color: #F44336;
            border: none;
            background: none;
        }

        table.table td a.edit {
            margin-right: 10%;
            color: #2196F3;
            border: none;
            background: none;
        }

        a span {
            float: left;
            margin-top: unset;
            margin-left: 2%;
        }

    </style>
</head>

<body class="open-menu-1">
    <div id="overlay">
        <div class="overlay__inner">
            <img src="./asset/images/processing.gif?v=1">
        </div>
    </div>
    <div id="msg_response"></div>

    @include('layouts/menu')

    <!-- the-table-content-1 -->
    <div class="the-table-content-1 my-0 d-block overflow-auto pb-5 mb-5">
        <!-- container -->
        <div class="container-fluid">

            <!-- User Management -->
            <div class="the-all-buttons-1 text-center py-3">
                <div class="row">
                    <div class="col-sm-6 col-sm-6 offset-md-3">
                        <input type="hidden" value="{{ \Session::has('success') }}" id="msg_success">
                        @if (\Session::has('success'))
                            <div class="alert alert-success msg_success">
                                <ul>
                                    <p>{!! \Session::get('success') !!}</p>
                                </ul>
                            </div>
                        @endif
                        <div class="alert alert-success msg_success_ajax" style="display: none;">
                            <ul class="msg_success_ajax"></ul>
                        </div>
                    </div>
                </div>
                <a href="/add_user" class="btn btn-secondary" style="float: right;display: flex;"><i
                        class="material-icons">&#xE147;</i>
                    <span>Add New User</span></a>

                <h3 style="float: left;margin-left: 1%;">User Management</h3>
            </div>
            <table class="table table-striped" id="user_table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 5%">#</th>
                        <th scope="col" style="width: 25%">Name</th>
                        <th scope="col" style="width: 30%">Email</th>
                        <th scope="col" style="width: 15%">Date Created</th>
                        <th scope="col" style="width: 10%">Role</th>
                        <th scope="col" style="width: 10%">Controllers</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($data) == 0)
                        <tr>
                            <td colspan="7" style="text-align: center;">No Users</td>
                        </tr>
                    @else
                        @foreach ($data as $index => $item)
                            <tr>
                                <th scope="row">{{ $index + 1 }}</th>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['email'] }}</td>
                                <td>{{ date_format($item['created_at'], 'Y-m-d') }}</td>
                                <td>{{ $item['role'] }}</td>
                                <td class="user_td">
                                    <a href="/edit_user?user_id={{ $item['id'] }}" class="edit"><i
                                            class="material-icons">edit</i></a>
                                    @if (\Illuminate\Support\Facades\Auth::user()->role == 'admin' && $item['id'] == \Illuminate\Support\Facades\Auth::id())
                                    @else
                                        <form id="delete_user">
                                            @csrf
                                            <button onclick="confirmation({{ $item['id'] }})" type="button"
                                                class="delete" title="Delete" data-toggle="tooltip"><i
                                                    class="material-icons">delete</i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <!-- User Management // -->
        </div>
        <!-- container // -->
    </div>
    <!-- the-table-content-1 // -->


    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    @include('layouts/footer')
    <script>
        $(document).ready(function() {
            var url = window.location.pathname;
            if (url == '/user_management') {
                $('.sidenav a.input_active').removeClass('active');
                $('.sidenav a.profile_active').removeClass('active');
                $('.sidenav .dropdown-btn').click();
            }

            $('#user_table').DataTable({
                'responsive': true,
                "searching": true,
                "columns": [{
                        "data": "#"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "role"
                    },
                    {
                        "data": "controls"
                    },
                ],
                "columnDefs": [{
                    "targets": 5,
                    "orderable": false,
                }],
                "order": [
                    [0, 'asc']
                ],
            });
        });


        function confirmation(id) {
            var result = confirm("Are you sure to delete?");
            if (result) {
                // Delete logic goes here
                console.log('deleeeeee');
                var data = $('#delete_user').serialize();
                $.ajax({
                    url: '/delete_user/' + id,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $('.msg_success_ajax').show();
                            $('ul.msg_success_ajax').html('<p>User deleted successfully</p>');
                            setTimeout(function() {
                                $('ul.msg_success_ajax').html('');
                                $('.msg_success_ajax').hide();
                                window.location.href = '/user_management';
                            }, 1000);
                        } else {
                            $('.msg_success_ajax').hide();
                        }
                        console.log(response.success);
                    }
                });
            }
        }

    </script>
</body>

</html>
