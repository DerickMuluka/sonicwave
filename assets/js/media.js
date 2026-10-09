(function(){
  const box = document.getElementById('mediaContent'); if (!box) return;

  function buildEmbed(t) {
    const src = t.source, id = t.external_id, url = t.source_url;
    if (src === 'youtube') return `<iframe src="https://www.youtube.com/embed/${id}?rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
    if (src === 'vimeo') return `<iframe src="https://player.vimeo.com/video/${id}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
    if (src === 'spotify') return `<iframe src="https://open.spotify.com/embed/track/${id}?theme=0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" allowfullscreen style="height:420px"></iframe>`;
    if (src === 'soundcloud') return `<iframe src="https://w.soundcloud.com/player/?url=${encodeURIComponent(url)}&color=%237c5cff&auto_play=false&hide_related=true&visual=true" allow="autoplay"></iframe>`;
    if (src === 'deezer') return `<iframe src="https://widget.deezer.com/widget/dark/track/${id}" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture"></iframe>`;
    if (src === 'apple') return `<iframe src="${url.replace('music.apple.com','embed.music.apple.com')}" allow="autoplay *; encrypted-media *;"></iframe>`;
    if (src === 'bandcamp') return `<iframe src="${url}" allow="autoplay"></iframe>`;
    if (t.embed_type === 'video') return `<video controls preload="metadata" src="${t.stream_url}" ${t.cover_path ? `poster="${t.cover_path}"` : ''}></video>`;
    return `<audio controls preload="metadata" src="${t.stream_url}"></audio>`;
  }

  async function loadOne(id) {
    try {
      const res = await API.get('/tracks/list.php?id=' + id);
      const t = (res.tracks || [])[0];
      if (!t) { box.innerHTML = `<div class="empty"><p>Media not found</p></div>`; return; }
      box.innerHTML = `
        <div style="display:flex;gap:20px;align-items:flex-end;margin-bottom:20px;flex-wrap:wrap">
          <div style="width:140px;height:140px;border-radius:14px;background:var(--grad-main);overflow:hidden;flex-shrink:0">
            ${t.cover_path ? `<img src="${t.cover_path}" style="width:100%;height:100%;object-fit:cover">` : '<div style="width:100%;height:100%;display:grid;place-items:center;font-size:44px">♪</div>'}
          </div>
          <div style="min-width:0">
            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.4px;font-weight:700;margin-bottom:8px">${escapeHtml(t.source)}</div>
            <h1 style="font-size:26px;font-weight:800;letter-spacing:-0.5px;margin-bottom:6px">${escapeHtml(t.title)}</h1>
            <p style="color:var(--muted);font-size:14px">${escapeHtml(t.artist)}</p>
          </div>
        </div>
        <div class="media-viewer">${buildEmbed(t)}</div>
        <div class="media-controls">
          <a class="btn btn-primary" href="${t.download_url}" download>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download
          </a>
          <button class="btn btn-ghost" onclick="Player.play([${JSON.stringify(t).replace(/"/g,'&quot;')}], 0)">Play in SonicWave</button>
          ${t.source_url ? `<a class="btn btn-ghost" href="${t.source_url}" target="_blank" rel="noopener">Open original</a>` : ''}
          <div class="spacer"></div>
          <div class="res-controls" id="resControls">
            <button data-res="360">360p</button>
            <button data-res="480">480p</button>
            <button data-res="720" class="active">720p</button>
            <button data-res="1080">1080p</button>
          </div>
        </div>`;

      // Resolution controls (visual + YouTube where applicable)
      document.getElementById('resControls')?.addEventListener('click', e => {
        const btn = e.target.closest('[data-res]'); if (!btn) return;
        document.querySelectorAll('#resControls button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const res = btn.dataset.res;
        const iframe = box.querySelector('.media-viewer iframe');
        if (iframe && iframe.src.includes('youtube.com/embed')) {
          const base = iframe.src.split('?')[0];
          iframe.src = base + '?rel=0&vq=' + (res === '1080' ? 'hd1080' : res === '720' ? 'hd720' : 'small');
        }
      });
    } catch (e) { box.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`; }
  }

  async function loadHub() {
    box.innerHTML = `<div class="empty"><p>Loading media hub…</p></div>`;
    try {
      const res = await API.get('/tracks/list.php?limit=80');
      const tracks = res.tracks || [];
      if (!tracks.length) { box.innerHTML = `<div class="empty"><div class="empty-icon">▶</div><h3>No media yet</h3><p>Import from YouTube, Spotify, SoundCloud and more.</p><a class="btn btn-primary" href="${window.BASE_URL}/upload.php">Import media</a></div>`; return; }
      box.innerHTML = `<div class="grid">${tracks.map(t => `
        <a class="card" href="${window.BASE_URL}/media.php?id=${t.id}">
          <div class="card-cover">${t.cover_path ? `<img src="${t.cover_path}" alt="">` : '▶'}<div class="play-overlay"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></div></div>
          <div class="card-title">${escapeHtml(t.title)}</div>
          <div class="card-sub">${escapeHtml(t.artist)} ${platformBadge(t.source)}</div>
        </a>`).join('')}</div>`;
    } catch (e) { box.innerHTML = `<div class="empty"><p>${escapeHtml(e.message)}</p></div>`; }
  }

  const id = new URLSearchParams(location.search).get('id');
  if (id) loadOne(parseInt(id, 10)); else loadHub();
})();