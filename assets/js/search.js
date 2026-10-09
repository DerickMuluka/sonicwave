(function(){
  const results = document.getElementById('results');
  if (!results) return;

  let currentGenre = '';
  let currentQuery = new URLSearchParams(location.search).get('q') || '';

  async function load() {
    results.innerHTML = `<div class="empty"><div class="empty-icon">⌕</div><p>Searching…</p></div>`;

    try {
      const params = new URLSearchParams({ limit: '80' });
      if (currentGenre) params.set('genre', currentGenre);

      if (currentQuery) {
        const res = await API.get('/search/universal.php?q=' + encodeURIComponent(currentQuery));
        renderUniversal(res);
        return;
      }

      const res = await API.get('/tracks/list.php?' + params.toString());
      renderLocal(res.tracks || []);
    } catch (e) {
      results.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`;
    }
  }

  function renderUniversal(res) {
    const local = res.local || [];
    const platform = res.platform;

    let html = '';

    if (platform) {
      html += `
        <div class="platform-suggest" style="background:linear-gradient(135deg,rgba(124,92,255,0.15),rgba(34,211,238,0.1));border:1px solid rgba(124,92,255,0.3);border-radius:14px;padding:18px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap">
          <div style="width:44px;height:44px;border-radius:12px;background:var(--grad-main);display:grid;place-items:center;flex-shrink:0">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:22px;height:22px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <div style="flex:1;min-width:200px">
            <div style="font-weight:700;font-size:14px;margin-bottom:2px">Import from URL</div>
            <div style="font-size:12px;color:var(--muted);word-break:break-all">${escapeHtml(platform.url)}</div>
          </div>
          <button class="btn btn-primary" id="importFromSearch">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Import
          </button>
        </div>`;
    }

    if (!local.length && !platform) {
      html += `<div class="empty"><div class="empty-icon">⌕</div><h3>No results for "${escapeHtml(res.query || '')}"</h3><p>Try a different search term or paste a media URL to import.</p></div>`;
    } else if (local.length) {
      const json = JSON.stringify(local).replace(/"/g,'&quot;');
      html += `<div data-track-list data-track-list="${json}" style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:8px">
        ${local.map(t => `
          <div class="track-row" data-id="${t.id}">
            <div class="track-cover" data-play-track="${t.id}">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
            <div class="track-info" data-play-track="${t.id}">
              <div class="track-name">${escapeHtml(t.title)}</div>
              <div class="track-artist">${escapeHtml(t.artist)} ${platformBadge(t.source)}</div>
            </div>
            <div class="track-meta duration">${fmtTime(t.duration)}</div>
            <div class="track-actions">
              <a class="icon-btn" href="${t.download_url}" download title="Download"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
              <button class="icon-btn ${t.liked?'liked':''}" data-like="${t.id}">${t.liked?'♥':'♡'}</button>
            </div>
          </div>`).join('')}
      </div>`;
    }
    results.innerHTML = html;

    document.getElementById('importFromSearch')?.addEventListener('click', async e => {
      const btn = e.currentTarget;
      btn.disabled = true; btn.textContent = 'Importing…';
      try {
        const r = await API.post('/media/import.php', { url: platform.url });
        toast('Imported: ' + r.title, 'success');
        if (r.downloaded) toast('Downloaded to your library', 'success', 2500);
        currentQuery = r.title;
        load();
      } catch (ex) {
        toast(ex.message, 'error');
        btn.disabled = false; btn.innerHTML = 'Import';
      }
    });
  }

  function renderLocal(tracks) {
    if (!tracks.length) {
      results.innerHTML = `<div class="empty"><div class="empty-icon">⌕</div><h3>No results</h3><p>Try another genre or search term.</p></div>`;
      return;
    }
    const json = JSON.stringify(tracks).replace(/"/g,'&quot;');
    results.innerHTML = `<div data-track-list data-track-list="${json}" style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:8px">
      ${tracks.map(t => `
        <div class="track-row" data-id="${t.id}">
          <div class="track-cover" data-play-track="${t.id}">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
          <div class="track-info" data-play-track="${t.id}">
            <div class="track-name">${escapeHtml(t.title)}</div>
            <div class="track-artist">${escapeHtml(t.artist)} ${platformBadge(t.source)}</div>
          </div>
          <div class="track-meta duration">${fmtTime(t.duration)}</div>
          <div class="track-actions">
            <a class="icon-btn" href="${t.download_url}" download title="Download"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
            <button class="icon-btn ${t.liked?'liked':''}" data-like="${t.id}">${t.liked?'♥':'♡'}</button>
          </div>
        </div>`).join('')}
    </div>`;
  }

  document.getElementById('genreChips')?.addEventListener('click', e => {
    const chip = e.target.closest('[data-genre]'); if (!chip) return;
    document.querySelectorAll('#genreChips .chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    currentGenre = chip.dataset.genre;
    currentQuery = '';
    load();
  });

  document.getElementById('globalSearch')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { currentQuery = e.target.value.trim(); load(); }
  });

  document.addEventListener('click', async e => {
    const likeBtn = e.target.closest('[data-like]');
    if (likeBtn) {
      e.stopPropagation();
      const tid = parseInt(likeBtn.dataset.like, 10);
      try {
        const res = await API.post('/tracks/like.php', { track_id: tid });
        likeBtn.textContent = res.liked ? '♥' : '♡';
        likeBtn.classList.toggle('liked', res.liked);
      } catch (ex) { toast(ex.message, 'error'); }
    }
  });

  load();
})();