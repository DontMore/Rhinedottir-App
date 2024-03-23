@include('partials.header')

@include('sweetalert::alert')

<div>
    <div class="row">
        <div class="col-md-2">
            @include('partials.sidebar')
        </div>

        <div class="col-md-10 main-content">
            @yield('container')
        </div>
    </div>
</div>

@include('partials.footer')
