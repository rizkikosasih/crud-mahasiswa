import { html, baseUrl } from './index.js';

export const siteUrl = path => {
  const {
    dataset: { path: url }
  } = html;
  const uri = url.slice(-2, -1) === '/' ? url.slice(0, -1) : url;
  if (!path) {
    return `${uri}/`;
  }
  return `${uri}/${path}`;
};

export const loadUrl = url => {
  if (url) {
    return (location.href = url);
  }
  return (location.href = siteUrl());
};
