<div class="player-bar" id="playerBar">
  <div class="now-playing">
    <div class="np-cover" id="npCover">♪</div>
    <div class="np-info">
      <div class="np-title" id="npTitle">—</div>
      <div class="np-artist" id="npArtist">—</div>
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
    <button class="pc-btn" id="btnVideoMode" title="Video mode">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
    </button>
    <button class="pc-btn" id="btnEq" title="Equalizer">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="20" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="4"/><line x1="12" y1="20" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="4"/><line x1="20" y1="20" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="4"/></svg>
    </button>
    <button class="pc-btn" id="btnLike" title="Like">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </button>
    <button class="pc-btn" id="btnDownload" title="Download">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
        <line x1="3" y1="21" x2="21" y2="21"/>
      </svg>
    </button>
    <div class="volume-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:var(--muted)"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
      <div class="vol-bar" id="volBar"><div class="vol-fill" id="volFill"></div></div>
    </div>
    <button class="pc-btn" id="btnClose" title="Stop playback" style="color:#fca5a5">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
</div>

<div class="eq-panel" id="eqPanel">
  <div class="eq-head">
    <h4>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="20" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="4"/><line x1="12" y1="20" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="4"/><line x1="20" y1="20" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="4"/></svg>
      Sound Equalizer
    </h4>
    <button class="close" id="eqClose">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>

  <div class="eq-presets" id="eqPresets">
    <button data-preset="flat" class="active">Flat</button>
    <button data-preset="bass">Bass</button>
    <button data-preset="treble">Treble</button>
    <button data-preset="vocal">Vocal</button>
    <button data-preset="rock">Rock</button>
    <button data-preset="jazz">Jazz</button>
    <button data-preset="electronic">EDM</button>
    <button data-preset="podcast">Podcast</button>
  </div>

  <div class="eq-global">
    <div class="eq-slider">
      <div class="lbl">Bass</div>
      <input type="range" id="eqBass" min="-12" max="12" step="1" value="0">
      <div class="val" id="eqBassVal">0 dB</div>
    </div>
    <div class="eq-slider">
      <div class="lbl">Mid</div>
      <input type="range" id="eqMid" min="-12" max="12" step="1" value="0">
      <div class="val" id="eqMidVal">0 dB</div>
    </div>
    <div class="eq-slider">
      <div class="lbl">Treble</div>
      <input type="range" id="eqTreble" min="-12" max="12" step="1" value="0">
      <div class="val" id="eqTrebleVal">0 dB</div>
    </div>
  </div>

  <div class="eq-bands" id="eqBands"></div>

  <div class="eq-foot">
    <button id="eqReset">Reset all</button>
    <button id="eqEnable" style="color:var(--primary-2)">Enable EQ</button>
  </div>
</div>

<audio id="audioEl" preload="metadata" crossorigin="anonymous"></audio>
<video id="videoEl" preload="metadata" crossorigin="anonymous" style="display:none"></video>

<div class="video-overlay" id="videoOverlay">
  <button class="video-overlay-close" id="videoClose">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <div class="video-overlay-inner" id="videoOverlayInner"></div>
</div>

<div class="toast-wrap" id="toastWrap"></div>