<style>
    body {
        background:
            radial-gradient(circle at top, #244B2A 0%, transparent 35%),


    }

    .content {
        overflow-x: hidden;
    }

    .login-box {
        width: 430px;
        max-width: 100%;
        margin: 0 auto;
        margin-top: -8rem;
        padding: 10px;
    }

    .card {
        background: #ffffff;
        border: none;
        border-radius: 20px;

        box-shadow:
            0 0 0 1px rgba(155, 234, 58, .15),

    }

    .card-body {
        border-radius: 16px;
    }

    .login-card-body {
        padding: 18px 32px 32px;
        background: #fff;
    }

    .form-group .control-label {
        text-align: left;
    }


    .login-header {
        text-align: center;
        margin: 0 0 14px;
    }

    .login-brand {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .login-brand img {
        display: block;
        width: 170px;
        height: auto;
        margin: 0 auto;
    }


    .login-brand h2 {
        font-size: 34px;
        font-weight: 700;
        color: #2E7D32;
        letter-spacing: .5px;
    }

    .login-subtitle {
        margin: 8px 0 0;
        line-height: 1.45;
        color: #7B8280;
        font-size: 14px;
    }

    .login-btn {
        padding-left: 2rem !important;
        padding-right: 2rem !important;
        border-radius: 8px;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #8b909a;
        font-size: 18px;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #2E7D32;
    }


    .has-icon-left input[type=password],
    .has-icon-left input[type=text] {
        padding-right: 45px;
    }

    @media (max-width:992px) {

        .login-box {
            width: 400px;
            margin-top: -6rem;
        }

        .login-card-body {
            padding: 32px;
        }

        .login-brand h2 {
            font-size: 30px;
        }

        .login-brand img {
            width: 180px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: auto;
        }

    }

    @media (max-width:768px) {

        body {
            padding: 20px;
        }

        .login-box {
            width: 360px;
            max-width: 100%;
            margin-top: -2rem;
            padding: 0;
        }

        .login-card-body {
            padding: 16px 24px 24px;
        }

        .login-brand {
            gap: 12px;
        }

        .login-brand img {
            width: 180px;
            height: auto;
        }

        .login-brand h2 {
            font-size: 26px;
        }

        .login-subtitle {
            font-size: 13px;
        }

        .login-btn {
            width: 100%;
            float: none !important;
            padding: 10px 18px !important;
        }

    }

    @media (max-width:576px) {

        body {
            padding: 15px;
        }

        .login-box {
            width: 100%;
            max-width: 100%;
            margin-top: 0;
            padding: 0;
        }

        .card,
        .card-body {
            border-radius: 12px;
        }

        .login-card-body {
            padding: 14px 18px 22px;
        }

        .login-header {
            margin-bottom: 20px;
        }

        .login-brand {
            margin-bottom: 2px;
        }

        .login-brand img {
            width: 150px;
            max-width: 90%;
            height: auto;
            margin-bottom: 8px;
        }

        .login-brand h2 {
            font-size: 22px;
        }

        .login-subtitle {
            font-size: 12px;
            line-height: 1.5;
        }
    }

    /* INPUT */

    .form-control {
        border: 1px solid #D7EAD9;
        border-radius: 8px;
    }

    .form-control:focus {
        border-color: #2E7D32;
        box-shadow: 0 0 0 .18rem rgba(46, 125, 50, .15);
    }

    .form-control-position i {
        color: #5F8F65;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6B7280;
        font-size: 18px;
    }

    .password-toggle:hover {
        color: #2E7D32;
    }

    .btn-primary {
        background: #2E7D32 !important;
        border-color: #2E7D32 !important;
        border-radius: 8px;
        font-weight: 600;
        transition: .2s;
    }

    .btn-primary:hover {
        background: #256A29 !important;
        border-color: #256A29 !important;
    }

    .login-btn {
        padding: 10px 32px !important;
    }

    .vs-checkbox-con input:checked~.vs-checkbox {
        background: #2E7D32 !important;
        border-color: #2E7D32 !important;
    }

    a {
        color: #2E7D32;
    }

    a:hover {
        color: #256A29;
    }

    @media (max-width:360px) {

        body {
            padding: 10px;
        }

        .login-card-body {
            padding: 12px 14px 18px;
        }

        .login-brand {
            flex-direction: column;
            gap: 8px;
        }

        .login-brand img {
            width: 135px;
            max-width: 95%;
            height: auto;
        }

        .login-brand h2 {
            font-size: 20px;
        }

        .login-subtitle {
            font-size: 11px;
        }

        .login-btn {
            font-size: 14px;
        }

    }
</style>

<div class="login-page bg-40">

    <div class="login-box">

        <div class="card">

            <div class="card-body login-card-body shadow-100">

                {{-- HEADER BARU --}}
                <div class="login-header">

                    <div class="login-brand">

                        <img src="{{ asset('images/LOGO HSB.png') }}" alt="HSB Audio">



                    </div>

                    <p class="login-subtitle">
                        Selamat datang kembali. Silakan masuk ke akun Anda.
                    </p>

                </div>

                <form id="login-form" method="POST" action="{{ admin_url('auth/login') }}">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    {{-- USERNAME --}}

                    <fieldset class="form-label-group form-group position-relative has-icon-left">

                        <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                            name="username" placeholder="{{ trans('admin.username') }}" value="{{ old('username') }}"
                            required autofocus>

                        <div class="form-control-position">
                            <i class="feather icon-user"></i>
                        </div>

                        <label>{{ trans('admin.username') }}</label>

                        <div class="help-block with-errors"></div>

                        @if ($errors->has('username'))
                            <span class="invalid-feedback text-danger" role="alert">

                                @foreach ($errors->get('username') as $message)
                                    <span class="control-label">

                                        <i class="feather icon-x-circle"></i>

                                        {{ $message }}

                                    </span><br>
                                @endforeach

                            </span>
                        @endif

                    </fieldset>

                    {{-- PASSWORD --}}

                    <fieldset class="form-label-group form-group position-relative has-icon-left">

                        <input minlength="5" maxlength="20" id="password" type="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password"
                            placeholder="{{ trans('admin.password') }}" required autocomplete="current-password">

                        {{-- icon kiri --}}
                        <div class="form-control-position">
                            <i class="feather icon-lock"></i>
                        </div>

                        {{-- icon mata --}}
                        <span class="password-toggle" id="togglePassword">
                            <i class="feather icon-eye"></i>
                        </span>

                        <label>{{ trans('admin.password') }}</label>

                        <div class="help-block with-errors"></div>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                @foreach ($errors->get('password') as $message)
                                    <span class="control-label">
                                        <i class="feather icon-x-circle"></i>
                                        {{ $message }}
                                    </span><br>
                                @endforeach
                            </span>
                        @endif

                    </fieldset>

                    {{-- REMEMBER ME --}}

                    <div class="form-group d-flex justify-content-between align-items-center">

                        <div class="text-left">

                            @if (config('admin.auth.remember'))
                                <fieldset class="checkbox">

                                    <div class="vs-checkbox-con vs-checkbox-primary">

                                        <input id="remember" name="remember" value="1" type="checkbox"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <span class="vs-checkbox">

                                            <span class="vs-checkbox--check">

                                                <i class="vs-icon feather icon-check"></i>

                                            </span>

                                        </span>

                                        <span>{{ trans('admin.remember_me') }}</span>

                                    </div>

                                </fieldset>
                            @endif

                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary float-right login-btn">

                        {{ __('admin.login') }}

                        &nbsp;

                        <i class="feather icon-arrow-right"></i>

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
    Dcat.ready(function() {

        $('#login-form').form({
            validate: true,
        });

        $('#togglePassword').on('click', function() {

            let input = $('#password');
            let icon = $(this).find('i');

            if (input.attr('type') === 'password') {

                input.attr('type', 'text');

                icon.removeClass('icon-eye')
                    .addClass('icon-eye-off');

            } else {

                input.attr('type', 'password');

                icon.removeClass('icon-eye-off')
                    .addClass('icon-eye');

            }

        });

    });
</script>
