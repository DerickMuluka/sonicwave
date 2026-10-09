const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

function toast(msg, type = 'info', duration = 3200) {
  const wrap = $('#toastWrap'); if (!wrap) return;
  const el = document.createElement('div');
  el.className = 'toast ' + type;
  el.textContent = msg;
  wrap.appendChild(el);
  setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; }, duration - 300);
  setTimeout(() => el.remove(), duration);
}
function fmtTime(s) {
  if (!isFinite(s) || s < 0) s = 0;
  return Math.floor(s/60) + ':' + String(Math.floor(s%60)).padStart(2,'0');
}
function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function platformBadge(source) {
  const labels = { upload:'Upload', youtube:'YouTube', spotify:'Spotify', soundcloud:'SoundCloud', vimeo:'Vimeo', apple:'Apple Music', deezer:'Deezer', bandcamp:'Bandcamp', direct:'Direct' };
  return `<span class="source-badge source-${escapeHtml(source)}">${labels[source] || source}</span>`;
}
async function logoutUser() {
  try { await API.post('/auth/logout.php', {}); } catch {}
  location.href = window.BASE_URL + '/login.php';
}

document.addEventListener('DOMContentLoaded', () => {
  // Sidebar toggle
  const toggle = $('#menuToggle');
  toggle?.addEventListener('click', () => $('#sidebar')?.classList.toggle('open'));
  window.addEventListener('click', e => {
    const sb = $('#sidebar');
    if (sb?.classList.contains('open') && !sb.contains(e.target) && !e.target.closest('#menuToggle')) sb.classList.remove('open');
  });

  // User dropdown
  const chip = $('#userChip'); const drop = $('#userDropdown');
  chip?.addEventListener('click', e => { e.stopPropagation(); drop.classList.toggle('open'); });
  window.addEventListener('click', e => {
    if (drop && !drop.contains(e.target) && !chip.contains(e.target)) drop.classList.remove('open');
  });

  // Global search
  const search = $('#globalSearch');
  search?.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      const q = search.value.trim();
      if (q) location.href = window.BASE_URL + '/search.php?q=' + encodeURIComponent(q);
    }
  });
});

async function loadSidebarPlaylists() {
  const box = $('#sidebar-playlists'); if (!box) return;
  try {
    const res = await API.get('/playlists/list.php');
    const items = res.playlists || [];
    if (!items.length) { box.innerHTML = '<div style="padding:8px 12px;font-size:12px;color:var(--muted)">No playlists yet</div>'; return; }
    box.innerHTML = items.slice(0, 6).map(p => `<a class="nav-link" href="${window.BASE_URL}/playlist.php?id=${p.id}"><span class="nav-icon">♪</span><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escapeHtml(p.name)}</span></a>`).join('');
  } catch (e) {}
}
document.addEventListener('DOMContentLoaded', loadSidebarPlaylists);