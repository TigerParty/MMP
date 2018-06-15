 <ul class="nav navbar-nav" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
    <li itemprop="name" class="{{ \App\Services\NavBarService::isActive('explore') }}">
      <a href="{{ (config('argodf.new_map_page_enabled')) ? asset('/explore') : asset('/map') }}" itemprop="url">
        <span>{{ trans('site.nav_bar.explore_dw') }}</span>
      </a>
    </li>

    <li class="{{ \App\Services\NavBarService::isActive('citizen_sms') }}" itemprop="name">
      <a href="{{ asset('/feedback') }}" itemprop="url">
        <span>{{ trans('site.nav_bar.citizen_sms') }}</span>
      </a>
    </li>

    @if(config('argodf.syllabus_enabled'))
    <li class="{{ \App\Services\NavBarService::isActive('syllabus') }}" itemprop="name">
      <a href="{{ asset('/syllabus') }}" itemprop="url">
        <span>{{ trans('site.nav_bar.syllabus') }}</span>
      </a>
    </li>
    @endif

    @if(config('argodf.external_nav_links'))
      @include('master.nav_external_links', array('links' => config('argodf.external_nav_links'), 'index' => 0))
    @endif
@if(\App\Services\NavBarService::isActive('/')!="active" && \App\Services\NavBarService::isActive('tutorial')!="active")
</ul><!--/ left nav items-->
<ul class="nav navbar-nav navbar-right">
@endif
    <li>
      <a href="#"
         class="dropdown-toggle"
         data-toggle="dropdown"
         role="button"
         aria-haspopup="true"
         aria-expanded="false">
         <span>
          {{ trans('site.nav_bar.how_it_works.main_title') }}
          <span class="caret"></span>
         </span>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a href="{{ asset('tutorial') }}">
            {{ trans('site.nav_bar.how_it_works.app') }}
          </a>
        </li>
      </ul>
    </li>
    <li class="{{ \App\Services\NavBarService::isActive('tutorial') }}" itemprop="name">
      <a href="{{ asset('tutorial') }}">
        <span>{{ trans('site.nav_bar.download_app') }}</span>
      </a>
    </li>
    @if( argo_is_accessible(config('argodf.admin_function_priority')))
        <li class="dropdown {{ \App\Services\NavBarService::isActive('admin') }}">
          <a href="admin_index.html"
             class="dropdown-toggle"
             data-toggle="dropdown"
             role="button"
             aria-haspopup="true"
             aria-expanded="false">
            <span>
                {{ Auth::user()->permission_level->name }}
            	<span class="caret"></span>
            </span>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="{{ asset('/admin') }}">
                {{ trans('site.nav_bar.admin_panel.name') }}
              </a>
            </li>

            <li>
              <a href="{{ asset('/admin/dync_form') }}">
                {{ trans('site.nav_bar.admin_panel.form') }}
              </a>
            </li>

            <li>
              <a href="{{ asset('/admin/notification') }}">
                {{ trans('site.nav_bar.admin_panel.notification') }}
              </a>
            </li>

            @if(config('argodf.project_status_enabled'))
            <li>
              <a href="{{ asset('/admin/status') }}">
                {{ trans('site.nav_bar.admin_panel.status') }}
              </a>
            </li>
            @endif

            @if(config('argodf.customize_homepage_enabled'))
            <li>
              <a href="{{ asset('/admin/customize_homepage') }}">
                {{ trans('site.nav_bar.admin_panel.customize_homepage') }}
              </a>
            </li>
            @endif

            @if(config('argodf.user_management_enabled'))
            <li>
              <a href="{{ asset('/management') }}">
                {{ trans('site.nav_bar.admin_panel.user_management') }}
              </a>
            </li>
            @endif

            @if( Auth::check() && config('argodf.external_menu_links'))
              @foreach(config('argodf.external_menu_links') as $title => $link)
              <li>
                <a href="{{ asset($link) }}">
                  {{ $title }}
                </a>
              </li>
              @endforeach
            @endif

            <li role="separator" class="divider">
            </li>

            <li>
              <a href="{{ asset('/logout') }}">
                {{ trans('form.btn.logout') }}
              </a>
            </li>
          </ul>
        </li>
        @elseif(Auth::check())
        <li><a href="{{ asset('/logout') }}">{{ trans('form.btn.logout') }}</a></li>
        @endif

        @if(!Auth::check())
        <li><a href="{{ asset('/login') }}">{{ trans('form.btn.login') }}</a></li>
    @endif
</ul>


