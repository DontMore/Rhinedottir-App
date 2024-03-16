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
    <a href="/logbook">
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
