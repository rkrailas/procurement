<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  {{-- <div class="brand-link">
    <img src="{{ asset('images/nissan_logo.png') }}" alt="AdminLTE Logo"
      class="brand-image img-rounded elevation-3" style="opacity: .8">
    <span class="brand-text" style="font-size: 18px; color: #c3002f;;font-weight: bold;">P2P System</span>
  </div> --}}

  <div class="brand-link d-flex justify-content-center" style="height:50px">
    <img src="{{ asset('images/nissan.png') }}" alt="AdminLTE Logo" style=" height:30px;">
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
              <a href="{{ route('purchaserequisitionlist') }}"
                class="nav-link {{ request()->is('purchaserequisitionlist') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>Purchase Requisition List</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('requisitioninbox') }}" 
                class="nav-link {{ request()->is('requisitioninbox') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>Requisition Inbox</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- RFQ -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-file-alt"></i>
            <p>
              RFQ
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('rfqlist') }}" 
                class="nav-link {{ request()->is('rfqlist') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>RFQ List</p>
              </a>
            </li>
            {{-- <li class="nav-item">
              <a href="{{ route('rfqinbox') }}" 
                class="nav-link {{ request()->is('rfqinbox') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>RFQ Inbox</p>
              </a>
            </li> --}}
          </ul>
        </li>

        <!-- Purchase Order -->
        {{-- <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon far fa-file-alt"></i>
            <p>
              Purchase Order
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">            
            <li class="nav-item">
              <a href="{{ route('purchaseorderlist') }}" 
                class="nav-link {{ request()->is('purchaseorderlist') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon-submenu"></i>
                <p>Purchase Order List</p>
              </a>
            </li>
          </ul>
        </li> --}}

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
