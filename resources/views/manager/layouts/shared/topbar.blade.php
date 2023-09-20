<!-- Topbar Start -->
<div class="navbar-custom">
    <div class="container-fluid">
        <ul class="list-unstyled topnav-menu float-right mb-0">


            <li class="dropdown d-none d-lg-inline-block">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen"
                   href="#">
                    <i class="fe-maximize noti-icon"></i>
                </a>
            </li>

            <li class="dropdown d-none d-lg-inline-block topbar-dropdown">
               
                @if(str_contains(request()->url(),'/ar'))
                 <?php 
                    $route = request()->url(); 
                    $route = str_replace('/ar' , '/en',$route);
                    ?>
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light "  href="{{$route}}"
                   >
                    <img src="{{asset('assets/images/flags/en.jpg')}}" 
                         height="16">
                </a>
                @else
                 <?php 
                    $route = request()->url(); 
                    $route = str_replace('/en' , '/ar',$route);
                ?>
                   <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light"   href="{{$route}}"
                    >
                    <img src="{{asset('assets/images/flags/ar.png')}}" 
                         height="16">
                </a>
                @endif
              
 
            </li>

            {{--<li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="fe-bell noti-icon"></i>
                    <span class="badge badge-danger rounded-circle noti-icon-badge">9</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">
                            <span class="float-right">
                                <a href="" class="text-dark">
                                    <small>Clear All</small>
                                </a>
                            </span>Notification
                        </h5>
                    </div>


                    <!-- All-->
                    <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                        View all
                        <i class="fe-arrow-right"></i>
                    </a>

                </div>
            </li>--}}

            <li class="dropdown notification-list">
                <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                    <i class="fe-settings noti-icon"></i>
                </a>
            </li>

            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown"
                   href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{\App\Helpers\TextUtil::getImageUrl(auth()->user()->avatar_url,\App\Helpers\TextUtil::$PLACEHOLDER_AVATAR_URL)}}" alt="user-image"
                         class="rounded-circle">
                    <span class="pro-user-name ml-1">
                        {{auth()->user()->name}} <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{__('manager.welcome')}} !</h6>
                    </div>

                    <!-- item-->
                    <a href="{{route('manager.setting.edit')}}" class="dropdown-item notify-item">
                        <i class="fe-settings"></i>
                        <span>{{__('manager.setting')}}</span>
                    </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item notify-item" href="{{ route('manager.logout') }}">
                        <i class="fe-log-out"></i>
                        <span>{{__('manager.logout')}}</span>
                    </a>
                </div>
            </li>


        </ul>

        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{route('manager.dashboard')}}" class="logo text-center logo-light">

                <span class="logo-sm">
                    <img  src="{{asset('assets/images/logo-light-sm.png')}}"alt="" height="24">
                </span>
                <span class="logo-lg">
                    <img style="    width: 81px;
    height: auto;" src="{{asset('assets/images/logo-light.png')}}"alt="" height="24">
                </span>
            </a>
            <a href="{{route('manager.dashboard')}}" class="logo text-center logo-dark">

                <span class="logo-sm">
                    <img src="{{asset('assets/images/logo-dark-sm.png')}}"alt="" height="24">
                </span>
                <span class="logo-lg">
                    <img src="{{asset('assets/images/logo-dark.png')}}"alt="" height="24">
                </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
                <button class="button-menu-mobile waves-effect waves-light">
                    <i class="fe-menu"></i>
                </button>
            </li>

        </ul>
        <div class="clearfix"></div>
    </div>
</div>
<!-- end Topbar -->
