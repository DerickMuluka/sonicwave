document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-pw-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.pwToggle);
      if (!target) return;
      const show = target.type === 'password';
      target.type = show ? 'text' : 'password';
      btn.innerHTML = show
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    });
  });

  const loginForm = document.getElementById('loginForm');
  const regForm = document.getElementById('registerForm');
  const err = document.getElementById('err');
  const ok = document.getElementById('ok');
  function showErr(m) { if (!err) return; err.textContent = m; err.classList.add('show'); }
  function showOk(m) { if (!ok) return; ok.textContent = m; ok.classList.add('show'); }
  function clear() { err?.classList.remove('show'); ok?.classList.remove('show'); }

  loginForm?.addEventListener('submit', async e => {
    e.preventDefault();
    clear();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.textContent = 'Signing in…';
    try {
      // Clear any stale player state from a previous session
      try {
        localStorage.removeItem('sw_player_state');
        localStorage.removeItem('sw_session_active');
      } catch {}
      const res = await API.post('/auth/login.php', {
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value
      });
      location.href = (res.user && res.user.role === 'admin') ? window.BASE_URL + '/admin.php' : window.BASE_URL + '/index.php';
    } catch (ex) {
      showErr(ex.message || 'Login failed');
      btn.disabled = false; btn.textContent = 'Sign in';
    }
  });

  regForm?.addEventListener('submit', async e => {
    e.preventDefault();
    clear();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.textContent = 'Creating account…';
    try {
      const res = await API.post('/auth/register.php', {
        username: document.getElementById('username').value.trim(),
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value
      });
      location.href = window.BASE_URL + '/login.php?registered=1&u=' + encodeURIComponent(res.username || '');
    } catch (ex) {
      showErr(ex.message || 'Registration failed');
      btn.disabled = false; btn.textContent = 'Create account';
    }
  });

  const params = new URLSearchParams(location.search);
  if (params.get('registered') === '1' && ok) showOk('Account created successfully. Please sign in to continue.');
});

// Global logout — clears player state so nothing resumes on next login
async function logoutUser() {
  try {
    localStorage.removeItem('sw_player_state');
    localStorage.removeItem('sw_session_active');
    // Pause any audio immediately
    const audio = document.getElementById('audioEl');
    const video = document.getElementById('videoEl');
    if (audio) { audio.pause(); audio.removeAttribute('src'); audio.load(); }
    if (video) { video.pause(); video.removeAttribute('src'); video.load(); }
  } catch {}
  try { await API.post('/auth/logout.php', {}); } catch {}
  location.href = window.BASE_URL + '/login.php';
}