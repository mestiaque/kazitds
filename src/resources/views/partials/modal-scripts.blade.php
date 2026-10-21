{{--
    Shared behaviour for the create/edit modals on master-data pages:
    - reopens the modal whose form failed validation (hidden "_modal" field)
    - opens a modal from a link: ?create=1, ?edit={id} or ?show={id}
    - select2 inside a modal needs dropdownParent, otherwise its search box can't take focus
--}}
@push('js')
<script>
$(function () {
    $('.modal [data-control="select2-modal"]').each(function () {
        $(this).select2({ dropdownParent: $(this).closest('.modal'), width: '100%' });
    });

    const params = new URLSearchParams(window.location.search);
    const target = @json(old('_modal'))
        || (params.has('create') ? 'createModal' : null)
        || (params.has('edit') ? 'editModal' + params.get('edit') : null)
        || (params.has('show') ? 'showModal' + params.get('show') : null);

    const modal = target && document.getElementById(target);
    if (modal) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    // Drop ?create / ?edit / ?show so a refresh doesn't open the modal again
    if (params.has('create') || params.has('edit') || params.has('show')) {
        ['create', 'edit', 'show', 'product_variant_id'].forEach(key => params.delete(key));
        const query = params.toString();
        history.replaceState(null, '', window.location.pathname + (query ? '?' + query : ''));
    }
});
</script>
@endpush
