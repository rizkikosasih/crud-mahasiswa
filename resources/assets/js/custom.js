'use strict';

(function (window, $) {
  const tooltipTriggerList = document.querySelectorAll('.tooltips');
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  const table = $('table#mahasiswa');
  const dataTable = new DataTable(table, {
    responsive: true,
    dom: `
      <'row justify-content-between align-items-center'
        <'col-auto'l>
        <'col-auto'f>
      >
      <'table-responsive my-2 w-100'tr>
      <'row justify-content-end'
        <'col-auto'p>
      >
    `,
    language: {
      emptyTable: '<div class="text-center">No Data Available</div>',
      lengthMenu: 'Show _MENU_'
    }
  });
})(window, $);
