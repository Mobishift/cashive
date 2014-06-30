@section('main')
<div id="devise-mini">
  <div class="container">
    <div class="well">

    @if ( $settings->initialized_flag )
      <h3>New User?</h3>
      <p>Please create an account to continue.</p>
    @else
      <h3>New Crowdtilt Open App</h3>
      <p>Looks like this is a new Crowdtilt Open App! Go ahead and create a user that will serve as your site admin:</p>
    @endif

    {{ Form::open(array('action' => 'UserController@store', 'role'=>'form')) }}
      <div class="form-group">
        {{ $errors->first() }}
        {{ $errors->first('password') }}
      </div>
      <div class="form-group">
        {{ Form::label('name', 'Full Name') }}
        {{ Form::text('name', Input::old('name'), array('autofocus' => true, 'class' => 'form-control')) }}
      </div>
      <div class="form-group">
        {{ Form::label('email', 'Email Address') }}
        {{ Form::text('email', Input::old('email'), array('placeholder' => 'awesome@awesome.com', 'class' => 'form-control')) }}
      </div>

      <div class="form-group">
        {{ Form::label('password', 'Password') }}
        {{ Form::password('password', array('class' => 'form-control') ) }}
      </div>
      <p>{{ Form::submit('Create my account', array('class' => 'btn btn-primary show_loader')) }}</p>
      <span class="loader" data-loader="user_form" style="display:none"></span>
    {{ Form::close() }}

    <p style="font-size: 12px">By creating this account, you agree to our <a href="http://open.crowdtilt.com/terms" target="_blank">Terms of Use</a> and <a href="http://open.crowdtilt.com/privacy" target="_blank">Privacy Policy</a></p>

     <br/>{{ link_to_route('sign_in_path', 'Already have an account?') }}<br />

    </div>
  </div>
</div>
@stop