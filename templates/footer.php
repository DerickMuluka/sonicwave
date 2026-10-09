<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/audio-engine.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/player.js') ?>"></script>
<script src="<?= asset('js/equalizer.js') ?>"></script>
<?php foreach ((array)$extraJs as $j): ?>
<script src="<?= asset('js/' . $j) ?>"></script>
<?php endforeach; ?>