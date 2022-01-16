<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <div class="brand-link">
    <img src="{{ asset('backend/dist/img/nissan-logo.png') }}" alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text" style="font-size: 18px; color: red;font-weight: bold;">P2P System</span>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    {{-- <div class="user-panel mt-1 mb-1 d-flex">
      <div class="image"></div>
      <div class="info">
        <a href="#" class="d-block">{{ auth()->user()->name }} {{ auth()->user()->lastname }} ({{ auth()->user()->company }})</a>
      </div>
    </div> --}}

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Purchase Requisition -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-file-alt"></i>
            <p>
              Purchase Requisition
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('purchase-requisition.purchaserequisitionlist') }}"
                class="nav-link {{ request()->is('purchase-requisition/purchaserequisitionlist') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>Purchase Requisition List</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>xxxxx</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>xxxxx</p>
              </a>
            </li>
          </ul>
        </li>

        

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

@push("js")
<script type="text/javascript">
  $(function () {
      var params = window.location.pathname;
      params = params.toLowerCase();

      if (params != "/") {
          $(".nav-sidebar li a").each(function (i) {
              var obj = this;
              var url = $(this).attr("href");
              if (url == "" || url == "#") {
                  return true;
              }
              url = url.toLowerCase();
              if (url.indexOf(params) > -1) {
                  $(this).parent().addClass("active open menu-open");
                  $(this).parent().parent().addClass("active open menu-open");
                  $(this).parent().parent().parent().addClass("active open menu-open");
                  $(this).parent().parent().parent().parent().addClass("active open menu-open");
                  $(this).parent().parent().parent().parent().parent().addClass("active open menu-open");
                  return false;
              }
          });
      }
  });
</script>
@endpush
