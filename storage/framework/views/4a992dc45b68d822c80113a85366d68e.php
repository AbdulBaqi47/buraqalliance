<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Language" content="en">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>
        <?php if(trim($__env->yieldContent('page_title'))): ?>
            <?php echo $__env->yieldContent('page_title'); ?> | Administrator
        <?php else: ?>
        Buraqalliance | Administrator
        <?php endif; ?>
    </title>

    <meta name="description" content="We provide luxury transport. With a fleet of extravagant vehicles, professional chauffeur drivers, and impeccable service">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    
    <!--begin::Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    <!--end::Fonts -->

    <!--end::Page Vendors Styles -->

    <!--begin::Global Theme Styles(used by all pages) -->
    <link rel="stylesheet" href="<?php echo e(asset('theme/plugins/global/plugins.bundle.css')); ?>" type="text/css" />

    <link rel="stylesheet" href="<?php echo e(asset('theme/css/style.bundle.css')); ?>" type="text/css" />

    <link rel="stylesheet" href="<?php echo e(asset('theme/plugins/custom/uppy/uppy.bundle.css')); ?>" type="text/css" />



    <!--end::Global Theme Styles -->

    <!--begin::Layout Skins(used by all pages) -->
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/skins/header/base/navy.css')); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/skins/header/menu/light.css')); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/skins/brand/navy.css')); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/skins/aside/light.css')); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo e(asset('css/dataTables.bootstrap4.min.css')); ?>" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />

    <!--end::Layout Skins -->
    <link rel="shortcut icon" href="<?php echo e(asset('media/logo/Buraq-logo-inerse.png')); ?>" />


    
    <style>

        /* Height of topbar */
        @media (min-width: 1025px){
            .kt-aside__brand,
            .kt-header{
                height: 55px;
            }
            .kt-header--fixed .kt-page{
                padding-top: 55px;
            }
        }

        @media (max-width: 768px){
            .kt-portlet .kt-portlet__head{
                flex-direction: column;
                gap: 12px;
                justify-content: flex-start;
                align-items: flex-start;
            }
            .kt-portlet .kt-portlet__head .kt-portlet__head-label .kt-portlet__head-title{
                margin-top: 1rem;
            }
        }

        .kr-datepicker[data-state="range"] {
            min-width: 16rem;
        }

        .form-control.is-invalid, .was-validated .form-control:invalid{
            background-position: center right calc(0.375em + 0.325rem);
        }

        /* Select2 container border on invalid input */
        .form-control.is-invalid ~ .select2-container .select2-selection {
            border-color: #fd397a;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .select2-selection.select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
        }


        .select2-selection.select2-selection--single {
            height: 34px !important;
        }
        .select2-selection.select2-selection--single .select2-selection__arrow {
            height: 34px !important;
        }
        .select2-container *:focus {
            outline: none;
        }

        .btn.btn-icon.btn-xs {
            height: 1.7rem;
            width: 1.7rem;
        }

        .flex-gap-1{ gap: .2rem; }
        .flex-gap-2{ gap: .5rem; }
        .flex-gap-3{ gap: .7rem; }
        .flex-gap-4{ gap: 1rem; }

        .kr-description{
            font-size: 12px;
            color: #999;
            display: block;
            margin: 0;
            white-space: pre-line;
        }

        .modal.krajax-modal .close {
            outline: none !important;
            color: #74788d;
            font-family: "LineAwesome";
            text-decoration: inherit;
            text-rendering: optimizeLegibility;
            text-transform: none;
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            font-smoothing: antialiased;
            transition: all 0.3s;
            padding: 1.25rem;
            margin: -1rem -1rem -1rem auto;
        }

        .modal.krajax-modal .close span {display: none;}

        .modal.krajax-modal .close::before{
            content: "";
            font-size: 1.3rem;
        }
        @media (min-width:768px){
            .modal.krajax-modal .modal-dialog{
                max-width: var(--mwidth);
            }
        }
        @media (max-width:576px){
            .modal.krajax-modal [kr-ajax-closebtn]{
                top: 7px;
                position: absolute;
                right: 8px;
            }
        }


        body.modal-open{overflow: hidden!important;}
        #toast-container .toast {
            opacity: 1 !important;
        }
        .form-control:disabled, .form-control[readonly] {
            background-color: #f7f8fa;
            opacity: 1;
        }

        .kr-uppy .kt-uppy__list .kt-uppy__list-label{
            display: flex;
            align-items: center;
        }
        .btn-hover-link:hover{
            background-color: initial !important;
        }

        /* ------------------------------------------- */
        /* ------------       SWITCH         --------- */
        /* ------------------------------------------- */

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .switch .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .switch input:checked+.slider {
            background-color: #2196F3;
        }

        .switch input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        .switch input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .switch .slider.round {
            border-radius: 34px;
        }

        .switch .slider.round:before {
            border-radius: 50%;
        }

        /* ------------------------------------------- */
        /* ------------       /SWITCH         --------- */
        /* ------------------------------------------- */

        /* ------------------------------------------- */
        /* ------------   SKELETON LOADER    --------- */
        /* ------------------------------------------- */

        .kr-skeleton-box {
            display: inline-block;
            height: 1em;
            position: relative;
            overflow: hidden;
            background-color: #eee;
        }

        .kr-skeleton-box::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            background-image: linear-gradient(90deg, rgba(255, 255, 255, 0) 0, rgba(255, 255, 255, 0.2) 20%, rgba(255, 255, 255, 0.5) 60%, rgba(255, 255, 255, 0));
            -webkit-animation: shimmer 2s infinite;
            animation: shimmer 2s infinite;
            content: "";
        }

        @-webkit-keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }
        /* ------------------------------------------- */
        /* ------------  /SKELETON LOADER    --------- */
        /* ------------------------------------------- */


        .kr-ajax-modal__full{
            padding: 0 !important;
        }

        .kr-ajax-modal__full .modal-dialog {
            min-width: 100%;
            margin: 0;
            height: 100%;
        }

        .kr-ajax-modal__full  .modal-dialog .modal-content {
            border-radius: 0;
            min-height: 100%;
        }
        .krajax-modal.kr-ajax-modal__full [kr-ajax-inner-footer] {
            position: absolute;
            width: 100%;
            bottom: 0;
        }
        .krajax-modal.kr-ajax-modal__full [kr-ajax-inner-content] {
            padding-bottom:80px !important;
        }

        .bootstrap-select .bootstrap-selector-noborder{
            border-radius:0 !important;
        }
        .krselect2--animation{
            box-shadow: 0px 0px 5px 0px #1edd3f;
            -webkit-transition: box-shadow 300ms cubic-bezier(0, 0, 0.34, 1.36);
            -ms-transition: box-shadow 300ms cubic-bezier(0, 0, 0.34, 1.36);
            transition: box-shadow 300ms cubic-bezier(0, 0, 0.34, 1.36);
        }

        /* ------------------------------------------- */
        /* ------------     TAGGER WIDGET    --------- */
        /* ------------------------------------------- */

        .kr-widget__tagger {
            position:relative;
        }
        .kr-widget__tagger > div{
            font-size: 1.75rem;
        }
        .kr-widget__tagger > div > span{
            padding-left: 1.5rem;
            color:#000;
        }
        .kr-widget__tagger > div > small{
            display: block;
            padding-left: 1.35rem;
            letter-spacing: 1px;
            margin-left: 5px;
            color: #6e8ba8;
            margin-top: -4px;
            text-transform: uppercase;
            font-size: .95rem;
            line-height: .8;
        }
        .kr-widget__tagger--danger > div:before {
            background: #fd397a;
        }
        .kr-widget__tagger--success > div:before {
            background: #1dc9b7;
        }
        .kr-widget__tagger--warning > div:before {
            background: #ffb822;
        }
        .kr-widget__tagger--primary > div:before {
            background: #5867dd;
        }
        .kr-widget__tagger--info > div:before {
            background: #5578eb;
        }
        .kr-widget__tagger > div:before {
            position: absolute;
            display: block;
            width: 0.3rem;
            height: 100%;
            top: 0.3rem;
            height: calc(100% - 0.3rem);
            content: "";
        }
        /* ------------------------------------------- */
        /* ------------    /TAGGER WIDGET    --------- */
        /* ------------------------------------------- */


        /* ------------------------------------------- */
        /* ------------    Animations    ------------- */
        /* ------------------------------------------- */
        .kr-animate {
            -webkit-animation-direction: alternate;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-delay: 1.1s;
            -webkit-animation-duration: 1s;
            -moz-animation-duration: 1s;
            -o-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            -moz-animation-fill-mode: both;
            -o-animation-fill-mode: both;
            animation-fill-mode: both;
            -webkit-animation-name: pulse;
            -moz-animation-name: pulse;
            -o-animation-name: pulse;
            animation-name: pulse;
        }
        /* ------------------------------------------- */
        /* ------------    /Animations    ------------ */
        /* ------------------------------------------- */

        .kr-aside-badge{
            position:absolute;
            right: 5%;
            width: 35px;
            border-radius: 15px !important;
        }
        @-webkit-keyframes pulse {
            0% { -webkit-transform: scale(1); }
            50% { -webkit-transform: scale(1.2); }
            100% { -webkit-transform: scale(1); }
        }
        @-moz-keyframes pulse {
            0% { -moz-transform: scale(1); }
            50% { -moz-transform: scale(1.2); }
            100% { -moz-transform: scale(1); }
        }
        @-o-keyframes pulse {
            0% { -o-transform: scale(1); }
            50% { -o-transform: scale(1.2); }
            100% { -o-transform: scale(1); }
        }
        @keyframes  pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .noti_circle{
            position: absolute;
            left: -3px;
            top: 0px;
            z-index: 1;
            background: #cc2311;
            border: .5px solid #FFF;
            border-radius: 50%;
            padding-top: 5px;
            height: 12px;
            width: 12px;
            font-family: sans-serif;
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            line-height: 2px;
            color: #FFF;
            -webkit-animation-direction: alternate;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-delay: 1.1s;
            -webkit-animation-duration: 1s;
            -moz-animation-duration: 1s;
            -o-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            -moz-animation-fill-mode: both;
            -o-animation-fill-mode: both;
            animation-fill-mode: both;
            -webkit-animation-name: pulse;
            -moz-animation-name: pulse;
            -o-animation-name: pulse;
            animation-name: pulse;
        }

    </style>

    
    <?php $__env->startSection('head'); ?>

	<?php echo $__env->yieldSection(); ?>


