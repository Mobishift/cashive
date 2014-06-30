<div id="header">
  <div class="container center">
    @if ($settings->logo_image_file_name)

      <a href="/"><img src="{{{ $settings->logo_image_file_name }}}" alt="logo" /></a>

    @else

      <h1><a href="/">{{{ $settings->site_name }}}</a></h1>

    @endif

    @if ( $settings->header_link_text && $settings->header_link_url )
    <div class="header_link">
      <a href="{{{ $settings->header_link_url }}}" target="_blank" class="btn">{{{ $settings->header_link_text }}}</a>
    </div>
    @endif

  </div>
</div>