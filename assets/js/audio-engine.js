/**
 * SonicWave Audio Engine
 * Wraps an <audio> or <video> element with a Web Audio graph:
 *   source → preamp → bass → mid → treble → masterGain → destination
 *
 * Also supports a 10-band equalizer via BiquadFilterNode chain.
 */

const AudioEngine = (() => {
  let ctx = null;
  let sourceNode = null;
  let masterGain = null;
  let preampGain = null;
  let bassFilter = null;
  let midFilter = null;
  let trebleFilter = null;
  let eqBands = [];
  let connectedEl = null;

  const EQ_FREQS = [32, 64, 125, 250, 500, 1000, 2000, 4000, 8000, 16000];
  const PRESETS = {
    flat:        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    bass:        [8, 7, 6, 4, 2, 0, 0, 0, 0, 0],
    treble:      [0, 0, 0, 0, 0, 1, 3, 5, 7, 8],
    vocal:       [-2, -2, 0, 3, 5, 5, 3, 1, 0, -1],
    rock:        [5, 4, 3, 1, -1, -1, 1, 3, 4, 5],
    jazz:        [3, 2, 1, 2, -1, -1, 0, 1, 2, 3],
    electronic:  [6, 5, 4, 2, 0, -1, 1, 3, 5, 6],
    podcast:     [-4, -3, -1, 2, 4, 5, 4, 2, 0, -2],
  };

  function getSettings() {
    try {
      return JSON.parse(localStorage.getItem('sw_eq_settings')) || defaultSettings();
    } catch { return defaultSettings(); }
  }
  function saveSettings(s) {
    localStorage.setItem('sw_eq_settings', JSON.stringify(s));
  }
  function defaultSettings() {
    return {
      enabled: false,
      preset: 'flat',
      bands: PRESETS.flat.slice(),
      bass: 0,        // global bass boost -12..+12 dB
      mid: 0,
      treble: 0,
      preamp: 0,      // -12..+12 dB
      volume: 0.8,
    };
  }

  function ensureContext() {
    if (ctx) return ctx;
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return null;
    ctx = new AudioCtx();
    return ctx;
  }

  function disconnectAll() {
    try { sourceNode?.disconnect(); } catch {}
  }

  /**
   * Attach the engine to a media element (audio or video).
   * Safe to call multiple times; reconnects to a fresh graph.
   */
  function attach(el) {
    if (!el) return false;
    const c = ensureContext();
    if (!c) return false;

    // If already attached to same element, skip
    if (connectedEl === el && sourceNode) {
      // Just resume context if suspended
      if (c.state === 'suspended') c.resume();
      return true;
    }

    // Rebuild graph
    disconnectAll();
    connectedEl = el;
    sourceNode = c.createMediaElementSource(el);

    preampGain = c.createGain();
    bassFilter = c.createBiquadFilter();
    bassFilter.type = 'lowshelf';
    bassFilter.frequency.value = 200;

    midFilter = c.createBiquadFilter();
    midFilter.type = 'peaking';
    midFilter.frequency.value = 1000;
    midFilter.Q.value = 0.8;

    trebleFilter = c.createBiquadFilter();
    trebleFilter.type = 'highshelf';
    trebleFilter.frequency.value = 4000;

    masterGain = c.createGain();

    // 10-band EQ
    eqBands = EQ_FREQS.map((freq, i) => {
      const f = c.createBiquadFilter();
      f.type = i === 0 ? 'lowshelf' : i === EQ_FREQS.length - 1 ? 'highshelf' : 'peaking';
      f.frequency.value = freq;
      f.Q.value = 1.2;
      f.gain.value = 0;
      return f;
    });

    // Chain: source → preamp → bass → 10 bands → mid → treble → master → dest
    sourceNode.connect(preampGain);
    preampGain.connect(bassFilter);
    let prev = bassFilter;
    eqBands.forEach(f => { prev.connect(f); prev = f; });
    prev.connect(midFilter);
    midFilter.connect(trebleFilter);
    trebleFilter.connect(masterGain);
    masterGain.connect(c.destination);

    // Apply saved settings
    applySettings(getSettings());
    return true;
  }

  function applySettings(s) {
    if (!ctx) return;
    s = s || getSettings();
    if (preampGain)  preampGain.gain.value  = dbToGain(s.preamp);
    if (bassFilter)  bassFilter.gain.value  = s.bass;
    if (midFilter)   midFilter.gain.value   = s.mid;
    if (trebleFilter) trebleFilter.gain.value = s.treble;
    if (masterGain)  masterGain.gain.value  = s.volume;
    eqBands.forEach((f, i) => { f.gain.value = s.bands?.[i] || 0; });
  }

  function dbToGain(db) { return Math.pow(10, db / 20); }

  function setVolume(v) {
    const s = getSettings();
    s.volume = Math.max(0, Math.min(1, v));
    saveSettings(s);
    if (masterGain && ctx) masterGain.gain.value = s.volume;
    if (connectedEl) connectedEl.volume = s.volume; // fallback for unmounted cases
  }
  function setBass(v)   { const s = getSettings(); s.bass   = clampDb(v); saveSettings(s); applySettings(s); }
  function setMid(v)    { const s = getSettings(); s.mid    = clampDb(v); saveSettings(s); applySettings(s); }
  function setTreble(v) { const s = getSettings(); s.treble = clampDb(v); saveSettings(s); applySettings(s); }
  function setPreamp(v) { const s = getSettings(); s.preamp = clampDb(v); saveSettings(s); applySettings(s); }

  function setBand(index, db) {
    const s = getSettings();
    if (!s.bands) s.bands = PRESETS.flat.slice();
    s.bands[index] = clampDb(db);
    s.preset = 'custom';
    saveSettings(s);
    applySettings(s);
  }

  function applyPreset(name) {
    const s = getSettings();
    if (!PRESETS[name]) return;
    s.preset = name;
    s.bands = PRESETS[name].slice();
    s.bass = 0; s.mid = 0; s.treble = 0;
    saveSettings(s);
    applySettings(s);
  }

  function clampDb(v) { return Math.max(-12, Math.min(12, Number(v) || 0)); }
  function resetAll() {
    const s = defaultSettings();
    s.volume = getSettings().volume;   // keep volume
    saveSettings(s);
    applySettings(s);
  }

  function resumeContext() {
    if (ctx && ctx.state === 'suspended') ctx.resume();
  }
  function suspendContext() {
    if (ctx && ctx.state === 'running') ctx.suspend();
  }

  function getState() {
    return {
      available: !!ctx,
      preset: getSettings().preset,
      settings: getSettings(),
      presets: Object.keys(PRESETS),
    };
  }

  return {
    attach,
    resumeContext,
    suspendContext,
    setVolume, setBass, setMid, setTreble, setPreamp, setBand,
    applyPreset,
    resetAll,
    getState,
    PRESETS,
    EQ_FREQS,
  };
})();