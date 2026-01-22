<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
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