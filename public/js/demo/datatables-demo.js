// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    order: [[0, 'desc']] // Urutkan ID terbesar (data terbaru) di paling atas
  });
});
