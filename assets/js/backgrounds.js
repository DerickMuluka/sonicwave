/**
 * Rotating hero backgrounds.
 * 5 curated images from Unsplash (royalty-free).
 * Changes every 60s with crossfade.
 * Respects prefers-reduced-motion.
 */
(function () {
  const IMAGES = [
    { url: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=1920&q=80&auto=format', vibe: 'Concert Lights' },
    { url: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=1920&q=80&auto=format', vibe: 'Studio Session' },
    { url: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=1920&q=80&auto=format', vibe: 'Live Stage' },
    { url: 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=1920&q=80&auto=format', vibe: 'Vinyl & Neon' },
    { url: 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=1920&q=80&auto=format', vibe: 'City Nights' },
  ];

  const INTERVAL = 60000; // 60s

  function init() {
    const hero = document.querySelector('[data-rotating-hero]');
    if (!hero) return;

    hero.innerHTML = `
      <div class="hero-bg-layer active" style="background-image:url('${IMAGES[0].url}')"></div>
      <div class="hero-bg-layer" style="background-image:url('${IMAGES[1].url}')"></div>
      <div class="hero-vibe-label" id="heroVibe">${IMAGES[0].vibe}</div>
    `;
    const layers = hero.querySelectorAll('.hero-bg-layer');
    const vibeLabel = document.getElementById('heroVibe');

    let current = 0;
    let showingAlt = false;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

    IMAGES.forEach(img => { const i = new Image(); i.src = img.url; });

    setInterval(() => {
      current = (current + 1) % IMAGES.length;
      const incoming = showingAlt ? layers[0] : layers[1];
      const outgoing = showingAlt ? layers[1] : layers[0];
      incoming.style.backgroundImage = `url('${IMAGES[current].url}')`;
      incoming.classList.add('active');
      outgoing.classList.remove('active');
      if (vibeLabel) {
        vibeLabel.style.opacity = '0';
        setTimeout(() => {
          vibeLabel.textContent = IMAGES[current].vibe;
          vibeLabel.style.opacity = '1';
        }, 300);
      }
      showingAlt = !showingAlt;
    }, INTERVAL);
  }

  document.addEventListener('DOMContentLoaded', init);
})();