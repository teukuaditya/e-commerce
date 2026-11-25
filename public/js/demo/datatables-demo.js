// Shared DataTables defaults for admin tables
$(document).ready(function () {
  var $tables = $('#dataTable');
  if (!$tables.length || !$.fn.DataTable) return;

  $tables.each(function () {
    var $t = $(this);
    if ($.fn.DataTable.isDataTable($t)) return;

    $t.DataTable({
      order: [[0, 'desc']],
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      autoWidth: false,
      language: {
        search: 'Search',
        searchPlaceholder: 'Search records...',
        lengthMenu: 'Show _MENU_ entries',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
        infoEmpty: 'Showing 0 to 0 of 0 entries',
        infoFiltered: '(filtered from _MAX_ total)',
        paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Prev' },
        zeroRecords: 'No matching records found'
      }
    });
  });
});
