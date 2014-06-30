@section('main')
<div id="admin">
  <div class="container content_box clearfix">

  @include('admin/header')


  <div id="admin_campaigns">
    {{ Form::open( array('action' => array('Admin\CampaignController@update', $campaign->id), 'method'=>'put', 'role'=>'form', 'files' => true, 'id'=>'admin_campaign_form', 'novalidate'=>'novalidate') ) }}
    {{--
    <%= form_for([:admin, @campaign], multipart: true, html: { class: "campaign_form", id: "admin_campaign_form" }) do |f| %>
    --}}

  <fieldset>
    <legend class="foldable"><a>Basic Information</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix form-group">
        <p class="explanation">This'll be both the page title (&lt;title&gt;&lt;/title&gt;) and the name in the header</p>
        <div class="col-md-6">
          <label>Campaign Name</label>
          {{ Form::text('campaign[title]', $campaign->name, array('class' => 'form-control')) }}
        </div>
      </div>
      <div class="field clearfix form-group">
        <p class="explanation">If you set this to be the default campaign, the homepage will redirect to the campaign.</p>
        <div class="col-md-6">
          <label>Default Campaign?</label>
          {{ Form::checkbox('campaign[is_default]', '1', $settings->default_campaign_id == $campaign->id ? true:false) }}
        </div>
      </div>
      <div class="field clearfix form-group">
        <p class="explanation">You can choose to set your goal based on the dollar amount you raise, or by the number of orders your contributors make.</p>
        <div class="col-md-6">
        <label>Goal</label>
          <div class="radio">
            <label>
              {{ Form::radio('campaign[goal_type]', 'dollars', $campaign->goal_type == 'dollars' ? true:false, array('id'=>'goal_type_dollars')) }} Dollar Amount
            </label>
          </div>
          <div class="amount_input currency" style="{{ $campaign->goal_type == 'dollars' ? "" : "display:none" }}">
            {{ Form::text('campaign[goal_dollars]', $campaign->goal_dollars, array('class' => 'form-control')) }}
            <span style="position:absolute">$</span>
          </div>

          <div class="radio">
            <label>
              {{ Form::radio('campaign[goal_type]', 'orders', $campaign->goal_type == 'orders' ? true:false, array('id'=>'goal_type_orders')) }} Number of Orders
            </label>
          </div>

          <div class="orders_input" style="{{ $campaign->goal_type == 'orders' ? "" : "display:none" }}">
            {{ Form::text('campaign[goal_orders]', $campaign->goal_orders, array('class' => 'form-control')) }}
          </div>
        </div>
      </div>

      <div class="field clearfix form-group">
        <p class="explanation">When your campaign to raise money should end.</p>
        <div class="col-md-6">
        <label>Expiration Date</label>
          {{ Form::text('campaign[expiration_date]', $campaign->expiration_date, array("placeholder" => "Click to select date", 'class' => 'form-control')) }}
        </div>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <legend class="foldable"><a>Payment Details</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix form-group">
        <div id="flexible_payment_options" style="{{ $campaign->goal_type == 'dollars' ? "" : 'display:none' }}">
          <div class="radio">
            <label>{{ Form::radio('campaign[payment_type]', 'any', $campaign->payment_type == 'any' ? true:false, array('id'=>'campaign_payment_type_any')) }} <strong>Any</strong> amount is fine.</label>
          </div>
          <div class="radio">
            <label>
              {{ Form::radio('campaign[payment_type]', 'min', $campaign->payment_type == 'min' ? true:false, array('id'=>'campaign_payment_type_min')) }} Require a <strong>minimum amount</strong> for each contribution. <i>(ex. $10 min. contribution)</i>
            </label>
          </div>
          <div id="min-amount" style="{{ $campaign->payment_type == 'min' ? "" : 'display:none' }}">
            <label class="inline"><strong>Minimum amount: $ &nbsp;</strong></label>
            {{ Form::text('campaign[min_payment_amount]', $campaign->simple_min_payment_amount(), array('class' => 'form-control')) }}
          </div>
        </div>

        <div class="radio">
          <label>{{ Form::radio('campaign[payment_type]', 'fixed', ($campaign->payment_type == 'fixed'|| $campaign->goal_type == 'orders') ? true:false, array('id'=>'campaign_payment_type_fixed')) }} Require a <strong>specific amount</strong> from all contributors. <i>(ex. $150 from each person)</i></label>
        </div>
        <div id="preset-amount" style="{{ $campaign->payment_type == 'fixed' ? "" : 'display:none' }}">
          <label class="inline"><strong>Preset amount: $ &nbsp;</strong></label>
          {{ Form::text('campaign[fixed_payment_amount]', $campaign->simple_fixed_payment_amount(), array('id'=>'campaign_fixed_payment_amount', 'class' => 'form-control')) }}
        </div>
      </div>

      <div id="global-shipping" class="field clearfix form-group" style="{{{ $campaign->payment_type == 'fixed' ? '' : 'display:none' }}}">
        <p id="campaign_collect_shipping_message" class="explanation" style="{{{ $campaign->payment_type == 'fixed' ? '' : 'display:none' }}}">Requires the user to include shipping address when checking out.</p>
        <p id="campaign_collect_shipping_warning" class="explanation message" style="{{{ $campaign->payment_type == 'fixed' ? '' : 'display:none' }}}">If you're creating a campaign with rewards, you can select whether or not to collect shipping as you create each reward below.</p>
        <div id="global-shipping-check">
          <label class="message">Collect Shipping Address?</label>
          {{ Form::checkbox('collect_shipping_flag', 'collect_shipping_flag', false) }}
        </div>
      </div>

      <div class="field clearfix form-group">
        <p class="explanation">Requires the user to provide additional information in a text box, such as sizes, colors, or other details.</p>
        <label>Collect Additional Information?</label>
          {{ Form::checkbox('campaign[collect_additional_info]', $campaign->collect_additional_info, $campaign->collect_additional_info, array('id'=>'campaign_collect_additional_info')) }}
          <div class="additional_info_input" style="{{{ $campaign->collect_additional_info ? '' : 'display:none' }}}">
            <label>Include a message describing what you need:</label>
            {{ Form::textarea('campaign[additional_info_input]', $campaign->additional_info_input, array('id'=>'campaign_additional_info_label', 'rows'=>2, 'class' => 'form-control')) }}
          </div>
      </div>

      <div class="field clearfix form-group">
        <p class="explanation">This passes the {{{Config::get('app.processing_fee_percentage')}}}% + {{{Config::get('app.processing_fee_flat_cents')}}}¢ per-transaction processing fee onto your contributors, adding the fee amount to their contribution amount when they check out. If you do not select this option, the processing fee will be deducted from the amount raised before being disbursed to your bank account.</p>
        <label>Pass Credit Card Processing Fee to Contributors?</label>
        {{ Form::checkbox('campaign[apply_processing_fee]', $campaign->apply_processing_fee, $campaign->apply_processing_fee, array('id'=>'campaign_apply_processing_fee')) }}
      </div>
    </div>

  </fieldset>

  <fieldset>
    <legend class="foldable"><a>Rewards (optional)</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix form-group">
        <div id="no-rewards" style="{{ $campaign->payment_type == 'fixed' ? '': 'display:none' }}">
          <p class="explanation inline">Rewards cannot be used for campaigns that require a specific amount from contributors.</p>
        </div>

        <div id="rewards" style="{{ $campaign->payment_type == 'fixed' ? 'display:none' : '' }}">
          <p class="explanation inline">Assign rewards for different levels of contribution. When contributors check out, they can choose to associate a reward with their contribution. You can set a cap on how many of each reward are available and the minimum contribution amount needed to claim one.</p>
          <div class="reference">
            <label>How To Reference a Reward (i.e. reward, perk, option)</label>
            {{ Form::text('campaign[reward_reference]', $campaign->reward_reference, array('id'=>'campaign_reward_reference', 'class' => 'form-control')) }}
            @if ($campaign->persisted && $campaign->expired)
              <label>Display number of rewards claimed after campaign has ended?</label>
                {{ Form::checkbox('campaign[include_rewards_claimed]', $campaign->include_rewards_claimed, $campaign->include_rewards_claimed, array('id'=>'campaign_include_rewards_claimed')) }}
            @endif
          </div>
          <ul>
            @foreach( $campaign->rewards as $index=>$reward )
              <li>
                <table class="table">
                  <tr>
                    <th>Reward</th>
                    <th>Number Claimed</th>
                    <th>Delete?</th>
                  </tr>
                  <tr>
                    <td>
                      <label>Minimum Contribution To Claim</label>
                      <div class="currency">
                        {{ Form::text("reward[$index][price]", $reward->price, array('class' => 'form-control')) }}
                        <span style="position:absolute">$</span>
                      </div>
                      <label>Title</label>
                      {{ Form::text("reward[$index][title]", $reward->title, array('class' => 'form-control')) }}
                      <span style="position:absolute">$</span>
                      <label>Image URL</label>
                      {{ Form::text("reward[$index][image_url]", $reward->image_url, array('class' => 'form-control')) }}
                      <label>Description</label>
                      {{ Form::textarea("reward[$index][description]", $reward->description, array('class' => 'form-control')) }}
                      <label>Estimated Delivery Date (i.e. May 2014)</label>
                      {{ Form::text("reward[$index][delivery_date]", $reward->delivery_date, array('class' => 'form-control')) }}
                      <label>Number Available (leave blank if unlimited)</label>
                      {{ Form::text("reward[$index][number]", $reward->unlimited ? '':$reward->number, array('class' => 'form-control')) }}
                      <label>Collect shipping address for this reward?</label>
                      {{ Form::checkbox("reward[$index][collect_shipping_flag]", $reward->collect_shipping_flag, $reward->collect_shipping_flag) }}
                      @if ( $campaign->persisted && $campaign->expired )
                        <label>Display number of reward claimed after campaign has ended?</label>
                        {{ Form::checkbox("reward[$index][include_claimed]", $reward->include_claimed, $reward->include_claimed) }}
                      @endif
                      {{ Form::hidden("reward[$index][id]", $reward->id) }}
                    </td>
                    {{-- 
                    <td>{{{ $reward->payments->successful->count }}}</td>
                    <td>
                      @if ( $reward->payments->successful->count > 0 )
                        N/A<input type="hidden" name="reward[][delete]" value=""/>
                      @else
                        <input type="checkbox" name="reward[][delete]" value="delete"/>
                      @endif
                    </td>
                    --}}
                  </tr>
                </table>
              </li>
            @endforeach
            <?php $reward_count = $campaign->rewards->count(); ?>
            <li>
                <table class="table">
                  <tr>
                    <th>Reward</th>
                    <th>Number Claimed</th>
                    <th>Delete?</th>
                  </tr>
                  <tr>
                    <td>
                      <label>Minimum Contribution To Claim</label>
                      <div class="currency">
                        {{ Form::text("reward[".($reward_count+1)."][price]", '0', array('class' => 'form-control')) }}
                        <span style="position:absolute">$</span>
                      </div>
                      <label>Title</label>
                      {{ Form::text('reward['.($reward_count+1).'][title]', '', array('class' => 'form-control')) }}
                      <span style="position:absolute">$</span>
                      <label>Image URL</label>
                      {{ Form::text('reward['.($reward_count+1).'][image_url]', '', array('class' => 'form-control')) }}
                      <label>Description</label>
                      {{ Form::textarea('reward['.($reward_count+1).'][description]', '', array('class' => 'form-control')) }}
                      <label>Estimated Delivery Date (i.e. May 2014)</label>
                      {{ Form::text('reward['.($reward_count+1).'][delivery_date]', '', array('class' => 'form-control')) }}
                      <label>Number Available (leave blank if unlimited)</label>
                      {{ Form::text('reward['.($reward_count+1).'][number]', '', array('class' => 'form-control')) }}
                      <label>Collect shipping address for this reward?</label>
                      {{ Form::checkbox('reward['.($reward_count+1).'][collect_shipping_flag]', '1') }}
                      @if ( $campaign->persisted && $campaign->expired )
                        <label>Display number of reward claimed after campaign has ended?</label>
                        {{ Form::checkbox('reward['.($reward_count+1).'][include_claimed]', '1') }}
                      @endif
                    </td>
                    <td>0</td>
                    <td>
                        <input type="checkbox" name="reward[{{{ $reward_count+1 }}}][delete]" value="1"/>
                    </td>
                  </tr>
                </table>
              </li>
          </ul>
          <a id="reward-add" href="#">Add Reward</a>
        </div>
      </div>
    </div>

  </fieldset>

  <fieldset>
    <legend class="foldable"><a>Page Content</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix form-group">
        <p class="explanation">A few other ideas: supporter, order, preorder, donor, contributor, participant, purchase.</p>
        <div class="col-md-6">
        <label>How To Reference a Contributor (i.e. 'backer' )</label>
        {{ Form::text('campaign[contributor_reference]', $campaign->contributor_reference, array('id'=>'campaign_contributor_reference', 'class' => 'form-control')) }}
        </div>
      </div>

      <div class="field clearfix form-group">
        <p class="explanation">We STRONGLY recommend including a video with your project. Just paste the youtube video id (it's the 11 character code that comes at the very end of the youtube url). You can also specify a placeholder image to show over top of the video in case you don't like your video's thumbnail.  Alternatively, you can choose to simply upload an image to show instead of a video.</p>
        <div class="radio">
          <label>{{ Form::radio('campaign[media_type]', 'video', $campaign->media_type == 'video' ? true:false, array('id'=>'campaign_media_type_video')) }} Use Video</label>
        </div>
        <div class="radio">
          <label>{{ Form::radio('campaign[media_type]', 'image', $campaign->media_type == 'image' ? true:false, array('id'=>'campaign_media_type_image')) }} Use Image</label>
        </div>

        <div id="video-options" class="col-md-6" style="{{ $campaign->media_type == 'video' ? "" : "display:none" }}">
          <label>Youtube Video ID</label>
          {{ Form::text('campaign[video_embed_id]', $campaign->video_embed_id, array('id'=>'campaign_video_embed_id', 'class' => 'form-control')) }}
          <label>Video Placeholder</label>
          @if ( $campaign->video_placeholder_file_name )
            {{ HTML::image( $campaign->video_placeholder_file_name ) }}<br/>
            {{ Form::file('campaign[video_placeholder]') }}<br/>
            {{ Form::checkbox('campaign[video_placeholder_delete]') }}<span>Delete current video</span>
          @else
            {{ Form::file('campaign[video_placeholder]') }}
          @endif
        </div>

        <div id="image-options" class="col-md-6" style="{{ $campaign->media_type == 'image' ? "" : "display:none" }}">
          <label>Image</label>
          @if ( $campaign->main_image_file_name )
            {{ HTML::image( $campaign->main_image_file_path() ) }}<br/>
            {{ Form::file('campaign[main_image]') }}<br/>
            {{ Form::checkbox('campaign[main_image_delete]') }}<span>Delete current image</span>
          @else
            {{ Form::file('campaign[main_image]') }}
          @endif
        </div>
      </div>

      <div class="field clearfix form-group">
        <p class="explanation">This text is displayed on the primary call to action button. Common choices include 'Pay', 'Contribute', 'Back this Project', 'Reserve for $199', etc.</p>
        <div class="col-md-6">
          <label>Primary Call to Action Button</label>
          {{ Form::text('campaign[primary_call_to_action_button]', $campaign->primary_call_to_action_button, array('id'=>'campaign_primary_call_to_action_button', 'class' => 'form-control')) }}
        </div>
      </div>

      <div class="field clearfix form-group">
        <label>Primary Call to Action Description</label>
        <p class="explanation inline">This formatted text gets displayed near the primary call to action button.</p>
        {{ Form::textarea('campaign[primary_call_to_action_description]', $campaign->primary_call_to_action_description, array('id'=>'campaign_primary_call_to_action_description', 'class' => 'form-control')) }}
      </div>

      <div class="field clearfix form-group">
        <label>Main Content</label>
        <p class="explanation inline">This is the meat and potatoes of your website&mdash;include rich text and images to engage your contributors!</p>
        {{ Form::textarea('campaign[main_content]', $campaign->main_content, array('id'=>'campaign_main_content', 'class' => 'form-control')) }}
      </div>

      <div class="field clearfix form-group">
        <p class="explanation">This text is displayed on the second call to action button position near the bottom of the homepage.</p>
        <div class="col-md-6">
        <label>Secondary Call to Action Button</label>
          {{ Form::text('campaign[secondary_call_to_action_button]', $campaign->secondary_call_to_action_button, array('id'=>'campaign_secondary_call_to_action_button', 'class' => 'form-control')) }}
        </div>
      </div>

      <div class="field clearfix form-group">
        <label>Secondary Call to Action Description</label>
        <p class="explanation inline">This formatted text gets displayed near the secondary call to action button.</p>
          {{ Form::textarea('campaign[secondary_call_to_action_description]', $campaign->secondary_call_to_action_description, array('id'=>'campaign_secondary_call_to_action_description', 'class' => 'form-control')) }}
      </div>

      <div class="field clearfix form-group">
        <label>FAQs</label>
        <p class="explanation inline">Add as many question/answer pairs as you'd like to display in the FAQ section of the homepage.</p>
          <ul class="faq sortable">
          @foreach( $campaign->faqs as $index=>$faq )
            <li>
              <div class="row">
                <div class="col-md-1 faq_index">{{{$index+1}}}</div>
                <input type="hidden" name="faq[{{{$index+1}}}][sort_order]" value="{{{$index+1}}}" />
                <div class="col-md-5">
                  <textarea name="faq[{{{$index+1}}}][question]" placeholder="Question" class="form-control" >{{{ $faq->question }}}</textarea>
                </div>
                <div class="col-md-5">
                  <textarea name="faq[{{{$index+1}}}][answer]" placeholder="Answer" class="form-control" >{{{ $faq->answer }}}</textarea>
                </div>
                <div class="col-md-1">
                  <a href="#" class="faq-delete icon-trash"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </div>
            </li>
          @endforeach
          <li>
            <div class="row">
              <div class="col-md-1 faq_index">{{{ $campaign->faqs()->count() + 1 }}}</div>
              <input type="hidden" name="faq[{{{ $campaign->faqs()->count() + 1 }}}][sort_order]" value="{{{ $campaign->faqs()->count() + 1 }}}" />
              <div class="col-md-5">
                <textarea name="faq[{{{ $campaign->faqs()->count() + 1 }}}][question]" placeholder="Question" class="form-control" ></textarea>
              </div>
              <div class="col-md-5">
                <textarea name="faq[{{{ $campaign->faqs()->count() + 1 }}}][answer]" placeholder="Answer" class="form-control" ></textarea>
              </div>
              <div class="col-md-1">
                <a href="#" class="faq-delete icon-trash"><span class="glyphicon glyphicon-trash"></span></a>
              </div>
            </div>
          </li>
        </ul>
        <a id="faq-add" href="#">Add FAQ</a>
      </div>
    </div>
  </fieldset>
{{-- 
  <fieldset>
    <legend class="foldable"><a>Comments</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix">
        <label>Include a user comment section?</label>
        <p class="explanation">Add a comment section where backers can discuss your campaign. You will need a free moderator account from <a href="https://disqus.com/admin/signup/" target="_blank">Disqus.</a></p>
        <%= f.check_box :include_comments %>
        <div class="include_comments_input" style="<%= @campaign.include_comments ? "" : "display:none" %>">
          <label>Enter your Disqus Site Shortname (<a href="https://disqus.com/admin/signup/" target="_blank">Need one?</a>)</label>
          <div class="input-append">
            <%= f.text_field :comments_shortname, :placeholder => 'shortname' %>
            <span class="add-on">.disqus.com</span>
          </div>
        </div>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <legend class="foldable"><a>Checkout Page Content</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix">
        <label>Checkout Sidebar Content</label>
        <p class="explanation inline">This content is displayed on the right sidebar of the checkout page. A question/answer format is often used.</p>
        <%= f.cktext_area :checkout_sidebar_content %>
      </div>

      <div class="field clearfix">
        <label>Confirmation Page Content</label>
        <p class="explanation inline">This content appears on the confirmation page when a contributor completes a successful transaction.</p>
        <%= f.cktext_area :confirmation_page_content %>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <legend class="foldable"><a>Sharing Details</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix">
        <p class="explanation">This is the default text that will be used for the tweet button.</p>
        <label>Tweet Text</label>
        <%= f.text_area :tweet_text, rows: 2 %>
      </div>

      <div class="field clearfix">
        <p class="explanation">The title shown when your site is shared via Facebook. Leave this blank if you want to use your project name.</p>
        <label>Facebook Title</label>
        <%= f.text_field :facebook_title %>
      </div>

      <div class="field clearfix">
        <p class="explanation">The description shown when your site is shared via Facebook.</p>
        <label>Facebook Description</label>
        <%= f.text_area :facebook_description, rows: 2 %>
      </div>

      <div class="field clearfix">
        <p class="explanation">The image shown when your site is shared via facebook. This should have a square aspect ratio and be at least 200px by 200px</p>
        <label>Facebook Image</label>
        <% if @campaign.facebook_image.file? %>
          <%= image_tag @campaign.facebook_image.url(:thumb) %><br/>
          <%= f.file_field :facebook_image %><br/>
          <%= f.check_box :facebook_image_delete %><span>Delete current image</span>
        <% else %>
          <%= f.file_field :facebook_image %>
        <% end  %>
      </div>
    </div>

  </fieldset>

  <fieldset>
    <legend class="foldable"><a>Publish</a></legend>

    <div class="foldable default_expanded">
      <div class="field clearfix">
        <p class="explanation">Check this box and click save below to make this campaign visible to non-admins.<br><br>If this box is checked, the campaign will show up on your homepage and be accessible to all visitors to your site.</p>
        <label>Visible to non-admins?</label>
        <%= f.check_box :published_flag %>
      </div>

      <div class="field clearfix">
        <% if !@campaign.production_flag %>
          <p class="explanation">Check this box and click save below once you're ready to activate payments and launch your campaign.<br><br>WARNING: Once you activate payments and launch your campaign, you won't be able to run further test transactions.</p>
          <% if @settings.payments_activated? %>
            <label>Activate payments and launch your campaign</label>
            <%= f.check_box :production_flag %>
          <% else %>
            You must set up your payment processor before activating payments and launching your campaign. <br><br>Visit <%= link_to "Payment Settings", admin_bank_account_path, target: "_blank" %> from the admin menu to do this.
          <% end %>
        <% else %>
          <p class="explanation">You have activated payments for this campaign, which means transactions WILL be processed. This cannot be undone for this campaign. If you activated payments by mistake, we recommend ending and un-publishing this campaign and creating a new one.</p>
          <label><i class="icon-ok"></i> Payments activated</label>
        <% end %>
      </div>
    </div>
  </fieldset>
--}}
  @if ( $campaign->persisted )
    <fieldset>
      <legend class="foldable"><a>Advanced Settings</a></legend>
      <div class="foldable">
          <div class="field clearfix">
            <p class="explanation">An api key to use the Crowdtilt Open API for <%=@campaign.name %>. This allows you to hook your campaign data into third party apps like <a href="https://backerkit.com/" target="_blank">BackerKit</a>.  More coming soon!</p>
            <label>API Endpoint</label>
            <p><%= api_campaign_url(id: @campaign.id)%></p>
            <label>API key</label>
            <p><%= @settings.api_key %></p>
            <label>Example Usage (Return a JSON array of campaign data)</label>
            <p><%= api_campaign_url(id: @campaign.id)%>?api_key=<%= @settings.api_key %></p>
            <label>Example Usage (Return a JSON array of all payments)</label>
            <p><%= api_campaign_url(id: @campaign.id)%>/payments?api_key=<%= @settings.api_key %></p>
          </div>
      </div>
    </fieldset>
  @endif

  {{ Form::submit('Save', array('class' => 'btn btn-primary show_loader')) }}
  <span class="loader" data-loader="campaign_form" style="display:none"></span>

{{form::close()}}
  </div>
  </div>
</div>
@stop