<div id="overall-header" class="container">
<div class="row header">
  <div class="col-xs-2 col-md-1 col-lg-1">
    <a href="/">
      <img src="{{ asset(env('SITE_THEME_LOGO', session()->get('header_logo'))) }}" alt="" class="img-responsive GhanaLogo">
    </a>
  </div>
  <div class="col-lg-10 col-md-10 col-xs-10">
    <a href="/"><h1>{{ session()->get('header_title', 'Title') }}</h1></a>
    <a href="/"><span class="subhead">{{ session()->get('header_subtitle', 'Subtitle') }}</span></a>
  </div>
</div><!-- .header -->

<nav class="navbar navbar-default argo-navbar">
  <div class="full-width">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div id="navbar" class="navbar-collapse collapse navbar-links">
      @include('master/navbar_items')
    </div><!--/.nav-collapse -->
  </div>
</nav>
</div><!-- container -->
