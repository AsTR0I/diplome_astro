@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="mb-2">
        <div class="v-input theme--light v-text-field v-text-field--single-line v-text-field--solo v-text-field--is-booted v-text-field--enclosed v-text-field--placeholder">
            <div class="v-input__control">
                <div class="v-input__slot">
                    <div class="v-text-field__slot">
                        <input required autofocus name="password" placeholder="Пароль" type="password">
                    </div>
                </div><!-- /.v-input__slot -->
            </div><!-- /.v-input__control -->
        </div><!-- /.v-input -->

        @error('password')
        <div class="v-text-field__details">
            <div class="v-messages theme--light error--text" role="alert">
                <div class="v-messages__wrapper">
                    <div class="v-messages__message message-transition-enter-to">{{ $message }}</div>
                </div>
            </div>
        </div>
        @enderror
    </div>

    <div class="mb-2">
        <button type="submit" class="white--text v-btn v-btn--block theme--light elevation-2 v-size--default primary" id="button">
            <span class="v-btn__content">
                <span>{{ __('Confirm Password') }}</span>
            </span>
        </button>
    </div>

    @if (Route::has('password.request'))
    <a class="btn btn-link" href="{{ route('password.request') }}">
        {{ __('Forgot Your Password?') }}
    </a>
    @endif
</form>
@endsection
