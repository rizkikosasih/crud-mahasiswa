'use strict';

(function (window, document, $) {
  const html = $('html');
  const _token = $('meta[name=csrf-token]').attr('content');
  const siteUrl = path => {
    const url = html.data('path');
    const urlTrue = url.slice(-2, -1) === '/' ? url.slice(0, -1) : url;
    return `${urlTrue}/${path}`;
  };
  const setCookie = (cname, cvalue) => {
    const expires = new Date(new Date().getTime() + 1000 * 60 * 60 * 24 * 365).toGMTString();
    document.cookie = `${cname}=${cvalue};expires=${expires};path=/`;
  };
  const getCookie = cname => {
    let name = `${cname}=`;
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return '';
  };

  const getStoredTheme = () => getCookie('theme');
  const setStoredTheme = theme => setCookie('theme', theme);

  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme) {
      return storedTheme;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  };

  if (getPreferredTheme() !== html.attr('data-bs-theme')) {
    setStoredTheme(getPreferredTheme());
    location.reload();
  }

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

  const dynamicModalId = '#dynamic-modal';
  const dynamicModal = $(dynamicModalId);
  const dynamicModalSelector = document.querySelector(dynamicModalId);
  const modalDialog = ['modal-dialog', 'modal-dialog-scrollable'];
  $(document).on('click', '.show-modal', e => {
    const _this = e.currentTarget;
    const data = _this.dataset;
    const { type, path, id } = data;
    $.ajax({
      cache: false,
      method: 'post',
      url: siteUrl(path),
      data: { type: type, id: id },
      success: result => {
        const { code, classDialog, title, body, footer } = result;
        if (code) {
          modalDialog.push(classDialog);

          dynamicModal.find('#modal-dialog').addClass(modalDialog.join(' '));
          dynamicModal.find('.modal-title').html(title);
          dynamicModal.find('.modal-body').html(body);
          if (footer) {
            dynamicModal.find('.modal-footer').html(footer).removeClass('d-none');
          }

          const newModal = new bootstrap.Modal(dynamicModalSelector);
          newModal.toggle();
        } else {
          console.error(result);
        }
      },
      error: e => {
        console.error(e);
      },
      beforeSend: () => {},
      complete: () => {
        dynamicModalSelector.addEventListener('hidden.bs.modal', evt => {
          const _this = $(evt.currentTarget);
          _this.find('#modal-dialog').removeClass();
          _this.find('.modal-title').html('');
          _this.find('.modal-body').html('');
          _this.find('.modal-footer').html('').addClass('d-none');
        });
      }
    });
  });
})(window, document, jQuery);
