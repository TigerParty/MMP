<footer>
  <div class="row">
    <div class="links">
      @if(!Auth::check())
      <h3> <a href="{{ asset('/login') }}">{{ Lang::get('form.btn.login') }} </a></h3>
      @elseif(Auth::check())
      <h3> <a href="{{ asset('/logout') }}">{{ Lang::get('form.btn.logout') }} </a></h3>
      @endif
    </div>
    <div class="links">
      <h3> <a href="{{ asset('/tutorial') }}">{{ Lang::get('site.nav_bar.download_app') }}</a></h3>
    </div>
    <div class="links">
      <h3> <a href="{{ asset('/') }}">
            {{ Lang::get('site.nav_bar.home') }}
          </a>
      </h3>
    </div>
    <div class="links">
      <h3> <a href="#">{{ Lang::get('site.nav_bar.explore_dw') }}</a></h3>
      <ul>
          <li> <a href="{{ (config('argodf.new_map_page_enabled')) ? asset('/explore') : asset('/map') }}">
                {{ Lang::get('site.nav_bar.map') }}
              </a>
          </li>
          @if(config('components.chart_gs.enabled'))
          <li>
            <a href="{{ asset('/chart_gs') }}">
              {{ Lang::get('site.nav_bar.chart_gs') }}
            </a>
          </li>
          @endif

          <li>
            <a href="{{ asset('/project') }}">
              {{ Lang::get('site.nav_bar.project') }}
            </a>
          </li>
      </ul>
    </div>
  </div>

  <div class="row">
    <img src="{{ asset('/images/footer.png') }}">
  </div>
</footer>
