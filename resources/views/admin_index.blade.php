<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from demo.imanpa.ir/cork/go/rtl/demo1/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 18 Jul 2020 11:04:04 GMT -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('admin_c/assets/img/favicon.ico') }}" />
    <link href="{{ asset('admin_c/assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('admin_c/assets/js/loader.js') }}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('admin_c/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_c/assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('admin_c/plugins/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin_c/assets/css/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('admin_c/assets/js/libs/jquery-3.1.1.min.js') }}"></script>


    @yield('style')

</head>

<body>

    @php
        if (request()->is('admin/advertise/reports') or request()->is('admin/comment/reports')) {
            $menu_report = true;
        } else {
            $menu_report = false;
        }

        if (request()->is('admin/question/categories') or request()->is('admin/questions')) {
            $question_menu = true;
        } else {
            $question_menu = false;
        }

        if (request()->is('admin/blogs') or request()->is('admin/blog/categories')) {
            $blog_menu = true;
        } else {
            $blog_menu = false;
        }

        if (
            request()->is('admin/advertise/all') or
            request()->is('admin/comments') or
            request()->is('admin/advertise/packages') or
            request()->is('admin/advertise/reports') or
            request()->is('admin/comment/reports')
        ) {
            $adver_menu = true;
        } else {
            $adver_menu = false;
        }

    @endphp

    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm">

            <ul class="navbar-item theme-brand flex-row  text-center">
                <li class="nav-item theme-logo">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('files/other/images/logo.png') }}" class="navbar-logo" alt="logo">
                    </a>
                </li>
                <li class="nav-item theme-text">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link"> بچرخ </a>
                </li>
            </ul>


            <ul class="navbar-item flex-row ml-md-auto">

                <li class="nav-item dropdown user-profile-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <img src="{{ asset('files/other/images/logo.png') }}" alt="avatar">
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="">
                            <div class="dropdown-item">
                                <a href="{{ route('admin.logout') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    خروج</a>
                            </div>
                        </div>
                    </div>
                </li>

            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <div class="sub-header-container">
        <header class="header navbar navbar-expand-sm">
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-menu">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </a>

            <ul class="navbar-nav flex-row">
                <li>
                    <div class="page-header">

                        <nav class="breadcrumb-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">ادمین
                                        {{ auth('admin')->user()->name }}</a></li>
                                {{--                            <li class="breadcrumb-item active" aria-current="page"><span>فروش ها</span></li> --}}
                            </ol>
                        </nav>

                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">

            <nav id="sidebar">
                <div class="shadow-bottom"></div>
                <ul class="list-unstyled menu-categories" id="accordionExample">

                    <li class="menu">
                        <a href="#blog" data-active="{{ $blog_menu == 'true' ? 'true' : '' }}"
                            data-toggle="collapse" aria-expanded="{{ $blog_menu == 'true' ? 'show' : '' }}"
                            class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                <span>مقالات</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-chevron-right">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled @if ($blog_menu) show @endif"
                            id="blog" data-parent="#accordionExample">
                            <li class="{{ request()->is('admin/blogs') ? 'active' : '' }}">
                                <a href="{{ route('blog.all') }}">مدیریت</a>
                            </li>
                            <li class="{{ request()->is('admin/blog/comments') ? 'active' : '' }}">
                                <a href="{{ route('blog.comment.all') }}">نظرات</a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu">
                        <a href="{{ route('contact.list') }}"
                            aria-expanded="{{ request()->is('admin/contacts') ? 'true' : '' }}"
                            class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-target">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="6"></circle>
                                    <circle cx="12" cy="12" r="2"></circle>
                                </svg>
                                <span>تماس با ما</span>
                            </div>
                        </a>
                    </li>


                    <li class="menu">
                        <a href="{{ route('four.choice.list.admin') }}"
                            aria-expanded="{{ request()->is('admin/four-choice/list') ? 'true' : '' }}"
                            class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-target">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="6"></circle>
                                    <circle cx="12" cy="12" r="2"></circle>
                                </svg>
                                <span>چهارجوابی</span>
                            </div>
                        </a>
                    </li>


                </ul>
                <!-- <div class="shadow-bottom"></div> -->

            </nav>


        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="layout-top-spacing">

                    @if (session('success'))
                        <div>
                            <p class="alert alert-success text-center">{{ session('success') }}</p>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <p style="color: #000000">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-10 p-3" style="border: 8px solid #6b68ac;background-color: #04052f">
                            @yield('content')
                        </div>
                    </div>

                </div>

            </div>
            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class=""> © کپی رایت</p>
                </div>
            </div>
        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('admin_c/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('admin_c/bootstrap/js/bootstrap.min.js') }}"></script>


    @yield('script')

</body>

<!-- Mirrored from demo.imanpa.ir/cork/go/rtl/demo1/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 18 Jul 2020 11:04:32 GMT -->

</html>
