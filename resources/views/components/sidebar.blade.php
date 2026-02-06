<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/40 z-30 md:hidden" x-cloak>
</div>

<aside
    class="fixed md:static inset-y-0 left-0 z-40 
           w-64 bg-white border-r border-gray-200 shadow-sm
           transform transition-transform duration-300
           md:translate-x-0  font-serif"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"> <!-- LOGO -->
    <div class=" flex items-center justify-center mt-4">
        <img src="{{ asset('image/logo.webp') }}" alt="HQA School Logo" class="h-22 object-cover" />
    </div>

    <!-- MENU -->
    <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
        <!-- Dashboard -->
        @php $user = auth()->user(); @endphp
        @if ($user->hasPermission('dashboard.view') || $user->isSuperAdmin())
            <a href="{{ route('dashboard.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                  hover:bg-blue-100 hover:text-[#00285E]
                  {{ request()->routeIs('dashboard.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
        @endif

        <!-- HomePage Dropdown -->
        @if (
            $user->hasAnyPermission([
                'homepage.view',
                'homepage.modal',
                'homepage.memories',
                'homepage.top_achievers',
                'homepage.news',
                'homepage.videos',
                'homepage.socials',
            ]) || $user->isSuperAdmin())
            <div x-data="{ open: {{ request()->routeIs('homeModal.*') || request()->routeIs('memories.*') || request()->routeIs('topAchievers.*') || request()->routeIs('news.*') || request()->routeIs('videos.*') || request()->routeIs('socials.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                           hover:bg-blue-100 hover:text-[#00285E]
                           {{ request()->routeIs('homeModal.*') || request()->routeIs('memories.*') || request()->routeIs('topAchievers.*') || request()->routeIs('news.*') || request()->routeIs('videos.*') || request()->routeIs('socials.*') ? 'bg-blue-100 text-[#00285E] font-bold' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        HomePage
                    </div>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition x-cloak class="pl-12 space-y-1">
                    @if ($user->hasPermission('homepage.modal') || $user->isSuperAdmin())
                        <a href="{{ route('homeModal.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('homeModal.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Home
                            PopUp</a>
                    @endif
                    @if ($user->hasPermission('homepage.memories') || $user->isSuperAdmin())
                        <a href="{{ route('memories.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('memories.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Home
                            Alumni</a>
                    @endif
                    @if ($user->hasPermission('homepage.top_achievers') || $user->isSuperAdmin())
                        <a href="{{ route('topAchievers.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('topAchievers.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Home
                            Top Achievers</a>
                    @endif
                    @if ($user->hasPermission('homepage.news') || $user->isSuperAdmin())
                        <a href="{{ route('news.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('news.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Home
                            News Section</a>
                    @endif
                    @if ($user->hasPermission('homepage.videos') || $user->isSuperAdmin())
                        <a href="{{ route('videos.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('videos.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Home
                            Videos Section</a>
                    @endif
                    @if ($user->hasPermission('homepage.socials') || $user->isSuperAdmin())
                        <a href="{{ route('socials.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('socials.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Home
                            Socials Section</a>
                    @endif
                </div>
            </div>
        @endif


        <!-- Donation Dropdown -->
        @if (
            $user->hasAnyPermission([
                'donation.view',
                'donation.achievements',
                'donation.fundraise',
                'donation.booking',
                // 'donation.images',
            ]) || $user->isSuperAdmin())


            <!-- Donation Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('achievements.*') || request()->routeIs('fundRaise.*') || request()->routeIs('donationBooking.*') || request()->routeIs('donationImage.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                           hover:bg-blue-100 hover:text-[#00285E]
                           {{ request()->routeIs('achievements.*') || request()->routeIs('fundRaise.*') || request()->routeIs('donationBooking.*') || request()->routeIs('donationImage.*') ? 'bg-blue-100 text-[#00285E] font-bold' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Donation
                    </div>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition x-cloak class="pl-12 space-y-1">
                    @if ($user->hasPermission('donation.achievements') || $user->isSuperAdmin())
                        <a href="{{ route('achievements.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('achievements.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Achievements</a>
                    @endif
                    @if ($user->hasPermission('donation.fundraise') || $user->isSuperAdmin())
                        <a href="{{ route('fundRaise.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('fundRaise.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">FundRaise
                            Goals</a>
                    @endif
                    @if ($user->hasPermission('donation.booking') || $user->isSuperAdmin())
                        <a href="{{ route('donationBooking.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('donationBooking.index') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Donation
                            Booking</a>
                        <a href="{{ route('donationBooking.scan') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('donationBooking.scan') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Scan
                            Check-in</a>
                    @endif
                    {{-- @if ($user->hasPermission('donation.images') || $user->isSuperAdmin())
                        <a href="{{ route('donationImage.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('donationImage.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Donation
                            Image</a>
                    @endif --}}
                </div>
            </div>
        @endif

        <!-- PTO Dropdown -->
        @if (
            $user->hasAnyPermission([
                'pto.view',
                'pto.events',
                'pto.subscribe',
                // 'pto.images',
                'pto.letter_guide',
                // 'pto.easy_join',
                // 'pto.fee',
            ]) || $user->isSuperAdmin())
            <div x-data="{ open: {{ request()->routeIs('ptoEvents.*') || request()->routeIs('ptoSubscribemails.*') || request()->routeIs('ptoImages.*') || request()->routeIs('ptoLetterGuide.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                           hover:bg-blue-100 hover:text-[#00285E]
                           {{ request()->routeIs('ptoEvents.*') || request()->routeIs('ptoSubscribemails.*') || request()->routeIs('ptoImages.*') || request()->routeIs('ptoLetterGuide.*') ? 'bg-blue-100 text-[#00285E] font-bold' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        PTO
                    </div>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition x-cloak class="pl-12 space-y-1">
                    @if ($user->hasPermission('pto.events') || $user->isSuperAdmin())
                        <a href="{{ route('ptoEvents.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('ptoEvents.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">PTO
                            Events</a>
                    @endif
                    @if ($user->hasPermission('pto.subscribe') || $user->isSuperAdmin())
                        <a href="{{ route('ptoSubscribemails.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('ptoSubscribemails.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">PTO
                            Subscribe Mails</a>
                    @endif
                    @if ($user->hasPermission('pto.attendees') || $user->isSuperAdmin())
                        <a href="{{ route('admin.pto-event-attendees.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all 
        {{ request()->routeIs('admin.pto-event-attendees.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">
                            PTO Attendees
                        </a>
                    @endif

                    {{-- @if ($user->hasPermission('pto.images') || $user->isSuperAdmin())
                        <a href="{{ route('ptoImages.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('ptoImages.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">PTO
                            Images</a>
                    @endif --}}
                    @if ($user->hasPermission('pto.letter_guide') || $user->isSuperAdmin())
                        <a href="{{ route('ptoLetterGuide.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('ptoLetterGuide.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">PTO
                            Letter Guide Download</a>
                    @endif
                    {{-- @if ($user->hasPermission('pto.easy_join') || $user->isSuperAdmin())
                        <a href="{{ route('easy-joins.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('easy-joins.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">PTO
                            Easy Join</a>
                    @endif --}}
                    @if ($user->hasPermission('pto.fee') || $user->isSuperAdmin())
                        <a href="{{ route('fee.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('fee.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">PTO
                            Fee Person</a>
                    @endif

                </div>
            </div>
        @endif

        <!-- Calendar -->
        @if ($user->hasPermission('calendar.manage') || $user->isSuperAdmin())
            <a href="{{ route('calender.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                  hover:bg-blue-100 hover:text-[#00285E]
                  {{ request()->routeIs('calender.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Calendar
            </a>
        @endif

        <!-- Teacher Job Post -->
        @if ($user->hasAnyPermission(['career.view', 'career.job_posts', 'career.job_applications']) || $user->isSuperAdmin())
            <div class="space-y-2">

                <!-- Teacher Jobs Dropdown -->
                <div x-data="{
                    open: {{ request()->routeIs('jobPost.*') || request()->routeIs('jobApp.*') ? 'true' : 'false' }}
                }" class="relative">

                    <!-- Parent Button -->
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl font-medium
                   text-gray-700 transition-all duration-200
                   hover:bg-blue-100 hover:text-[#00285E]
                   {{ request()->routeIs('jobPost.*') || request()->routeIs('jobApp.*')
                       ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm'
                       : '' }}">

                        <div class="flex items-center gap-3">
                            <!-- Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>

                            Teacher Jobs
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>

                    </button>

                    <!-- Dropdown Items -->
                    <div x-show="open" x-collapse class="mt-2 ml-6 space-y-1">

                        <!-- Teacher Job Post -->
                        @if ($user->hasPermission('career.job_posts') || $user->isSuperAdmin())
                            <a href="{{ route('jobPost.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm transition
                       {{ request()->routeIs('jobPost.*')
                           ? 'bg-blue-50 text-[#00285E] font-semibold'
                           : 'text-gray-600 hover:bg-blue-50 hover:text-[#00285E]' }}">
                                Teacher Job Post
                            </a>
                        @endif

                        <!-- Job Applications -->
                        @if ($user->hasPermission('career.job_applications') || $user->isSuperAdmin())
                            <a href="{{ route('jobApp.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm transition
                       {{ request()->routeIs('jobApp.*')
                           ? 'bg-blue-50 text-[#00285E] font-semibold'
                           : 'text-gray-600 hover:bg-blue-50 hover:text-[#00285E]' }}">
                                Job Applications
                            </a>
                        @endif

                    </div>
                </div>

            </div>
        @endif



        <!-- Alumni Dropdown -->
        @if (
            $user->hasAnyPermission([
                'alumni.view',
                'alumni.huston',
                'alumni.events',
                'alumni.posts',
                // 'alumni.images',
                'alumni.forms',
                // 'alumni.mails',
            ]) || $user->isSuperAdmin())
            <div x-data="{ open: {{ request()->routeIs('alumniHuston.*') || request()->routeIs('alumniEvent.*') || request()->routeIs('alumniPosts.*') || request()->routeIs('alumniImages.*') || request()->routeIs('alumniForm.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                           hover:bg-blue-100 hover:text-[#00285E]
                           {{ request()->routeIs('alumniHuston.*') || request()->routeIs('alumniEvent.*') || request()->routeIs('alumniPosts.*') || request()->routeIs('alumniImages.*') || request()->routeIs('alumniForm.*') ? 'bg-blue-100 text-[#00285E] font-bold' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Alumni
                    </div>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition x-cloak class="pl-12 space-y-1">
                    @if ($user->hasPermission('alumni.huston') || $user->isSuperAdmin())
                        <a href="{{ route('alumniHuston.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniHuston.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Alumni
                            Houston </a>
                    @endif

                    @if ($user->hasPermission('alumni.events') || $user->isSuperAdmin())
                        <a href="{{ route('alumniEvent.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniEvent.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Alumni
                            Event</a>
                    @endif
                    @if ($user->hasPermission('alumni.posts') || $user->isSuperAdmin())
                        <a href="{{ route('alumniPosts.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniPosts.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Alumni
                            Posts</a>
                    @endif
                    {{-- @if ($user->hasPermission('alumni.images') || $user->isSuperAdmin())
                        <a href="{{ route('alumniImages.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniImages.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Alumni
                            Images</a>
                    @endif --}}


                    @if ($user->hasPermission('alumni.fee') || $user->isSuperAdmin())
                        <a href="{{ route('alumniFee.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniFee.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">
                            Alumni Fee Per Person
                        </a>
                    @endif
                    @if ($user->hasPermission('alumni.attendees') || $user->isSuperAdmin())
                        <a href="{{ route('admin.alumni-event-attendees.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all
        {{ request()->routeIs('admin.alumni-event-attendees.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">
                            Alumni Event Attendees
                        </a>
                    @endif

                    @if ($user->hasPermission('alumni.forms') || $user->isSuperAdmin())
                        <a href="{{ route('alumniForm.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniForm.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Alumni
                            Form</a>
                    @endif
                    {{-- @if ($user->hasPermission('alumni.mails') || $user->isSuperAdmin())
                        <a href="{{ route('alumniMail.index') }}"
                            class="block py-2.5 px-4 rounded-lg text-sm font-medium text-gray-600 transition-all {{ request()->routeIs('alumniMail.*') ? 'bg-blue-200 text-[#00285E] font-bold' : 'hover:bg-gray-200' }}">Alumni
                            Mail</a>
                    @endif --}}
                </div>
            </div>
        @endif

        <!-- Users -->
        @if ($user->hasPermission('managers.view') || $user->isSuperAdmin())
            <a href="{{ route('managers.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                  hover:bg-blue-100 hover:text-[#00285E]
                  {{ request()->routeIs('managers.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Users
            </a>
        @endif

        <!-- Sponsor Packages -->
        @if ($user->hasPermission('sponsor_packages.view') || $user->isSuperAdmin())
            <a href="{{ route('sponsor-packages.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                  hover:bg-blue-100 hover:text-[#00285E]
                  {{ request()->routeIs('sponsor-packages.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Sponsor Packages
            </a>
        @endif

        <!-- Coupons -->
        @if ($user->hasPermission('coupons.view') || $user->isSuperAdmin())
            <a href="{{ route('coupons.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                  hover:bg-blue-100 hover:text-[#00285E]
                  {{ request()->routeIs('coupons.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Coupons
            </a>
        @endif
        <!-- Contact Sponsor -->
        <a href="{{ route('contact-sponser.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
          hover:bg-blue-100 hover:text-[#00285E]
          {{ request()->routeIs('contact-sponser.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">

            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2
                 a3 3 0 00-5.356-1.857M7 20H2v-2
                 a3 3 0 015.356-1.857M7 20v-2
                 a3 3 0 015.356-1.857M15 7
                 a3 3 0 11-6 0 3 3 0 016 0zm6 3
                 a2 2 0 11-4 0 2 2 0 014 0zM3 10
                 a2 2 0 114 0 2 2 0 01-4 0z" />
            </svg>

            Contact Sponsor
        </a>


        <!-- Permissions (Super Admin Only) -->
        @if ($user->isSuperAdmin())
            <a href="{{ route('permissions.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
                  hover:bg-blue-100 hover:text-[#00285E]
                  {{ request()->routeIs('permissions.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Permissions
            </a>
        @endif
        <a href="{{ route('admin.donations.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-gray-700 transition-all duration-200
    hover:bg-blue-100 hover:text-[#00285E]
    {{ request()->routeIs('admin.donations.*') ? 'bg-blue-100 text-[#00285E] font-bold border-r-4 border-[#00285E] shadow-sm' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            General Donations
        </a>
    </nav>
</aside>
