(function(){
  const main = document.querySelector('[data-admin]'); if (!main) return;
  const page = main.dataset.admin;

  async function loadStats() {
    try {
      const res = await API.get('/admin/stats.php');
      const grid = document.getElementById('statGrid'); if (!grid) return;
      grid.innerHTML = `
        <div class="stat-card primary"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="label">Total Users</div><div class="value">${res.users}</div><div class="sub">+${res.new_users_week} this week</div></div>
        <div class="stat-card cool"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div><div class="label">Total Tracks</div><div class="value">${res.tracks}</div><div class="sub">${res.pending} pending</div></div>
        <div class="stat-card warm"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3" fill="currentColor"/></svg></div><div class="label">Total Plays</div><div class="value">${res.plays.toLocaleString()}</div><div class="sub">Across all tracks</div></div>
        <div class="stat-card success"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div><div class="label">Active Today</div><div class="value">${res.active_today}</div><div class="sub">Unique listeners</div></div>`;
    } catch (e) { console.error(e); }
  }
  async function loadUsers() {
    try {
      const res = await API.get('/admin/users.php');
      const tbody = document.getElementById('usersBody'); if (!tbody) return;
      tbody.innerHTML = res.users.map(u => `
        <tr>
          <td>${u.id}</td>
          <td>${escapeHtml(u.username)}</td>
          <td>${escapeHtml(u.email)}</td>
          <td><span class="role-badge role-${u.role}">${u.role}</span></td>
          <td>${u.track_count}</td>
          <td>${new Date(u.created_at).toLocaleDateString()}</td>
          <td>
            ${u.role !== 'admin' ? `<button class="btn btn-ghost" data-promote="${u.id}" style="padding:6px 10px;font-size:12px">Make Mod</button>
            <button class="btn btn-danger" data-ban="${u.id}" style="padding:6px 10px;font-size:12px">Ban</button>` : ''}
          </td>
        </tr>`).join('');
    } catch (e) { console.error(e); }
  }
  async function loadTracks() {
    try {
      const res = await API.get('/admin/tracks.php');
      const tbody = document.getElementById('tracksBody'); if (!tbody) return;
      tbody.innerHTML = res.tracks.map(t => `
        <tr>
          <td>${t.id}</td>
          <td>${escapeHtml(t.title)}</td>
          <td>${escapeHtml(t.artist)}</td>
          <td>${platformBadge(t.source)}</td>
          <td>${t.plays}</td>
          <td>${t.is_approved ? '✓' : '⏳'}</td>
          <td><button class="btn btn-danger" data-del-track="${t.id}" style="padding:6px 10px;font-size:12px">Delete</button></td>
        </tr>`).join('');
    } catch (e) { console.error(e); }
  }
  document.addEventListener('click', async e => {
    const ban = e.target.closest('[data-ban]');
    if (ban) {
      if (!confirm('Ban this user?')) return;
      try { await API.post('/admin/users.php', { action: 'ban', user_id: parseInt(ban.dataset.ban, 10) }); toast('User banned', 'success'); loadUsers(); } catch (ex) { toast(ex.message, 'error'); }
    }
    const promote = e.target.closest('[data-promote]');
    if (promote) {
      try { await API.post('/admin/users.php', { action: 'promote', user_id: parseInt(promote.dataset.promote, 10), role: 'moderator' }); toast('Role updated', 'success'); loadUsers(); } catch (ex) { toast(ex.message, 'error'); }
    }
    const del = e.target.closest('[data-del-track]');
    if (del) {
      if (!confirm('Delete this track?')) return;
      try { await API.post('/admin/tracks.php', { action: 'delete', track_id: parseInt(del.dataset.delTrack, 10) }); toast('Track deleted', 'success'); loadTracks(); } catch (ex) { toast(ex.message, 'error'); }
    }
  });
  if (page === 'users') loadUsers();
  else if (page === 'tracks') loadTracks();
  else loadStats();
})();