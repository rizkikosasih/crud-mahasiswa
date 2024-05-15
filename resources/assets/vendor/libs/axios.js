/**
 * Axios
 */
import axios from 'axios';
window.axios = axios;

const _token = document.querySelector('meta[name="csrf-token"]').content;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers['X-CSRF-TOKEN'] = _token;
