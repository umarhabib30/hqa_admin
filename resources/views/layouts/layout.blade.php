<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @stack('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" crossorigin="anonymous">
    <style>
        .dataTables_wrapper { padding: 0 0.5rem; }
        .dataTables_wrapper .dataTables_length select { padding: 0.25rem 1.5rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; background: #fff; }
        .dataTables_wrapper .dataTables_filter input { padding: 0.375rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; margin-left: 0.25rem; }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_length label { color: #6b7280; font-size: 0.875rem; }
        .dataTables_wrapper .dataTables_paginate { margin-top: 0.75rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.25rem 0.5rem; margin: 0 1px; border-radius: 0.375rem; border: 1px solid #e5e7eb; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #00285E !important; border-color: #00285E !important; color: #fff !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f3f4f6 !important; border-color: #d1d5db; color: #111 !important; }
        .dataTables_wrapper table.dataTable thead th { background: #f9fafb; }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 0.75rem; }
        /* Left-align all table cells including Action column */
        .dataTables_wrapper table.dataTable th,
        .dataTables_wrapper table.dataTable td { text-align: left !important; }
        .dataTables_wrapper table.dataTable td.text-right,
        .dataTables_wrapper table.dataTable th.text-right { text-align: left !important; }
        .dataTables_wrapper table.dataTable td .flex.justify-end,
        .dataTables_wrapper table.dataTable td .flex.justify-center { justify-content: flex-start !important; }
        .dataTables_wrapper table.dataTable td [class*="justify-end"],
        .dataTables_wrapper table.dataTable td [class*="justify-center"] { justify-content: flex-start !important; }
        /* All table rows white – no stripe or grey */
        .dataTables_wrapper table.dataTable tbody tr,
        .dataTables_wrapper table.dataTable tbody tr.odd,
        .dataTables_wrapper table.dataTable tbody tr.even { background: #fff !important; }
        .dataTables_wrapper table.dataTable tbody tr:hover { background: #fff !important; }
    </style>
    <!-- Toastr CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKM265s8gK9QicfEoG7knrFt0EuV6bJm73PvvrLpzjAU6YewsGmmIOzBSSMSmc5QwDFi1Cdm42HcAUy3xZf5xg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100 min-h-screen " x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <x-sidebar />

        {{-- MAIN AREA --}}
        <div class="flex-1 flex flex-col">

            {{-- TOPBAR --}}
            <x-topBar />

            {{-- PAGE CONTENT --}}
            <main class="flex-1 p-6 font-serif">
                @yield('content')
            </main>

        </div>

    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- jQuery (for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastr CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-5p1YQSXQt3nHAfHixokKcddJgsIRsEhDRbugzuDUFcPRl1BDpRP70dNDO7xjMnIKh4j/wZUp3NEPoE+N1Ns5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    @stack('scripts')
    
    <script>
        // SweetAlert for delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const button = form.querySelector('.delete-btn');
                    const itemName = button ? button.getAttribute('data-name') : 'this item';
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You want to delete ${itemName}? This action cannot be undone!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>