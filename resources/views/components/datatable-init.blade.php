@props(['tableId', 'orderColumn' => 0, 'orderDir' => 'desc'])
@push('scripts')
<script>
$(function() {
    var $t = $('#{{ $tableId }}');
    if ($t.length && $t.find('tbody tr').length > 0 && !$t.find('tbody tr td[colspan]').length) {
        $t.DataTable({
            order: [[{{ $orderColumn }}, '{{ $orderDir }}']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_',
                infoEmpty: 'Showing 0 to 0 of 0',
                infoFiltered: '(filtered from _MAX_)',
                paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' },
                zeroRecords: 'No matching records.'
            },
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    }
});
</script>
@endpush
