@if ($errors->all())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
@if (session()->has('message_type'))
    <div class="alert alert-{{ session()->get('message_type') }}">
        {{ session()->get('message_text') }}
    </div>
@endif
