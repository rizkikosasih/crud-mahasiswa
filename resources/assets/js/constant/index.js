export const html = document.querySelector('html');
export const baseUrl = html.dataset.url + '/';
export const _token = $('meta[name=csrf-token]').attr('content');
