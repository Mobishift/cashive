@section('main')
<div id="devise-mini">
  <div class="container">
    <div class="well">

    <h3>Sign In</h3>
    <p>Please sign in to continue.</p>

      {{ Form::open(array('route' => 'sign_in_path', 'role'=>'form')) }}

        <div class="form-group">
          {{ Form::label('email', 'Email') }}
          {{ Form::text('email', Input::old('email'), array('placeholder' => 'awesome@awesome.com', 'class' => 'form-control')) }}
        </div>

        <div class="form-group">
          {{ Form::label('password', 'Password') }}
          {{ Form::password('password', array('class' => 'form-control') ) }}
        </div>

        <div class="form-group">
          {{ Form::checkbox('remember_me', 'Remember Me') }} Remember Me
        </div>

        {{ Form::submit('Sign me in!', array('class' => 'btn btn-primary')) }}
      {{ Form::close() }}

      <br /><br />

      Don't have an account?  {{ link_to_action('UserController@create', 'Sign up') }}

    </div>
  </div>
</div>
@stop