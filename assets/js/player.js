const Player = (() => {
  const audio = $('#audioEl');
  const video = $('#videoEl');
  const bar   = $('#playerBar');
  const overlay = $('#videoOverlay');
  const overlayInner = $('#videoOverlayInner');
  if (!audio || !bar) return { play:()=>{}, toggle:()=>{}, next:()=>{}, prev:()=>{}, current:()=>null, queue:()=>[], stop:()=>{} };

  let queue = [], index = -1, shuffle = false, repeat = 'off', currentTrack = null, seekDragging = false, mode = 'audio';

  const K_STATE = 'sw_player_state';
  const K_VOL   = 'sw_volume';
  const K_SESSION = 'sw_session_active';

  function persistState() {
    try {
      localStorage.setItem(K_STATE, JSON.stringify({
        queue, index, shuffle, repeat,
        currentTime: (mode === 'video' ? video.currentTime : audio.currentTime) || 0,
        mode,
        savedAt: Date.now(),
      }));
      localStorage.setItem(K_SESSION, '1');
    } catch {}
  }
  function loadState() {
    try {
      if (localStorage.getItem(K_SESSION) !== '1') return null;
      const raw = localStorage.getItem(K_STATE);
      if (!raw) return null;
      const s = JSON.parse(raw);
      if (Date.now() - (s.savedAt || 0) > 6 * 60 * 60 * 1000) {
        localStorage.removeItem(K_STATE);
        localStorage.removeItem(K_SESSION);
        return null;
      }
      return s;
    } catch { return null; }
  }
  function clearState() {
    localStorage.removeItem(K_STATE);
    localStorage.removeItem(K_SESSION);
  }

  const els = {
    cover: $('#npCover'), title: $('#npTitle'), artist: $('#npArtist'),
    play: $('#btnPlay'), prev: $('#btnPrev'), next: $('#btnNext'),
    shuffle: $('#btnShuffle'), repeat: $('#btnRepeat'),
    seekWrap: $('#seekWrap'), seekFill: $('#seekFill'),
    cur: $('#timeCur'), dur: $('#timeDur'),
    vol: $('#volBar'), volFill: $('#volFill'),
    like: $('#btnLike'), download: $('#btnDownload'),
    videoMode: $('#btnVideoMode'),
    close: $('#btnClose'),
    eqBtn: $('#btnEq'),
    eqPanel: $('#eqPanel'),
  };

  const PLAY_SVG  = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
  const PAUSE_SVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>';

  const savedVol = parseFloat(localStorage.getItem(K_VOL));
  const initVol  = isFinite(savedVol) ? savedVol : 0.8;
  audio.volume = initVol;
  video.volume = initVol;
  if (els.volFill) els.volFill.style.width = (initVol * 100) + '%';

  function show() { bar.classList.add('visible'); document.body.classList.remove('no-player'); }
  function hide() { bar.classList.remove('visible'); document.body.classList.add('no-player'); }

  function setTrackUI(t) {
    if (!t) { hide(); return; }
    show();
    els.title.textContent  = t.title;
    els.artist.textContent = t.artist;
    if (t.cover_path) {
      els.cover.innerHTML = `<img src="${t.cover_path}" alt="">`;
      els.cover.classList.add('has-image');
    } else {
      els.cover.innerHTML = '';
      els.cover.classList.remove('has-image');
    }
    if (els.like) {
      els.like.classList.toggle('liked', !!t.liked);
      els.like.style.color = t.liked ? 'var(--accent)' : '';
    }
    if (els.videoMode) {
      els.videoMode.style.display = (t.embed_type === 'video' || t.embed_type === 'iframe') ? '' : 'none';
    }
  }

  function isEmbed(t) {
    return t && t.source && !t.stream_url && t.embed_url;
  }

  function buildEmbedHtml(t) {
    const src = t.source, id = t.external_id, url = t.source_url;
    if (src === 'youtube')    return `<iframe src="https://www.youtube.com/embed/${id}?autoplay=1&rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
    if (src === 'vimeo')      return `<iframe src="https://player.vimeo.com/video/${id}?autoplay=1" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
    if (src === 'spotify')    return `<iframe src="https://open.spotify.com/embed/track/${id}?utm_source=generator&theme=0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" allowfullscreen style="height:480px"></iframe>`;
    if (src === 'soundcloud') return `<iframe src="https://w.soundcloud.com/player/?url=${encodeURIComponent(url)}&color=%237c5cff&auto_play=true&hide_related=true&show_comments=false&visual=true" allow="autoplay"></iframe>`;
    if (src === 'deezer')     return `<iframe src="https://widget.deezer.com/widget/dark/track/${id}?autoplay=true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture"></iframe>`;
    if (src === 'apple')      return `<iframe src="${url.replace('music.apple.com','embed.music.apple.com')}" allow="autoplay *; encrypted-media *;"></iframe>`;
    if (src === 'bandcamp')   return `<iframe src="${url}" allow="autoplay"></iframe>`;
    return '';
  }

  function openVideoOverlay(t) {
    if (!overlay || !overlayInner) return;
    if (t.stream_url && t.embed_type === 'video') {
      overlayInner.innerHTML = `<video src="${t.stream_url}" controls autoplay style="width:100%;aspect-ratio:16/9;border-radius:16px"></video>`;
    } else {
      const html = buildEmbedHtml(t);
      if (!html) return;
      overlayInner.innerHTML = html;
    }
    overlay.classList.add('open');
  }
  function closeVideoOverlay() {
    if (!overlay) return;
    overlay.classList.remove('open');
    overlayInner.innerHTML = '';
  }
  $('#videoClose')?.addEventListener('click', closeVideoOverlay);
  overlay?.addEventListener('click', e => { if (e.target === overlay) closeVideoOverlay(); });

  async function play(tracks, startIndex = 0, restoreTime = 0) {
    if (tracks && tracks.length) { queue = tracks.slice(); index = startIndex; }
    if (index < 0 || index >= queue.length) return;
    currentTrack = queue[index];
    setTrackUI(currentTrack);

    if (isEmbed(currentTrack)) {
      audio.pause(); video.pause();
      els.play.innerHTML = PLAY_SVG;
      els.cover?.classList.remove('playing');
      els.cover?.classList.add('paused');
      openVideoOverlay(currentTrack);
      persistState();
      return;
    }

    if (!currentTrack.stream_url) {
      // Skip broken track
      toast('Skipping unplayable track', 'info', 1500);
      if (queue.length > 1) {
        index = (index + 1) % queue.length;
        return play();
      }
      return;
    }

    if (currentTrack.embed_type === 'video') {
      mode = 'video';
      video.src = currentTrack.stream_url;
      video.style.display = '';
      audio.style.display = 'none';
      if (typeof AudioEngine !== 'undefined' && AudioEngine.attach) AudioEngine.attach(video);
      if (restoreTime > 0) video.currentTime = restoreTime;
      try { await video.play(); els.play.innerHTML = PAUSE_SVG; } catch (e) { console.warn(e); }
    } else {
      mode = 'audio';
      audio.src = currentTrack.stream_url;
      audio.style.display = '';
      video.style.display = 'none';
      if (typeof AudioEngine !== 'undefined' && AudioEngine.attach) AudioEngine.attach(audio);
      if (restoreTime > 0) audio.currentTime = restoreTime;
      try { await audio.play(); els.play.innerHTML = PAUSE_SVG; } catch (e) { console.warn(e); }
    }

    if (typeof AudioEngine !== 'undefined' && AudioEngine.resumeContext) AudioEngine.resumeContext();
    API.post('/tracks/play.php', { track_id: currentTrack.id }).catch(() => {});
    $$('.track-row.playing').forEach(el => el.classList.remove('playing'));
    const row = document.querySelector(`.track-row[data-id="${currentTrack.id}"]`);
    if (row) row.classList.add('playing');
    persistState();
  }

  function toggle() {
    if (!currentTrack) return;
    if (isEmbed(currentTrack)) { openVideoOverlay(currentTrack); return; }
    const el = mode === 'video' ? video : audio;
    if (!el.src) return;
    if (el.paused) { if (typeof AudioEngine !== 'undefined' && AudioEngine.resumeContext) AudioEngine.resumeContext(); el.play(); }
    else el.pause();
  }

  function next(auto = false) {
    if (!queue.length) return;
    if (repeat === 'one' && auto) {
      const el = mode === 'video' ? video : audio;
      el.currentTime = 0; el.play();
      return;
    }
    index = shuffle ? Math.floor(Math.random() * queue.length) : (index + 1) % queue.length;
    play();
  }
  function prev() {
    if (!queue.length) return;
    const el = mode === 'video' ? video : audio;
    if (el.currentTime > 3) { el.currentTime = 0; return; }
    index = (index - 1 + queue.length) % queue.length;
    play();
  }

  function stop() {
    try { audio.pause(); audio.removeAttribute('src'); audio.load(); } catch {}
    try { video.pause(); video.removeAttribute('src'); video.load(); } catch {}
    try { closeVideoOverlay(); } catch {}
    queue = []; index = -1; currentTrack = null;
    setTrackUI(null);
    clearState();
    if (els.play) els.play.innerHTML = PLAY_SVG;
    if (els.seekFill) els.seekFill.style.width = '0%';
    if (els.cur) els.cur.textContent = '0:00';
    if (els.dur) els.dur.textContent = '0:00';
    els.cover?.classList.remove('playing', 'paused');
    toast('Playback stopped', 'info', 1800);
  }

  ['timeupdate','loadedmetadata','play','pause','ended'].forEach(evt => {
    [audio, video].forEach(el => {
      el.addEventListener(evt, () => {
        if (evt === 'timeupdate') {
          if (seekDragging) return;
          const pct = el.duration ? (el.currentTime / el.duration) * 100 : 0;
          if (els.seekFill) els.seekFill.style.width = pct + '%';
          if (els.cur) els.cur.textContent = fmtTime(el.currentTime);
          if (Math.floor(el.currentTime) % 3 === 0) persistState();
        }
        if (evt === 'loadedmetadata') {
          if (els.dur) els.dur.textContent = fmtTime(el.duration);
        }
        if (evt === 'play') {
          if (els.play) els.play.innerHTML = PAUSE_SVG;
          els.cover?.classList.add('playing');
          els.cover?.classList.remove('paused');
        }
        if (evt === 'pause') {
          if (els.play) els.play.innerHTML = PLAY_SVG;
          els.cover?.classList.add('paused');
          els.cover?.classList.remove('playing');
        }
        if (evt === 'ended') {
          if (repeat === 'off' && !shuffle && index === queue.length - 1) { persistState(); return; }
          next(true);
        }
      });
    });
  });

  els.play?.addEventListener('click', toggle);
  els.next?.addEventListener('click', () => next(false));
  els.prev?.addEventListener('click', prev);
  els.shuffle?.addEventListener('click', () => { shuffle = !shuffle; els.shuffle.classList.toggle('active', shuffle); persistState(); });
  els.repeat?.addEventListener('click', () => {
    repeat = repeat === 'off' ? 'all' : repeat === 'all' ? 'one' : 'off';
    els.repeat.classList.toggle('active', repeat !== 'off');
    persistState();
  });
  els.close?.addEventListener('click', stop);
  els.videoMode?.addEventListener('click', () => { if (currentTrack) openVideoOverlay(currentTrack); });

  function seekFromEvent(e) {
    const el = mode === 'video' ? video : audio;
    const rect = els.seekWrap.getBoundingClientRect();
    const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
    const pct = Math.max(0, Math.min(1, x / rect.width));
    if (el.duration) el.currentTime = pct * el.duration;
    els.seekFill.style.width = (pct * 100) + '%';
  }
  els.seekWrap?.addEventListener('mousedown', e => { seekDragging = true; seekFromEvent(e); });
  document.addEventListener('mousemove', e => { if (seekDragging) seekFromEvent(e); });
  document.addEventListener('mouseup', () => { if (seekDragging) { seekDragging = false; persistState(); } });
  els.seekWrap?.addEventListener('click', seekFromEvent);

  els.vol?.addEventListener('click', e => {
    const rect = els.vol.getBoundingClientRect();
    const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
    audio.volume = pct; video.volume = pct;
    if (typeof AudioEngine !== 'undefined' && AudioEngine.setVolume) AudioEngine.setVolume(pct);
    els.volFill.style.width = (pct * 100) + '%';
    localStorage.setItem(K_VOL, pct);
  });

  els.like?.addEventListener('click', async () => {
    if (!currentTrack) return;
    try {
      const res = await API.post('/tracks/like.php', { track_id: currentTrack.id });
      currentTrack.liked = res.liked;
      els.like.classList.toggle('liked', res.liked);
      els.like.style.color = res.liked ? 'var(--accent)' : '';
      toast(res.liked ? 'Added to Liked' : 'Removed from Liked', 'success', 1800);
    } catch (e) { toast(e.message, 'error'); }
  });

  els.download?.addEventListener('click', e => {
    e.preventDefault();
    if (currentTrack) openDownloadDialog(currentTrack);
  });

  window.addEventListener('beforeunload', persistState);
  window.addEventListener('pagehide', persistState);

  /**
   * INSTANT RESUME — reads state synchronously and starts playback immediately.
   * This is what makes music feel continuous.
   */
  function instantResume() {
    const s = loadState();
    if (!s || !s.queue || !s.queue.length) return;

    queue = s.queue;
    index = s.index;
    shuffle = !!s.shuffle;
    repeat = s.repeat || 'off';
    if (els.shuffle) els.shuffle.classList.toggle('active', shuffle);
    if (els.repeat)  els.repeat.classList.toggle('active', repeat !== 'off');

    const track = queue[index];
    if (!track) return;

    if (isEmbed(track)) {
      currentTrack = track;
      setTrackUI(track);
      return;
    }

    // Do NOT show the bar until playback actually starts (avoids flash of stopped state)
    if (!track.stream_url) {
      // Skip broken first track
      const nextValid = queue.findIndex(t => t.stream_url);
      if (nextValid < 0) return;
      index = nextValid;
    }

    currentTrack = queue[index];
    setTrackUI(currentTrack);

    if (currentTrack.embed_type === 'video') {
      mode = 'video';
      video.src = currentTrack.stream_url;
      video.style.display = '';
      audio.style.display = 'none';
      if (s.currentTime > 0) {
        video.addEventListener('loadedmetadata', () => { video.currentTime = s.currentTime; }, { once: true });
      }
      video.play().catch(() => {
        // Autoplay blocked — will resume on first user interaction
        const resume = () => { video.play(); document.removeEventListener('click', resume); };
        document.addEventListener('click', resume, { once: true });
      });
    } else {
      mode = 'audio';
      audio.src = currentTrack.stream_url;
      audio.style.display = '';
      video.style.display = 'none';
      if (s.currentTime > 0) {
        audio.addEventListener('loadedmetadata', () => { audio.currentTime = s.currentTime; }, { once: true });
      }
      audio.play().catch(() => {
        const resume = () => { audio.play(); document.removeEventListener('click', resume); };
        document.addEventListener('click', resume, { once: true });
      });
    }

    if (typeof AudioEngine !== 'undefined' && AudioEngine.attach) {
      AudioEngine.attach(mode === 'video' ? video : audio);
      if (AudioEngine.resumeContext) AudioEngine.resumeContext();
    }
  }

  // Run immediately (before DOMContentLoaded completes) to minimize gap
  instantResume();
  document.addEventListener('DOMContentLoaded', instantResume);

  return { play, toggle, next, prev, stop, queue: () => queue, current: () => currentTrack, persistState };
})();

