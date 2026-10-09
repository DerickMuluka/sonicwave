const API = {
  base: window.BASE_URL + '/api',
  csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
  async req(path, { method = 'GET', body = null, formData = null } = {}) {
    const opts = { method, credentials: 'same-origin', headers: {} };
    if (!['GET','HEAD'].includes(method)) opts.headers['X-CSRF-Token'] = this.csrf;
    if (formData) opts.body = formData;
    else if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
    const res = await fetch(this.base + path, opts);
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch { data = { error: text || 'Invalid response' }; }
    if (!res.ok) throw new Error(data.error || `Request failed (${res.status})`);
    return data;
  },
  get(p) { return this.req(p); },
  post(p, body) { return this.req(p, { method: 'POST', body }); },
  upload(p, fd) { return this.req(p, { method: 'POST', formData: fd }); }
};