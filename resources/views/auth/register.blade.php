@extends('layouts.layoutBasic')

@section('content')
<div class="row" id="register">
    <div class="col-md-6 col-md-offset-3">
        <h1>Register</h1>
        <hr>

        <form class="form-horizontal" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                <label for="first_name" class="control-label">First Name:</label>
                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>

                @if ($errors->has('first_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                <label for="last_name" class="control-label">Last Name:</label>
                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>

                @if ($errors->has('last_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="control-label">E-Mail Address:</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">Password:</label>
                <input id="password" type="password" class="form-control" name="password" v-validate="{
                    rules: {
                        regex: /^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=@&?]).*$/,
                        required: true,
                        confirmed: 'password_confirmation',
                        min: 9,
                        max: 72, 
                    }
                }" :class="{'input': true, 'is-danger': errors.has('password') }" required>

                <p class="form-text text-muted">Password must be at least 9 characters long and contain 3 of the 4 categories: uppercase, lowercase, numbers, special characters.</p>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="control-label">Confirm Password:</label>
                <input id="password_confirmation" type="password" class="form-control" :class="{'input': true, 'is-danger': errors.has('password') }" name="password_confirmation" required>
            </div>

            <span v-show="errors.has('password')" class="help is-danger">@{{ errors.first('password') }}</span>

            <div class="form-group">
                <button type="submit" :disabled="errors.has('password')" class="btn btn-primary form-control">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="{{ asset('/vendor/veeValidate/vee-validate.js') }}"></script>
    <script>
        $(document).ready(function(){
            const dict = {
                en: {
                    custom: {
                        password: {
                            regex: 'Password does not meet requirements'
                        }
                    }
                }
            };
            VeeValidate.Validator.updateDictionary(dict);
            Vue.use(VeeValidate);
            var register = new Vue({
                el: '#register'
            });
        });
    </script>
@stop
