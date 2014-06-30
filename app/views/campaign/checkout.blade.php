@extends('layouts.base')

@section('header')
@include( 'header' )
@stop

@section('main')
<div id="checkout">
  <div class="container content_box clearfix">
    <div class="main_content">
      <h3><a href="{{{action('CampaignController@show', $campaign->id)}}}">{{{$campaign->name}}}</a></h3>

      <div class="well checkout_block">
        {{ Form::open( array('action' => array('CampaignController@checkoutProcess', $campaign->id), 'method'=>'post', 'role'=>'form', 'id'=>'amount_form', 'novalidate'=>'novalidate') ) }}
        <input type="hidden" name="payment_primary_type" value="alipay">
          @if ( $campaign->payment_type == 'fixed' )
            <div class="quantity_input">
            <h4 class="amount_header">Please choose a quantity: </h4>
            <br/>

              <span>${{{$campaign->simple_fixed_payment_amount()}}}&nbsp; x &nbsp;</span>
              <select id="quantity" name="quantity" style="width:65px">
                <option value="01" selected>01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
              </select>
              <span>&nbsp; = &nbsp;$<span id="total">{{{$campaign->simple_fixed_payment_amount()}}}</span></span>

            </div>

            <input id="amount" type="hidden" name="amount" value="{{{$campaign->simple_fixed_payment_amount()}}}" data-original="{{{$campaign->simple_fixed_payment_amount()}}}"/>

          @else

            <div class="amount_section">
            <h4 class="amount_header">Please enter an amount: </h4>
            <br/>
            <div class="amount_input" style="position:relative">
              <input id="amount" type="text" name="amount" value="@if($campaign->rewards->count()){{{$campaign->rewards[0]->price}}}@endif"/>&nbsp;&nbsp;
              <span style="position:absolute">$</span>
            </div>
            @if( $campaign->payment_type == 'min' )<span class="minimum">Minimum is ${{{$campaign->simple_min_payment_amount()}}}</span>@endif
            <label class="error hide"></label>
            </div>
            <input id="quantity" type="hidden" name="quantity" value="1"/>

          @endif

          @if($campaign->rewards->count())
            <div id="reward_select" data-reference="{{{$campaign->reward_reference}}}">
            <h4>Select your {{{$campaign->reward_reference}}}: </h4>
            <ul>
<!--               <li class="reward_option active <%= raw('hide') if @reward %> clearfix" data-id="0" data-price="<%= number_with_precision(@campaign.min_payment_amount, precision: 2) %>"> -->
              <li class="reward_option active clearfix" data-id="0" data-price="{{{$campaign->simple_min_payment_amount()}}}">
                  <input class="reward_input" type="radio" name="reward" value="0">
                  <label class="price"></label>
                  <div class="reward_description">
                    <p class="title">No {{{$campaign->reward_reference}}}</p>
                  </div>
                  <a class="reward_edit" href="#" style="display:none">edit</a>
              </li>
              @foreach($campaign->rewards as $reward)
              @if($reward->visible())
<!--                 <li class="reward_option <%= raw('active') unless reward.sold_out? %> <%= ((@reward.id == reward.id) ? raw('selected') : raw('hide')) if @reward %> clearfix" data-id="<%= reward.id %>" data-price="<%= number_with_precision(reward.price, precision: 2) %>"> -->
                <li class="reward_option @if(!$reward->sold_out()) active @endif clearfix" data-id="{{{$reward->id}}}" data-price="{{{$reward->price}}}">
<!--                   <input class="reward_input" type="radio" name="reward" value="<%= reward.id %>" <%= raw('disabled') if reward.sold_out? %><%= raw('checked="checked"') if @reward && @reward.id == reward.id %>> -->
                  <input class="reward_input" type="radio" name="reward" value="{{{$reward->id}}}" @if($reward->sold_out()) disabled @endif>
                  <label class="price">${{{$reward->price}}} +</label>
                  <div class="reward_description">
                    <p class="title">{{{$reward->title}}}</p>
                    <p class="claimed">
                      {{{$reward->sold_number()}}} @if(!$reward->is_unlimited()) of {{{$reward->number}}} @endif claimed
                      @if($reward->sold_out()) (All gone!) @endif
                    </p>
                    <p class="description">{{{$reward->description}}}</p>
                    <p class="delivery">Estimated Delivery: {{{$reward->delivery_date}}}</p>
                  </div>
                  <a class="reward_edit" href="#" style="display:none">edit</a>
<!--                   <a class="reward_edit" href="#" style="<%= 'display:none' unless @reward && @reward.id == reward.id %>">edit</a> -->
                </li>
              @endif
              @endforeach
            </ul>
            </div>
          @endif


          <button type="submit" class="btn btn-primary" id="continue_to_checkout">Continue to checkout</button>

        {{form::close()}}
      </div>

    </div>

    <div class="sidebar">
      <div class="custom_content">
        {{$campaign->checkout_sidebar_content}}
      </div>
    </div>

  </div>
</div>

@stop