<div id="kt_header" class="kt-header kt-grid__item   kt-header--fixed " style="background-color: #0E2547">
    <!-- begin:: Header Topbar - LEFT -->
    {{-- @if (config('app.debug'))
        <div class="d-flex align-items-center">
            <h4 class="ml-1">
                <span class="badge badge-danger">{{ config('app.name') }}</span>
            </h4>
        </div>
    @else

        <div></div>
    @endif --}}

    <!-- begin:: Header Topbar - CENTER -->
    <div class="d-flex justify-content-center align-items-center" style="padding-bottom: 1px; background-color: #242939;">
        <div class="kt-header__topbar-item kt-header__topbar-item--user dropdown">
            <div hidden id="appconfig_header_pending_cheques" class="my-0 ms-4 d-flex flex-column"></div>
        </div>

    </div>

    <!-- begin:: Header Topbar - RIGHT -->
    <div class="kt-header__topbar" style="padding-top: 8px" class="mt-2" >
        <div hidden class="kt-header__topbar-item kt-header__topbar-item--user dropdown kt-header-notification-visa">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px">
                <span class="kt-header__topbar-icon" title="">
                    <i class="fab fa-cc-visa text-white" title="Visa"></i>
                    <span class="noti_circle"></span>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                <form>
                    <div class="kt-head" style="background-image: url({{asset('theme/media/misc/head_bg_sm.jpg')}})">
                        <h3 class="kt-head__title">Expiring Visa Notifications</h3>
                        <div class="kt-head__sub"><span class="kt-head__desc"></span></div>
                    </div>
                    <div class="kt-notification kt-margin-t-30 kt-margin-b-20 kt-scroll ps" data-scroll="true" data-height="270" data-mobile-height="220" style="height: 270px; overflow: hidden;">


                    </div>
                </form>
            </div>
        </div>
        <div hidden class="kt-header__topbar-item kt-header__topbar-item--user dropdown kt-header-notification-rta">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px">
                <span class="kt-header__topbar-icon" title="">
                    <i class="text-white fa fa-route" title="RTA"></i>
                    <span class="noti_circle"></span>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                <form>
                    <div class="kt-head" style="background-image: url({{asset('theme/media/misc/head_bg_sm.jpg')}})">
                        <h3 class="kt-head__title">Expiring RTA Notifications</h3>
                        <div class="kt-head__sub"><span class="kt-head__desc"></span></div>
                    </div>
                    <div class="kt-notification kt-margin-t-30 kt-margin-b-20 kt-scroll ps" data-scroll="true" data-height="270" data-mobile-height="220" style="height: 270px; overflow: hidden;">


                    </div>
                </form>
            </div>
        </div>
        <div hidden class="kt-header__topbar-item kt-header__topbar-item--user dropdown kt-header-notification-liscense">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px">
                <span class="kt-header__topbar-icon" title="">
                    <i class="text-white fa flaticon-doc"  title="License"></i>
                    <span class="noti_circle"></span>
                </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                <form>
                    <div class="kt-head" style="background-image: url({{asset('theme/media/misc/head_bg_sm.jpg')}})">
                        <h3 class="kt-head__title">Expiring License Notifications</h3>
                        <div class="kt-head__sub"><span class="kt-head__desc"></span></div>
                    </div>
                    <div class="kt-notification kt-margin-t-30 kt-margin-b-20 kt-scroll ps" data-scroll="true" data-height="270" data-mobile-height="220" style="height: 270px; overflow: hidden;">


                    </div>
                </form>
            </div>
        </div>


        <!--begin: User Bar -->
        <div class="kt-header__topbar-item kt-header__topbar-item--user">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">

                <!--use "kt-rounded" class for rounded avatar style-->
                <div class="kt-header__topbar-user kt-rounded-">
                    <span class="kt-header__topbar-welcome kt-hidden-mobile">Welcome,</span>
                    <span class="kt-header__topbar-username kt-hidden-mobile">{{Auth::user()->name}}</span>

                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    <span class="kt-badge kt-badge--username kt-badge--lg kt-badge--brand kt-badge--bold">{{ strtoupper(substr(Auth::user()->name, 0,1)) }}</span>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-sm">
                <div class="kt-user-card kt-margin-b-40 kt-margin-b-30-tablet-and-mobile" style="background-image: url({{asset('theme/media/misc/head_bg_sm.jpg')}})">
                    <div class="kt-user-card__wrapper">
                        <div class="kt-user-card__pic">

                            <!--use "kt-rounded" class for rounded avatar style-->
                            <span class="kt-badge kt-badge--username kt-badge--xl kt-badge--danger kt-badge--bold">{{ strtoupper(substr(Auth::user()->name, 0,1)) }}</span>
                        </div>
                        <div class="kt-user-card__details">
                            <div class="kt-user-card__name">{{Auth::user()->name}}</div>
                            <div class="kt-user-card__position">{{Auth::user()->email}}</div>
                        </div>
                    </div>
                </div>
                <ul class="kt-nav kt-margin-b-10">
                    <li class="kt-nav__item">
                        <a href="{{ route('central.admin.profile') }}" class="kt-nav__link">
                            <span class="kt-nav__link-icon"><i class="flaticon2-calendar-3"></i></span>
                            <span class="kt-nav__link-text">My Profile</span>
                        </a>
                    </li>
                    <li class="kt-nav__separator kt-nav__separator--fit"></li>
                    <li class="kt-nav__custom kt-space-between">
                        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="btn btn-label-brand btn-upper btn-sm btn-bold">Sign Out</a>
                        <form id="logout-form" action="{{ route('central.admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <!--end: User Bar -->

        <div class="kr-tasks-container">{{-- JS will append button here --}}</div>


    </div>

    <!-- end:: Header Topbar -->
</div>
