(function() {
  const tab = new URLSearchParams(location.search).get('tab') || 'all';
  const box = document.getElementById('libContent');
  if (!box) return;

  const currentUserId = window.CURRENT_USER?.id || 0;

  const renderTracks = (tracks, emptyMsg) => {
    if (!tracks.length) {
      box.innerHTML = `<div class="empty"><div class="empty-icon">♪</div><h3>${emptyMsg}</h3><p>Start building your library by uploading or importing music.</p><a href="${window.BASE_URL}/upload.php" class="btn btn-primary">Upload or import</a></div>`;
      return;
    }
    const json = JSON.stringify(tracks).replace(/"/g, '&quot;');
    box.innerHTML = `<div data-track-list data-track-list="${json}" style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:8px">
      ${tracks.map(t => {
        const isOwner = Number(t.user_id) === Number(currentUserId);
        return `
        <div class="track-row" data-id="${t.id}">
          <div class="track-cover" data-play-track="${t.id}">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
          <div class="track-info" data-play-track="${t.id}">
            <div class="track-name">${escapeHtml(t.title)}</div>
            <div class="track-artist">${escapeHtml(t.artist)} ${platformBadge(t.source)}</div>
          </div>
          <div class="track-meta duration">${fmtTime(t.duration)}</div>
          <div class="track-actions">
            <a class="icon-btn" href="${t.download_url}" download title="Download"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
            <button class="icon-btn ${t.liked?'liked':''}" data-like="${t.id}" title="Like">${t.liked?'♥':'♡'}</button>
            <button class="icon-btn" data-add-pl="${t.id}" title="Add to playlist">+</button>
            ${isOwner ? `<button class="icon-btn" data-delete-track="${t.id}" title="Delete" style="color:#fca5a5"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg></button>` : ''}
          </div>
        </div>`;
      }).join('')}
    </div>`;
  };

  async function load() {
    box.innerHTML = `<div class="empty"><p>Loading…</p></div>`;
    try {
      if (tab === 'playlists') return loadPlaylists();
      let path = '/tracks/list.php?limit=200';
      if (tab === 'liked') path += '&liked=1';
      if (tab === 'mine') path += '&mine=1';
      const res = await API.get(path);
      renderTracks(res.tracks || [], tab === 'liked' ? 'No liked tracks yet' : tab === 'mine' ? 'You haven\'t uploaded anything yet' : 'Library is empty');
    } catch (e) {
      box.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`;
    }
  }

  async function loadPlaylists() {
    try {
      const res = await API.get('/playlists/list.php');
      const items = res.playlists || [];
      box.innerHTML = `<div style="margin-bottom:16px"><button class="btn btn-primary" id="newPlBtn">+ New Playlist</button></div>
        ${items.length ? `<div class="grid">${items.map(p => `<a class="card" href="${window.BASE_URL}/playlist.php?id=${p.id}"><div class="card-cover">♪</div><div class="card-title">${escapeHtml(p.name)}</div><div class="card-sub">${p.track_count} track${p.track_count!==1?'s':''}</div></a>`).join('')}</div>`
        : `<div class="empty"><div class="empty-icon">♪</div><h3>No playlists yet</h3><p>Create your first playlist to organise tracks.</p></div>`}`;
      document.getElementById('newPlBtn')?.addEventListener('click', async () => {
        const name = prompt('Playlist name:'); if (!name) return;
        try { await API.post('/playlists/create.php', { name }); toast('Playlist created', 'success'); loadPlaylists(); }
        catch (e) { toast(e.message, 'error'); }
      });
    } catch (e) { box.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`; }
  }

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
    const addBtn = e.target.closest('[data-add-pl]');
    if (addBtn) {
      e.stopPropagation();
      const tid = parseInt(addBtn.dataset.addPl, 10);
      try {
        const pls = await API.get('/playlists/list.php');
        if (!pls.playlists?.length) { toast('Create a playlist first', 'info'); return; }
        const name = prompt('Add to playlist:\n' + pls.playlists.map(p => '• ' + p.name).join('\n'));
        const target = pls.playlists.find(p => p.name.toLowerCase() === (name||'').toLowerCase());
        if (!target) return;
        await API.post('/playlists/add.php', { playlist_id: target.id, track_id: tid });
        toast('Added to playlist', 'success');
      } catch (ex) { toast(ex.message, 'error'); }
    }
    const delBtn = e.target.closest('[data-delete-track]');
    if (delBtn) {
      e.stopPropagation();
      const tid = parseInt(delBtn.dataset.deleteTrack, 10);
      if (!confirm('Delete this track permanently? This cannot be undone.')) return;
      try {
        await API.post('/tracks/delete.php', { track_id: tid });
        toast('Track deleted', 'success');
        delBtn.closest('.track-row')?.remove();
      } catch (ex) { toast(ex.message, 'error'); }
    }
  });

  load();
})();