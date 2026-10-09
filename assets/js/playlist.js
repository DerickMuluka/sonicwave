(function(){
  const main = document.querySelector('[data-playlist-id]');
  if (!main) return;
  const pid = parseInt(main.dataset.playlistId, 10);
  const box = document.getElementById('plTracks');
  const isOwner = main.dataset.isOwner === '1';

  let currentTracks = [];

  async function load() {
    box.innerHTML = `<div class="empty"><div class="empty-icon">♪</div><p>Loading tracks…</p></div>`;
    try {
      const res = await API.get('/playlists/tracks.php?playlist_id=' + pid);
      currentTracks = res.tracks || [];
      renderTracks(currentTracks, res.playlist);
    } catch (e) {
      box.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`;
    }
  }

  function renderTracks(tracks, playlist) {
    if (!tracks.length) {
      box.innerHTML = `
        <div class="empty">
          <div class="empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
          </div>
          <h3>This playlist is empty</h3>
          <p>Add tracks from your library — click "Add tracks" above.</p>
          ${isOwner ? '<button class="btn btn-primary" id="addEmptyBtn">Add tracks</button>' : ''}
        </div>`;
      document.getElementById('addEmptyBtn')?.addEventListener('click', openAddModal);
      return;
    }

    const json = JSON.stringify(tracks).replace(/"/g,'&quot;');
    box.innerHTML = `
      <div data-track-list data-track-list="${json}" style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:8px">
        ${tracks.map((t, i) => `
          <div class="track-row" data-id="${t.id}">
            <div class="track-cover" data-play-track="${t.id}">
              ${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}
            </div>
            <div class="track-info" data-play-track="${t.id}">
              <div class="track-name">${i + 1}. ${escapeHtml(t.title)}</div>
              <div class="track-artist">
                ${escapeHtml(t.artist)}
                ${platformBadge(t.source)}
              </div>
            </div>
            <div class="track-meta duration">${fmtTime(t.duration)}</div>
            <div class="track-actions">
              ${isOwner ? `<button class="icon-btn" data-remove-pl="${t.id}" title="Remove from playlist">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>` : ''}
            </div>
          </div>
        `).join('')}
      </div>`;
  }

  async function openAddModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-backdrop open';
    modal.id = 'addTrackModal';
    modal.innerHTML = `
      <div class="modal" style="max-width:640px;max-height:80vh;display:flex;flex-direction:column">
        <h3>Add tracks to playlist</h3>
        <input type="text" id="addSearch" placeholder="Search your library…" style="margin-bottom:14px">
        <div id="addList" style="flex:1;overflow-y:auto;min-height:200px;background:var(--bg-0);border:1px solid var(--border);border-radius:10px;padding:8px">
          <div class="empty" style="padding:30px"><p>Loading…</p></div>
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px">
          <button class="btn btn-ghost" id="addClose">Close</button>
        </div>
      </div>`;
    document.body.appendChild(modal);

    document.getElementById('addClose').addEventListener('click', () => modal.remove());
    modal.addEventListener('click', e => { if (e.target === modal) modal.remove(); });

    let allTracks = [];
    async function loadLibrary() {
      try {
        const res = await API.get('/tracks/list.php?limit=200');
        allTracks = res.tracks || [];
        renderAddList(allTracks);
      } catch (e) {
        document.getElementById('addList').innerHTML = `<div class="empty" style="padding:30px"><p>${escapeHtml(e.message)}</p></div>`;
      }
    }

    function renderAddList(tracks) {
      const list = document.getElementById('addList');
      if (!tracks.length) {
        list.innerHTML = `<div class="empty" style="padding:30px"><p>No tracks available</p></div>`;
        return;
      }
      const existingIds = new Set(currentTracks.map(t => t.id));
      list.innerHTML = tracks.map(t => {
        const already = existingIds.has(t.id);
        return `
          <div class="track-row" style="cursor:default">
            <div class="track-cover">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
            <div class="track-info">
              <div class="track-name">${escapeHtml(t.title)}</div>
              <div class="track-artist">${escapeHtml(t.artist)} ${platformBadge(t.source)}</div>
            </div>
            <button class="btn ${already ? 'btn-ghost' : 'btn-primary'}" data-add-now="${t.id}" ${already ? 'disabled' : ''} style="padding:6px 12px;font-size:12px">
              ${already ? '✓ Added' : '+ Add'}
            </button>
          </div>`;
      }).join('');
    }

    document.getElementById('addSearch').addEventListener('input', e => {
      const q = e.target.value.toLowerCase();
      const filtered = allTracks.filter(t =>
        t.title.toLowerCase().includes(q) || t.artist.toLowerCase().includes(q)
      );
      renderAddList(filtered);
    });

    document.getElementById('addList').addEventListener('click', async e => {
      const btn = e.target.closest('[data-add-now]');
      if (!btn || btn.disabled) return;
      const tid = parseInt(btn.dataset.addNow, 10);
      btn.disabled = true; btn.textContent = 'Adding…';
      try {
        await API.post('/playlists/add.php', { playlist_id: pid, track_id: tid });
        btn.textContent = '✓ Added';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-ghost');
        toast('Track added', 'success', 1500);
        const res = await API.get('/playlists/tracks.php?playlist_id=' + pid);
        currentTracks = res.tracks || [];
      } catch (ex) {
        toast(ex.message, 'error');
        btn.disabled = false; btn.textContent = '+ Add';
      }
    });

    loadLibrary();
  }

  document.getElementById('playAllBtn')?.addEventListener('click', () => {
    if (!currentTracks.length) return toast('Playlist is empty', 'info');
    Player.play(currentTracks, 0);
  });

  document.getElementById('addTracksBtn')?.addEventListener('click', openAddModal);

  document.getElementById('editPlBtn')?.addEventListener('click', () => {
    const meta = main.dataset;
    const modal = document.createElement('div');
    modal.className = 'modal-backdrop open';
    modal.innerHTML = `
      <div class="modal">
        <h3>Edit playlist</h3>
        <label style="font-size:12px;color:var(--muted);font-weight:600;display:block;margin-bottom:6px">Name</label>
        <input type="text" id="editName" value="${escapeHtml(meta.plName || '')}" maxlength="150">
        <label style="font-size:12px;color:var(--muted);font-weight:600;display:block;margin-bottom:6px">Description</label>
        <textarea id="editDesc" rows="3" maxlength="500">${escapeHtml(meta.plDesc || '')}</textarea>
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);margin-bottom:14px">
          <input type="checkbox" id="editPublic" ${meta.plPublic === '1' ? 'checked' : ''} style="width:auto">
          Make public
        </label>
        <div style="display:flex;gap:8px;justify-content:flex-end">
          <button class="btn btn-ghost" data-close>Cancel</button>
          <button class="btn btn-primary" id="saveEdit">Save</button>
        </div>
      </div>`;
    document.body.appendChild(modal);
    modal.querySelector('[data-close]').addEventListener('click', () => modal.remove());
    modal.addEventListener('click', e => { if (e.target === modal) modal.remove(); });

    document.getElementById('saveEdit').addEventListener('click', async () => {
      try {
        await API.post('/playlists/update.php', {
          playlist_id: pid,
          name: document.getElementById('editName').value.trim(),
          description: document.getElementById('editDesc').value.trim(),
          is_public: document.getElementById('editPublic').checked,
        });
        toast('Playlist updated', 'success');
        setTimeout(() => location.reload(), 500);
      } catch (e) { toast(e.message, 'error'); }
    });
  });

  document.getElementById('deletePlBtn')?.addEventListener('click', async () => {
    if (!confirm('Delete this playlist permanently?')) return;
    try {
      await API.post('/playlists/delete.php', { playlist_id: pid });
      toast('Playlist deleted', 'success');
      setTimeout(() => location.href = window.BASE_URL + '/library.php?tab=playlists', 600);
    } catch (e) { toast(e.message, 'error'); }
  });

  document.addEventListener('click', async e => {
    const rm = e.target.closest('[data-remove-pl]');
    if (rm) {
      e.stopPropagation();
      const tid = parseInt(rm.dataset.removePl, 10);
      if (!confirm('Remove this track from the playlist?')) return;
      try {
        await API.post('/playlists/remove.php', { playlist_id: pid, track_id: tid });
        toast('Track removed', 'success', 1500);
        load();
      } catch (ex) { toast(ex.message, 'error'); }
    }
  });

  load();
})();