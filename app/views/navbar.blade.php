@if ($user) {{-- user_signed_in? && current_user.admin? --}}
<div id="navbar">
  <nav class="navbar navbar-inverse navbar-fixed-top"r ole="navigation">
      <div class="container clearfix">
      @if($user->admin)
      <ul class="nav navbar-nav pull-left">

         @if (Request::is('/'))  {{-- current_page?(root_path) --}}
           <li>
             <a href="{{{ URL::to('/admin') }}}"><span class="glyphicon glyphicon-edit"></span> {{{trans('navbar.edit_homepage')}}}</a>
           </li>
           <li>
              <a href="<{{{ URL::action('CampaignController@create') }}}"><span class="glyphicon glyphicon-plus"></span>  {{{trans('navbar.new_campaign')}}}</a>
           </li>
         @endif

         @if ( Request::is('admin/*') ) {{-- request.fullpath.include? 'admin' --}}
           <li>
             <a href="{{ url('/') }}"><span class="glyphicon glyphicon-left"></span> {{{trans('navbar.goto_homepage')}}}</a>
           </li>
           <li>
              <a href="{{ URL::action('Admin\CampaignController@create') }}"><span class="glyphicon glyphicon-plus"></span> {{{trans('navbar.new_campaign')}}}</a>
           </li>
         @endif

         @if ( isset($campaign) && Helper::routeController() === 'campaign' )
           <li>
            <a href="{{ URL::action('Admin\CampaignController@edit', array($campaign->id)) }}"><span class="glyphicon glyphicon-edit"></span> {{{trans('navbar.edit_campaign')}}}</a>
           </li>

           <li>
            <a href="{{ URL::action('Admin\CampaignController@getPayments', array($campaign->id)) }}"><span class="glyphicon glyphicon-list"></span> {{{trans('navbar.view_payments')}}}</a>
           </li>

           @if ( ! $campaign->published_flag )
             <li class="status red show_tooltip" data-placement="bottom" data-title="Visible to ADMINS ONLY">
               {{{trans('navbar.not_published')}}}
             </li>
           @else
             <li class="status green show_tooltip" data-placement="bottom" data-title="Visible to ALL">
               {{{trans('navbar.published')}}}
             </li>
           @endif

           @if ( ! $campaign->production_flag )
             <li class="status red show_tooltip" data-placement="bottom" data-title="Transactions WILL NOT be processed">
               {{{trans('navbar.sandbox_payments')}}}
             </li>
           @else
             <li class="status green show_tooltip" data-placement="bottom" data-title="Transactions WILL be processed">
               {{{trans('navbar.payments_activated')}}}
             </li>
           @endif

         @endif

       </ul>
       @endif
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <img src="/images/user_icon.png" />
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                {{{ $user->name }}} <b class="caret"></b>
              </a>
              <ul class="dropdown-menu">
                <li>{{link_to_action('UserController@edit', 'Account', $user->id)}}</li>
                <li>{{ HTML::link('/admin', 'Admin') }}</li>
                <li class="divider"></li>
                <li>{{ link_to_route('sign_out_path', 'Sign Out') }}</li>
              </ul>
          </li>
        </ul>
      </div>
  </nav>
  <div class="spacer" style="height:30px; width: 100%"></div>
</div>
@endif
