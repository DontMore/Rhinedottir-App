<!-- navbar mobile -->
<div class="navbar-mobile">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ URL::previous() }}"><i class="fa-solid fa-arrow-left"></i></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link {{ request()->is('logbook*') ? 'active' : '' }}" aria-current="page" href="{{ route('logbook.index') }}">Logbook</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() === 'user.edit' ? 'active' : '' }}" href="{{ route('user.edit', ['id' => auth()->user()->id]) }}">Setting</a>
          </li>
          <li class="nav-item dropdown">
            <div class="">
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
              </form>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>
<!-- End Navbar Mobile -->

<!-- navbar desktop -->
<div class="sidebar">
  
  <!-- Nama orang -->
  <div class="row user-name">
    <h5><span class="fa-regular fa-user mr-1"></span> {{ auth()->user()->name }}</h5>
  </div>

  <!-- Menu Dashboard -->
  <div class="menu {{ request()->is('dashboard*') ? 'active' : '' }}">
    <a href="{{ route('dashboard.index') }}">
      <span data-feather="home"></span>
      Dashboard <span class="sr-only">(current)</span>
    </a>
  </div>
  
  <!-- Menu Management Stock -->
  <div class="mt-3 menu {{ request()->is('management-stock*') ? 'active' : '' }}">
    <a href="{{ route('management-stock.index') }}">
        <span data-feather="file"></span>
        Management Stock
    </a>
  </div>

  <!-- Menu Stock Opname -->
  <div class="mt-3 menu {{ request()->is('stock-opname*') ? 'active' : '' }}">
    <a href="/stock-opname">
      <span data-feather="file"></span>
      Stock Opname
    </a>
  </div>

  <!-- Menu Logbook -->
  <div class="mt-3 menu {{ request()->is('logbook*') ? 'active' : '' }}">
    <a href="{{ route('logbook.index') }}">
      <span data-feather="file"></span>
      Logbook
    </a>
  </div>

  <!-- Menu Orders -->
  <div class="mt-3 menu {{ request()->is('order*') ? 'active' : '' }}">
    <a href="/order">
      <span data-feather="file"></span>
      Orders
    </a>
  </div>

  <!-- Menu Reports -->
  <div class="mt-3 menu {{ request()->is('report*') ? 'active' : '' }}">
    <a href="/report">
      <span data-feather="bar-chart-2"></span>
      Reports
    </a>
  </div>

  <!-- Menu Users -->
  <div class="mt-3 menu {{ request()->is('user-list*') ? 'active' : '' }}">
    <a href="/user-list">
      <span data-feather="layers"></span>
      Users
    </a>
  </div>

<!-- Menu Settings -->
<div class="mt-3 menu {{ Route::currentRouteName() === 'user.edit' ? 'active' : '' }}">
  <a href="{{ route('user.edit', ['id' => auth()->user()->id]) }}">
    <span data-feather="file-text"></span>
    Setting
  </a>
</div>




  <!-- Button Logout -->
  <div class="btn-logout">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </form>
  </div>
</div>
