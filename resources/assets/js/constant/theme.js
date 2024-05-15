import { loadUrl } from './url';
import { getCookie, setCookie } from './cookie';

export const getStoredTheme = () => getCookie('theme');

export const setStoredTheme = theme => loadUrl(setCookie('theme', theme));

export const getPreferredTheme = () => {
  const storedTheme = getStoredTheme();
  if (storedTheme) {
    return storedTheme;
  }

  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};
