@if ($errors->all())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
@if (session()->has('alert_type'))
    <div class="alert alert-{{ session()->get('alert_type') }}">
        {{ session()->get('alert_text') }}
    </div>
@endif
