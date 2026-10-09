document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('uploadForm'); if (!form) return;
  const drop = document.getElementById('dropZone');
  const input = document.getElementById('audioInput');
  const info = document.getElementById('fileInfo');
  const progress = document.getElementById('upProgress');
  const fill = document.getElementById('upFill');
  const btn = document.getElementById('upBtn');

  drop?.addEventListener('click', () => input.click());
  ['dragover','dragenter'].forEach(ev => drop?.addEventListener(ev, e => { e.preventDefault(); drop.style.borderColor = 'var(--primary)'; }));
  ['dragleave','drop'].forEach(ev => drop?.addEventListener(ev, e => { e.preventDefault(); drop.style.borderColor = 'var(--border)'; }));
  drop?.addEventListener('drop', e => { if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; onFile(); } });
  input?.addEventListener('change', onFile);

  function onFile() {
    const f = input.files[0]; if (!f) return;
    info.textContent = `Selected: ${f.name} (${(f.size/1024/1024).toFixed(2)} MB)`;
    const base = f.name.replace(/\.[^.]+$/, '');
    const t = document.getElementById('fTitle'); const a = document.getElementById('fArtist');
    if (t && !t.value) t.value = base;
    if (a && !a.value) a.value = 'Unknown Artist';
  }

  form.addEventListener('submit', e => {
    e.preventDefault();
    const audio = input.files[0];
    if (!audio) return toast('Please select an audio file', 'error');
    if (audio.size > 40 * 1024 * 1024) return toast('File exceeds 40MB limit', 'error');

    const fd = new FormData(form);
    progress.style.display = 'block';
    btn.disabled = true; btn.textContent = 'Uploading…';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', window.BASE_URL + '/api/tracks/upload.php');
    xhr.setRequestHeader('X-CSRF-Token', document.querySelector('meta[name="csrf-token"]').content);
    xhr.upload.addEventListener('progress', ev => { if (ev.lengthComputable) fill.style.width = (ev.loaded / ev.total * 100) + '%'; });
    xhr.onload = () => {
      btn.disabled = false; btn.textContent = 'Upload track';
      try {
        const res = JSON.parse(xhr.responseText);
        if (xhr.status === 200 && res.ok) {
          toast('Track uploaded successfully', 'success');
          setTimeout(() => location.href = window.BASE_URL + '/library.php', 800);
        } else toast(res.error || 'Upload failed', 'error');
      } catch { toast('Upload failed', 'error'); }
    };
    xhr.onerror = () => { btn.disabled = false; btn.textContent = 'Upload track'; toast('Network error', 'error'); };
    xhr.send(fd);
  });

  const importBtn = document.getElementById('importBtn');
  importBtn?.addEventListener('click', async () => {
    const url = document.getElementById('mediaUrl').value.trim();
    if (!url) return toast('Paste a media URL first', 'error');
    importBtn.disabled = true; importBtn.textContent = 'Importing…';
    try {
      const res = await API.post('/media/import.php', { url });
      toast('Imported: ' + res.title, 'success');
      document.getElementById('mediaUrl').value = '';
      setTimeout(() => location.href = window.BASE_URL + '/library.php', 900);
    } catch (e) { toast(e.message, 'error'); }
    finally { importBtn.disabled = false; importBtn.textContent = 'Import'; }
  });
});