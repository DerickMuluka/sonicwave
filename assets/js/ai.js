document.addEventListener('DOMContentLoaded', () => {
  // AI Mood DJ
  document.querySelectorAll('[data-ai-mood]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const mood = btn.dataset.aiMood;
      try {
        const res = await API.post('/ai/dj.php', { mood });
        if (res.tracks?.length) {
          Player.play(res.tracks, 0);
          toast(`AI DJ: ${mood} mode activated`, 'success');
        } else toast('No tracks match this mood yet', 'info');
      } catch (e) { toast(e.message, 'error'); }
    });
  });

  // AI recommend refresh
  const recBtn = document.getElementById('aiRefresh');
  recBtn?.addEventListener('click', async () => {
    recBtn.disabled = true;
    try {
      const res = await API.get('/ai/recommend.php?limit=8');
      const box = document.getElementById('aiRecs');
      if (!box) return;
      if (!res.tracks?.length) { box.innerHTML = '<p style="color:var(--muted);font-size:13px">Play more tracks so we can learn your taste.</p>'; return; }
      box.innerHTML = `<div class="grid">${res.tracks.map(t => `
        <div class="card" data-play-track="${t.id}">
          <div class="card-cover">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '♪'}</div>
          <div class="card-title">${escapeHtml(t.title)}</div>
          <div class="card-sub">${escapeHtml(t.artist)}</div>
        </div>`).join('')}</div>`;
    } catch (e) { toast(e.message, 'error'); }
    finally { recBtn.disabled = false; }
  });
});