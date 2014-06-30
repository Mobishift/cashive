@extends('layouts.base')

@section('header')
@include( 'header' )
@stop

@section('main')
<div id="campaign">
<div id="funding_area">
  <div class="container clearfix">

    <div class="center">
      <h2 class="campaign_title"><a href="{{{ URL::to('campaigns') }}}">{{{ $campaign->name }}}</a></h2>
    </div>

    <div class="clearfix">
      @if ( $campaign->media_type == 'video' )

        <div id="video">
          @if ( $campaign->video_placeholder_file_name )
            <div id="video_image">
              <img src="{{{ $campaign->video_placeholder_file_name }}}" alt="video_place_holder" />
            </div>
            @if ( $campaign->video_embed_id )
              <div id="player" class="hidden" style="display: none;">
                <object width="512" height="385">
                  <param name="movie" value="https://www.youtube.com/v/<%= @campaign.video_embed_id %>?autohide=1&showinfo=0&rel=0&autoplay=1" />
                  <param name="allowFullScreen" value="true" />
                  <param name="allowscriptaccess" value="always" />
                  <embed src="https://www.youtube.com/v/<%= @campaign.video_embed_id %>?autohide=1&showinfo=0&rel=0&autoplay=1" type="application/x-shockwave-flash" width="512" height="385" allowscriptaccess="always" allowfullscreen="true"></embed>
                </object>
              </div>
            @endif
          @else
            @if ( $campaign->video_embed_id )
              <iframe width='512' height='385' src="https://www.youtube.com/embed/<%= @campaign.video_embed_id %>?rel=0"></iframe>
            @endif
          @endif
        </div>

      @else

        <div id="image">
          <img src="{{{ $campaign->main_image_file_path() }}}" alt="project_image" />
        </div>

      @endif

      <div id="backing">
        <ul>

          @if ( $campaign->goal_type === 'dollars' )
            <li class="stats" id="backers">
              {{{ $campaign->number_of_contributions() }}}
              <span>{{{ $campaign->contributor_reference }}}</span>
            </li>
            <li class="stats">
              {{{ Currency::format($campaign->raised_amount, $campaign->goal_type) }}}
              <span>of {{{ Currency::format($campaign->simple_goal_dollars(), $campaign->goal_type) }}}</span>
            </li>
          @else
            <li class="stats">
              {{{ $campaign->orders() }}} {{{ $campaign->contributor_reference }}}
              <span>of {{{ $campaign->goal_orders }}} needed</span>
            </li>
          @endif

          <li class="stats" id="days" date-element="{{{ date_timestamp_get( new DateTime($campaign->expiration_date) ) }}}" >
          </li>
        </ul>

        @if ( $campaign->raised_amount() < $campaign->simple_goal_dollars() )
          <div id='progress_bg' class='small'>
            <div id='progress' class='' style='width: {{{ $campaign->completion_percentage() }}}%;'>
            </div>
          </div>
        @else
          <div id="progress_bg">
            <div id="progress">
            </div>
            <div id='progress_text'>{{{ ceil($campaign->completion_percentage()) }}}% {{{ $campaign->progress_text }}}/div>
          </div>
        @endif

        <div id="reserve_container">
          <div class="call_to_action_button">
            @if ( $campaign->expired() )
              @if ( $campaign->include_rewards_claimed )
                <div id="backing">
                  <ul>
                    <li class="stats-awards">
                      {{{ $campaign->rewards_claimed }}} Claimed Rewards
                    </li>
                  </ul>
                </div>
              @endif
              <span class="blue_button expired">
                {{{ $campaign->primary_call_to_action_button }}}
              </span>
            @else
              <a href="{{ URL::action( 'CampaignController@checkout', ['$id' => $campaign->id] ) }}" class="blue_button" id="main_cta">
                {{{ $campaign->primary_call_to_action_button }}}
              </a>
            @endif
          </div>
          <div class="powered_by_crowdtilt">
            <a href="http://open.crowdtilt.com/?utm_source=powered_by&utm_medium=base&utm_campaign=link_powered_by" target="_blank"><img src="/images/powered_by@2x.png" width="141px" height="16px" /></a>
          </div>
          <div class="call_to_action_description">
            {{ $campaign->primary_call_to_action_description }}
          </div>
        </div>
      </div>
    </div>

{{--
    <div class="share">
      <%= render 'shared/share_buttons', campaign: @campaign, settings: @settings %>
    </div>
--}}

  </div>
</div>