// ══════════════════════════════════════════════════════════
// DOWNLOAD DIALOG
// ══════════════════════════════════════════════════════════
function openDownloadDialog(track) {
  const existing = document.getElementById('dlDialog');
  if (existing) existing.remove();

  const blocked = ['spotify','apple','deezer'].includes(track.source);

  const dialog = document.createElement('div');
  dialog.id = 'dlDialog';
  dialog.className = 'modal-backdrop open';
  dialog.innerHTML = `
    <div class="modal" style="max-width:520px">
      <h3>Download "${escapeHtml(track.title)}"</h3>
      <p style="font-size:13px;color:var(--muted);margin-bottom:18px">
        ${blocked
          ? 'This track is on a DRM-protected platform — downloading is not permitted. You can stream it in the player, or open the original source.'
          : 'Choose a format. Our server downloads and converts the source, then serves the file directly to you.'}
      </p>

      ${blocked ? `
        <div style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.3);color:#fcd34d;padding:12px 14px;border-radius:10px;font-size:12px;line-height:1.6;margin-bottom:16px">
          <strong>Not available for download.</strong> Spotify, Apple Music, and Deezer content is encrypted with DRM that cannot be removed.
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end">
          <button class="btn btn-ghost" onclick="document.getElementById('dlDialog').remove()">Close</button>
          ${track.source_url ? `<a class="btn btn-primary" href="${track.source_url}" target="_blank" rel="noopener">Open source</a>` : ''}
        </div>
      ` : `
        <div style="margin-bottom:16px">
          <label style="font-size:12px;color:var(--muted);font-weight:600;display:block;margin-bottom:8px">Format</label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px" id="fmtChoices">
            <button class="chip" data-format="mp3" style="justify-content:center;padding:14px 10px;flex-direction:column;gap:6px">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
              MP3
            </button>
            <button class="chip active" data-format="mp4" style="justify-content:center;padding:14px 10px;flex-direction:column;gap:6px">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
              MP4
            </button>
            <button class="chip" data-format="webm" style="justify-content:center;padding:14px 10px;flex-direction:column;gap:6px">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              WebM
            </button>
          </div>
        </div>

        <div style="margin-bottom:16px">
          <label style="font-size:12px;color:var(--muted);font-weight:600;display:block;margin-bottom:8px">Quality (video only)</label>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px" id="qChoices">
            <button class="chip" data-quality="360">360p</button>
            <button class="chip" data-quality="480">480p</button>
            <button class="chip active" data-quality="720">720p</button>
            <button class="chip" data-quality="best">Best</button>
          </div>
        </div>

        <div style="background:rgba(124,92,255,0.08);border:1px solid rgba(124,92,255,0.25);padding:11px 14px;border-radius:10px;font-size:12px;color:#a78bfa;line-height:1.5;margin-bottom:16px">
          ⓘ Server-side download may take 10–60 seconds depending on track length. Keep this window open.
        </div>

        <div id="dlStatus" style="display:none;margin-bottom:16px">
          <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--muted)">
            <div style="width:16px;height:16px;border:2px solid rgba(255,255,255,0.15);border-top-color:var(--primary-2);border-radius:50%;animation:spin 0.8s linear infinite"></div>
            <span id="dlStatusText">Preparing download…</span>
          </div>
        </div>

        <div style="display:flex;gap:8px;justify-content:flex-end">
          <button class="btn btn-ghost" onclick="document.getElementById('dlDialog').remove()">Cancel</button>
          <button class="btn btn-primary" id="dlStart">Download</button>
        </div>
      `}
    </div>
  `;
  document.body.appendChild(dialog);

  if (blocked) return;

  let fmt = 'mp4', quality = '720';
  dialog.querySelectorAll('#fmtChoices .chip').forEach(c => c.addEventListener('click', () => {
    dialog.querySelectorAll('#fmtChoices .chip').forEach(x => x.classList.remove('active'));
    c.classList.add('active');
    fmt = c.dataset.format;
    const q = dialog.querySelector('#qChoices');
    if (q) q.style.opacity = (fmt === 'mp3') ? '0.4' : '1';
  }));
  dialog.querySelectorAll('#qChoices .chip').forEach(c => c.addEventListener('click', () => {
    if (fmt === 'mp3') return;
    dialog.querySelectorAll('#qChoices .chip').forEach(x => x.classList.remove('active'));
    c.classList.add('active');
    quality = c.dataset.quality;
  }));

  dialog.querySelector('#dlStart')?.addEventListener('click', async () => {
    const status = dialog.querySelector('#dlStatus');
    const statusText = dialog.querySelector('#dlStatusText');
    const startBtn = dialog.querySelector('#dlStart');
    startBtn.disabled = true; startBtn.textContent = 'Downloading…';
    status.style.display = 'block';

    const t0 = Date.now();
    const ticker = setInterval(() => {
      const s = Math.floor((Date.now() - t0) / 1000);
      statusText.textContent = `Downloading… ${s}s elapsed`;
    }, 1000);

    try {
      const params = new URLSearchParams({ id: track.id, format: fmt, quality });
      const downloadUrl = `${window.BASE_URL}/api/media/download.php?${params}`;
      const resp = await fetch(downloadUrl, { credentials: 'same-origin' });

      if (!resp.ok) {
        const err = await resp.json().catch(() => ({ error: 'Download failed' }));
        clearInterval(ticker);
        status.innerHTML = `<div style="color:#fca5a5;font-size:13px">✕ ${escapeHtml(err.error || 'Download failed')}${err.detail ? '<br><span style="font-size:11px;opacity:.7">' + escapeHtml(err.detail) + '</span>' : ''}</div>`;
        startBtn.disabled = false; startBtn.textContent = 'Retry';
        return;
      }

      const blob = await resp.blob();
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${track.artist} - ${track.title}.${fmt}`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);

      clearInterval(ticker);
      status.innerHTML = `<div style="color:#86efac;font-size:13px">✓ Download complete</div>`;
      setTimeout(() => dialog.remove(), 1200);
    } catch (e) {
      clearInterval(ticker);
      status.innerHTML = `<div style="color:#fca5a5;font-size:13px">✕ ${escapeHtml(e.message)}</div>`;
      startBtn.disabled = false; startBtn.textContent = 'Retry';
    }
  });
}

const __style = document.createElement('style');
__style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(__style);

document.addEventListener('click', async e => {
  const playBtn = e.target.closest('[data-play-track]');
  if (!playBtn) return;
  e.preventDefault();
  const id = parseInt(playBtn.dataset.playTrack, 10);
  const ctxEl = playBtn.closest('[data-track-list]');
  let list = [];
  if (ctxEl && ctxEl.dataset.trackList) {
    try { list = JSON.parse(ctxEl.dataset.trackList); } catch {}
  }
  if (!list.length) {
    try { const res = await API.get('/tracks/list.php?id=' + id); list = res.tracks || []; }
    catch (err) { toast('Could not load track', 'error'); return; }
  }
  const idx = Math.max(0, list.findIndex(t => t.id === id));
  Player.play(list, idx);
});