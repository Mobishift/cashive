@include( 'admin/breadcrumbs' )

<ul class="nav nav-pills">
  <li class="{{{ $active === 'campaign' ? 'active' : '' }}}">
    {{ link_to_action('Admin\\CampaignController@index', "Campaigns") }}
  </li>
  <li class="{{{ $active === 'homepage' ? 'active' : '' }}}">
    {{ link_to_route('admin_homepage_path', "Homepage") }}
  </li>
{{--
  <li class="{{{ $active === 'customize' ? 'active' : '' }}}">
    {{ link_to_route('admin_customize_path', "Customize") }}
  </li>
  <li class="dropdown {{{ $active === 'settings' ? 'active' : '' }}}">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        Settings
        <b class="caret"></b>
      </a>
    <ul class="dropdown-menu">
      <li>{{ link_to_route('admin_site_path', "Site Settings") }}</li>
      <li>{{ link_to_route('admin_payment_path', "Payment Settings") }}</li>
      <li>{{ link_to_route('admin_notification_path', "Notification Settings") }}</li>
    </ul>
  </li>
--}}
</ul>
