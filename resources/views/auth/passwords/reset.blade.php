@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-2">
        <div class="v-input theme--light v-text-field v-text-field--single-line v-text-field--solo v-text-field--is-booted v-text-field--enclosed v-text-field--placeholder">
            <div class="v-input__control">
                <div class="v-input__slot">
                    <div class="v-text-field__slot">
                        <input required autocomplete="email" autofocus name="email" placeholder="E-mail адрес" type="email" value="{{ old('email') }}">
                    </div>
                </div><!-- /.v-input__slot -->
            </div><!-- /.v-input__control -->
        </div><!-- /.v-input -->

        @error('email')
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
        <div class="v-input theme--light v-text-field v-text-field--single-line v-text-field--solo v-text-field--is-booted v-text-field--enclosed v-text-field--placeholder">
            <div class="v-input__control">
                <div class="v-input__slot">
                    <div class="v-text-field__slot">
                        <input required autocomplete="new-password" name="password" placeholder="Пароль" type="password">
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
        <div class="v-input theme--light v-text-field v-text-field--single-line v-text-field--solo v-text-field--is-booted v-text-field--enclosed v-text-field--placeholder">
            <div class="v-input__control">
                <div class="v-input__slot">
                    <div class="v-text-field__slot">
                        <input required autocomplete="new-password" name="password_confirmation" placeholder="Повторите пароль" type="password">
                    </div>
                </div><!-- /.v-input__slot -->
            </div><!-- /.v-input__control -->
        </div><!-- /.v-input -->
    </div>

    <button type="submit" class="white--text v-btn v-btn--block theme--light elevation-2 v-size--default primary" id="button">
        <span class="v-btn__content">
            <span>{{ __('Reset Password') }}</span>
        </span>
    </button>
</form>
@endsection
