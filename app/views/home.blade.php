@extends('layouts.base')

@section('main')
<div id="homepage">
  <div class="container clearfix">

      <div class="homepage_content clearfix">
        {{ $settings->homepage_content }}
      </div>
    <div class="campaigns">

      @if($campaigns->count() > 0)
        @foreach($campaigns->get() as $campaign)
<!--           <% if campaign.published_flag %> -->
          <a href="{{{action('CampaignController@show', $campaign->id)}}}" class="campaign clearfix">

            @if($campaign->media_type == 'video' and $campaign->video_embed_id)

<!--
              <% if campaign.video_placeholder.file? %>
                <%= image_tag campaign.video_placeholder.url(:main), alt: "main_image" %>
              <% else %>
                <%= image_tag "https://img.youtube.com/vi/#{campaign.video_embed_id}/hqdefault.jpg", alt: "main_image" %>
              <% end %>
-->

            @else

              @if($campaign->main_image_file_name)
                {{ HTML::image( $campaign->main_image_file_path(), "main_image" ) }}
              @else
                <div class="placeholder"></div>
              @endif

            @endif

            <p class="info">
<!--               <%= truncate(campaign.name, length: 50) %> -->
              {{{$campaign->name}}}
            </p>

            @if ( $campaign->raised_amount() < $campaign->simple_goal_dollars() )
              <div id='progress_bg'>
                <div id='progress' class='' style='width: {{{ $campaign->completion_percentage() }}}%;'>
                </div>
              </div>
            @else
              <div id="progress_bg">
                <div id="progress">
                </div>
                </div>
            @endif

            <p class="numbers pull-left">
              @if ( $campaign->goal_type === 'dollars' )
              <strong>${{{$campaign->raised_amount()}}}</strong><br/>
              {{{ $campaign->progress_text }}}
              @else
               <strong>{{{ $campaign->orders() }}}</strong><br/>
               {{{ $campaign->contributor_reference }}}
<!--               <%= campaign.contributor_reference.pluralize(campaign.orders) %> -->
              @endif
            </p>

            <p class="numbers pull-right" style="text-align:right">
            @if ( $campaign->expired() )
              <strong>No</strong><br/>days left!
            @else
              <strong>{{{ \Carbon\Carbon::parse($campaign->expiration_date)->format('Y-m-d') }}}</strong>
              <span style="display: block; width: 100px;">
                {{{ \Carbon\Carbon::parse($campaign->expiration_date)->diffInDays() }}} days left
              </span>
<!--
              <strong><%= distance_of_time_in_words_to_now(campaign.expiration_date).gsub(/\D/, "") %></strong>
              <span style="display: block; width: 100px;">
                <%= distance_of_time_in_words_to_now(campaign.expiration_date).gsub(/\d/, "").gsub("about", "") %> left
              </span>
-->
            @endif
            </p>

          </a>
<!--           <% end %> -->
        @endforeach
      @else

      <div class="center" style="padding-left:0px"><h4>No campaigns yet. @if($user and $user->admin){{ link_to_action('Admin\\CampaignController@create', 'Start one now!') }}@endif</h4></div>

      @endif
    </div>
  </div>
</div>
@stop