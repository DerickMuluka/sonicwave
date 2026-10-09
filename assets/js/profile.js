document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('profileTabs')?.addEventListener('click', e => {
    const t = e.target.closest('.tab'); if (!t) return;
    document.querySelectorAll('#profileTabs .tab').forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`.tab-panel[data-panel="${t.dataset.tab}"]`)?.classList.add('active');
    if (t.dataset.tab === 'history') loadHistory();
  });

  document.querySelectorAll('[data-pw-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      const t = document.getElementById(btn.dataset.pwToggle);
      if (!t) return;
      const show = t.type === 'password';
      t.type = show ? 'text' : 'password';
    });
  });

  document.getElementById('pwForm')?.addEventListener('submit', async e => {
    e.preventDefault();
    const err = document.getElementById('pwErr');
    const ok  = document.getElementById('pwOk');
    err.style.display = 'none'; ok.style.display = 'none';
    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Updating…';
    try {
      await API.post('/user/password.php', {
        current_password: document.getElementById('curPw').value,
        new_password:     document.getElementById('newPw').value,
        confirm_password: document.getElementById('conPw').value,
      });
      ok.textContent = 'Password updated successfully.';
      ok.style.display = 'block';
      e.target.reset();
    } catch (ex) {
      err.textContent = ex.message;
      err.style.display = 'block';
    } finally {
      btn.disabled = false; btn.textContent = 'Update password';
    }
  });

  let historyLoaded = false;
  async function loadHistory() {
    if (historyLoaded) return;
    historyLoaded = true;
    const box = document.getElementById('historyBox');
    if (!box) return;
    try {
      const res = await API.get('/user/history.php');
      const tracks = res.tracks || [];
      if (!tracks.length) {
        box.innerHTML = `<div class="empty"><div class="empty-icon">♪</div><h3>No listening history yet</h3><p>Play some tracks and they'll appear here.</p></div>`;
        return;
      }
      const json = JSON.stringify(tracks).replace(/"/g,'&quot;');
      box.innerHTML = `<div data-track-list data-track-list="${json}" style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:8px">
        ${tracks.map(t => `
          <div class="track-row" data-id="${t.id}">
            <div class="track-cover" data-play-track="${t.id}">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
            <div class="track-info" data-play-track="${t.id}">
              <div class="track-name">${escapeHtml(t.title)}</div>
              <div class="track-artist">${escapeHtml(t.artist)} ${platformBadge(t.source)}</div>
            </div>
            <div class="track-meta">${t.last_played ? new Date(t.last_played).toLocaleDateString() : ''}</div>
          </div>`).join('')}
      </div>`;
    } catch (e) { box.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`; }
  }
  loadHistory();
});