'use strict';

(function (window, $) {
  const html = $('html');
  const _token = $('meta[name=csrf-token]').attr('content');
  const siteUrl = path => {
    const url = html.data('path');
    const urlTrue = url.slice(-2, -1) === '/' ? url.slice(0, -1) : url;
    return `${urlTrue}/${path}`;
  };

  let modalDialog = ['modal-dialog', 'modal-dialog-centered', 'modal-dialog-scrollable'];

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': _token }
  });

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
    },
    ajax: {
      cache: false,
      method: 'post',
      url: siteUrl('list')
    },
    serverSide: true,
    processing: true,
    columns: [
      { name: 'mahasiswa.id', orderable: true },
      { name: 'mahasiswa.nama', orderable: true },
      { name: 'mahasiswa.jenis_kelamin', orderable: true },
      { name: 'mahasiswa.alamat', orderable: true },
      { name: 'count_matkul', orderable: true },
      { name: 'aksi', orderable: false }
    ],
    order: {
      name: 'mahasiswa.id',
      dir: 'desc'
    }
  });

  const dynamicModal = $('#dynamic-modal');
  $(document).on('click', '.show-modal', e => {
    const _this = e.currentTarget;
    const data = _this.dataset;
    $.ajax({
      cache: false,
      method: 'post',
      url: siteUrl(data.path)
    });
  });
})(window, $);
