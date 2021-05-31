<!-- the-top-header-1 -->
<div class="the-top-header-1 py-1 position-fixed">
    <nav class="navbar navbar-expand-lg navbar-light static-top">
        <div class="container-fluid">
            <div class="div_fa_list">
                <i class="fa fa-list-ul" aria-hidden="true"></i>
            </div>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">

                        <div class="dropdown show profile">
                            <a class="btn dropdown-toggle" href="#" role="button" id="ProfileLink"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{\Illuminate\Support\Facades\Auth::user()->name}}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="ProfileLink">
                            <a class="dropdown-item" href="/profile"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out" aria-hidden="true"></i> {{ __('Logout') }}
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        @csrf
                                    </form>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
<!-- the-top-header-1 // -->

<div class="sidenav">
    <div class="site_name">
        <h4>{{ config('app.name') }}</h4>
    </div>
    <a href="/search" class="input_active">Search</a>
    <a href="/profile" class="profile_active">Profile</a>
    @if (\Illuminate\Support\Facades\Auth::user()->role == 'admin')
        <button class="dropdown-btn input_active">User Management
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="/user_management" id="user_mang">User Management</a>
            <a href="/add_user" id="add_new_user">Add user</a>
        </div>
    @endif
</div>