</head>
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-aside-secondary--enabled kt-page--loading <?php if($helper_service->helper->getConfig()->sidebar!=1): ?> kt-aside--minimize <?php endif; ?>" >
    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
        <div class="kt-header-mobile__logo">
            <a href="<?php echo e(url('/')); ?>" class="text-white h5 mb-0">
                Buraqalliance
            </a>
        </div>
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toolbar-toggler kt-header-mobile__toolbar-toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
            
            <button class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
        </div>
    </div>
    <!-- end:: Header Mobile -->

    <!-- begin:: Root -->
    <div class="kt-grid kt-grid--hor kt-grid--root">

        <!-- begin:: Page -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

            <!-- begin:: Aside -->
            <?php echo $__env->make('Central.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <!-- end:: Aside -->

            <!-- begin:: Wrapper -->
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                <!-- begin:: Header -->
                <?php echo $__env->make('Central.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <!-- end:: Header -->


                <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor m-0" id="kt_content">

                    <!-- begin:: Content -->
                    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                        <?php echo $__env->make('Central.includes.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>

            </div>

            <!-- end:: Wrapper -->
        </div>

        <!-- end:: Page -->
    </div>


    <button id="log-activity-action" kr-ajax-block-page-when-processing kr-ajax-modalclosed="kingriders.Plugins.LOG_ACTIVITY.close_modal" kr-ajax-size="40%" kr-ajax-contentloaded="kingriders.Plugins.LOG_ACTIVITY.content_loaded" hidden kr-ajax="#"></button>


    <!-- end:: Root -->

    <!-- begin::Global Config(global config for global JS sciprts) -->
    <script>
        var KTAppOptions = {
            "colors": {
                "state": {
                    "brand": "#5d78ff",
                    "metal": "#c4c5d6",
                    "light": "#ffffff",
                    "accent": "#00c5dc",
                    "primary": "#5867dd",
                    "success": "#34bfa3",
                    "info": "#36a3f7",
                    "warning": "#ffb822",
                    "danger": "#fd3995",
                    "focus": "#9816f4"
                },
                "base": {
                    "label": [
                        "#c5cbe3",
                        "#a1a8c3",
                        "#3d4465",
                        "#3e4466"
                    ],
                    "shape": [
                        "#f0f3ff",
                        "#d9dffa",
                        "#afb4d4",
                        "#646c9a"
                    ]
                }
            }
        };
    </script>

    <!-- end::Global Config -->

    <!--begin::Global Theme Bundle(used by all pages) -->
    <script src="<?php echo e(asset('theme/plugins/global/plugins.bundle.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('theme/js/scripts.bundle.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('theme/plugins/custom/uppy/uppy.bundle.js')); ?>" type="text/javascript"></script>

    <script src="<?php echo e(asset('js/jquery.dataTables.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap4.min.js')); ?>" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <?php $__env->startSection('critical_foot'); ?>

	<?php echo $__env->yieldSection(); ?>
    <!--end::Global Theme Bundle -->

    
    <script type="text/javascript">
        var dateFormat = function () {
            var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
                timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
                timezoneClip = /[^-+\dA-Z]/g,
                pad = function (val, len) {
                    val = String(val);
                    len = len || 2;
                    while (val.length < len) val = "0" + val;
                    return val;
                };

            // Regexes and supporting functions are cached through closure
            return function (date, mask, utc) {
                var dF = dateFormat;

                // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
                if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
                    mask = date;
                    date = undefined;
                }

                // Passing date through Date applies Date.parse, if necessary
                date = date ? new Date(date) : new Date;
                if (isNaN(date)) throw SyntaxError("invalid date");

                mask = String(dF.masks[mask] || mask || dF.masks["default"]);

                // Allow setting the utc argument via the mask
                if (mask.slice(0, 4) == "UTC:") {
                    mask = mask.slice(4);
                    utc = true;
                }

                var _ = utc ? "getUTC" : "get",
                    d = date[_ + "Date"](),
                    D = date[_ + "Day"](),
                    m = date[_ + "Month"](),
                    y = date[_ + "FullYear"](),
                    H = date[_ + "Hours"](),
                    M = date[_ + "Minutes"](),
                    s = date[_ + "Seconds"](),
                    L = date[_ + "Milliseconds"](),
                    o = utc ? 0 : date.getTimezoneOffset(),
                    flags = {
                        d: d,
                        dd: pad(d),
                        ddd: dF.i18n.dayNames[D],
                        dddd: dF.i18n.dayNames[D + 7],
                        m: m + 1,
                        mm: pad(m + 1),
                        mmm: dF.i18n.monthNames[m],
                        mmmm: dF.i18n.monthNames[m + 12],
                        yy: String(y).slice(2),
                        yyyy: y,
                        h: H % 12 || 12,
                        hh: pad(H % 12 || 12),
                        H: H,
                        HH: pad(H),
                        M: M,
                        MM: pad(M),
                        s: s,
                        ss: pad(s),
                        l: pad(L, 3),
                        L: pad(L > 99 ? Math.round(L / 10) : L),
                        t: H < 12 ? "a" : "p",
                        tt: H < 12 ? "am" : "pm",
                        T: H < 12 ? "A" : "P",
                        TT: H < 12 ? "AM" : "PM",
                        Z: utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                        o: (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                        S: ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                    };

                return mask.replace(token, function ($0) {
                    return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
                });
            };
        }();
        // Some common format strings
        dateFormat.masks = {
            "default": "ddd mmm dd yyyy HH:MM:ss",
            shortDate: "m/d/yy",
            mediumDate: "mmm d, yyyy",
            longDate: "mmmm d, yyyy",
            fullDate: "dddd, mmmm d, yyyy",
            shortTime: "h:MM TT",
            mediumTime: "h:MM:ss TT",
            longTime: "h:MM:ss TT Z",
            isoDate: "yyyy-mm-dd",
            isoTime: "HH:MM:ss",
            isoDateTime: "yyyy-mm-dd'T'HH:MM:ss",
            isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
        };

        // Internationalization strings
        dateFormat.i18n = {
            dayNames: [
                "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
                "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
            ],
            monthNames: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
            ]
        };

        // For convenience...
        Date.prototype.format = function (mask, utc) {
            return dateFormat(this, mask, utc);
        };



        function toDate(dStr, format) {
            var now = new Date();
            if (format == "h:m") {
                now.setHours(dStr.substr(0, dStr.indexOf(":")));
                now.setMinutes(dStr.substr(dStr.indexOf(":") + 1));
                now.setSeconds(0);
                return now;
            } else if (format == "h:m:s") {
                now.setHours(dStr.split(':')[0]);
                now.setMinutes(dStr.split(':')[1]);
                now.setSeconds(dStr.split(':')[2]);
                return now;
            } else
                return "Invalid Format";
        }
        String.prototype.toDate = function (format) {
            return toDate(this, format);
        };
        String.prototype.toRound = function (decimal_val) {
            var _num = this;
            if (/^([-]{0,1}\d*[.]{0,1}\d+)$/.test(_num)) {
                return parseFloat((parseFloat(_num)).toFixed(decimal_val));
            }
            throw new Error("Invalid Number");
        }
        Number.prototype.toRound = function (decimal_val) {
            var _num = this;
            return parseFloat((parseFloat(_num)).toFixed(decimal_val));
        }
        Element.prototype.trigger = function (type) {
            var elem = this;
            var event = document.createEvent("MouseEvents");
            event.initMouseEvent(type, true, true, elem.ownerDocument.defaultView,
                0, 0, 0, 0, 0, false, false, false, false, 0, null);
            elem.dispatchEvent(event);
        }
    </script>

    
    <script id="handlebars-krajaxmodal" type="text/x-handlebars-template">
        <div id="{{id}}" class="modal krajax-modal p-0 {{modal_type}}" data-title="{{page_title}}" data-url="{{url}}" kr-index="{{index}}">
            <div class="modal-dialog modal-dialog-centered" style="--mwidth: {{modal_size}};">
                <div class="modal-content">
                    {{{content}}}
                </div>
            </div>
        </div>
    </script>

    <script type="text/javascript">
        console.log("localStorage.KR_DEBUG="+localStorage.KR_DEBUG+" %c[use localStorage.KR_DEBUG=true to show logs]", "background: #eee; color: #000;font-weight:bold");
        /*****************************************************************************
                                    GLOBAL OBJECT FOR SETTING
        *****************************************************************************/

        var kingriders={
            Data:{
                user:<?php echo Auth::user(); ?>

            },
            Config:{
                storage_path: "<?php echo e($helper_service->helper->storage_basepath()); ?>"
            },
            Utils:{

                // Checks if string is valid URL
                isValidUrl(string) {
                    let url;

                    try {
                        url = new URL(string);
                    } catch (_) {
                        return false;
                    }

                    return url.protocol === "http:" || url.protocol === "https:";
                },

                // Restricts input for the given textbox to the given inputFilter.
                setInputFilter(textbox, inputFilter, errMsg) {
                    [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
                        textbox.addEventListener(event, function(e) {
                        if (inputFilter(this.value)) {
                            // Accepted value.
                            if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0) {
                                this.classList.remove("is-invalid");
                                this.setCustomValidity("");
                            }

                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        }
                        else if (this.hasOwnProperty("oldValue")) {
                            // Rejected value: restore the previous one.
                            this.classList.add("is-invalid");
                            this.setCustomValidity(errMsg);
                            this.reportValidity();
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        }
                        else {
                            // Rejected value: nothing to restore.
                            this.value = "";
                        }
                        });
                    });
                },
                capitalizeFirstLetter(string){
                    return string.charAt(0).toUpperCase() + string.slice(1);
                },
                setCookie:function(cname, cvalue, exdays){
                    var d = new Date();
                    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                    var expires = "expires="+d.toUTCString();
                    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
                },
                getCookie:function(cname){
                    var name = cname + "=";
                    var ca = document.cookie.split(';');
                    for(var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                        }
                    }
                    return "";
                },
                isObjectEmpty:function(obj){
                    for(var key in obj) {
                        if(obj.hasOwnProperty(key))
                            return false;
                    }
                    return true;
                },
                formdata_to_object:function(data){

                    let method = function (object,pair) {

                        let keys = pair[0].replace(/\]/g,'').split('[');
                        let key = keys[0];
                        let value = pair[1];

                        if (keys.length > 1) {

                            let i,x,segment;
                            let last = value;
                            let type = isNaN(keys[1]) ? {} : [];

                            value = segment = object[key] || type;

                            for (i = 1; i < keys.length; i++) {

                                x = keys[i];

                                if (i == keys.length-1) {
                                    if (Array.isArray(segment)) {
                                        segment.push(last);
                                    } else {
                                        segment[x] = last;
                                    }
                                } else if (segment[x] == undefined) {
                                    segment[x] = isNaN(keys[i+1]) ? {} : [];
                                }

                                segment = segment[x];

                            }

                        }

                        object[key] = value;

                        return object;

                    }

                    let object = Array.from(data).reduce(method,{});

                    return object;
                },
                time_conversion:function(){
                    /*
                    * Require 2 data-attributes
                    *   1) data-utc-to-local="{Time you want to convert}"
                    *   2) data-local-format="{Format you want to convert in}"
                    * -----Example--------
                    * data-utc-to-local="April 25, 2020 12:38:45 PM"
                    * data-local-format="mmmm dd, yyyy hh:MM:ss TT"
                    */
                    document.querySelectorAll('[data-utc-to-local]').forEach(function(elem){
                        var _time = elem.getAttribute('data-utc-to-local');
                        if(_time !== ""){
                            var _format = elem.getAttribute('data-local-format');
                            var _converted = new Date(new Date(_time).format('yyyy-mm-dd HH:MM:ss')+' UTC').format(_format)

                            elem.textContent=_converted;
                            elem.value=_converted;
                            elem.removeAttribute('data-utc-to-local');
                            elem.removeAttribute('data-local-format');
                        }
                    });
                },
                buildQueryString: function(array){
                    if (history.pushState) {
                        var str = '';
                        Object.keys(array).forEach(function(x, i){
                            str+=str==''?x+'='+array[x]:'&'+x+'='+array[x]
                        });
                        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?'+str;
                        window.history.pushState({path:newurl},'',newurl);
                    }
                },
                fetchQueryString:function(name) {
                    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                    var results = regex.exec(location.search);
                    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
                },
                isDebug:function(){
                    if(localStorage.getItem("KR_DEBUG") == true || localStorage.getItem("KR_DEBUG") == "true"){
                        return true;
                    }
                    return false;
                },
                updateUrlParem:function(param, paramVal){
                    var url = window.location.href;
                    var newAdditionalURL = "";
                    var tempArray = url.split("?");
                    var baseURL = tempArray[0];
                    var additionalURL = tempArray[1];
                    var temp = "";
                    if (additionalURL) {
                        tempArray = additionalURL.split("&");
                        for (var i=0; i<tempArray.length; i++){
                            if(tempArray[i].split('=')[0] != param){
                                newAdditionalURL += temp + tempArray[i];
                                temp = "&";
                            }
                        }
                    }

                    var rows_txt = temp + "" + param + "=" + paramVal;
                    var updated_url = baseURL + "?" + newAdditionalURL + rows_txt;

                    /* Update the url */
                    window.history.pushState({path:updated_url},'',updated_url);

                    kingriders.Plugins.KR_AJAX.ModalOnAir._refreshBaseUrl();
                    return updated_url;
                },
                getUrlParem:function(name) {
                    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                    var results = regex.exec(location.search);
                    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
                },
                replaceUrl:function(title=null, url=null){
                    if (window.history.replaceState && url) {
                        //prevents browser from storing history with each change:
                        window.history.replaceState(null, title, url);

                        document.title = title;
                    }


                },

                copyTextToClipboard: function(text){
                    return new Promise(function(resolve, reject){
                        var textArea = document.createElement("textarea");

                        //
                        // *** This styling is an extra step which is likely not required. ***
                        //
                        // Why is it here? To ensure:
                        // 1. the element is able to have focus and selection.
                        // 2. if the element was to flash render it has minimal visual impact.
                        // 3. less flakyness with selection and copying which **might** occur if
                        //    the textarea element is not visible.
                        //
                        // The likelihood is the element won't even render, not even a
                        // flash, so some of these are just precautions. However in
                        // Internet Explorer the element is visible whilst the popup
                        // box asking the user for permission for the web page to
                        // copy to the clipboard.
                        //

                        // Place in the top-left corner of screen regardless of scroll position.
                        textArea.style.position = 'fixed';
                        textArea.style.top = 0;
                        textArea.style.left = 0;

                        // Ensure it has a small width and height. Setting to 1px / 1em
                        // doesn't work as this gives a negative w/h on some browsers.
                        textArea.style.width = '2em';
                        textArea.style.height = '2em';

                        // We don't need padding, reducing the size if it does flash render.
                        textArea.style.padding = 0;

                        // Clean up any borders.
                        textArea.style.border = 'none';
                        textArea.style.outline = 'none';
                        textArea.style.boxShadow = 'none';

                        // Avoid flash of the white box if rendered for any reason.
                        textArea.style.background = 'transparent';


                        textArea.value = text;

                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();

                        try {
                            var successful = document.execCommand('copy');
                            var msg = successful ? 'successful' : 'unsuccessful';
                            resolve();
                        } catch (err) {
                            reject(err);
                        }

                        document.body.removeChild(textArea);
                    })
                },

                /* This will handle page settings for each user, like user collapse the sidebar, next time it will be collapsed by default */
                ConfigManager:{
                    getConfig:function(){
                        /* Get the already data from config */
                        var config = kingriders.Utils.getCookie("KRCONFIG")||null;
                        if(!config){
                            /* Save the default values */
                            config={
                                sidebar:1,  // Sidebar will be open
                                clientdetails_sidebar:1, // On client details page, sidebar will be open
                            };
                        }
                        else{
                            /* Convert to json */
                            config=JSON.parse(config);
                        }

                        return config;
                    },
                    setConfig:function(config){
                        /* Save config for 30 days, if config don't change in 30 days, it will be reset */
                        kingriders.Utils.setCookie("KRCONFIG", JSON.stringify(config), 30);
                    },
                    setSideBarState:function(isShow=true){
                        /* Get the already data from config */
                        var self = this;
                        var config = self.getConfig();

                        /* Set the sidebar state */
                        config.sidebar = isShow?1:0;

                        /* Update config */
                        self.setConfig(config);
                    },
                    setClientDetailSideBarState:function(isShow=true){
                        /* Get the already data from config */
                        var self = this;
                        var config = self.getConfig();

                        /* Set the sidebar state */
                        config.clientdetails_sidebar = isShow?1:0;

                        /* Update config */
                        self.setConfig(config);
                    }
                }
            },
            Plugins:{
                Selectors:{
                    select2:'.kr-select2',
                    datepicker:'.kr-datepicker',
                    bootstrap_selectpicker:'.kr-bootstrapselect',
                    kr_ajax:'[kr-ajax]', /* specifiy a kr-ajax="{url}" */
                    kr_ajax_content:'[kr-ajax-content]', /* remote page ajax cotnent wrapper, we will fetch the html within */
                    kr_ajax_head:'[kr-ajax-head]', /* all the scripts and styles we need to include too */
                    kr_ajax_preload:'[kr-ajax-preload]', /* used to preload the html before the click happens */
                    kr_ajax_closebtn:'[kr-ajax-closebtn]' /* will append a close button to this selector */
                },
                update_select2:function(elem){
                    /* reinit select2 */
                    var is_dynamic = elem.hasAttribute('data-dynamic');

                    var has_source = elem.hasAttribute('data-source');
                    var has_template = elem.hasAttribute('data-template');

                    var placeholder = elem.hasAttribute('data-placeholder')?elem.getAttribute('data-placeholder'):'Select an option';
                    var width = elem.hasAttribute('data-width')?elem.getAttribute('data-width'):'100%';
                    var config = {
                        placeholder: placeholder,
                        width:width,
                        dropdownAutoWidth: true
                    };
                    if(is_dynamic){
                        config.tags=true;
                        var createTagCallback = elem.hasAttribute('data-createtag')?elem.getAttribute('data-createtag'):null;
                        var insertTagCallback = elem.hasAttribute('data-inserttag')?elem.getAttribute('data-inserttag'):null;

                        if(createTagCallback)config.createTag=eval(createTagCallback);
                        if(insertTagCallback)config.insertTag=eval(insertTagCallback);
                    }
                    if(has_source){
                        config.data = eval(elem.getAttribute('data-source'));
                        var selectedOp = $(elem).find(':selected');
                        if(selectedOp.length > 0){
                            var dataSelectedIndex = config.data.findIndex(function(item){
                                return item.id === selectedOp.val()
                            });
                            if(dataSelectedIndex > -1){
                                config.data[dataSelectedIndex].selected = true
                            }

                        }
                        $(elem).empty(); // SO UPDATED DATA-SOURCE CAN BE APPLIED
                    }
                    if(has_template){
                        config.templateResult = eval(elem.getAttribute('data-template'));
                    }
                    $(elem).select2(config);
                },
                refresh_plugins:function(){
                    /* for select2 init */
                    $(this.Selectors.select2).each(function(){
                        var is_dynamic = this.hasAttribute('data-dynamic');

                        var has_source = this.hasAttribute('data-source');
                        var has_template = this.hasAttribute('data-template');

                        var placeholder = this.hasAttribute('data-placeholder')?this.getAttribute('data-placeholder'):'Select an option';
                        var width = this.hasAttribute('data-width')?this.getAttribute('data-width'):'100%';
                        var config = {
                            placeholder: placeholder,
                            width:width,
                            dropdownAutoWidth: true
                        };
                        if(is_dynamic){
                            config.tags=true;
                            var createTagCallback = this.hasAttribute('data-createtag')?this.getAttribute('data-createtag'):null;
                            var insertTagCallback = this.hasAttribute('data-inserttag')?this.getAttribute('data-inserttag'):null;

                            if(createTagCallback)config.createTag=eval(createTagCallback);
                            if(insertTagCallback)config.insertTag=eval(insertTagCallback);
                        }
                        if(has_source){
                            config.data = eval(this.getAttribute('data-source'));
                            // $(this).empty(); // SO UPDATED DATA-SOURCE CAN BE APPLIED
                        }
                        if(has_template){
                            config.templateResult = eval(this.getAttribute('data-template'));
                        }
                        if(!$(this).hasClass("select2-hidden-accessible")){

                            $(this).select2(config);
                        }
                    });

                    /* for datepicker/monthpicker init*/
                    $(this.Selectors.datepicker).each(function(){
                        var state = $(this).attr('data-state');
                        var format = state=='month'?'mmmm yyyy':'mmmm dd, yyyy';

                        /* destroy the picker if already initialized */
                        if($(this).hasClass('kr-datepicker--init') && state === 'range')return true;

                        if($(this).hasClass('kr-datepicker--init'))$(this).datepicker('destroy');

                        /*set default date*/
                        var defaultdate = null;
                        if(this.hasAttribute('data-default')){
                            defaultdate = this.getAttribute('data-default') || null;
                            if(defaultdate && defaultdate !== "") defaultdate=new Date(defaultdate).format(format);
                        }
                        else{
                            defaultdate=new Date(Date.now()).format(format);
                        }

                        if(defaultdate && defaultdate !== ""){
                            $(this).val(defaultdate);
                        }

                        if(!this.hasAttribute('placeholder')){

                            // Set default placeholder
                            this.setAttribute('placeholder', state === "month" ? "Select month" : "Select date");
                        }

                        /*Set picker*/
                        if(state=='month'){
                            $(this).datepicker({
                                format: "MM yyyy",
                                minViewMode: 1,
                                maxViewMode: 2,
                                autoclose: true,
                                todayHighlight: true
                            }).on('hide', function(e) {
                                /* workaround to stop the event from propagating to the bootstrap modal (so hide event don't call) */
                                e.stopPropagation();
                            }).on('changeDate', function(e) {
                                // Change input val too
                                e.currentTarget.setAttribute('data-default', e.date.format('yyyy-mm-dd'));
                            });
                        }
                        else if(state=='range'){

                            $(this).daterangepicker({
                                buttonClasses: 'btn btn-sm',
                                applyClass: "btn-primary",
                                cancelClass: "btn-secondary",

                                startDate: moment().subtract(6, 'days'),
                                endDate: moment(),

                                ranges: {
                                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                                    'All': [moment().subtract(20, 'year').startOf('year'), moment().add(20, 'year').endOf('year')],
                                },

                                locale: {
                                    format: 'MMM DD, YYYY'
                                }
                            });
                        }
                        else{
                            $(this).datepicker({
                                format: "MM dd, yyyy",
                                minViewMode: 0,
                                maxViewMode: 2,
                                autoclose: true,
                                todayHighlight: true
                            }).on('hide', function(e) {
                                /* workaround to stop the event from propagating to the bootstrap modal (so hide event don't call) */
                                e.stopPropagation();
                            }).on('changeDate', function(e) {
                                // Change input val too
                                e.currentTarget.setAttribute('data-default', e.date.format('yyyy-mm-dd'));
                            });
                        }

                        $(this).addClass('kr-datepicker--init');
                    });

                    /* for bootstrap select */
                    $(this.Selectors.bootstrap_selectpicker).selectpicker();

                    /* for uppy file upload */
                    kingriders.Plugins.uppy.init();


                    // lazy loads elements with default selector as '.lozad'
                    lozad().observe();

                    // Popovers [data-toggle="kt-popover"]
                    KTApp.initPopovers();

                    // [type=number] to [type=text] and add eventlisterner
                    $('input[type=number]').each(function(){
                        var regex = /^-?\d*[.,]?\d{0,3}$/;
                        var msg = "Must be a valid number with at most 3 decimal places";
                        if(this.hasAttribute('data-no-decimal')){
                            regex = /^\d*$/;
                            msg = "Must be a valid number with no decimal places";
                        }

                        kingriders.Utils.setInputFilter(this, function(value) {
                            return regex.test(value);
                        }, msg);

                        this.setAttribute('type', 'text');
                    });

                    // [datepicker] - remove readonly and disable typing
                    // so form's validation works on them
                    var dateEls = document.querySelectorAll(this.Selectors.datepicker);
                    if(dateEls.length > 0){
                        dateEls.forEach(function(el){
                            el.readOnly = false;
                            el.autocomplete = 'off';

                            el.addEventListener('keypress', function(e){
                                e.preventDefault();
                            })
                        })
                    }


                },
                KR_AJAX:{
                    /*
                    <button type="button" kr-ajax-autohide="1" kr-ajax-size="30%" kr-ajax-modalclosed="PARTINVOICE_MODULE.supplier_module.modal_closed" kr-ajax-submit="PARTINVOICE_MODULE.supplier_module.form_submit" kr-ajax-contentloaded="PARTINVOICE_MODULE.supplier_module.form_loaded" kr-ajax-preload kr-ajax="{\{route('central.admin.parts.supplier.add')}\}" class="btn btn-info btn-elevate btn-square">
                        <i class="flaticon2-plus-1"></i>
                        Create supplier
                    </button>
                    */
                    cache:{
                        enabled:true,
                    },
                    queue:[],
                    count:1,
                    reference:[],
                    processing:false,
                    onclick:function(e, additional){
                        e.preventDefault(e);

                        /* check if preload request, is yes, we need to do everything in silence */
                        var is_preload = false;
                        if( typeof additional !== "undefined" && additional ){
                            if( typeof additional.preload !== "undefined" ) is_preload=additional.preload;
                        }

                        var contentCallback = this.hasAttribute('kr-ajax-contentloaded')?this.getAttribute('kr-ajax-contentloaded'):null;
                        contentCallback && (contentCallback=eval(contentCallback));

                        /* check if model is laoded already, we will skip adding in the queue */
                        var modal_init = this.hasAttribute('data-target');
                        if(modal_init){
                            contentCallback(this);
                            return;
                        }

                        var self=this;

                        /* check if ajax is already in processing */
                        var in_processing = this.hasAttribute('kr-ajax-processing');
                        if(in_processing){
                            $(this).removeAttr('kr-ajax-preload');
                            KTApp.block(self,{
                                opacity:.2,
                                size:"sm",
                                type: 'v2',
                                state: 'primary',
                            });
                            return;
                        }


                        /* check if element is already pushed in queue, don't push it again */
                        var already_has = kingriders.Plugins.KR_AJAX.queue.findIndex(function(x){return x.is(self)});
                        if(already_has>-1)kingriders.Plugins.KR_AJAX.queue.splice(already_has, 1);

                        /* if preload, we will append this to end of queue, so first come, first serve */
                        if(is_preload)kingriders.Plugins.KR_AJAX.queue.unshift($(this));
                        else {
                            kingriders.Plugins.KR_AJAX.queue.push($(self)); /* else, we gave priority to element beacause if may be direct clicked */
                            $(self).removeAttr('kr-ajax-preload');

                            KTApp.block(self,{
                                opacity:.2,
                                size:"sm",
                                type: 'v2',
                                state: 'primary',
                            });
                        }

                        /* finally, handle the queue of ajax */
                        kingriders.Plugins.KR_AJAX.handle();
                    },
                    showErrors:function(jqXHR, permission_err=false){

                        if(permission_err){
                            var msg='Permission denied';
                            /* we need to show a toast of permission denied */
                            if(typeof jqXHR.responseText !== "undefined" && jqXHR.responseText != ""){
                                msg+=' for route '+jqXHR.responseText;
                            }
                            /* show alert */
                            toastr.error(msg, "Forbidden");

                            return;
                        }
                        var msg ='Unable to create.';
                        var title='Oops...';
                        if(typeof jqXHR.responseJSON !== "undefined" && typeof jqXHR.responseJSON.message !== "undefined"){
                            msg=jqXHR.responseJSON.message;

                            /* need to check if request has errors */
                            if(typeof jqXHR.responseJSON.errors !== "undefined" && jqXHR.responseJSON.errors){
                                var strs='';
                                title=jqXHR.responseJSON.message;
                                Object.keys(jqXHR.responseJSON.errors).forEach(function(key, i){
                                    if(i!=0)strs+='<br/>';
                                    strs+='<strong>'+(key.charAt(0).toUpperCase()+key.slice(1)).replace(/_/,' ')+':</strong> '+jqXHR.responseJSON.errors[key]
                                });
                                msg=strs;
                            }
                        }

                        /* show the alert */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: title,
                            html: msg,
                        });
                    },
                    // Just like showErrors only it will return response instad of showing errors
                    generateErrors:function(jqXHR, permission_err=false){

                        if(permission_err){
                            var msg='Permission denied';
                            /* we need to show a toast of permission denied */
                            if(typeof jqXHR.responseText !== "undefined" && jqXHR.responseText != ""){
                                msg+=' for route '+jqXHR.responseText;
                            }
                            /* show alert */
                            toastr.error(msg, "Forbidden");

                            return;
                        }
                        var msg ='Unable to create.';
                        var title='Oops...';
                        if(typeof jqXHR.responseJSON !== "undefined" && typeof jqXHR.responseJSON.message !== "undefined"){
                            msg=jqXHR.responseJSON.message;

                            /* need to check if request has errors */
                            if(typeof jqXHR.responseJSON.errors !== "undefined" && jqXHR.responseJSON.errors){
                                var strs='';
                                title=jqXHR.responseJSON.message;
                                Object.keys(jqXHR.responseJSON.errors).forEach(function(key, i){
                                    if(i!=0)strs+='<br/>';
                                    strs+='<strong>'+(key.charAt(0).toUpperCase()+key.slice(1)).replace(/_/,' ')+':</strong> '+jqXHR.responseJSON.errors[key]
                                });
                                msg=strs;
                            }
                        }

                        return {
                            title: title,
                            msg: msg
                        }
                    },
                    handle:function(){
                        /* check if ajax isn't already in process */
                        if(kingriders.Plugins.KR_AJAX.processing)return;
                        /* check if anyhting is in queue */
                        if(kingriders.Plugins.KR_AJAX.queue.length){
                            /* get the first element insertted */
                            /* Queue should be consist of jQuery elements */
                            var button = kingriders.Plugins.KR_AJAX.queue.pop();
                            var $this = button[0]; /* javascript element */

                            button.attr('kr-ajax-processing', 1); /* so if clicked multiple times we can neget it */

                            /* set state in processing so it won't interuppted */
                            kingriders.Plugins.KR_AJAX.processing=true;

                            var cache = kingriders.Plugins.KR_AJAX.cache;
                            var ajax_content_selector = kingriders.Plugins.Selectors.kr_ajax_content;
                            var ajax_head_selector = kingriders.Plugins.Selectors.kr_ajax_head;
                            var ajax_closebtn_selector = kingriders.Plugins.Selectors.kr_ajax_closebtn;

                            /* get needed payload */
                            var is_preload = $this.hasAttribute('kr-ajax-preload');
                            var url = $($this).attr('kr-ajax');
                            var modal_init = $this.hasAttribute('data-target');
                            var formCallback = $this.hasAttribute('kr-ajax-submit')?$this.getAttribute('kr-ajax-submit'):null;
                            formCallback && (formCallback=eval(formCallback));

                            var contentCallback = $this.hasAttribute('kr-ajax-contentloaded')?$this.getAttribute('kr-ajax-contentloaded'):null;
                            contentCallback && (contentCallback=eval(contentCallback));

                            if(!contentCallback){
                                /* we need to show error, because $this is required to manupulate data */
                                kingriders.Utils.isDebug() && console.error("Undefined function: contentCallback", "$this is required function called after content has been loaded, please spacify a kr-ajax-contentloaded on kr-ajax module");
                            }

                            var modal_size = $this.hasAttribute('kr-ajax-size')?$this.getAttribute('kr-ajax-size'):'90%';
                            var modal_type = $this.hasAttribute('kr-ajax-modal-type')?$this.getAttribute('kr-ajax-modal-type'):'';

                            var auto_hide = $this.hasAttribute('kr-ajax-autohide')?$this.getAttribute('kr-ajax-autohide'):1;

                            var modalclosedCallback = $this.hasAttribute('kr-ajax-modalclosed')?$this.getAttribute('kr-ajax-modalclosed'):null;
                            modalclosedCallback && (modalclosedCallback=eval(modalclosedCallback));

                            var modalclosingCallback = $this.hasAttribute('kr-ajax-modalclosing')?$this.getAttribute('kr-ajax-modalclosing'):null;
                            modalclosingCallback && (modalclosingCallback=eval(modalclosingCallback));


                            var blockPage = $this.hasAttribute('kr-ajax-block-page-when-processing');


                            if(!modal_init){
                                /* we need to create a dynamic modal and link it with $this button */

                                /* Reference link */
                                var index = kingriders.Plugins.KR_AJAX.count;

                                /*check if button has link*/
                                var btnRef= $this.hasAttribute('kr-ajax-ref')?$this.getAttribute('kr-ajax-ref'):null;
                                if(btnRef){
                                    index = parseInt(btnRef)||kingriders.Plugins.KR_AJAX.count;
                                }

                                var modal_id = 'kr_modal--'+index;

                                /* add ref to button */
                                button.attr('kr-ajax-ref', index);

                                /* we need to send ajax for that */
                                $.ajax({
                                    url : url,
                                    type : 'GET',
                                    dataType: 'html',
                                    cache: false,
                                    beforeSend: function() {
                                        /* add ref to button */
                                        button.attr('kr-ajax-processing', 1);

                                        if(blockPage){
                                            KTApp.blockPage({
                                                overlayColor: '#000',
                                                type: 'v2',
                                                state: 'primary',
                                                message: 'Please wait while data is processing...'
                                            });
                                        }

                                    },
                                    complete: function(){
                                        kingriders.Plugins.KR_AJAX.processing=false;
                                        button.removeAttr('kr-ajax-processing');
                                        KTApp.unblock($this);
                                        if(blockPage)KTApp.unblockPage();
                                        contentCallback($this);

                                        /* now its time to handle next request (if any) */
                                        kingriders.Plugins.KR_AJAX.handle();
                                    },
                                })
                                .done(function (response) {
                                    var html = $(response);

                                    var MODAL = $('#'+modal_id);

                                    var page_title = html.filter('title').text().trim();

                                    /* remove modal if already found */
                                    if(MODAL.length){
                                        /* remove the modal and its data like scripts adn things */
                                        var modal_index = MODAL.attr('kr-index');
                                        $('[kr-index="'+modal_index+'"]').remove();
                                    }

                                    /* resolvig html, scripts, styles */
                                    var content_staging1 = html.find(ajax_content_selector).wrap('<p/>').parent().html();
                                    content_staging1 = $('<div />',{html:content_staging1});

                                    /* append the cross button */
                                    content_staging1.find(ajax_closebtn_selector).append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>');

                                    /* final content after menupulating html */
                                    var content = content_staging1.html();

                                    var scripts_styles = html.find(ajax_head_selector);
                                    if(scripts_styles.length==0) scripts_styles = html.filter(ajax_head_selector);

                                    /* generating html based on data */
                                    var template = $('#handlebars-krajaxmodal').html();

                                    // Compile the template data into a function
                                    var templateScript = Handlebars.compile(template);


                                    var context = {
                                        id:modal_id,
                                        index: index,
                                        content:content,
                                        modal_size:modal_size,
                                        modal_type: modal_type=="full"?'kr-ajax-modal__full':'',
                                        url:url,
                                        page_title:page_title
                                    }

                                    var modal_html = templateScript(context);

                                    /* appending html to modal, and scripts and styles to body */
                                    $('body')
                                    .append(modal_html);

                                    scripts_styles.each(function(i, elem){
                                        this.setAttribute('kr-index', index);
                                        $('body').append(this);
                                    });

                                    /* attaching modal to button */
                                    button.attr('data-toggle', 'modal').attr('data-target', '#'+modal_id);

                                    kingriders.Plugins.KR_AJAX.count++;


                                    MODAL = $('#'+modal_id);

                                    var events_attached=[];


                                    /* If full modal, add perfect scrollable to body */
                                    if( modal_type=="full" ){
                                        /* this will attach an event for modal opening for once, so once event is triggered, event will be detack */
                                        MODAL.one('shown.bs.modal',function(){

                                            var scroll_container = MODAL.find('[kr-ajax-inner-content]');
                                            if(scroll_container.length){

                                                /* Init the scroller and adjust the height */
                                                KTUtil.scrollInit(scroll_container[0], {
                                                    mobileNativeScroll: true,
                                                    resetHeightOnDestroy: true,
                                                    handleWindowResize: true,
                                                    height: function () {
                                                        var height;

                                                        height = KTUtil.getViewPort().height;

                                                        if(MODAL.find('[kr-ajax-inner-header]').length){
                                                            height -= MODAL.find('[kr-ajax-inner-header]').outerHeight();
                                                        }

                                                        if(MODAL.find('[kr-ajax-inner-footer]').length){
                                                            height -= MODAL.find('[kr-ajax-inner-footer]').outerHeight();
                                                        }


                                                        return height;
                                                    }
                                                });

                                            }
                                        });

                                        /* We also attach event everytime modal will be shown, need to reset scrolltop */

                                        MODAL.on('shown.bs.modal',function(e){
                                            var scroll_container = MODAL.find('[kr-ajax-inner-content]');
                                            if(scroll_container.length) scroll_container.scrollTop(0);

                                            /* Reset scroller */
                                            var scroll_container = MODAL.find('[kr-ajax-inner-content]');
                                            if(scroll_container.length){
                                                KTUtil.scrollUpdate(scroll_container[0]);
                                            }

                                            /* Update url */
                                            kingriders.Plugins.KR_AJAX.ModalOnAir.do_url($(this));

                                        });
                                        events_attached.push('shown');



                                    }

                                    if(events_attached.indexOf('shown')==-1){
                                        /* Seems current model isn't full type, attach an event */
                                        MODAL.on('shown.bs.modal',function(e){

                                            /* Update url */
                                            kingriders.Plugins.KR_AJAX.ModalOnAir.do_url($(this));

                                        });
                                    }

                                    MODAL.on('hidden.bs.modal',function(){
                                        /* Update url */

                                        /* Check if any other modal is shown, we need to update utl of that modal */
                                        var visible_modal = $($('.krajax-modal:visible').get(-1));
                                        if(visible_modal.length){
                                            kingriders.Plugins.KR_AJAX.ModalOnAir.do_url(visible_modal);

                                        }
                                        else{
                                            /* Revert the url */
                                            kingriders.Plugins.KR_AJAX.ModalOnAir.undo_url();
                                        }

                                    });

                                    /* SHOW MODAL */
                                    is_preload = button[0].hasAttribute('kr-ajax-preload');
                                    !is_preload && MODAL.modal('show');

                                    /* refresh the plugins so all selec2, datepicker can be initialized wihin the modal */
                                    kingriders.Plugins.refresh_plugins();

                                    /* attach the onsubmit event to form within modal, if defined */

                                    MODAL.find('form').off('submit').on('submit', function(e){
                                        e.preventDefault();
                                        if(auto_hide==1){
                                            /* add attribute that this model is hid programically */
                                            MODAL.attr('data-sys-hide', '');
                                            MODAL.modal('hide');
                                        }
                                        var form = this;
                                        var _url = $(form).attr('action');

                                        /* disabled the submit button */
                                        $(form).find('[type=submit]').prop('disabled', true);

                                        var unique_id=new Date().getTime();

                                        /* present the data to callback */
                                        if(formCallback && typeof formCallback == "function"){
                                            var formDataObj=kingriders.Utils.formdata_to_object(new FormData(form));
                                            var result = {
                                                response:formDataObj,
                                                modal:MODAL,
                                                state:'beforeSend',
                                                linker:unique_id
                                            };
                                            formCallback(result);
                                        }

                                        $.ajax({
                                            url : _url,
                                            type : 'POST',
                                            data: new FormData(form),
                                            contentType: false,
                                            cache: false,
                                            processData:false,
                                            complete:function(){
                                                /* enabled the submit button */
                                                $(form).find('[type=submit]').prop('disabled', false);
                                            }
                                        })
                                        .done(function (response) {
                                            if(formCallback && typeof formCallback == "function"){
                                                var result = {
                                                    response:response,
                                                    modal:MODAL,
                                                    state:'completed',
                                                    linker:unique_id
                                                };
                                                formCallback(result);
                                            }
                                        })
                                        .fail(function (jqXHR, textStatus, errorThrown) {
                                            kingriders.Utils.isDebug() && console.error("kr_ajax_submit", jqXHR);

                                            /* this will handle & show errors */
                                            var is_permission_err = false;
                                            if(jqXHR.status == 403){
                                                /* we should show alerts on preload ajax requests */
                                                is_permission_err = true;
                                            }

                                            kingriders.Plugins.KR_AJAX.showErrors(jqXHR, is_permission_err);

                                            /* sends an error response */
                                            if(formCallback && typeof formCallback == "function"){
                                                var result = {
                                                    response:null,
                                                    modal:MODAL,
                                                    state:'error',
                                                    linker:unique_id
                                                };
                                                formCallback(result);
                                            }
                                        });
                                    });

                                    /* attach close event to modal (if any) */
                                    if(modalclosedCallback){
                                        MODAL.off('hidden.bs.modal.krajax').on('hidden.bs.modal.krajax',modalclosedCallback);
                                    }
                                    if(modalclosingCallback){
                                        MODAL.off('hide.bs.modal.krajax').on('hide.bs.modal.krajax',modalclosingCallback);
                                    }


                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    kingriders.Utils.isDebug() && console.error("kr_ajax", jqXHR);

                                    /* this will handle & show errors */
                                    if(jqXHR.status == 403){
                                        /* we should show alerts on preload ajax requests */
                                        is_preload = button[0].hasAttribute('kr-ajax-preload');
                                        !is_preload && kingriders.Plugins.KR_AJAX.showErrors(jqXHR, true);
                                    }
                                });

                            }
                            else contentCallback($this);
                        }
                    },
                    resetModal: function(index){
                        /* remove modal html */
                        $('[kr-index='+index+']').remove();

                        /* Remove Attributes from buttons */
                        $('[kr-ajax][data-target="#kr_modal--'+index+'"]')
                        .removeAttr('kr-ajax-ref')
                        .removeAttr('data-toggle')
                        .removeAttr('kr-ajax-processing')
                        .removeAttr('data-target');
                    },
                    ModalOnAir:function(){

                        /* Base data, when page is refreshed */
                        var BASE_URL=window.location.href;
                        var BASE_TITLE = document.title;

                        var _UpdateAttribute=function(param){
                            /* Update attributes like data-url & data-title, so  when we update the url, things will go accordingly */
                            if(param.modal){
                                if(param.url)param.modal.attr('data-url', param.url);
                                if(param.title)param.modal.attr('data-title', param.title);
                            }
                        }

                        var _UpdateUrl=function(modal){
                            /* Update the url */

                            var title = modal.attr('data-title')||null;
                            var url = modal.attr('data-url')||null;

                            /* Update url */
                            kingriders.Utils.replaceUrl(title, url);
                        }

                        var _ResetUrl=function(){
                            /* Restore url to base */
                            kingriders.Utils.replaceUrl(BASE_TITLE, BASE_URL);
                        }

                        return {
                            /* Will use by each pages to add custom urls (mostly on edit/view pages) */
                            update:_UpdateAttribute,

                            /* These fucntion will use by KRAJAX module only */
                            do_url:_UpdateUrl,
                            undo_url:_ResetUrl,

                            _refreshBaseUrl:function(){
                                BASE_URL=window.location.href;
                            }
                        };
                    }()
                },
                uppy:function(){


                    return {
                        selector:'.kr-uppy',
                        uppyInstances:[],
                        _DataTransfer:function(){
                            return class _DataTransfer {
                                constructor() {
                                    return new ClipboardEvent("").clipboardData || new DataTransfer();
                                }
                            }
                        }(),
                        files_to_form:function(uppy, form, input_name){

                            var files = uppy.getFiles();

                            var uppyFileID = "uppy_fileinput-"+uppy.getID();


                            /* remove all files */
                            $('input[type=file].kr-uppy-file[data-uppy-id="'+uppyFileID+'"]').remove();

                            /* append files to form */
                            files.forEach(function(file,index){

                                var fileData = null;
                                if(file.data instanceof File){
                                    fileData = file.data;
                                }
                                else{
                                    // File type might be Blob, we need to make a file instance out of that
                                    fileData = new File([file.data], file.name, {
                                        type: file.type,
                                        lastModified: new Date(),
                                    });
                                }


                                /* attach uppy to data_transfer */
                                var $dt=new kingriders.Plugins.uppy._DataTransfer();
                                /* attach the file to datatransfer and appedn to file input */
                                $dt.items.add(fileData);

                                var input = document.createElement('input');
                                input.type="file";
                                input.setAttribute('data-id', file.id);
                                input.setAttribute('data-uppy-id', uppyFileID);
                                input.className="kr-uppy-file";
                                input.name = input_name;
                                input.hidden=true;
                                input.files = $dt.files;

                                form.prepend(input);
                            });

                        },
                        addFile: function(ID, path, fileName = 'image.jpg'){
                            const self = this;

                            return new Promise(function(resolve, reject){
                                if(!(path && path !== ''))return resolve();

                                var uppy = self.uppyInstances.find(function(x){return x.getID() === ID});
                                if(typeof uppy !== "undefined" && uppy){
                                    var urlToObjects= async()=> {
                                        let formData = new FormData();
                                        formData.append('url', path);
                                        const payload = {
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            method: "post",
                                            body: formData
                                        }
                                        const response = await fetch("/admin/files/generate", payload);

                                        const blob = await response.blob();
                                        const file = new File([blob], fileName, {type: blob.type});
                                        uppy.addFile({
                                            name: fileName,
                                            type: blob.type,
                                            data: blob
                                        });

                                        return resolve();
                                    }
                                    return urlToObjects()
                                }

                                return reject();

                            });
                        },
                        getInstance: function(ID){
                            var uppy = this.uppyInstances.find(function(x){return x.getID() === ID});
                            if(typeof uppy !== "undefined" && uppy)return uppy;
                            return null;
                        },
                        removeInstance: function(ID){
                            var newInstances = this.uppyInstances.filter(function(x){return x.getID() !== ID});
                            this.uppyInstances = newInstances;
                        },
                        init:function(){

                            // const Tus = Uppy.Tus;
                            var ProgressBar = Uppy.ProgressBar;
                            var StatusBar = Uppy.StatusBar;
                            var FileInput = Uppy.FileInput;
                            var Informer = Uppy.Informer;
                            var ThumbnailGenerator = Uppy.ThumbnailGenerator;
                            var Form = Uppy.Form;

                            var self=this;

                            /* loop through each uppy instance and init */
                            $(this.selector).each(function(index, elem){
                                /* check if uppy isn't alraedy initialized */
                                var $this=$(this);
                                if($this.find('.uppy-Root').length==0){

                                    var form = $this.parents('form');

                                    /* add required divs */
                                    $this.html('<div class="kt-uppy__wrapper"></div><div class="kt-uppy__list"></div><div class="kt-uppy__informer kt-uppy__informer--min"></div>');

                                    /* get config data */
                                    var max_file_size = this.hasAttribute('uppy-size')?parseInt(this.getAttribute('uppy-size'))||1:1;
                                    var max_files = this.hasAttribute('uppy-max')?parseInt(this.getAttribute('uppy-max'))||1:1;
                                    var min_files = this.hasAttribute('uppy-min')?parseInt(this.getAttribute('uppy-min'))||1:1;
                                    var allowed_filetypes = this.hasAttribute('uppy-filetypes')?this.getAttribute('uppy-filetypes'):null;
                                    var is_multiple = this.hasAttribute('uppy-multiple');
                                    if(max_files>1)is_multiple=true;
                                    var label = this.hasAttribute('uppy-label')?this.getAttribute('uppy-label'):"Attach File";
                                    var input_name = this.hasAttribute('uppy-input')?this.getAttribute('uppy-input'):"file";
                                    is_multiple && (input_name+="[]");

                                    /* generate an id and attach to this element */

                                    var elemId = 'kr_uppy_'+index;
                                    $this.attr('id', elemId); /* attach id to this element */

                                    var id = '#' + elemId;
                                    var $uploadedList = $(id + ' .kt-uppy__list');


                                    /* initialize uppy */

                                    var restrictions = {
                                        maxFileSize: max_file_size*1000000, // 1mb
                                        maxNumberOfFiles: max_files,
                                        minNumberOfFiles: min_files,
                                    }

                                    if(allowed_filetypes && allowed_filetypes !== ""){
                                        allowed_filetypes = allowed_filetypes.split('|');
                                        restrictions.allowedFileTypes = allowed_filetypes;
                                    }

                                    // check if uppy instance already find, remove it first
                                    var ins = kingriders.Plugins.uppy.getInstance(elemId);
                                    if(typeof ins !== "undefined" && ins){
                                        kingriders.Plugins.uppy.removeInstance(elemId);
                                    }

                                    var uppyMin = Uppy.Core({
                                        id:elemId,
                                        debug: true,
                                        autoProceed: false,
                                        allowMultipleUploads:is_multiple,
                                        restrictions: restrictions
                                    });

                                    /* So in child view we can fetch it */
                                    self.uppyInstances.push(uppyMin);

                                    uppyMin.use(Form, {
                                        target: '.kt-form'
                                    });
                                    uppyMin.use(FileInput, { target: id + ' .kt-uppy__wrapper', pretty: false });
                                    uppyMin.use(Informer, { target: id + ' .kt-uppy__informer'  });
                                    uppyMin.use(ThumbnailGenerator, {
                                        id: elemId+'_ThumbnailGenerator',
                                        thumbnailWidth: 50,
                                        // thumbnailHeight: 200,
                                        // thumbnailType: 'image/jpeg',
                                        waitForThumbnailsBeforeUpload: false
                                    });


                                    // demo file upload server

                                    $(id + ' .uppy-FileInput-input').addClass('kt-uppy__input-control').attr('id', elemId + '_input_control');
                                    $(id + ' .uppy-FileInput-container').append('<label class="kt-uppy__input-label btn btn-label-brand btn-bold btn-font-sm" for="' + (elemId + '_input_control') + '">'+label+'</label>');

                                    var $fileLabel = $(id + ' .kt-uppy__input-label');
                                    var $fileInput = $(id + ' .kt-uppy__input-control');

                                    uppyMin.on('file-added', function(file) {
                                        var sizeLabel = "bytes";
                                        var filesize = file.size;
                                        if (filesize > 1024){
                                            filesize = filesize / 1024;
                                            sizeLabel = "kb";

                                            if(filesize > 1024){
                                                filesize = filesize / 1024;
                                                sizeLabel = "MB";
                                            }
                                        }
                                        var uploadListHtml = ''+
                                        '<div class="kt-uppy__list-item" data-id="'+file.id+'">'+
                                        '   <div class="kt-uppy__list-label">'+
                                        '       <div class="uppy-Dashboard-Item-preview">'+
                                        '           <div class="uppy-Dashboard-Item-previewInnerWrap kt-uppy__list-img-preview" style="background-color: rgb(206 206 206);">'+
                                        '               <div style="height: 70px;width: 45px;"></div>'+
                                        '           </div>'+
                                        '       </div>'+
                                        '       <span class="ml-2 text-break">'+file.name+' ('+ Math.round(filesize, 2) +' '+sizeLabel+')</span>'+
                                        '   </div>'+
                                        '   <span class="kt-uppy__list-remove" data-id="'+file.id+'"><i class="flaticon2-cancel-music"></i></span>'+
                                        '</div>';
                                        $uploadedList.append(uploadListHtml);

                                        /* append the files in form accordingly */
                                        kingriders.Plugins.uppy.files_to_form(uppyMin, form, input_name);

                                    });

                                    uppyMin.on('thumbnail:generated', (file, preview) => {
                                        var fileId = file.id;

                                        /* find row from upload list and append preview image */
                                        var img_container=$uploadedList.find('.kt-uppy__list-item[data-id="'+file.id+'"]');
                                        if(img_container.length){
                                            img_container.find('.kt-uppy__list-img-preview').html('<img class="uppy-Dashboard-Item-previewImg" src="'+preview+'">');
                                        }

                                    });

                                    $(document).on('click.uppy', id + ' .kt-uppy__list .kt-uppy__list-remove', function(){
                                        var itemId = $(this).attr('data-id');
                                        uppyMin.removeFile(itemId);

                                        /* append the files in form accordingly */
                                        kingriders.Plugins.uppy.files_to_form(uppyMin, form, input_name);

                                        $(id + ' .kt-uppy__list-item[data-id="'+itemId+'"').remove();
                                    });
                                }
                            });
                        }
                    }
                }(),

                // Log Activity
                LOG_ACTIVITY:{
                    close_modal: function(e){
                        let target = e.target;

                        if(target){
                            /* Get the index of modal */
                            let index = parseFloat(target.getAttribute('kr-index'))||null;
                            if(index){
                                /* Reset the modal */
                                kingriders.Plugins.KR_AJAX.resetModal(index);
                            }
                        }
                    },

                    content_loaded: function(){

                        setTimeout(function() {
                            $('.kt-log_activity .submit_log').trigger('click');
                        }, 300);

                    }
                }
            },
        };

        /*****************************************************************************
                                    GLOBAL SCRIPTS
        *****************************************************************************/
        $(function(){

            /* store local time in cookie so we can get it from server any time */
            var localUtcOffset = moment().utcOffset();
            var timecookie = kingriders.Utils.getCookie('localUtcOffset');
            if(timecookie=="")kingriders.Utils.setCookie('localUtcOffset', localUtcOffset, 1);

            /* [PopOver] Disable sanitizing these elements */
            $.fn.popover.Constructor.Default.whiteList.table = [];
            $.fn.popover.Constructor.Default.whiteList.tr = [];
            $.fn.popover.Constructor.Default.whiteList.td = [];
            $.fn.popover.Constructor.Default.whiteList.th = [];
            $.fn.popover.Constructor.Default.whiteList.div = [];
            $.fn.popover.Constructor.Default.whiteList.tbody = [];
            $.fn.popover.Constructor.Default.whiteList.thead = [];

            /* this will update the plugins like select2, datepickers*/
            kingriders.Plugins.refresh_plugins();


            /* global Settings */
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            /* dynamic ajax html loading (best for new pages) */
            $(document).on('click.krevent', kingriders.Plugins.Selectors.kr_ajax, kingriders.Plugins.KR_AJAX.onclick);

            /* preload the kr-ajax module */
            setTimeout(function(){
                $(kingriders.Plugins.Selectors.kr_ajax+''+kingriders.Plugins.Selectors.kr_ajax_preload).each(function(i, elem){
                    /* initiate the ajax */
                    // kingriders.Plugins.KR_AJAX.queue.push(this);
                    $(this).trigger('click.krevent', {
                        preload:true
                    });
                });
            },100);

            /* usefull handlebars helpers */
            Handlebars.registerHelper("x", function(expression, options) {
                var result;

                // you can change the context, or merge it with options.data, options.hash
                var context = this;

                // yup, i use 'with' here to expose the context's properties as block variables
                // you don't need to do {{x 'this.age + 2'}}
                // but you can also do {{x 'age + 2'}}
                // HOWEVER including an UNINITIALIZED var in a expression will return undefined as the result.
                with(context) {
                    result = (function() {
                    try {
                        return eval(expression);
                    } catch (e) {
                        kingriders.Utils.isDebug() && console.warn('•Expression: {{x \'' + expression + '\'}}\n•JS-Error: ', e, '\n•Context: ', context);
                    }
                    }).call(context); // to make eval's lexical this=context
                }
                return result;
            });

            Handlebars.registerHelper("xif", function(expression, options) {
                return Handlebars.helpers["x"].apply(this, [expression, options]) ? options.fn(this) : options.inverse(this);
            });
            Handlebars.registerHelper("inc", function(value, options)
            {
                return parseInt(value) + 1;
            });
            Handlebars.registerHelper("formatdate", function(datetime, format)
            {
                return moment(datetime).format(format);
            });

            /* Plugin Settings */
            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: true,
                progressBar: true,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                timeOut: "5000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            };

            /* handle model within model in single page */
            $(document).on('shown.bs.modal', '.krajax-modal', function(e){
                var index = $('.krajax-modal.show').index(this);
                var baseIndex = 1050;
                var modalIndex = baseIndex+index;
                var backdropIndex = modalIndex-1;
                $(this).css('z-index', modalIndex);
                $('.modal-backdrop.show:not(.fade)').eq(index).css('z-index', backdropIndex);
            });

            $(document).on('hidden.bs.modal', '.krajax-modal', function(e){
                /* need to check if some other modal is already open */
                if($('.krajax-modal:visible').length) $('body').addClass('modal-open');
                else $('body').removeClass('modal-open');
            });


            /* Fetching App-Setting */

        });

    </script>


    <!--begin::Page Scripts(used by this page) -->
    <?php $__env->startSection('foot'); ?>

	<?php echo $__env->yieldSection(); ?>

    <!--end::Page Scripts -->

    <script>
        (function(){
            if(typeof KINGVIEW === "undefined"){
                /* Seems page was not loaded in OnAir, store current url */
                localStorage.OnAirUrl = window.location.href;
            }
        })();
    </script>


</body>
</html>
<?php /**PATH C:\Users\DELL 5300\Documents\buraqalliance\resources\views/Central/layouts/app.blade.php ENDPATH**/ ?>