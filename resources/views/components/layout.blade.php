<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport" />
	<title>{{ $title }} &mdash; {{ config('app.name') }}</title>

	<!-- General CSS Files -->
	<link rel="stylesheet" href="{{ url('assets/bootstrap/css/bootstrap.min.css') }}" />
	<link rel="stylesheet" href="{{ url('assets/fontawesome/css/all.css') }}" />

	<!-- CSS Libraries -->
	<link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.bootstrap4.css" />

	<!-- Template CSS -->
	<link rel="stylesheet" href="{{ url('assets/css/style.css') }}" />
	<link rel="stylesheet" href="{{ url('assets/css/components.css') }}" />

	<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.min.css" rel="stylesheet" />
	<link rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap4.min.css" />
</head>

<body>
	<div id="app">
		<div class="main-wrapper">
			<div class="navbar-bg"></div>
			<nav class="navbar navbar-expand-lg main-navbar">
				<form class="form-inline mr-auto">
					<ul class="navbar-nav mr-3">
						<li>
							<a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
						</li>
					</ul>
				</form>
					<ul class="navbar-nav navbar-right">
						<li class="dropdown dropdown-list-toggle">
							<a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
								<i class="far fa-bell"></i>
								@php $__unread = auth()->user()->unreadNotifications()->count(); @endphp
								@if($__unread > 0)
									<span class="badge badge-danger navbar-badge">{{ min($__unread, 99) }}</span>
								@endif
							</a>
							<div class="dropdown-menu dropdown-list dropdown-menu-right">
								<div class="dropdown-header">Notifikasi
									<form action="{{ route('notifications.readAll') }}" method="POST" class="float-right ml-2">
										@csrf
										<button class="btn btn-sm btn-link">Tandai semua dibaca</button>
									</form>
								</div>
								<div class="dropdown-list-content dropdown-list-icons">
									@foreach(auth()->user()->notifications()->latest()->take(5)->get() as $n)
										<a href="{{ route('notifications.open', $n->id) }}" class="dropdown-item {{ is_null($n->read_at) ? 'dropdown-item-unread' : '' }}">
											<div class="dropdown-item-icon bg-primary text-white">
												<i class="fas fa-triangle-exclamation"></i>
											</div>
											<div class="dropdown-item-desc">
												<strong>{{ data_get($n->data,'title','Laporan Kerusakan Baru') }}</strong>
												<div class="time">Oleh {{ data_get($n->data,'reporter','Pengguna') }} • {{ $n->created_at->diffForHumans() }}</div>
											</div>
										</a>
									@endforeach
								</div>
							</div>
						</li>
						<li class="dropdown">
							<a href="#" class="nav-link nav-link-lg" id="themeToggle" title="Tema">
								<i class="fas fa-moon"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown{{ request()->routeIs('kerusakan.list') ? ' active' : '' }}">
                        <a href="{{ route('kerusakan.list') }}" class="nav-link"><i class="fas fa-exclamation-circle"></i> <span>Daftar Kerusakan</span></a>
                    </li>
                    @can('lapor kerusakan')
                    <li class="nav-item dropdown{{ request()->routeIs('kerusakan.create') ? ' active' : '' }}">
                        <a href="{{ route('kerusakan.create') }}" class="nav-link"><i class="fas fa-triangle-exclamation"></i> <span>Lapor Kerusakan</span></a>
                    </li>
                    @endcan
                    @can('kelola kerusakan')
                    <li class="nav-item dropdown{{ request()->routeIs('kerusakan.index') ? ' active' : '' }}">
                        <a href="{{ route('kerusakan.index') }}" class="nav-link"><i class="fas fa-tools"></i> <span>Kelola Kerusakan</span></a>
                    </li>
                    @endcan
						<li class="dropdown">
							<a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
								<img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle mr-1" />
								<div class="d-sm-none d-lg-inline-block">Halo, {{ auth()->user()->name }}</div>
							</a>
						<div class="dropdown-menu dropdown-menu-right">
							<div class="dropdown-title">Akun sejak: {{ auth()->user()->diffForHumanDate(auth()->user()->created_at) }}
							</div>
							@can('mengatur profile')
							<a href="{{ route('profile.index') }}" class="dropdown-item has-icon"> <i class="fas fa-cog"></i>
								Pengaturan Profil </a>
							@endcan
							<div class="dropdown-divider"></div>
							{{--
							<a class="dropdown-item has-icon text-danger" href="{{ route('logout') }}">
								<i class="fas fa-sign-out-alt"></i>
								{{ __('Logout') }}
							</a>
							--}}
							<form id="logout-form" action="{{ route('logout') }}" method="POST">
								@csrf

								<button type="submit" class="dropdown-item has-icon btn-link text-danger logout">
									Logout
								</button>
							</form>
						</div>
					</li>
				</ul>
			</nav>

			<div class="main-sidebar">
				<aside id="sidebar-wrapper">
					<div class="sidebar-brand">
						<a href="{{ route('home') }}">DamarDproject</a>
					</div>
					<div class="sidebar-brand sidebar-brand-sm">
						<a href="{{ route('home') }}">DP</a>
					</div>
					<ul class="sidebar-menu">
						<li class="menu-header">Dashboard</li>
						<li class="nav-item dropdown{{ request()->routeIs('home') ? ' active' : '' }}">
							<a href="{{ route('home') }}" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a>
						</li>
						<li class="menu-header">Manajemen</li>
						@can('lihat barang')
						<li class="nav-item dropdown{{ request()->routeIs('barang.index') ? ' active' : '' }}">
							<a href="{{ route('barang.index') }}" class="nav-link"><i class="fas fa-boxes-stacked"></i> <span>Data
									Barang</span></a>
						</li>
						@endcan @can('lihat perolehan')
						<li class="nav-item dropdown{{ request()->routeIs('perolehan.index') ? ' active' : '' }}">
							<a class="nav-link" href="{{ route('perolehan.index') }}"><i class="far fa-face-laugh"></i>
								<span>Data Perolehan</span></a>
						</li>
						@endcan @can('lihat ruangan')
						<li class="nav-item dropdown{{ request()->routeIs('ruangan.index') ? ' active' : '' }}">
							<a href="{{ route('ruangan.index') }}" class="nav-link"><i class="fas fa-map-location-dot"></i> <span>Data
									Ruangan</span></a>
						</li>
						@endcan @can('lihat pengguna')
						<li class="nav-item dropdown{{ request()->routeIs('pengguna.index') ? ' active' : '' }}">
							<a href="{{ route('pengguna.index') }}" class="nav-link"><i class="fas fa-users"></i> <span>Data
									Pengguna</span></a>
						</li>
						@endcan
						<li class="nav-item dropdown{{ request()->routeIs('peminjaman.index') ? ' active' : '' }}">
							<a href="{{ route('peminjaman.index') }}" class="nav-link">
								<i class="fas fa-hand-holding"></i> <span>Peminjaman</span>
								@can('kelola peminjaman')
									@php $__pendingBorrowings = \App\Borrowing::where('status','pending')->count(); @endphp
									@if($__pendingBorrowings > 0)
										<span class="badge badge-danger ml-2">{{ $__pendingBorrowings }}</span>
									@endif
								@endcan
							</a>
						</li>
						<li class="nav-item dropdown{{ request()->routeIs('laporan.index') ? ' active' : '' }}">
							<a href="{{ route('laporan.index') }}" class="nav-link"><i class="fas fa-file-alt"></i> <span>Laporan</span></a>
						</li>
						<li class="menu-header">Pengaturan</li>
						@can('mengatur profile')
						<li class="nav-item dropdown{{ request()->routeIs('profile.index') ? ' active' : '' }}">
							<a href="{{ route('profile.index') }}" class="nav-link"><i class="fas fa-cog"></i> <span>Pengaturan
									Profil</span></a>
						</li>
						@endcan @can('lihat peran dan hak akses')
						<li class="nav-item dropdown{{ request()->routeIs('peran-dan-hak-akses.index') ? ' active' : '' }}">
							<a href="{{ route('peran-dan-hak-akses.index') }}" class="nav-link"><i class="fas fa-user-shield"></i>
								<span>Peran & Hak Akses</span></a>
						</li>
						@endcan
                    
					</ul>

					<div class="mt-4 mb-4 p-3 hide-sidebar-mini">
						<form id="logout-form" action="{{ route('logout') }}" method="POST">
							<button type="submit" class="btn btn-danger btn-lg btn-block btn-icon-split logout">
								<i class="fas fa-fw fa-sign-out-alt"></i>
								Logout
							</button>
							@csrf
						</form>
					</div>
				</aside>
			</div>

			<!-- Main Content -->
			<div class="main-content">
				<section class="section">
					<div class="section-header">
						<h1>{{ $page_heading }}</h1>
					</div>

					{{ $slot }}
					<div class="text-center text-muted small py-3">
						© {{ date('Y') }} DamarDbarangsekolahku
					</div>
				</section>
			</div>
		</div>
	</div>

	<!-- General JS Scripts -->
	<script src="{{ url('assets/js/jquery-3.5.1.min.js') }}"></script>
	<script src="{{ url('assets/js/popper.min.js') }}"></script>
	<script src="{{ url('assets/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ url('assets/js/jquery.nicescroll.min.js') }}"></script>
	<script src="{{ url('assets/js/moment.min.js') }}"></script>
	<script src="{{ url('assets/js/stisla.js') }}"></script>

	<!-- JS Libraies -->
		<script src="https://cdn.datatables.net/2.0.6/js/dataTables.js"></script>
		<script src="https://cdn.datatables.net/2.0.6/js/dataTables.bootstrap4.js"></script>
		<script>
			// Hindari popup alert DataTables, log ke console saja
			if ($.fn && $.fn.dataTable && $.fn.dataTable.ext) {
				$.fn.dataTable.ext.errMode = 'console';
			}
		</script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

	<!-- Template JS File -->
	<script src="{{ url('assets/js/scripts.js') }}"></script>
	<script src="{{ url('assets/js/custom.js') }}"></script>

	<!-- Page Specific JS File -->
	<script src="{{ url('assets/js/page/index-0.js') }}"></script>

		<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>

		<script src="{{ asset('js/scripts.js') }}"></script>

		<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

		<script>
			$(document).ready(function () {
          // Dark mode toggle
          (function(){
            function updateThemeIcon(){
              var icon = document.querySelector('#themeToggle i');
              if(!icon) return;
              icon.className = document.body.classList.contains('dark-mode') ? 'fas fa-sun' : 'fas fa-moon';
            }
            try {
              var saved = localStorage.getItem('theme');
              if (saved === 'dark') document.body.classList.add('dark-mode');
            } catch(e) {}
            updateThemeIcon();
            $('#themeToggle').on('click', function(e){
              e.preventDefault();
              document.body.classList.toggle('dark-mode');
              try {
                localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
              } catch(e) {}
              updateThemeIcon();
            });
          })();

          // Poll unread notifications count periodically
          setInterval(function(){
            $.getJSON("{{ route('notifications.poll') }}").done(function(res){
              if (!res) return;
              var badge = $('.notification-toggle .navbar-badge');
              if (res.count > 0) {
                if (badge.length === 0) {
                  $('.notification-toggle').append('<span class="badge badge-danger navbar-badge"></span>');
                  badge = $('.notification-toggle .navbar-badge');
                }
                badge.text(Math.min(res.count, 99));
              } else {
                badge.remove();
              }
            });
          }, 30000);

          $(".delete-button").click(function (e) {
            e.preventDefault();
            Swal.fire({
              title: "Hapus?",
              text: "Data tidak akan bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya",
            cancelButtonText: "Batal",
            reverseButtons: true,
          }).then((result) => {
            if (result.value) {
              $(this).parent().submit();
            }
          });
        });

        $(".logout").click(function (e) {
          e.preventDefault();
          Swal.fire({
            title: "Keluar?",
            text: "Anda akan keluar dari aplikasi!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya",
            cancelButtonText: "Batal",
            reverseButtons: true,
          }).then((result) => {
            if (result.value) {
              $(this).parent().submit();
            }
          });
        });
      });
	</script>

	@include('utilities.toast')

	<style>
		/* Dark mode overrides */
		body.dark-mode { background-color: #0f172a; color: #e2e8f0; }
		body.dark-mode a { color: #93c5fd; }
		body.dark-mode .navbar-bg, body.dark-mode .main-navbar { background-color: #111827; }
		body.dark-mode .main-sidebar, body.dark-mode #sidebar-wrapper { background-color: #0b1220; }
		body.dark-mode .sidebar-brand a, body.dark-mode .sidebar-menu li a { color: #cbd5e1; }
		body.dark-mode .sidebar-menu li.active > a { color: #ffffff; }
		body.dark-mode .card { background-color: #111827; color: #e5e7eb; border-color: #1f2937; }
		body.dark-mode .card .card-header { border-color: #1f2937; }
		body.dark-mode .table { color: #e5e7eb; }
		body.dark-mode .table thead th { color: #f3f4f6; border-color: #1f2937; }
		body.dark-mode .table td, body.dark-mode .table th { border-color: #1f2937; }
		body.dark-mode .form-control, body.dark-mode .custom-select, body.dark-mode .input-group-text {
			background-color: #0f172a; border-color: #334155; color: #e2e8f0;
		}
		body.dark-mode .dropdown-menu { background-color: #0f172a; color: #e2e8f0; border-color: #1f2937; }
		body.dark-mode .dropdown-item { color: #e2e8f0; }
		body.dark-mode .dropdown-item:hover { background-color: #1f2937; color: #fff; }
		body.dark-mode .btn-outline-secondary { color:#cbd5e1; border-color:#475569; }
		body.dark-mode .btn-outline-secondary:hover { background:#334155; color:#fff; border-color:#334155; }
	</style>
	@stack('modal')
	@stack('js')
</body>

</html>
