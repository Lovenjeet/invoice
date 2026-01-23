@props(['id', 'columns' => [], 'ajax' => false, 'url' => '', 'title' => 'Data Table'])

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ $title }}</h5>
        @if(isset($actions))
            <div>
                {{ $actions }}
            </div>
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="{{ $id }}" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>

