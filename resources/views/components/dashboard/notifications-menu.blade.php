<li class="dropdown notification-list topbar-dropdown" id="notification">
    <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
        <i id="img-notification" class="fe-bell noti-icon" data-count = {{ $newCount }}></i>
        {{-- <span class="badge badge-danger rounded-circle noti-icon-badge" data-count = {{ $newCount }}>{{ $newCount }}</span> --}}
        @if ( $newCount <> 0)<span class="badge badge-danger rounded-circle noti-icon-badge unread-count" id="unread-count" data-count = {{ $newCount }}>{{ $newCount }}</span> @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-lg ">

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

        <div class="noti-scroll nk-notification" data-simplebar>
            @forelse ($notifications as $notification)
                
                <a href="{{ $notification->data['url'] }}?notification_id={{ $notification->id }}" class="dropdown-item notify-item @if($notification->unread()) notification-unread @endif">
                    <div class="notify-icon bg-primary">
                        <img class="icon-circle" src="{{asset( $notification->data['icon'])}}" alt="" style="width:40px">
                    </div>
                    <p class="notify-details">{{ Str::words($notification->data[ app()->getLocale()]['body'], 30 , '...') }}
                        <small class="text-muted">{{ $notification->created_at->diffForHumans()}}</small>
                    </p>
                </a>
            @empty
                <div class="nk-notification-item dropdown-inner" id="no-notification">
                    you have no notification
                </div>
            @endforelse


            



            {{-- <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item active">
                <div class="notify-icon">
                    <img src="{{asset('assets/images/users/user-1.jpg')}}" class="img-fluid rounded-circle" alt="" /> </div>
                <p class="notify-details">Cristina Pride</p>
                <p class="text-muted mb-0 user-msg">
                    <small>Hi, How are you? What about our next meeting</small>
                </p>
            </a>
 --}}
            <!-- item-->
           {{--  <a href="javascript:void(0);" class="dropdown-item notify-item">
                <div class="notify-icon bg-primary">
                    <i class="mdi mdi-comment-account-outline"></i>
                </div>
                <p class="notify-details">Caleb Flakelar commented on Admin
                    <small class="text-muted">1 min ago</small>
                </p>
            </a>
 --}}
            <!-- item-->
           {{--  <a href="javascript:void(0);" class="dropdown-item notify-item">
                <div class="notify-icon">
                    <img src="{{asset('assets/images/users/user-4.jpg')}}" class="img-fluid rounded-circle" alt="" /> </div>
                <p class="notify-details">Karen Robinson</p>
                <p class="text-muted mb-0 user-msg">
                    <small>Wow ! this admin looks good and awesome design</small>
                </p>
            </a>
 --}}
            <!-- item-->
            {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                <div class="notify-icon bg-warning">
                    <i class="mdi mdi-account-plus"></i>
                </div>
                <p class="notify-details">New user registered.
                    <small class="text-muted">5 hours ago</small>
                </p>
            </a>
 --}}
            {{-- <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
                <div class="notify-icon bg-info">
                    <i class="mdi mdi-comment-account-outline"></i>
                </div>
                <p class="notify-details">Caleb Flakelar commented on Admin
                    <small class="text-muted">4 days ago</small>
                </p>
            </a> --}}

            {{-- <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
                <div class="notify-icon bg-secondary">
                    <i class="mdi mdi-heart"></i>
                </div>
                <p class="notify-details">Carlos Crouch liked
                    <b>Admin</b>
                    <small class="text-muted">13 days ago</small>
                </p>
            </a> --}}
        </div>

        <!-- All-->
        <a href="{{ route('admin.notifications.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
            View all
            <i class="fe-arrow-right"></i>
        </a>

    </div>
</li>