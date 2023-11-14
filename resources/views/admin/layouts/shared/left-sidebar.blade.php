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
                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle">
                    <a href="#">
                        <i data-feather="home"></i>
                        <span>{{ __('admin.subServices') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu mt-2">
                        <li class="">
                            <a href="{{route('admin.sub-categories.index')}}">
                                <span> {{__('admin.subServices')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.sub-categories-requests.index')}}">
                                <span> {{__('admin.sub_services_requests')}} </span>
                            </a>
                        </li>
                    </ul>

                </li>

                <li class="menu-title">{{__('admin.navigation')}}</li>

                <li  class="submenu mb-2 submenu-toggle" id="submenu-toggle2">
                    <a href="#">
                        <i data-feather="users"></i>
                        <span> {{__('admin.users')}} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu2 mt-2">
                        <li class="">
                            <a href="{{route('admin.users.index')}}">
                                <span> {{__('admin.users')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.users-banners.index')}}">
                                <span> Banners </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle3">
                    <a href="#">
                        <i data-feather="tag"></i>
                        <span>{{ __('admin.coupon') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu3 mt-2">
                        <li class="mt-3">
                            <a href="{{route('admin.coupons.index')}}">
                                <span> {{__('admin.all-coupon')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.coupons.category.index',1)}}">
                                <span> {{__('admin.shops-coupons')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.coupons.category.index',2)}}">
                                <span> {{__('admin.delivery-coupons')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.coupons.requests.index',1)}}">
                                <span> {{__('admin.shops-coupons-requests')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.coupons.requests.index',2)}}">
                                <span> {{__('admin.delivery-coupons-requests')}} </span>
                            </a>
                        </li>

                    </ul>

                </li>
                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle4">
                    <a href="#">
                        <i data-feather="home"></i>
                        <span>{{ __('admin.shops') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu4 mt-2">
                        <li class="">
                            <a href="{{ route('admin.shops.index') }}">
                                <span> {{__('admin.shops')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.shop_requests.index')}}">
                                <span> {{__('admin.shop_request')}} </span>
                            </a>
                        </li>
                         <li class="mt-3">
                            <a href="{{route('admin.wallets.index')}}">
                                <span> {{__('admin.CouponBook')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.wallets-requests.index')}}">
                                <span> {{__('admin.CouponBook-requests')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.shops-banners.index')}}">
                                <span> Banners </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="submenu mb-2 submenu-toggle" id="submenu-toggle5">
                    <a href="#">
                        <i data-feather="home"></i>
                        <span>{{ __('admin.delivery_boy') }}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu5 mt-2">
                        <li class="">
                            <a href="{{route('admin.delivery-boys.index')}}">
                                <span> {{__('admin.delivery_boy')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.delivery-boy-request.index')}}">
                                <span> {{__('admin.delivery_boy_request')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.drivers-banners.index')}}">
                                <span> Banners </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{route('admin.orders.index')}}">
                        <i data-feather="shopping-bag"></i>
                        <span>  {{__('admin.orders')}} </span>
                    </a>
                </li>


                {{-- <li class="submenu mb-2 submenu-toggle" id="submenu-toggle4">
                    <a href="#">
                        <i data-feather="tag"></i>
                        <span> {{__('admin.user_wallet')}}</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="sub-menu4 mt-2">
                        <li class="">
                            <a href="{{route('admin.wallets.index')}}">
                                <span> {{__('admin.wallets')}} </span>
                            </a>
                        </li>
                        <li class="mt-3">
                            <a href="{{route('admin.wallets-requests.index')}}">
                                <span> {{__('admin.wallets-requests')}} </span>
                            </a>
                        </li>
                    </ul>

                </li> --}}

                <li>
                    <a href="{{route('admin.transactions.index')}}">
                        <i data-feather="dollar-sign"></i>
                        <span>  {{__('admin.transactions')}} </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('admin.banners.create')}}">
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
