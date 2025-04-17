<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" rel="stylesheet"> --}}
    <!-- DataTables Responsive CSS -->
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- <script type="module" src="{{ asset('js/app.js') }}"></script> --}}
    <!-- DataTables CSS -->
    <style>
        .dropdown-menu {
            min-width: 150px;
        }

        .dropdown-item {
            font-size: 14px;
            font-weight: bold;
            padding: 8px 15px;
        }

        .dropdown-item:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div id="loading-screen">
        <div class="loading-spinner"></div>
        <p>Loading...</p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingScreen = document.getElementById('loading-screen');
            const loadingTimeout = setTimeout(() => {
                loadingScreen.classList.add('active');
            }, 300);
            window.addEventListener('load', function() {
                clearTimeout(loadingTimeout);
                loadingScreen.classList.remove('active');
                setTimeout(() => {
                    loadingScreen.remove();
                }, 300);
            });
        });
    </script>
    <main>
        @include('layouts.navigation')
        {{ $slot }}
    </main>

    <!-- الفوتر -->
    {{-- <footer class="bg-gray-900 text-white text-center py-2">
        © {{ date('Y') }} www.Nadim.pro | All rights reserved.
    </footer> --}}
    <script>
        // Get the sidebar, close button, and search button elements
        let sidebar = document.querySelector(".sidebar");
        let closeBtn = document.querySelector("#btn");
        // let searchBtn = document.querySelector(".bx-search");
        let navList = document.querySelector(".nav-list");
        // Event listener for the menu button to toggle the sidebar open/close
        closeBtn.addEventListener("click", () => {
            sidebar.classList.toggle("open"); // Toggle the sidebar's open state
            navList.classList.toggle("scroll"); // Toggle scroll state
            menuBtnChange(); // Call function to change button icon
        });
        // Event listener for the search button to open the sidebar
        // searchBtn.addEventListener("click", () => {
        //     sidebar.classList.toggle("open");
        //     navList.classList.toggle("scroll");
        //     menuBtnChange(); // Call function to change button icon
        // });
        // Function to change the menu button icon
        function menuBtnChange() {
            if (sidebar.classList.contains("open")) {
                closeBtn.classList.replace("bx-menu", "bx-menu-alt-right"); // Change icon to indicate closing
            } else {
                closeBtn.classList.replace("bx-menu-alt-right", "bx-menu"); // Change icon to indicate opening
            }
        }
        // دالة لتحويل التاريخ إلى تنسيق قابل للاستخدام في حقل datetime-local
        function formatDateForInput(date) {
            // تحويل التاريخ من التنسيق ISO 8601 إلى تنسيق 'yyyy-MM-ddThh:mm'
            var formattedDate = new Date(date);
            var year = formattedDate.getFullYear();
            var month = ('0' + (formattedDate.getMonth() + 1)).slice(-2); // إضافة صفر إذا كان الشهر أحادي الرقم
            var day = ('0' + formattedDate.getDate()).slice(-2); // إضافة صفر إذا كان اليوم أحادي الرقم
            var hours = ('0' + formattedDate.getHours()).slice(-2); // إضافة صفر إذا كان الوقت أحادي الرقم
            var minutes = ('0' + formattedDate.getMinutes()).slice(-2); // إضافة صفر إذا كانت الدقائق أحادية الرقم
            return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
        }
    </script>


    @livewireScripts
</body>

</html>
