<p>{{ Lang::get('notification.new_report_created.body') }}</p>
@foreach ($urls as $url)
	<a href="{{ $url }}">{{ $url }}</a></br>
@endforeach