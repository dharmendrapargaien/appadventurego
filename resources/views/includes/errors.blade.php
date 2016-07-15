@if (count($errors) > 0)
    <div class="callout callout-danger">
        <h4><i class="fa fa-exclamation-circle"></i> Errors</h4>
        <ul class="unstyled">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif