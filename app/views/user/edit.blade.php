@section('main')
<div id="devise-mini">
  <div class="container">
    <div class="well">

    <h3>
      Account: {{$user->name}}
      <span class="loader" data-loader="user_header" style="display:none"></span>
    </h3>
    {{ Form::open(array('action' => array('UserController@update', $user->id), 'method'=>'put', 'role'=>'form')) }}
      <div class="form-group">
        {{ Form::label('name', 'Full Name') }}
        {{ Form::text('name', $user->name, array('autofocus' => true, 'class' => 'form-control')) }}
      </div>

      <div class="form-group">
        {{ Form::label('email', 'Email') }}
        {{ Form::text('email', $user->email, array('placeholder' => 'awesome@awesome.com', 'class' => 'form-control')) }}
      </div>

{{--
      <% if devise_mapping.confirmable? && resource.pending_reconfirmation? %>
        <div>Currently waiting confirmation for: <%= resource.unconfirmed_email %></div>
      <% end %>
--}}
      <br />
      <div><p>Change your password:</p></div>

      <div class="form-group">
        {{ Form::label('password', 'Enter your new password') }}
        {{ Form::password('password', array('class' => 'form-control') ) }}
      </div>

      <div class="form-group">
        {{ Form::label('password_confirmation', 'Re-enter your new password') }}
        {{ Form::password('password_confirmation', array('class' => 'form-control') ) }}
      </div>

      <br />

      <div class="form-group">
        {{ Form::label('current_password', 'Current Password (we need this to confirm your changes)') }}
        {{ Form::password('current_password', array('class' => 'form-control') ) }}
      </div>

      {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}

    </div>
  </div>
</div>
@stop