<div id="campaign_body">
  <div class="container clearfix">
    <div class="main_content {{{ $campaign->rewards() ? 'narrow' : '' }}}">
      @if ( $campaign->include_comments )
        <div class="tabbable {{{ $campaign->rewards() ? 'narrow' : '' }}}">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Description</a></li>
            <li><a href="#tab2" data-toggle="tab" onclick="load_disqus()">Comments <span class="badge badge-info" id="count"></span></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade active in" id="tab1">
              {{ $campaign->main_content }}
            </div>
            <div class="tab-pane fade in" id="tab2">
              <div class="comment_content">
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                  var disqus_shortname = '<%= @campaign.comments_shortname.downcase %>';
                  var disqus_identifier = '<%= @campaign.slug %>';
                  var disqus_title = '<%= @campaign.name %>';
                  var disqus_URL = '<%= "#{request.protocol}#{request.host_with_port}#{request.fullpath}" %>';
                  var disqus_developer = <%= Rails.env.production? ? 0 : 1 %>;
                  var disqus_loaded = false;
                  var load_disqus = function() {
                    if (!disqus_loaded) {
                      var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                      dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                      disqus_loaded = true;
                    }
                  };
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments.</a></noscript>
              </div>
            </div>
          </div>
        </div>
      @else
        {{ $campaign->main_content }}
      @endif
    </div>

    @if ( $campaign->rewards )
      <div class="rewards_sidebar">
        <h3>{{{ $campaign->reward_reference }}}</h3>
        <ul>
          @foreach( $campaign->rewards as $reward )
            @if ( $reward->visible() )
              <li id="rewards_click">
                <a href="{{ URL::action( 'CampaignController@checkout', ['$id' => $campaign->id] ) }}"  onclick="<%= 'return false' if reward.sold_out? || @campaign.expired? %>" class="<%= 'disabled' if reward.sold_out? || @campaign.expired? %>">
                  <p class="price">{{{ $reward->price }}}</p>
                  <p class="title">{{{ $reward->title }}}</p>
                  @if ( $reward->image_url )<p class="image"><img src="{{{ $reward->image_url }}}"></p>@endif
                  <p class="description">{{{ $reward->description }}}</p>
                  <p class="delivery">Estimated Delivery: {{{ $reward->delivery_date }}}</p>
                  @if ( $campaign->expired() )
                    @if ( $reward->include_claimed )
                      <p class="claimed">
                        <%= reward.payments.successful.count %> <%= "of #{reward.number}" unless reward.unlimited? %> claimed <%= '(All gone!)' if reward.sold_out? %>
                      </p>
                    @endif
                  @else
                    <p class="claimed">
                      {{{ $reward->sold_number() }}}
                    </p>
                  @endif
                </a>
              </li>
            @endif
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</div>

<div id="second_call_to_action">
  <div class="container">
    <div>{{ $campaign->secondary_call_to_action_description }}</div>
    <div class="center">
      @if ( $campaign->expired() )
          <span class="blue_button expired">
            {{{ $campaign->secondary_call_to_action_button }}}
          </span>
      @else
        <a href="{{{ URL::to('campaign/checkout') }}}" class="blue_button" id="secondary_cta">
          {{{ $campaign->secondary_call_to_action_button }}}
        </a>
      @endif
      <div class="powered_by_crowdtilt">
        <a href="http://open.crowdtilt.com/?utm_source=powered_by&utm_medium=base&utm_campaign=link_powered_by" target="_blank"><img src="/images/powered_by@2x.png" width="141px" height="16px" /></a>
      </div>
    </div>
  </div>
</div>

@if ( $campaign->faqs->count() > 0 )
  <div id="faq">
    <div class="container clearfix">
      <div class="center">
        <h3>Frequently Asked Questions</h3>
      </div>
      <ul>
        @for($i = 0; $i < ceil($campaign->faqs->count() / 2.0); $i++)
          <li class=''>
            <details class=''>
              <summary class=''>{{{$campaign->faqs[$i]->question}}}</summary>
              <p>{{str_replace("\n", '<br/>', $campaign->faqs[$i]->answer)}}</p>
            </details>
          </li>
        @endfor
      </ul>
      <ul class="col2">
        @for($i = ceil($campaign->faqs->count() / 2.0); $i < $campaign->faqs->count(); $i++)
          <li class=''>
            <details class=''>
              <summary class=''>{{{$campaign->faqs[$i]->question}}}</summary>
              <p>{{str_replace("\n", '<br/>', $campaign->faqs[$i]->answer)}}</p>
            </details>
          </li>
        @endfor
      </ul>
    </div>
  </div>
@endif
</div>

@if ( $campaign->include_comments )
  <div class="hide"><a href="<%= "#{request.protocol}#{request.host_with_port}#{request.fullpath}" %>#disqus_thread" data-disqus-identifier="<%= @campaign.slug %>" id="parse"></a></div>
  <script type="text/javascript">
    var disqus_shortname = '<%= @campaign.comments_shortname.downcase %>';
    (function () {
      $.getScript("//disqus.com/forums/"+disqus_shortname+"/get_num_replies.js?url0="+encodeURIComponent("<%= "#{request.protocol}#{request.host_with_port}#{request.fullpath}" %>"));
    }());
  </script>
  <script>
    window.onload = function(){

      var parse = document.getElementById('parse');
      var count = document.getElementById('count');

      count.innerHTML=parseInt(parse.innerHTML,10);
    };
  </script>
@endif
@stop