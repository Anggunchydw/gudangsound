<style>
    body {
        background: #edf2f7;
    }

    .login-box {
        margin-top: -8rem;
        padding: 5px;
        width: 430px;
    }

    .card,
    .card-body {
        border-radius: 16px;
    }

    .login-card-body {
        padding: 38px 36px;
    }

    .content {
        overflow-x: hidden;
    }

    .form-group .control-label {
        text-align: left;
    }

    /* =======================
       Logo
    ======================= */

    .login-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .login-brand {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 14px;
        margin-bottom: 10px;
    }

    .login-brand img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 14px;
        border: 0.5px solid #e5e7eb;
        background: #fff;
        padding: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .login-brand h2 {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
    }

    .login-subtitle {
        color: #9ca3af;
        font-size: 14px;
        margin: 0;
    }

    /* tombol */

    .login-btn {
        padding-left: 2rem !important;
        padding-right: 1.5rem !important;
        border-radius: 8px;
    }

    /* icon mata */

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
        color: #0c3d78;
    }

    /* beri ruang agar icon mata tidak menimpa tulisan */
    .has-icon-left input[type=password],
    .has-icon-left input[type=text] {
        padding-right: 45px;
    }
</style>

<div class="login-page bg-40">

    <div class="login-box">

        <div class="card">

            <div class="card-body login-card-body shadow-100">

                {{-- HEADER BARU --}}
                <div class="login-header">

                    <div class="login-brand">

                        <img src="{{ asset('images/logo-hsb.jpg') }}" alt="HSB Audio">

                        <h2>HSB Audio</h2>

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
    Dcat.ready(function () {

    $('#login-form').form({
        validate: true,
    });

    $('#togglePassword').on('click', function () {

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
