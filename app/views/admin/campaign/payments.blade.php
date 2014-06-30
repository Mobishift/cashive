@section('main')
<div id="admin">
  <div class="container content_box clearfix">
    @include('admin/header')

  <div id="admin_payments">
    <h4>{{{$campaign->name}}}</h4>
    @if($payments->count() > 0)
      <br />

      <div class="search id">
        <form accept-charset="UTF-8" action="{{{action('Admin\\CampaignController@getPayments', $campaign->id)}}}" method="get">

          <input id="payment_id" type="text" name="payment_id" placeholder="Search by Payment ID" value="@if($payment_id){{{$payment_id}}}@endif"/>
          <button type="submit" class="btn btn-primary show_loader" data-loader="admin_header">Search</button>
        @if($payment_id)
          {{ link_to_action('Admin\\CampaignController@getPayments', 'clear', $campaign->id, array('class'=>'show_loader')) }}
        @endif
        </form>
      </div>
      <div class="search email">
        <form accept-charset="UTF-8" action="{{{action('Admin\\CampaignController@getPayments', $campaign->id)}}}" method="get">

          <input id="payment_id" type="text" name="email" placeholder="Search by email" value="@if($email){{{$email}}}@endif"/>
          <button type="submit" class="btn btn-primary show_loader" data-loader="admin_header">Search</button>
        @if($email)
          {{ link_to_action('Admin\\CampaignController@getPayments', 'clear', $campaign->id, array('class'=>'show_loader')) }}
        @endif
        </form>
      </div>

      <a href="{{{action('Admin\\CampaignController@getPayments', $campaign->id)}}}.csv" class="pull-right">
        <button type="button" class="btn btn-default">
          <i class="icon-download"></i> Download CSV
        </button>
      </a>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            @if($campaign->goal_type == 'orders')
            <th>Quantity</th>
            @endif
            <th>Amount</th>
            <th>User Fee</th>
            <th>Date</th>
            <th>Status</th>
            <th>Payment ID</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments->get() as $payment)
          <tr>
            <td>{{{$payment->user->name}}}</td>
            <td class="email">{{{$payment->user->email}}}</td>
            @if($campaign->goal_type == 'orders')
            <td>{{{$payment->quantity}}}</td>
            @endif
            <td class="amount">${{{$payment->amount}}}</td>
            <td class="user_fee_amount">${{{$payment->user_fee_amount}}}</td>
            <td>{{{$payment->created_at}}}</td>
            <td class="status">{{{$payment->status_en()}}}</td>
            <td class="ct_payment_id">{{{$payment->out_trade_no}}}</td>
            <td>@if($payment->can_refund())<a class="refund-payment" style="cursor: pointer">Refund</a>@endif</td>
            <td style="width: 18px"><span class="loader" style="display: none"></span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <p>No payments yet!</p>
    @endif

  </div>
  </div>
</div>
@stop