@extends('admin.layouts.admin')

@section('title', trans('changelog::admin.title'))

@push('footer-scripts')
    <script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
    <script>
        const sortable = Sortable.create(document.getElementById('categories'), {
            animation: 150,
            group: 'category',
            handle: '.sortable-handle'
        });

        function serialize(categories) {
            const serialized = [];

            [].slice.call(categories.children).forEach(function (category) {

                serialized.push({
                    id: category.dataset['categoryId']
                });
            });

            return serialized
        }

        const saveButton = document.getElementById('save');
        const saveButtonIcon = saveButton.querySelector('.btn-spinner');

        saveButton.addEventListener('click', function () {
            saveButton.setAttribute('disabled', '');
            saveButtonIcon.classList.remove('d-none');

            axios.post('{{ route('changelog.admin.categories.update-order') }}', {
                'categories': serialize(sortable.el)
            })
                .then(function (json) {
                    createAlert('success', json.data.message, true);
                })
                .catch(function (error) {
                    createAlert('danger', error, true)
                })
                .finally(function () {
                    saveButton.removeAttribute('disabled');
                    saveButtonIcon.classList.add('d-none');
                });
        });
    </script>
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">{{ trans('changelog::admin.categories.title') }}</h6>
        </div>
        <div class="card-body">
            <ol class="list-unstyled sortable" id="categories">
                @each('changelog::admin.categories._category', $categories, 'category')
            </ol>

            <a href="{{ route('changelog.admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
            </a>

            @if(! $categories->isEmpty())
                <button type="button" class="btn btn-success" id="save">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                    <span class="spinner-border spinner-border-sm btn-spinner d-none" role="status"></span>
                </button>
            @endif
        </div>
    </div>

    @if(! $categories->isEmpty())
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('changelog::admin.updates.title') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ trans('messages.fields.name') }}</th>
                            <th scope="col">{{ trans('messages.fields.category') }}</th>
                            <th scope="col">{{ trans('messages.fields.date') }}</th>
                            <th scope="col">{{ trans('messages.fields.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($updates as $update)
                            <tr>
                                <th scope="row">{{ $update->id }}</th>
                                <td>{{ $update->name }}</td>
                                <td>
                                    <a href="{{ route('changelog.categories.show', $update->category) }}" target="_blank">
                                        {{ $update->category->name }}
                                    </a>
                                </td>
                                <td>{{ format_date_compact($update->created_at) }}</td>
                                <td>
                                    <a href="{{ route('changelog.admin.updates.edit', $update) }}" class="mx-1"
                                       title="{{ trans('messages.actions.edit') }}" data-toggle="tooltip"><i
                                                class="bi bi-pencil-square"></i></a>
                                    <a href="{{ route('changelog.admin.updates.destroy', $update) }}" class="mx-1"
                                       title="{{ trans('messages.actions.delete') }}" data-toggle="tooltip"
                                       data-confirm="delete"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

                {{ $updates->links() }}

                <a class="btn btn-primary" href="{{ route('changelog.admin.updates.create') }}">
                    <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
                </a>
            </div>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ trans('admin.nav.settings.settings') }}
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('changelog.admin.settings.update') }}" method="POST">
                @csrf

                <div class="row gx-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="titleInput">{{ trans('messages.fields.title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="titleInput" name="title" value="{{ old('title', $title) }}" required>

                        @error('title')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="webhookInput">{{ trans('changelog::admin.settings.webhook') }}</label>
                        <input type="text" class="form-control @error('webhook') is-invalid @enderror" id="webhookInput" name="webhook" placeholder="https://discord.com/api/webhooks/.../..." value="{{ old('webhook', $webhook) }}">

                        @error('webhook')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="apiTokensInput">{{ trans('changelog::admin.settings.api_tokens') }}</label>
                    <input type="password" class="form-control @error('api_tokens') is-invalid @enderror" id="apiTokensInput" name="api_tokens" value="{{ old('api_tokens', $api_tokens) }}" aria-describedby="apiTokensHelp">
                    <small id="apiTokensHelp" class="form-text">
                        {{ trans('changelog::admin.settings.api_tokens_help', ['default' => 'Use long random tokens (>=32 chars). Separate multiple tokens with commas.']) }}
                    </small>

                    @error('api_tokens')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
