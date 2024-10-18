{{-- kt-menu__item--active --}}

@php
    $route_name = request()->route()->getName();
@endphp
<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
<div class="kt-aside  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside" style="background-color: #0E2547">

    <!-- begin::Aside Brand -->
    <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">

        <div class="kt-aside__brand-tools">
            <button
                class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left @if ($helper_service->helper->getConfig()->sidebar != 1) kt-aside__brand-aside-toggler--active @endif"
                id="kt_aside_toggler"><span></span></button>
        </div>
        <div class="kt-aside__brand-logo ml-2  mt-4">
            <a href="{{ url('/') }}" class="text-white h5 mb-0">
                <img alt="Logo" class="w-100" src="{{ asset('media/logo/Buraq-logo.png') }}" />
            </a>
        </div>
    </div>

    <!-- end:: Aside Brand -->

    <!-- begin:: Aside Menu -->
    <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="kt-aside-menu kt-scroll " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
            <ul class="kt-menu__nav ">

                @if ($helper_service->routes->has_access('central.admin.tenants.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'central.admin.tenants.view') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('central.admin.tenants.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon flaticon-plus"></i>
                            <span class="kt-menu__link-text">Tenants</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <!-- end:: Aside Menu -->
</div>
