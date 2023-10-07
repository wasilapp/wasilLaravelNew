<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li>
                    <a href="{{route('admin.dashboard')}}">
                        <i data-feather="airplay"></i>
                        <span> {{__('admin.dashboard')}} </span>
                    </a>
                </li>

                <li class="menu-title">{{__('admin.services')}}</li>

                <li>
                    <a href="{{route('admin.categories.index')}}">
                        <i data-feather="square"></i>
                        <span> {{__('admin.services')}} </span>
                    </a>
                </li>

                {{-- <li>
                    <a href="{{route('admin.sub-categories.index')}}">
                        <i data-feather="grid"></i>
                        <span> {{__('admin.subServices')}} </span>
                    </a>
                </li> --}}
                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle3">
                    <a href="#">
                        <i data-feather="home"></i>
                        <span>{{ __('admin.subServices') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu mt-2">
                        <li class="">
                            <a href="{{route('admin.sub-categories.index')}}">
                               {{-- <i data-feather="home"></i> --}}
                                <span> {{__('admin.subServices')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.sub-categories-shops.index')}}">
                               {{-- <i data-feather="home"></i> --}}
                                <span> {{__('admin.subServicesShops')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.sub-categories-requests.index')}}">
                                {{-- <i data-feather="user-check"></i> --}}
                                <span> {{__('admin.sub_services_requests')}} </span>
                            </a>
                        </li>
                    </ul>

                </li>

                <li class="menu-title">{{__('admin.navigation')}}</li>

                <li>
                    <a href="{{route('admin.users.index')}}">
                        <i data-feather="users"></i>
                        <span> {{__('admin.users')}} </span>
                    </a>
                </li>

                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle">
                    <a href="#">
                        <i data-feather="home"></i>
                        <span>{{ __('admin.shops') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu mt-2">
                        <li class="">
                            <a href="{{ route('admin.shops.index') }}">
                               {{-- <i data-feather="home"></i> --}}
                                <span> {{__('admin.shops')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.shop_requests.index')}}">
                                {{-- <i data-feather="user-check"></i> --}}
                                <span> {{__('admin.shop_request')}} </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle2">
                    <a href="#">
                        <i data-feather="home"></i>
                        <span>{{ __('admin.delivery_boy') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu mt-2">
                        <li class="">
                            <a href="{{route('admin.delivery-boys.index')}}">
                               {{-- <i data-feather="home"></i> --}}
                                <span> {{__('admin.delivery_boy')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.delivery-boy-request.index')}}">
                                {{-- <i data-feather="user-check"></i> --}}
                                <span> {{__('admin.delivery_boy_request')}} </span>
                            </a>
                        </li>
                    </ul>
                </li>

                 {{-- <li>
                    <a href="{{route('admin.delivery-boys.index')}}">
                        <i data-feather="truck"></i>
                        <span> {{__('admin.delivery_boy')}} </span>
                    </a>
                </li> --}}

                <li>
                    <a href="{{route('admin.orders.index')}}">
                        <i data-feather="shopping-bag"></i>
                        <span>  {{__('admin.orders')}} </span>
                    </a>
                </li>


                 <li>
                    <a href="{{route('admin.coupons.index')}}">
                        <i data-feather="tag"></i>
                        <span> {{__('admin.coupon')}} </span>
                    </a>
                </li>

                 <li>
                    <a href="{{route('admin.wallet-coupons.index')}}">
                        <i data-feather="tag"></i>
                        <span> {{__('admin.user_wallet')}} </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('admin.transactions.index')}}">
                        <i data-feather="dollar-sign"></i>
                        <span>  {{__('admin.transactions')}} </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('admin.banners.index')}}">
                        <i data-feather="home"></i>
                        <span> {{__('admin.banners')}} </span>
                    </a>
                </li>


                <li class="menu-title">{{__('admin.other')}}</li>

                <li>
                    <a href="{{route('admin.notifications.create')}}">
                        <i data-feather="bell"></i>
                        <span> {{__('admin.notification')}} </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('admin.appdata.index')}}">
                        <i data-feather="sliders"></i>
                        <span> {{__('admin.app_data')}} </span>
                    </a>
                </li>

                 <li>
                    <a href="{{route('user.privacy')}}">
                        <i data-feather="settings"></i>
                        <span> {{__('admin.privacy')}} </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('admin.setting.edit')}}">
                        <i data-feather="settings">></i>
                        <span> {{__('admin.setting')}} </span>
                    </a>
                </li>
            </ul>



        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
