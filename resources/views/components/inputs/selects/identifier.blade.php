<select name="identifier_id" class="form-control" id="">
    <option value="" selected disabled>Selecione...</option>
    @foreach ($identifiers as $identifier)
        <option value="{{ $identifier->id }}" @selected(intval(old('identifier_id', $selectedId)) === $identifier->id)>{{ $identifier->name }}</option>
    @endforeach
</select>
