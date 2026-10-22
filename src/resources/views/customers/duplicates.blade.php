@extends('me::master')

@section('title', trans('kazitds::kazitds.Duplicate Customers'))

@push('buttons')
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-encodex-list">
        <i class="fas fa-arrow-left"></i> @lang('kazitds::kazitds.Customers')
    </a>
@endpush

@section('content')
    <div class="card border-left-primary shadow mb-4">
        <div class="card-body">
            <div class="alert alert-info py-2 small">
                <i class="fas fa-info-circle me-1"></i>
                @lang('kazitds::kazitds.duplicate_customers_help')
            </div>

            @forelse ($duplicateGroups as $group)
                <form action="{{ route('customers.merge') }}" method="POST" class="border rounded p-3 mb-4 duplicate-group"
                    onsubmit="return confirm('{{ __('kazitds::kazitds.merge_confirm') }}')">
                    @csrf

                    <div class="mb-2">
                        <strong>{{ toBanglaNumber($loop->iteration) }}.</strong>
                        @foreach ($group['reasons'] as $reason)
                            <span class="badge bg-warning text-dark">@lang('kazitds::kazitds.' . ($reason === 'phone' ? 'Same phone' : 'Same name'))</span>
                        @endforeach
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover table-encodex mb-2">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center">@lang('kazitds::kazitds.Keep')</th>
                                    <th class="text-center">@lang('kazitds::kazitds.Merge')</th>
                                    <th>@lang('kazitds::kazitds.ID')</th>
                                    <th>@lang('kazitds::kazitds.Name')</th>
                                    <th>@lang('kazitds::kazitds.Phone')</th>
                                    <th class="text-end">@lang('kazitds::kazitds.Sales')</th>
                                    <th class="text-end">@lang('kazitds::kazitds.Due Amount')</th>
                                    <th>@lang('kazitds::kazitds.Created At')</th>
                                    <th class="text-center">@lang('kazitds::kazitds.View')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['customers'] as $customer)
                                    <tr>
                                        <td class="text-center">
                                            <input type="radio" name="keep_id" value="{{ $customer->id }}" class="form-check-input keep-radio" @checked($loop->first)>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="merge_ids[]" value="{{ $customer->id }}" class="form-check-input merge-checkbox"
                                                data-due="{{ (float) $customer->due_amount }}" @checked(! $loop->first) @disabled($loop->first)>
                                        </td>
                                        <td>#{{ toBanglaNumber($customer->id) }}</td>
                                        {{-- pre-wrap keeps extra spaces visible, which is often the only difference --}}
                                        <td style="white-space: pre-wrap">{{ $customer->name }}</td>
                                        <td>{{ $customer->phone ?: __('kazitds::kazitds.N/A') }}</td>
                                        <td class="text-end">{{ toBanglaNumber($customer->sales_count) }}</td>
                                        <td class="text-end">@lang('kazitds::kazitds.TK.') {{ toBanglaNumber($customer->due_amount, 2) }}</td>
                                        <td class="text-nowrap">{{ formatDateTime($customer->created_at) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-encodex-show" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @lang('kazitds::kazitds.Due after merge'):
                            <strong>@lang('kazitds::kazitds.TK.') <span class="merged-due">{{ toBanglaNumber($group['customers']->sum('due_amount'), 2) }}</span></strong>
                        </div>
                        <button type="submit" class="btn btn-sm btn-savex">
                            <i class="fas fa-object-group"></i> @lang('kazitds::kazitds.Merge Customers')
                        </button>
                    </div>
                </form>
            @empty
                <div class="text-center text-muted py-4">
                    @lang('kazitds::kazitds.No duplicate customers found.')
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.querySelectorAll('.duplicate-group').forEach(function (form) {
            const radios = form.querySelectorAll('.keep-radio');
            const checkboxes = form.querySelectorAll('.merge-checkbox');
            const mergedDue = form.querySelector('.merged-due');
            const submitButton = form.querySelector('button[type="submit"]');
            let previousKeptId = null;

            function refresh() {
                const keptId = form.querySelector('.keep-radio:checked').value;
                let total = 0;

                checkboxes.forEach(function (checkbox) {
                    const isKept = checkbox.value === keptId;
                    if (checkbox.value === previousKeptId && ! isKept) {
                        checkbox.checked = true;
                    }
                    checkbox.disabled = isKept;
                    if (isKept) {
                        checkbox.checked = false;
                    }
                    if (isKept || checkbox.checked) {
                        total += parseFloat(checkbox.dataset.due);
                    }
                });

                mergedDue.textContent = total.toLocaleString('{{ app()->getLocale() === 'bn' ? 'bn-BD' : 'en-US' }}', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                submitButton.disabled = ! form.querySelector('.merge-checkbox:checked');
                previousKeptId = keptId;
            }

            radios.forEach(radio => radio.addEventListener('change', refresh));
            checkboxes.forEach(checkbox => checkbox.addEventListener('change', refresh));
            refresh();
        });
    </script>
@endsection
