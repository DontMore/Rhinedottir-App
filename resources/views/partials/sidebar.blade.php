<nav class="fixed top-0 z-50 w-full bg-gray-900 border-b border-gray-700">
   <div class="px-3 py-3 lg:px-5 lg:pl-3">
      <div class="flex items-center justify-between">
         <div class="flex items-center justify-start rtl:justify-end">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-400 rounded-lg sm:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600">
               <span class="sr-only">Open sidebar</span>
               <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
               </svg>
            </button>
            <a href="https://flowbite.com" class="flex ms-2 md:me-24">
               <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-white">Rhinedottir</span>
            </a>
         </div>

         <div class="flex items-center">
            <div class="relative ms-3">
               <!-- User Menu Button -->
               <button type="button" class="flex items-center text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                  <span class="sr-only">Open user menu</span>
                  <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="User photo">
               </button>

               <!-- Dropdown Menu -->
               <div class="hidden absolute right-0 z-50 mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-lg" id="dropdown-user">
                  <div class="px-4 py-3 border-b border-gray-700">
                     <p class="text-sm font-medium text-white">
                        @auth
                        {{ auth()->user()->name }}
                        @else
                        Guest
                        @endauth
                     </p>
                     <p class="text-xs text-gray-400 mt-1">
                        <span class="inline-flex items-center">
                           <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                              <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                           </svg>
                           @auth
                           {{ auth()->user()->role }} - Org: {{ auth()->user()->organization_guid }}
                           @else
                           Guest
                           @endauth
                        </span>
                     </p>
                  </div>
                  <ul class="py-2 text-sm">
                     @auth
                     <li>
                        <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}"
                           class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                           </svg>
                           Settings
                        </a>
                     </li>
                     <li>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                           @csrf
                           <button type="submit"
                              class="flex w-full items-center px-4 py-2 text-red-400 hover:bg-gray-700 hover:text-red-300 transition-colors duration-200">
                              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                              </svg>
                              Sign out
                           </button>
                        </form>
                     </li>
                     @endauth
                  </ul>
               </div>
            </div>


         </div>
      </div>
</nav>

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-gray-900 border-r border-gray-700 sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-gray-900">
      <ul class="space-y-2 font-medium">
         {{-- Dashboard - Admin & Superadmin dengan organization_guid check --}}
         @canany(['admin', 'superadmin'])
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('dashboard.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
               </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         @endif
         @endcanany

         {{-- Stock Management - Admin & Superadmin dengan organization_guid check --}}
         @canany(['admin', 'superadmin'])
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('management-stock.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Stock Management</span>
            </a>
         </li>
         @endif
         @endcanany

         {{-- Stock Opname - Semua role dengan organization_guid check --}}
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('stock.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Stock Opname</span>
            </a>
         </li>
         @endif

         {{-- Orders - Semua role dengan organization_guid check --}}
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('order.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Orders</span>
            </a>
         </li>
         @endif

         {{-- Reports - Admin & Superadmin dengan organization_guid check --}}
         @canany(['admin', 'superadmin'])
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('report.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Reports</span>
            </a>
         </li>
         @endif
         @endcanany

         {{-- Users - Admin & Superadmin dengan organization_guid check --}}
         @canany(['admin', 'superadmin'])
         @if(auth()->user()->organization_guid)
         <li>
            <a href="user-list" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Users</span>
            </a>
         </li>
         @endif
         @endcanany

         {{-- Audit Trail - Admin & Superadmin dengan organization_guid check --}}
         @canany(['admin', 'superadmin'])
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('audit-logs.index') }} " class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group ">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white " xmlns="http://www.w3.org/2000/svg " fill="none " viewBox="0 0 24 24 " stroke-width="1.5 " stroke="currentColor ">
                  <path stroke-linecap="round " stroke-linejoin="round " d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z "> </path>
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap ">Audit Trail </span>
            </a>
         </li>
         @endif
         @endcanany

         {{-- Settings - Semua role dengan organization_guid check --}}
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('settings.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Settings</span>
            </a>
         </li>
         @endif

         {{-- Super Admin Menu - Hanya Superadmin tanpa organization_guid check (global access) --}}
         @can('superadmin')
         <li>
            <a href="{{ route('superadmin.index') }}" class="flex items-center p-2 text-yellow-300 rounded-lg hover:bg-yellow-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-yellow-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Super Admin</span>
            </a>
         </li>
         @endcan

         {{-- Logbook - Semua role dengan organization_guid check --}}
         @if(auth()->user()->organization_guid)
         <li>
            <a href="{{ route('logbook.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-all duration-75 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h1.5m9 0h-9" />
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Logbook</span>
            </a>
         </li>
         @endif
      </ul>
   </div>
</aside>