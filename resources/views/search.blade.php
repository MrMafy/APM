@if (isset($data) && !$data->isEmpty())
    @foreach ($data as $el)
        <div class="alert alert-info">
            <h3>{{ $el->projNum }}</h3>
            <p>{{ $el->projManager }}</p>
            <p><small>{{ $el->objectName }}</small></p>
            <a href="{{ route('project-data-one', $el->id) }}">
                <button class="btn btn-outline-primary">Детальнее</button>
            </a>
        </div>
    @endforeach
@else
    <p class="nothing">Нет соответствующих записей</p>
{{--    <div class="d-flex justify-content-center">--}}
{{--        {!! $data->appends(['search' => $search_text])->links() !!}--}}
{{--    </div>--}}
@endif
