@include('partials.header')

@include('sweetalert::alert')

<div class="app-wrapper">
    @include('partials.sidebar')
    <main class="main-content">
        @yield('container')
    </main>
</div>

@include('partials.footer')

<style>
.app-wrapper {
    display: flex;
    min-height: 100vh;
}

.main-content {
    flex: 1;
    min-width: 0;
    padding: 1rem;
    margin-left: 280px;
}

@media (max-width: 992px) {
    .main-content {
        margin-left: 0;
    }
}
</style>

@stack('scripts')
