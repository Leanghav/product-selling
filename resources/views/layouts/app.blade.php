<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Product Selling System')
    </title>


    {{-- Bootstrap 5 --}}

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    {{-- Bootstrap Icons --}}

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;

            width: 250px;
            height: 100vh;

            background: #212529;

            color: white;

            overflow-y: auto;

            z-index: 1000;
        }


        .sidebar-logo {

            padding: 22px 20px;

            font-size: 21px;

            font-weight: bold;

            border-bottom: 1px solid rgba(255,255,255,.1);

        }


        .sidebar-logo i {

            margin-right: 8px;

        }


        .menu-title {

            padding: 18px 20px 8px;

            font-size: 11px;

            text-transform: uppercase;

            color: #adb5bd;

            letter-spacing: 1px;

        }


        .sidebar a {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 11px 20px;

            margin: 2px 10px;

            border-radius: 6px;

            color: #ced4da;

            text-decoration: none;

            transition: .2s;

        }


        .sidebar a i {

            font-size: 17px;

            width: 20px;

        }


        .sidebar a:hover {

            background: #343a40;

            color: white;

        }


        .sidebar a.active {

            background: #0d6efd;

            color: white;

        }


        /* =========================
           MAIN
        ========================= */

        .main {

            margin-left: 250px;

            min-height: 100vh;

        }


        /* =========================
           TOPBAR
        ========================= */

        .topbar {

            height: 65px;

            background: white;

            border-bottom: 1px solid #dee2e6;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 25px;

        }


        .topbar-title {

            font-size: 18px;

            font-weight: 600;

        }


        /* =========================
           CONTENT
        ========================= */

        .content {

            padding: 25px;

        }


        /* =========================
           CARD
        ========================= */

        .card {

            border: none;

            border-radius: 10px;

        }


        /* =========================
           TABLE
        ========================= */

        .table {

            margin-bottom: 0;

        }


        .table th {

            white-space: nowrap;

        }


        /* =========================
           FORM
        ========================= */

        .form-control,
        .form-select {

            min-height: 42px;

        }


        textarea.form-control {

            min-height: 100px;

        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 768px) {

            .sidebar {

                width: 70px;

            }


            .sidebar-logo span,
            .sidebar a span,
            .menu-title {

                display: none;

            }


            .sidebar a {

                justify-content: center;

                padding: 12px;

            }


            .sidebar a i {

                margin: 0;

            }


            .main {

                margin-left: 70px;

            }


            .content {

                padding: 15px;

            }

        }

    </style>

</head>


<body>


{{-- =========================
     SIDEBAR
========================= --}}

<div class="sidebar">


    <div class="sidebar-logo">

        <i class="bi bi-shop"></i>

        <span>
            Product Selling
        </span>

    </div>


    <div class="menu-title">
        Main
    </div>


    {{-- Dashboard --}}

    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

        <i class="bi bi-speedometer2"></i>

        <span>
            Dashboard
        </span>

    </a>


    <div class="menu-title">
        Management
    </div>


    {{-- Categories --}}

    <a href="{{ route('categories.index') }}"
       class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">

        <i class="bi bi-tags"></i>

        <span>
            Categories
        </span>

    </a>


    {{-- Products --}}

    <a href="{{ route('products.index') }}"
       class="{{ request()->routeIs('products.*') ? 'active' : '' }}">

        <i class="bi bi-box-seam"></i>

        <span>
            Products
        </span>

    </a>


    {{-- Customers --}}

    <a href="{{ route('customers.index') }}"
       class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">

        <i class="bi bi-people"></i>

        <span>
            Customers
        </span>

    </a>


    {{-- Suppliers --}}

    <a href="{{ route('suppliers.index') }}"
       class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">

        <i class="bi bi-truck"></i>

        <span>
            Suppliers
        </span>

    </a>


    <div class="menu-title">
        Sales
    </div>


   {{-- POS --}}
    <a href="{{ route('sales.create') }}">

        <i class="bi bi-cart3"></i>

        <span>
            POS / Sales
        </span>

    </a>



    <!-- {{-- Invoices --}}
    <a href="{{ route('sales.index') }}">

        <i class="bi bi-receipt"></i>

        <span>
            Invoices
        </span>

    </a> -->

    {{-- Invoices --}}
    <a href="{{ route('invoices.index') }}">
        <i class="bi bi-receipt"></i>
        <span>Invoices</span>
    </a>


    <div class="menu-title">
        Purchase
    </div>


    {{-- Purchases --}}

    <a href="{{ route('purchases.index') }}">
        <i class="bi bi-bag-check"></i>
        <span>Purchases</span>
    </a>


    {{-- Stock --}}

    <a href="{{ route('stock-movements.index') }}">
        <i class="bi bi-arrow-left-right"></i>
        <span>Stock Movements</span>
    </a>


    <div class="menu-title">
        Finance
    </div>


    {{-- Expenses --}}

    <a href="#">

        <i class="bi bi-cash-stack"></i>

        <span>
            Expenses
        </span>

    </a>


    {{-- Payments --}}

    <a href="#">

        <i class="bi bi-credit-card"></i>

        <span>
            Payments
        </span>

    </a>


    <div class="menu-title">
        Reports
    </div>


    {{-- Reports --}}

    <a href="{{ route('reports.index') }}">
        <i class="bi bi-bar-chart"></i>
        <span>Reports</span>
    </a>

    <a href="{{ route('reports.profit') }}"
    class="nav-link">
        <i class="bi bi-graph-up-arrow"></i>
        <span>Profit Report</span>
    </a>

    {{-- Settings --}}

    <a href="{{ route('settings.index') }}">
        <i class="bi bi-gear"></i>
        Settings
    </a>


</div>



{{-- =========================
     MAIN
========================= --}}

<div class="main">


    {{-- TOPBAR --}}

    <div class="topbar">

        <div class="topbar-title">

            @yield('title', 'Dashboard')

        </div>


        <div>

            <span class="text-muted">

                <i class="bi bi-person-circle"></i>

                Admin

            </span>

        </div>

    </div>



    {{-- CONTENT --}}

    <div class="content">


        {{-- SUCCESS MESSAGE --}}

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show"
                 role="alert">

                <i class="bi bi-check-circle"></i>

                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        {{-- ERROR MESSAGE --}}

        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show"
                 role="alert">

                <i class="bi bi-exclamation-triangle"></i>

                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        {{-- VALIDATION ERRORS --}}

        @if($errors->any())

            <div class="alert alert-danger">

                <strong>
                    Please fix the following errors:
                </strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        @yield('content')


    </div>

</div>



{{-- Bootstrap JS --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


@stack('scripts')

</body>

</html>