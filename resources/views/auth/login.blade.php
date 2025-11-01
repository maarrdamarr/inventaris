<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	<title>Login &mdash; {{ config('app.name') }}</title>

	<!-- General CSS Files -->
	<link rel="stylesheet" href="{{ url('assets/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/fontawesome/css/all.css') }}">

	<!-- Template CSS -->
	<link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
	<link rel="stylesheet" href="{{ url('assets/css/components.css') }}">
</head>

<body>
    <div id="app" class="login-page">
        <div class="bg-slides">
            <div class="slide active" style="background-image:url('https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=1600&q=80');"></div>
            <div class="slide" style="background-image:url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1600&q=80');"></div>
            <div class="slide" style="background-image:url('https://images.unsplash.com/photo-1559757180-47f12f4b3c16?auto=format&fit=crop&w=1600&q=80');"></div>
            <div class="bg-overlay"></div>
            <div class="greeting-wrap">
                <h1 id="greetings" class="greeting-title">Selamat Datang!</h1>
                <div class="greeting-sub">Bali, Indonesia</div>
                <div class="greeting-credit">Photos from Unsplash</div>
            </div>
        </div>

        <div class="login-card auth-card rounded-lg">
            <h4 class="text-dark font-weight-bold mb-4">Aplikasi <span class="text-primary">Inventaris Barang Sekolah</span></h4>
            @include('utilities.alert')
            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                @csrf
                <div class="form-group">
                    <label for="email" class="mb-1">Email</label>
                    <div class="input-group input-group-lg input-icon">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input id="email" type="email" class="form-control form-control-lg rounded-right @error('email') is-invalid @enderror" name="email" tabindex="1" placeholder="nama@sekolah.sch.id" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="d-block"><label for="password" class="control-label mb-1">Password</label></div>
                    <div class="input-group input-group-lg input-icon">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input id="password" type="password" class="form-control form-control-lg rounded-right @error('password') @enderror" name="password" tabindex="2" placeholder="Masukkan kata sandi" required>
                        <div class="input-group-append">
                            <span class="input-group-text toggle-password" role="button" title="Tampilkan/Sembunyikan"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="invalid-feedback">Mohon masukkan password!</div>
                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group d-flex align-items-center justify-content-between mt-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
                        <label class="custom-control-label" for="rememberMe">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-gradient btn-lg shadow-sm" tabindex="4">Login</button>
                </div>
            </form>
            <center><p class="text-muted small mb-0">© {{ date('Y') }} DamarDbarangsekolahku</p></center>
        </div>
    </div>

	<!-- General JS Scripts -->
	<script src="{{ url('assets/js/jquery-3.5.1.min.js') }}"></script>
	<script src="{{ url('assets/js/popper.min.js') }}"></script>
	<script src="{{ url('assets/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ url('assets/js/jquery.nicescroll.min.js') }}"></script>
	<script src="{{ url('assets/js/moment.min.js') }}"></script>
	<script src="{{ url('assets/js/stisla.js') }}"></script>

	<!-- Template JS File -->
	<script src="{{ url('assets/js/scripts.js') }}"></script>
	<script src="{{ url('assets/js/custom.js') }}"></script>

	<!-- Page Specific JS File -->
	@include('layouts.partials.greetings')

	<script>
		$(document).ready(function() {
            $("#greetings").html(greetings());
			// Toggle password visibility
			$('.toggle-password').on('click', function(){
				const input = $('#password');
				const icon = $(this).find('i');
				if (input.attr('type') === 'password') {
					input.attr('type', 'text');
					icon.removeClass('fa-eye').addClass('fa-eye-slash');
				} else {
					input.attr('type', 'password');
					icon.removeClass('fa-eye-slash').addClass('fa-eye');
				}
			});

            // Background slider
            const slides = $('.bg-slides .slide');
            let idx = 0;
            setInterval(function(){
                const next = (idx + 1) % slides.length;
                $(slides[idx]).removeClass('active');
                $(slides[next]).addClass('active');
                idx = next;
            }, 6000);
        });
	</script>

	<style>
		.login-page { position: relative; min-height: 100vh; overflow: hidden; }
		.bg-slides { position: fixed; inset: 0; z-index: 0; }
		.bg-slides .slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1s ease-in-out; }
		.bg-slides .slide.active { opacity: 1; }
		.bg-slides .bg-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,.15) 0%, rgba(0,0,0,.4) 70%); }
		.greeting-wrap { position: fixed; left: 24px; bottom: 24px; color: #fff; z-index: 2; }
		.greeting-title { font-size: 42px; font-weight: 700; margin-bottom: 4px; text-shadow: 0 3px 10px rgba(0,0,0,.35); }
		.greeting-sub { opacity: .9; margin-bottom: 6px; }
		.greeting-credit { font-size: 12px; opacity: .85; }

		.login-card { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 420px; max-width: 92%; background: #fff; padding: 28px; z-index: 3; box-shadow: 0 10px 30px rgba(31,45,61,.12); border-radius: 16px; }
		.auth-card { border-radius: 16px; }
		.input-icon .input-group-text { background: #fff; border-right: 0; }
		.input-icon .form-control { border-left: 0; }
		.input-group-lg .form-control, .input-group-lg .input-group-text { border-radius: 12px; }
		.btn-gradient { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); border: none; color: #fff; padding: .65rem 1.25rem; border-radius: 10px; }
		.btn-gradient:hover { filter: brightness(1.05); color: #fff; }
	</style>

	@include('utilities.toast')
</body>

</html>
