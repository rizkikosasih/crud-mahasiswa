'use strict';

document.addEventListener('DOMContentLoaded', function () {
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

  /* Theme */
  if (getPreferredTheme() !== html.attr('data-bs-theme')) {
    setStoredTheme(getPreferredTheme());
    location.reload();
  }

  /* Setup headers ajax */
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': _token }
  });

  /* Toast */
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: toast => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });

  /* Tooltip */
  const tooltipTriggerList = document.querySelectorAll('.tooltips');
  [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

  /* Data Table */
  const table = $('table#mahasiswa');
  const dataTable = new DataTable(table, {
    responsive: true,
    dom: `
      <'row justify-content-between align-items-center'
        <'col-auto'l>
        <'col-auto'f>
      >
      <'table-responsive my-3 w-100'tr>
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

  /* Dynamic Modal */
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
        const { code, classDialog, title, body, footer, option = {} } = result;
        if (code) {
          modalDialog.push(classDialog || 'modal-lg');

          dynamicModal.find('#modal-dialog').addClass(modalDialog.join(' '));
          dynamicModal.find('.modal-title').html(title);
          dynamicModal.find('.modal-body').html(body);
          if (footer) {
            dynamicModal.find('.modal-footer').html(footer).removeClass('d-none');
          }

          const newModal = new bootstrap.Modal(dynamicModalSelector, option);
          newModal.show();
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

  /* Swal Confirm */
  $(document).on('click', '.swal-confirm', e => {
    const _this = e.currentTarget;
    const { path, message } = _this.dataset;
    const url = siteUrl(path);
    Swal.fire({
      icon: 'warning',
      title: 'Pemberitahuan',
      html: `<p>${message}</p>`,
      showConfirmButton: true,
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Tidak',
      customClass: {
        confirmButton: 'btn btn-danger',
        cancelButton: 'btn btn-secondary',
        actions: 'gap-2'
      },
      buttonsStyling: false
    }).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          cache: false,
          method: 'get',
          url: url,
          success: res => {
            const resType = typeof res;
            if (resType !== 'object') {
              location.reload();
            } else {
              const { code, message, error } = res;
              const toastIcon = code === 200 ? 'success' : 'error';
              const toastMessage = code === 200 ? message : error;
              dynamicModal.hide();
              dataTable.ajax.reload();
              Toast.fire({
                icon: toastIcon,
                title: toastMessage
              });
            }
          }
        });
      }
    });
  });

  /* Element Input Mata Kuliah */
  $(document).on('keydown', 'input.matkul', e => {
    const keyCode = e.which || e.keyCode;
    if (keyCode === 13) {
      e.preventDefault();
      e.stopPropagation();
    }
  });

  /* Tambah Mata Kuliah */
  const addMatkul = e => {
    const _this = e.currentTarget;
    const body = $(_this).parents('.matkul-list');
    const itemList = $('.matkul-item');
    const itemLength = itemList.length;
    $(_this).parent().html(`
      <button type="button" class="btn btn-danger delete-matkul">
        Hapus
      </button>
    `);
    body.append(`
      <tr class="matkul-item">
        <td class="text-center" id="number">${itemLength + 1}</td>
        <td>
          <input
            type="text"
            name="matkul[]"
            id="matkul"
            class="form-control"
            value=""
            required
          />
        </td>
        <td class="text-center">
          <div class="btn-group btn-group-sm" id="action-matkul">
            <button type="button" class="btn btn-primary add-matkul">Tambah</button>
          </div>
        </td>
      </tr>
    `);
  };
  $(document).on('click', 'button.add-matkul', addMatkul);

  /* Hapus Mata Kuliah */
  const deleteMatkul = e => {
    const _this = e.currentTarget;
    $(_this).parents('.matkul-item').remove();
    const body = $(_this).parents('.matkul-list');
    const itemList = $('.matkul-item');
    const itemLength = itemList.length;
    itemList.map((k, v) => {
      const iteration = k + 1;
      const currentItem = $(v);
      currentItem.find('td#number').html(iteration);
      if (iteration === itemLength) {
        currentItem.find('.action-matkul').html(`
          <button type="button" class="btn btn-primary add-matkul">
            Tambah
          </button>
        `);
      }
    });
  };
  $(document).on('click', 'button.delete-matkul', deleteMatkul);

  /* Initialize change preview image */
  $(document).on('change', 'input[type=file].image', e => {
    e.preventDefault();
    const _this = $(e.currentTarget);
    const input = _this[0];
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        _this.parents('.form-image').find('img').attr('src', e.target.result).fadeIn('slow');
      };
      reader.readAsDataURL(input.files[0]);
    }
  });

  /* Submit Form */
  $(document).on('submit', '.form-validation', e => {
    e.preventDefault();
    e.stopPropagation();

    const _this = e.currentTarget;
    if (!_this.checkValidity()) {
      _this.classList.add('was-validated');
    } else {
      const {
        attributes: {
          action: { value: url },
          method: { value: method }
        }
      } = _this;

      $.ajax({
        method: method,
        url: url,
        data: new FormData(_this),
        contentType: false,
        processData: false,
        error: (xhr, opt, err) => {
          console.info(err);
          $(_this).find('button#submit').html('Submit');
        },
        beforeSend: () => {
          $(_this).find('button#submit').html('Proses ...');
        },
        success: result => {
          const resultType = typeof result;
          if (resultType !== 'object') {
            location.reload();
          } else {
            const { code, message, error } = result;
            const toastIcon = code === 200 ? 'success' : 'error';
            const toastMessage = code === 200 ? message : error;
            dynamicModal.hide();
            dataTable.ajax.reload();
            Toast.fire({
              icon: toastIcon,
              title: toastMessage
            });
          }
        },
        complete: () => {
          $(_this).find('button#submit').html('Submit');
        }
      });
    }
  });
});
