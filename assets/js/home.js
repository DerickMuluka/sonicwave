document.addEventListener('DOMContentLoaded', () => {
  // ── AI Mood DJ ─────────────────────────────────────
  document.querySelectorAll('[data-ai-mood]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const mood = btn.dataset.aiMood;

      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<span class="emoji">⏳</span>Loading…';
      btn.disabled = true;

      try {
        const res = await API.post('/ai/dj.php', { mood });
        if (res.tracks?.length) {
          Player.play(res.tracks, 0);
          const msg = res.fallback
            ? `AI DJ: no perfect "${mood}" matches yet — playing a mix`
            : `AI DJ: ${res.count} ${mood} tracks queued`;
          toast(msg, 'success');
        } else {
          toast('No tracks available — upload some music first', 'info');
        }
      } catch (e) {
        toast(e.message, 'error');
      } finally {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
      }
    });
  });

  // ── AI Recommendations ────────────────────────────
  async function loadRecs() {
    const recBtn = document.getElementById('aiRefresh');
    if (recBtn) { recBtn.disabled = true; recBtn.textContent = 'Loading…'; }
    try {
      const res = await API.get('/ai/recommend.php?limit=8');
      const box = document.getElementById('aiRecs');
      if (!box) return;
      if (!res.tracks?.length) {
        box.innerHTML = '<p style="color:var(--muted);font-size:13px">Play a few tracks so we can learn your taste.</p>';
        return;
      }
      const json = JSON.stringify(res.tracks).replace(/"/g, '&quot;');
      box.innerHTML = `<div data-track-list data-track-list="${json}" style="display:grid;gap:2px">
        ${res.tracks.map(t => `
          <div class="mini-track" data-play-track="${t.id}">
            <div class="cover">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
            <div class="info">
              <div class="name">${escapeHtml(t.title)}</div>
              <div class="artist">${escapeHtml(t.artist)}</div>
            </div>
            <span class="play-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
          </div>
        `).join('')}
      </div>`;
    } catch (e) {
      // silent
    } finally {
      if (recBtn) { recBtn.disabled = false; recBtn.textContent = 'Refresh'; }
    }
  }
  document.getElementById('aiRefresh')?.addEventListener('click', loadRecs);
  loadRecs();
});