<meta property="fb:app_id" content="{{ Config::get('services.fb_comment_plugin.app_id') }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ request()->url() }}" />
<meta property="og:title" content="{{ Lang::get('site.title') }}" />
<meta property="og:image" content="{{ asset(session()->get('header_logo')) }}" />
<meta property="og:description" content="{{ Lang::get('site.subtitle') }}" />
