@extends('layouts.auth')

@section('content')
@if (session('status'))
<div class="v-alert v-sheet v-sheet--outlined theme--light v-alert--outlined v-alert--text success--text">
    <div class="v-alert__wrapper">
        <div class="v-alert__content">
            {{ session('status') }}
        </div>
    </div>
</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

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

    <button type="submit" class="white--text v-btn v-btn--block theme--light elevation-2 v-size--default primary" id="button">
        <span class="v-btn__content">
            <span>{{ __('Send Password Reset Link') }}</span>
        </span>
    </button>
</form>
@endsection
