document.addEventListener('DOMContentLoaded', () => {
  const panel = document.getElementById('eqPanel');
  const btnEq = document.getElementById('btnEq');
  const closeBtn = document.getElementById('eqClose');
  if (!panel || !btnEq) return;

  // Toggle panel
  btnEq.addEventListener('click', () => panel.classList.toggle('open'));
  closeBtn?.addEventListener('click', () => panel.classList.remove('open'));

  // Build 10-band sliders
  const bandBox = document.getElementById('eqBands');
  if (bandBox) {
    const freqs = AudioEngine.EQ_FREQS;
    const labels = ['32','64','125','250','500','1k','2k','4k','8k','16k'];
    bandBox.innerHTML = freqs.map((f, i) => `
      <div class="eq-band">
        <div class="hz">${labels[i]}</div>
        <input type="range" min="-12" max="12" step="1" value="0" data-band="${i}">
        <div class="db" data-band-db="${i}">0</div>
      </div>
    `).join('');

    bandBox.addEventListener('input', e => {
      const input = e.target;
      if (input.matches('input[data-band]')) {
        const i = parseInt(input.dataset.band, 10);
        AudioEngine.setBand(i, parseInt(input.value, 10));
        const dbEl = bandBox.querySelector(`[data-band-db="${i}"]`);
        if (dbEl) dbEl.textContent = input.value;
      }
    });
  }

  // Global bass/mid/treble
  const bass   = document.getElementById('eqBass');
  const mid    = document.getElementById('eqMid');
  const treble = document.getElementById('eqTreble');
  const bassV  = document.getElementById('eqBassVal');
  const midV   = document.getElementById('eqMidVal');
  const trebV  = document.getElementById('eqTrebleVal');

  bass?.addEventListener('input',   () => { AudioEngine.setBass(+bass.value);     bassV.textContent = bass.value + ' dB'; });
  mid?.addEventListener('input',    () => { AudioEngine.setMid(+mid.value);       midV.textContent = mid.value + ' dB'; });
  treble?.addEventListener('input', () => { AudioEngine.setTreble(+treble.value); trebV.textContent = treble.value + ' dB'; });

  // Presets
  document.getElementById('eqPresets')?.addEventListener('click', e => {
    const btn = e.target.closest('[data-preset]');
    if (!btn) return;
    document.querySelectorAll('#eqPresets button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const preset = btn.dataset.preset;
    AudioEngine.applyPreset(preset);
    // Reflect bands in sliders
    const presetBands = AudioEngine.PRESETS[preset] || [];
    document.querySelectorAll('input[data-band]').forEach((inp, i) => {
      const val = presetBands[i] ?? 0;
      inp.value = val;
      const dbEl = document.querySelector(`[data-band-db="${i}"]`);
      if (dbEl) dbEl.textContent = val;
    });
    // Reset global
    if (bass)   { bass.value = 0;   bassV.textContent   = '0 dB'; }
    if (mid)    { mid.value = 0;    midV.textContent    = '0 dB'; }
    if (treble) { treble.value = 0; trebV.textContent   = '0 dB'; }
    toast('Preset: ' + preset, 'success', 1500);
  });

  // Reset all
  document.getElementById('eqReset')?.addEventListener('click', () => {
    AudioEngine.resetAll();
    document.querySelectorAll('input[data-band]').forEach((inp, i) => {
      inp.value = 0;
      const dbEl = document.querySelector(`[data-band-db="${i}"]`);
      if (dbEl) dbEl.textContent = 0;
    });
    if (bass)   { bass.value = 0;   bassV.textContent   = '0 dB'; }
    if (mid)    { mid.value = 0;    midV.textContent    = '0 dB'; }
    if (treble) { treble.value = 0; trebV.textContent   = '0 dB'; }
    document.querySelectorAll('#eqPresets button').forEach(b => b.classList.remove('active'));
    document.querySelector('#eqPresets [data-preset="flat"]')?.classList.add('active');
    toast('Equalizer reset', 'success', 1500);
  });

  // Restore from storage on load
  const state = AudioEngine.getState();
  if (state.settings) {
    if (bass)   { bass.value = state.settings.bass;     bassV.textContent   = state.settings.bass + ' dB'; }
    if (mid)    { mid.value = state.settings.mid;       midV.textContent    = state.settings.mid + ' dB'; }
    if (treble) { treble.value = state.settings.treble; trebV.textContent   = state.settings.treble + ' dB'; }
    (state.settings.bands || []).forEach((v, i) => {
      const inp = document.querySelector(`input[data-band="${i}"]`);
      if (inp) inp.value = v;
      const dbEl = document.querySelector(`[data-band-db="${i}"]`);
      if (dbEl) dbEl.textContent = v;
    });
    document.querySelectorAll('#eqPresets button').forEach(b => {
      b.classList.toggle('active', b.dataset.preset === state.settings.preset);
    });
  }
});