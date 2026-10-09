<div class="player-bar" id="playerBar">
  <div class="now-playing">
    <div class="np-cover" id="npCover">♪</div>
    <div class="np-info">
      <div class="np-title" id="npTitle">Nothing playing</div>
      <div class="np-artist" id="npArtist">Select a track to begin</div>
    </div>
  </div>

  <div class="player-controls">
    <div class="pc-buttons">
      <button class="pc-btn" id="btnShuffle" title="Shuffle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="4" y1="4" x2="9" y2="9"/></svg>
      </button>
      <button class="pc-btn" id="btnPrev" title="Previous">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
      </button>
      <button class="pc-btn pc-play" id="btnPlay" title="Play">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
      </button>
      <button class="pc-btn" id="btnNext" title="Next">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 6h2v12h-2zM6 6l8.5 6L6 18z"/></svg>
      </button>
      <button class="pc-btn" id="btnRepeat" title="Repeat">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      </button>
    </div>
    <div class="progress-row">
      <span class="time" id="timeCur">0:00</span>
      <div class="seek-wrap" id="seekWrap">
        <div class="seek-bar"><div class="seek-fill" id="seekFill"></div></div>
      </div>
      <span class="time" id="timeDur">0:00</span>
    </div>
  </div>

  <div class="player-extras">
    <button class="pc-btn" id="btnLike" title="Like">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </button>
    <a class="pc-btn" id="btnDownload" title="Download" href="#" download>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
    </a>
    <div class="volume-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:var(--muted)"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
      <div class="vol-bar" id="volBar"><div class="vol-fill" id="volFill"></div></div>
    </div>
  </div>
</div>

<audio id="audioEl" preload="metadata" crossorigin="anonymous"></audio>
<div class="toast-wrap" id="toastWrap"></div>