@section('main')
<div id="admin">
  <div class="container content_box clearfix">

    @include('admin/header')

    <div id="admin_homepage">
    {{ Form::open( array('action' => array('Admin\CampaignController@getHomepage'), 'method'=>'post', 'role'=>'form', 'files' => true, 'id'=>'admin_homepage_form', 'novalidate'=>'novalidate') ) }}
    {{--
      <%= form_for(@settings, url: admin_homepage_path, multipart: true, html: { id: "admin_homepage_form" }) do |f| %>
    --}}

        <fieldset>
          <legend class="foldable"><a>Homepage Content</a></legend>

          <div class="foldable default_expanded">
            <div class="field clearfix">
              <p class="explanation inline">This content is shown on the homepage in addition to the campaign titles.</p>
              {{--
              <%= f.cktext_area :homepage_content %>
              --}}
              {{ Form::textarea('homepage_content', $settings->homepage_content, array('id'=>'homepage_content', 'rows'=>2, 'class' => 'form-control')) }}
            </div>
          </div>

        </fieldset>
{{--
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
              <p class="explanation">The image shown when your site is shared via facebook. This should have a square aspect ratio and be at least 200px by 200px.</p>
              <label>Facebook Image</label>
              <% if @settings.facebook_image.file? %>
                <%= image_tag @settings.facebook_image.url(:thumb) %><br/>
                <%= f.file_field :facebook_image %><br/>
                <%= f.check_box :facebook_image_delete %><span>Delete current image</span>
              <% else %>
                <%= f.file_field :facebook_image %>
              <% end  %>
            </div>
          </div>

        </fieldset>
--}}
        {{ Form::submit('Save', array('class' => 'btn btn-primary show_loader')) }}
        <span class="loader" data-loader="project_form" style="display:none"></span>

      {{form::close()}}
    </div>
  </div>
</div>
@stop