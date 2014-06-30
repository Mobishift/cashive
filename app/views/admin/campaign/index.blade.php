@extends('layouts.admin')
@section('main')
<div id="admin">
  <div class="container content_box clearfix">

    @include('admin/header')

    <div id="admin_campaigns">

      <table class="table table-striped">
        <tr>
          <th>Name</th>
          <th>Goal</th>
          <th>Raised</th>
          <th>Expiration<br/>Date</th>
<!--
          <th>Published?</th>
          <th>Payments<br/>Activated?</th>
-->
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>

      @foreach( $campaigns as $campaign )
        <tr>
          <td>{{{ $campaign->name }}}</td>

          <td>
            @if ( $campaign->goal_type == "dollars" )
            	{{{ Currency::format($campaign->goal_dollars, 'USD') }}}
            @else
              {{{ $campaign->goal_orders }}}
            @endif
          </td>
          <td>
            @if ( $campaign->goal_type == "dollars" )
              {{{ Currency::format($campaign->stats_raised_amount, 'USD') }}}
            @else
              {{{ $campaign->goal_orders }}}
            @endif
          </td>

          <td>{{{ with( new DateTime($campaign->expiration_date) )->format("m/d/Y") }}}</td>
<!--
          <td>{{ $campaign->published_flag ? '<span class="glyphicon glyphicon-ok"></span>' : '' }}</td>
          <td>{{ $campaign->production_flag ? '<span class="glyphicon glyphicon-ok"></span>' : '' }}</td>
-->
          <td>{{ link_to_action( 'Admin\\CampaignController@edit', 'Edit', array($campaign->id)) }}</td>
          <td>{{ link_to_action( 'CampaignController@show', 'View', array($campaign->id)) }}</td>
          <td>{{ link_to_route( 'admin_campaigns_payments_path', 'Payments', array($campaign->id)) }}</td>
          <td>{{ link_to_route( 'admin_campaigns_copy_path', 'Copy', array($campaign->id)) }}</td>
        </tr>
      @endforeach
      </table>

      <br />

      {{ link_to_action( 'Admin\\CampaignController@create', 'New Campaign') }}
    </div>
  </div>
</div>

{{-- <% if flash[:signup_modal] %>
<div class="modal hide fade" id="signupModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Your page is almost ready!</h3>
  </div>
  <div class="modal-body">
    <p>Start out by creating your first campaign.</p>
    <p>If you have any questions as you get started, reach out to us: <a href="mailto:open@crowdtilt.com?subject=I%20have%20questions%20about%20getting%20started%20with%20Crowdtilt%20Open">open@crowdtilt.com</a>.</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Close</a>
    <a href="<%= new_admin_campaign_path %>" class="btn btn-primary">Start campaign!</a>
  </div>
</div>

<% content_for :scripts do %>
<script type="text/javascript">
    $(window).load(function(){
        $('#signupModal').modal('show');
    });
</script>
<% end %>

<% end %>
--}}
@stop