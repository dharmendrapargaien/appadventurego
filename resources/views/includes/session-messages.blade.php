{{-- if session has flashMessages --}}
@if(session()->has('flashMessages'))
	{{-- if session has success  --}}
	@if(count(session('flashMessages')['success']) > 0)
		<div class="callout callout-success">
			<h4><i class="fa fa-check"></i> Success</h4>
			<ul class="unstyled">
				@foreach(session('flashMessages')['success'] as $message)
				<li>{{ $message }}</li>
				@endforeach
			</ul>
		</div>	
	@endif
	{{--end if session has success  --}}
	{{-- if session has error  --}}
	@if(count(session('flashMessages')['error']) > 0)
		<div class="callout callout-danger">
			<h4><i class="fa fa-exclamation-circle"></i> Errors</h4>
			<ul class="unstyled">
				@foreach(session('flashMessages')['error'] as $message)
					<li>{{$message}}</li>
				@endforeach
			</ul>
		</div>
	@endif
	{{--end if session has error  --}}
	{{-- if session has warning  --}}
	@if(count(session('flashMessages')['warning']) > 0)
		<div class="callout callout-warning">
			<h4><i class="fa fa-warning"></i> Warnings</h4>
			<ul class="unstyled">
				@foreach(session('flashMessages')['warning'] as $message)
					<li>{{$message}}</li>
				@endforeach
			</ul>
		</div>
	@endif
	{{--end if session has warning  --}}
	{{-- if session has info  --}}
	@if(count(session('flashMessages')['info']) > 0)
		<div class="callout callout-info">
			<h4><i class="fa fa-info"></i> Info</h4>
			<ul class="unstyled">
				@foreach(session('flashMessages')['info'] as $message)
					<li>{{$message}}</li>
				@endforeach
			</ul>
		</div>
	@endif
	{{--end if session has info  --}}
@endif
{{-- end if session has flashMessages --}}