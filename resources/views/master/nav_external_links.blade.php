@foreach($links as $link => $object)
  @if(is_string($object))
    <li>
      <a href="{{ $object }}">
        <span>{{ $link }}</span>
      </a>
    </li>
  @elseif(is_array($object) && $index==0)
    <li class="dropdown external_links_dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
         aria-expanded="false">
        <span>{{ $link }}<span class="caret"></span></span>
      </a>
      <ul class="dropdown-menu">

        @foreach($object as $sub_link=>$url)
          @include('master.nav_external_links', array('links' => array($sub_link=>$url), 'index'=> $index+1))
        @endforeach
      </ul>
    </li>
  @elseif(is_array($object) && $index>0)
    <li class="sub-dropdown">
      <a class="disabled">
        {{ $link }}<span class="caret"></span>
      </a>
      <ul>
        @foreach($object as $sub_link=>$url)
          @include('master.nav_external_links', array('links' => array($sub_link=>$url), 'index'=> $index+1))
        @endforeach
      </ul>
    </li>
  @endif

@endforeach

