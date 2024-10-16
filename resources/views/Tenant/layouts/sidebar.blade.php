{{-- kt-menu__item--active --}}

@php
    $route_name = request()->route()->getName();
    $dept_param = request()->route()->parameters['dept'] ?? null;
@endphp
<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
<div class="kt-aside  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

    <!-- begin::Aside Brand -->
    <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">

        <div class="kt-aside__brand-tools">
            <button
                class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left @if ($helper_service->helper->getConfig()->sidebar != 1) kt-aside__brand-aside-toggler--active @endif"
                id="kt_aside_toggler"><span></span></button>
        </div>
        <div class="kt-aside__brand-logo ml-2 justify-content-center" style="flex: 1;">
            <a href="{{ url('/') }}" class="text-white h5 mb-0">
                {{-- <img alt="Logo" class="w-25" src="{{ asset('media/logo/company-logo.png') }}" /> --}}
                {{ tenant('name') }}
            </a>
        </div>
    </div>

    <!-- end:: Aside Brand -->

    <!-- begin:: Aside Menu -->
    <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="kt-aside-menu kt-scroll " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
            <ul class="kt-menu__nav ">

                @if (
                    $helper_service->routes->has_access('tenant.admin.vehicles.vehicle.view') ||
                    $helper_service->routes->has_access('tenant.admin.vehicles.bike.view')
                )
                    <li class="kt-menu__item kt-menu__item--submenu @if (strpos($route_name, 'tenant.admin.vehicles') !== false) kt-menu__item--open @endif" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:;" class="kt-menu__link  kt-menu__toggle">
                            <i class="kt-menu__link-icon flaticon-network"></i>
                            <span class="kt-menu__link-text">Vehicles</span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true">
                                    <span class="kt-menu__link">
                                        <span class="kt-menu__link-text">Vehicles</span>
                                    </span>
                                </li>
                                @if ($helper_service->routes->has_access('tenant.admin.vehicles.vehicle.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.vehicles.vehicle.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.vehicles.vehicle.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Vehicle</span>
                                    </a>
                                </li>
                                @endif
                                @if($helper_service->routes->has_access('tenant.admin.vehicles.bike.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.vehicles.bike.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.vehicles.bike.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Bike</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (
                    $helper_service->routes->has_access('tenant.admin.clients.aggregator.view') ||
                    $helper_service->routes->has_access('tenant.admin.clients.supplier.view')
                )
                    <li class="kt-menu__item kt-menu__item--submenu @if (strpos($route_name, 'tenant.admin.clients') !== false) kt-menu__item--open @endif" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:;" class="kt-menu__link  kt-menu__toggle">
                            <i class="kt-menu__link-icon flaticon-network"></i>
                            <span class="kt-menu__link-text">Clients</span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true">
                                    <span class="kt-menu__link">
                                        <span class="kt-menu__link-text">Clients</span>
                                    </span>
                                </li>
                                @if ($helper_service->routes->has_access('tenant.admin.clients.aggregator.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.clients.aggregator.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.clients.aggregator.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Aggregators</span>
                                    </a>
                                </li>
                                @endif
                                @if($helper_service->routes->has_access('tenant.admin.clients.supplier.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.clients.supplier.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.clients.supplier.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Suppliers</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('tenant.admin.drivers.view') ||
                    $helper_service->routes->has_access('tenant.admin.drivers.riders.view') ||
                    $helper_service->routes->has_access('tenant.admin.drivers.passports.view')
                )
                    <li class="kt-menu__item kt-menu__item--submenu @if (strpos($route_name, 'tenant.admin.drivers') !== false) kt-menu__item--open @endif" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:;" class="kt-menu__link  kt-menu__toggle">
                            <i class="kt-menu__link-icon flaticon-car"></i>
                            <span class="kt-menu__link-text">Drivers</span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true">
                                    <span class="kt-menu__link">
                                        <span class="kt-menu__link-text">Drivers</span>
                                    </span>
                                </li>
                                @if ($helper_service->routes->has_access('tenant.admin.drivers.driver.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.drivers.driver.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.drivers.driver.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Drivers</span>
                                    </a>
                                </li>
                                @endif
                                @if ($helper_service->routes->has_access('tenant.admin.drivers.riders.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.drivers.riders.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.drivers.riders.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Riders</span>
                                    </a>
                                </li>
                                @endif
                                @if($helper_service->routes->has_access('tenant.admin.drivers.passports.view'))
                                <li class="kt-menu__item @if($route_name === 'tenant.admin.drivers.passports.view') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.drivers.passports.view')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Passport Management</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('tenant.admin.sims.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'tenant.admin.sims') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('tenant.admin.sims.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon fa fa-sim-card"></i>
                            <span class="kt-menu__link-text">Sims</span>
                        </a>
                    </li>
                @endif

                @if (
                    $helper_service->routes->has_custom_access('addon_department', ['visa_department']) ||
                    $helper_service->routes->has_custom_access('addon_department', ['driving_license_dubai']) ||
                    $helper_service->routes->has_custom_access('addon_department', ['driving_license_sharjah']) ||
                    $helper_service->routes->has_custom_access('addon_department', ['rta_card'])
                )
                    <li class="kt-menu__item kt-menu__item--submenu @if (strpos($route_name, 'tenant.admin.addons.') !== false) kt-menu__item--open @endif" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:;" class="kt-menu__link  kt-menu__toggle">
                            <i class="kt-menu__link-icon flaticon-app"></i>
                            <span class="kt-menu__link-text">Addons</span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true">
                                    <span class="kt-menu__link">
                                        <span class="kt-menu__link-text">Addons</span>
                                    </span>
                                </li>
                                @if ($helper_service->routes->has_custom_access('addon_department'))
                                <li class="kt-menu__item @if($dept_param === 'all') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.addons.view','all')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">All Addons</span>
                                    </a>
                                </li>
                                @endif
                                @if ($helper_service->routes->has_custom_access('addon_department', ['visa_department']))
                                <li class="kt-menu__item @if($dept_param === 'visa_department') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.addons.view', 'visa_department')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Visa Department</span>
                                    </a>
                                </li>
                                @endif
                                @if ($helper_service->routes->has_custom_access('addon_department', ['driving_license_dubai', 'driving_license_sharjah']))
                                <li class="kt-menu__item @if($dept_param === 'driving_license') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.addons.view', 'driving_license')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Driving License</span>
                                    </a>
                                </li>

                                @elseif ($helper_service->routes->has_custom_access('addon_department', ['driving_license_dubai']))
                                <li class="kt-menu__item @if($dept_param === 'driving_license_dubai') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.addons.view', 'driving_license_dubai')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Driving License</span>
                                    </a>
                                </li>

                                @elseif ($helper_service->routes->has_custom_access('addon_department', ['driving_license_sharjah']))
                                <li class="kt-menu__item @if($dept_param === 'driving_license_sharjah') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.addons.view', 'driving_license_sharjah')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Driving License</span>
                                    </a>
                                </li>

                                @endif
                                @if ($helper_service->routes->has_custom_access('addon_department', ['rta_card']))
                                <li class="kt-menu__item @if($dept_param === 'rta_card') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.addons.view','rta_card')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">RTA Card</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                <li class="kt-menu__section ">
                    <h4 class="kt-menu__section-text">Accounts</h4>
                    <i class="kt-menu__section-icon flaticon-more-v2"></i>
                </li>

                @if ($helper_service->routes->has_access('tenant.admin.ledger.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'tenant.admin.ledger') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('tenant.admin.ledger.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-newspaper-o"></i>
                            <span class="kt-menu__link-text">Daily Ledger</span>
                        </a>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('module.accounts.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'module.accounts') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('module.accounts.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-dollar"></i>
                            <span class="kt-menu__link-text">Accounts</span>
                        </a>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('tenant.admin.invoices.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'tenant.admin.invoices') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('tenant.admin.invoices.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-ioxhost"></i>
                            <span class="kt-menu__link-text">Invoices</span>
                        </a>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('accounts.transaction.receivables'))
                    <li class="kt-menu__item @if (strpos($route_name, 'accounts.transaction.receivables') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('accounts.transaction.receivables') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-angle-double-down"></i>
                            <span class="kt-menu__link-text">Receivable</span>
                        </a>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('accounts.transaction.pending'))
                    <li class="kt-menu__item @if (strpos($route_name, 'accounts.transaction.pending') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('accounts.transaction.pending') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-angle-double-up"></i>
                            <span class="kt-menu__link-text">Payables & Cheques</span>
                        </a>
                    </li>
                @endif


                @if (
                    $helper_service->routes->has_access('tenant.admin.imports.statement_ledger') ||
                    $helper_service->routes->has_access('tenant.admin.imports.transaction_ledgers') ||
                    $helper_service->routes->has_access('tenant.admin.imports.incomes') ||
                    $helper_service->routes->has_access('tenant.admin.imports.vehicle_bills') ||
                    $helper_service->routes->has_access('tenant.admin.imports.simbills') ||
                    $helper_service->routes->has_access('tenant.admin.imports.installments')
                )
                <li class="kt-menu__item  kt-menu__item--submenu @if(strpos($route_name, "tenant.admin.imports.") !==false) kt-menu__item--open @endif" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <i class="kt-menu__link-icon la la-bolt"></i>
                        <span class="kt-menu__link-text">Imports</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Imports</span>
                                </span>
                            </li>
                            @if ($helper_service->routes->has_access('tenant.admin.imports.statement_ledger'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.statement_ledger") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.statement_ledger')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Statement Legder</span>
                                </a>
                            </li>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.imports.incomes'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.incomes") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.incomes')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Income</span>
                                </a>
                            </li>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.imports.vehicle_bills'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.vehicle_bills") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.vehicle_bills')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Vehicle Bills</span>
                                </a>
                            </li>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.imports.transaction_ledgers'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.transaction_ledgers") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.transaction_ledgers')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Transaction Ledger</span>
                                </a>
                            </li>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.imports.installments'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.installments") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.installments')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Installments</span>
                                </a>
                            </li>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.imports.simbills'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.simbills") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.simbills')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Sim Bills</span>
                                </a>
                            </li>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.imports.sims'))
                            <li class="kt-menu__item @if(strpos($route_name, "tenant.admin.imports.sims") !==false) kt-menu__item--active @endif" aria-haspopup="true">
                                <a href="{{route('tenant.admin.imports.sims')}}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Sims</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if ($helper_service->routes->has_access('tenant.admin.installment.view'))
                    <li class="kt-menu__item kt-menu__item--submenu @if (strpos($route_name, 'tenant.admin.installment.') !== false) kt-menu__item--open @endif" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                        <a href="javascript:;" class="kt-menu__link  kt-menu__toggle">
                            <i class="kt-menu__link-icon flaticon2-copy"></i>
                            <span class="kt-menu__link-text">Installments</span>
                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </a>

                        <div class="kt-menu__submenu ">
                            <span class="kt-menu__arrow"></span>
                            <ul class="kt-menu__subnav">
                                <li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true">
                                    <span class="kt-menu__link">
                                        <span class="kt-menu__link-text">Installments</span>
                                    </span>
                                </li>
                                <li class="kt-menu__item @if(request()->route()->type === 'all') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.installment.view','all')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">All</span>
                                    </a>
                                </li>
                                <li class="kt-menu__item @if(request()->route()->type === 'charged') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.installment.view', 'charged')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Charged</span>
                                    </a>
                                </li>
                                <li class="kt-menu__item @if(request()->route()->type === 'pending') kt-menu__item--active @endif" aria-haspopup="true">
                                    <a href="{{route('tenant.admin.installment.view', 'pending')}}" class="kt-menu__link ">
                                        <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="kt-menu__link-text">Pending</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('tenant.admin.employee.ledger.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'tenant.admin.employee.ledger') !== false) kt-menu__item--open @endif"
                        aria-haspopup="true">
                        <a href="{{ route('tenant.admin.employee.ledger.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon flaticon2-graphic"></i>
                            <span class="kt-menu__link-text">Employee Ledger</span>
                        </a>
                    </li>
                @endif

                <li class="kt-menu__section ">
                    <h4 class="kt-menu__section-text">Settings</h4>
                    <i class="kt-menu__section-icon flaticon-more-v2"></i>
                </li>

                @if ($helper_service->routes->has_access('tenant.admin.addons.setting.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'tenant.admin.addons.setting.view') !== false) kt-menu__item--open @endif" aria-haspopup="true">
                        <a href="{{ route('tenant.admin.addons.setting.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-gears"></i>
                            <span class="kt-menu__link-text">Addon Settings</span>
                        </a>
                    </li>
                @endif

                @if ($helper_service->routes->has_access('tenant.admin.statementledger.groups.view'))
                    <li class="kt-menu__item @if (strpos($route_name, 'tenant.admin.statementledger.groups.view') !== false) kt-menu__item--open @endif" aria-haspopup="true">
                        <a href="{{ route('tenant.admin.statementledger.groups.view') }}" class="kt-menu__link ">
                            <i class="kt-menu__link-icon la la-gears"></i>
                            <span class="kt-menu__link-text">Statement Groups</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <!-- end:: Aside Menu -->
</div>
