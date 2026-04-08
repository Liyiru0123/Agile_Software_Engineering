@extends('layouts.app')

@section('title', 'Wordle Game')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#FAF0E6] via-[#F7E7D4] to-[#F2DEC7] py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/85 backdrop-blur rounded-3xl border border-[#E7D7C3] shadow-xl p-6 md:p-8">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-3xl font-black tracking-wide text-[#3B2722]">WORDLE</h2>
                    <p class="text-sm text-[#7A5648] mt-1">Guess the 5-letter word in 6 tries.</p>
                </div>
                <div class="status-panel text-right">
                    <p class="status-label">
                        <span class="status-dot"></span>
                        Live Status
                    </p>
                    <p id="status" class="status-text">Game started</p>
                </div>
            </div>

            <div class="mt-5 howto-card" aria-label="How to play Wordle">
                <p class="howto-title">How to Play</p>
                <ul class="howto-list">
                    <li>Guess the hidden 5-letter word in 6 attempts.</li>
                    <li>Type letters, then press <strong>ENTER</strong> to submit.</li>
                    <li>Use <strong>DEL</strong> or <strong>Backspace</strong> to remove a letter.</li>
                    <li>Only valid words in the word list will be accepted.</li>
                    <li>After completing a 5-letter guess, press <strong>ENTER</strong> to submit it.</li>
                    <li>Check the keyboard below to track which letters are used.</li>
                </ul>
                <div class="legend-row" role="list" aria-label="Color legend">
                    <span class="legend-chip" role="listitem">
                        <span class="legend-tile correct">A</span>
                        <span>Correct letter, correct spot</span>
                    </span>
                    <span class="legend-chip" role="listitem">
                        <span class="legend-tile present">B</span>
                        <span>Correct letter, wrong spot</span>
                    </span>
                    <span class="legend-chip" role="listitem">
                        <span class="legend-tile absent">C</span>
                        <span>Letter not in the word</span>
                    </span>
                </div>
            </div>

            <div class="mt-6 grid gap-2 board-wrap" id="board" aria-label="Wordle board"></div>

            <div class="mt-6 space-y-2 keyboard-wrap" id="keyboard" aria-label="Wordle keyboard"></div>

            <div class="mt-6 flex items-center gap-3 flex-wrap">
                <button id="restartBtn" class="px-5 py-2.5 rounded-xl bg-[#4A2C2A] text-white font-semibold hover:bg-[#6B3D2E] transition">
                    New Game
                </button>
                <p class="text-xs text-[#8B6B5A]">Tip: Use your keyboard or click letters below.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-panel {
        background: linear-gradient(135deg, #fffaf3 0%, #f4e7d8 100%);
        border: 1px solid #dcc4af;
        border-radius: 0.85rem;
        padding: 0.55rem 0.8rem;
        box-shadow: 0 6px 18px rgba(74, 44, 42, 0.12);
        min-width: 11.5rem;
    }

    .status-label {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #8f624c;
    }

    .status-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
        background: #c79c3d;
        box-shadow: 0 0 0 rgba(199, 156, 61, 0.45);
        animation: statusPulse 1.8s infinite;
    }

    .status-text {
        margin-top: 0.3rem;
        font-size: 0.95rem;
        font-weight: 800;
        color: #3b2722;
        line-height: 1.35;
    }

    .howto-card {
        background: linear-gradient(135deg, #fffaf3 0%, #f5e8d9 100%);
        border: 1px solid #dcc4af;
        border-radius: 1rem;
        padding: 0.85rem 0.95rem;
        box-shadow: 0 6px 18px rgba(74, 44, 42, 0.1);
    }

    .howto-title {
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #7f5645;
        margin-bottom: 0.4rem;
    }

    .howto-list {
        margin: 0;
        padding-left: 1.05rem;
        color: #4a2c2a;
        font-size: 0.9rem;
        line-height: 1.45;
    }

    .legend-row {
        margin-top: 0.55rem;
        display: grid;
        gap: 0.35rem;
    }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.82rem;
        color: #5f463b;
    }

    .legend-tile {
        width: 1.45rem;
        height: 1.45rem;
        border-radius: 0.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #fff;
    }

    .legend-tile.correct {
        background: #5c8d57;
    }

    .legend-tile.present {
        background: #c79c3d;
    }

    .legend-tile.absent {
        background: #7a6a63;
    }

    .board-wrap {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.92) 0%, rgba(252, 245, 236, 0.9) 100%);
        border: 1px solid #ecd9c5;
        border-radius: 1rem;
        padding: 0.75rem;
    }

    .keyboard-wrap {
        background: #fbf3e9;
        border: 1px solid #eddcc8;
        border-radius: 1rem;
        padding: 0.75rem;
    }

    .wordle-row {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.5rem;
    }

    .wordle-tile {
        aspect-ratio: 1 / 1;
        border-radius: 0.75rem;
        border: 2px solid #d8c6b3;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.5rem;
        color: #3b2722;
        text-transform: uppercase;
        transition: transform 0.12s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    .wordle-tile.filled {
        border-color: #b08968;
    }

    .wordle-tile.flip {
        transform: rotateX(90deg);
    }

    .wordle-tile.correct {
        background: #5c8d57;
        border-color: #5c8d57;
        color: #fff;
    }

    .wordle-tile.present {
        background: #c79c3d;
        border-color: #c79c3d;
        color: #fff;
    }

    .wordle-tile.absent {
        background: #7a6a63;
        border-color: #7a6a63;
        color: #fff;
    }

    .kbd-row {
        display: flex;
        justify-content: center;
        gap: 0.35rem;
    }

    .kbd-key {
        min-width: 2.1rem;
        height: 2.8rem;
        border-radius: 0.6rem;
        border: 1px solid #ceb49f;
        background: #f6ecdf;
        color: #4a2c2a;
        font-weight: 700;
        padding: 0 0.55rem;
        cursor: pointer;
        user-select: none;
    }

    .kbd-key:hover {
        background: #ecdcc8;
    }

    .kbd-key.wide {
        min-width: 4.8rem;
    }

    .kbd-key.correct {
        background: #5c8d57;
        border-color: #5c8d57;
        color: #fff;
    }

    .kbd-key.present {
        background: #c79c3d;
        border-color: #c79c3d;
        color: #fff;
    }

    .kbd-key.absent {
        background: #7a6a63;
        border-color: #7a6a63;
        color: #fff;
    }

    @keyframes statusPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(199, 156, 61, 0.45);
        }
        70% {
            box-shadow: 0 0 0 0.45rem rgba(199, 156, 61, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(199, 156, 61, 0);
        }
    }

    @media (max-width: 640px) {
        .status-panel {
            width: 100%;
            text-align: left;
        }

        .status-label {
            justify-content: flex-start;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const WORDS = @json($words ?? []);
    const FALLBACK_WORDS = ['APPLE', 'BEACH', 'CHAIR', 'DREAM', 'EAGLE'];

    const boardEl = document.getElementById('board');
    const keyboardEl = document.getElementById('keyboard');
    const statusEl = document.getElementById('status');
    const restartBtn = document.getElementById('restartBtn');

    const MAX_ROWS = 6;
    const MAX_COLS = 5;
    const KEY_ROWS = ['QWERTYUIOP', 'ASDFGHJKL', 'ENTERZXCVBNMDEL'];

    let target = '';
    let row = 0;
    let col = 0;
    let guesses = [];
    let gameOver = false;

    function randomWord() {
        const pool = WORDS.length ? WORDS : FALLBACK_WORDS;
        return pool[Math.floor(Math.random() * pool.length)].toUpperCase();
    }

    function buildBoard() {
        boardEl.innerHTML = '';
        for (let r = 0; r < MAX_ROWS; r++) {
            const rowEl = document.createElement('div');
            rowEl.className = 'wordle-row';
            rowEl.dataset.row = String(r);

            for (let c = 0; c < MAX_COLS; c++) {
                const tile = document.createElement('div');
                tile.className = 'wordle-tile';
                tile.dataset.row = String(r);
                tile.dataset.col = String(c);
                rowEl.appendChild(tile);
            }

            boardEl.appendChild(rowEl);
        }
    }

    function buildKeyboard() {
        keyboardEl.innerHTML = '';

        KEY_ROWS.forEach((line) => {
            const rowEl = document.createElement('div');
            rowEl.className = 'kbd-row';

            const letters = line === 'ENTERZXCVBNMDEL'
                ? ['ENTER', ...'ZXCVBNM'.split(''), 'DEL']
                : line.split('');

            letters.forEach((ch) => {
                const key = document.createElement('button');
                key.type = 'button';
                key.className = 'kbd-key';
                if (ch === 'ENTER' || ch === 'DEL') {
                    key.classList.add('wide');
                }
                key.dataset.key = ch;
                key.textContent = ch;
                key.addEventListener('click', () => onInput(ch));
                rowEl.appendChild(key);
            });

            keyboardEl.appendChild(rowEl);
        });
    }

    function getTile(r, c) {
        return boardEl.querySelector(`.wordle-tile[data-row="${r}"][data-col="${c}"]`);
    }

    function updateStatus(message) {
        statusEl.textContent = message;
    }

    function paintKey(letter, cls) {
        const key = keyboardEl.querySelector(`.kbd-key[data-key="${letter}"]`);
        if (!key) {
            return;
        }

        const rank = { absent: 1, present: 2, correct: 3 };
        const current = key.classList.contains('correct') ? 'correct'
            : key.classList.contains('present') ? 'present'
            : key.classList.contains('absent') ? 'absent'
            : '';

        if (!current || rank[cls] > rank[current]) {
            key.classList.remove('correct', 'present', 'absent');
            key.classList.add(cls);
        }
    }

    function evaluateGuess(guess) {
        const marks = Array(MAX_COLS).fill('absent');
        const targetChars = target.split('');
        const guessChars = guess.split('');

        for (let i = 0; i < MAX_COLS; i++) {
            if (guessChars[i] === targetChars[i]) {
                marks[i] = 'correct';
                targetChars[i] = '*';
                guessChars[i] = '#';
            }
        }

        for (let i = 0; i < MAX_COLS; i++) {
            if (guessChars[i] === '#') {
                continue;
            }
            const idx = targetChars.indexOf(guessChars[i]);
            if (idx !== -1) {
                marks[i] = 'present';
                targetChars[idx] = '*';
            }
        }

        return marks;
    }

    function submitGuess() {
        if (col < MAX_COLS || gameOver) {
            return;
        }

        const guess = guesses[row].join('');
        const normalizedGuess = guess.toUpperCase();
        if (!WORDS.length) {
            updateStatus('Word list is empty in database. Using fallback words.');
        }

        if (!WORDS.length ? !FALLBACK_WORDS.includes(normalizedGuess) : !WORDS.includes(normalizedGuess)) {
            updateStatus('Not in word list');
            return;
        }

        const marks = evaluateGuess(guess);

        for (let i = 0; i < MAX_COLS; i++) {
            const tile = getTile(row, i);
            if (!tile) {
                continue;
            }

            setTimeout(() => {
                tile.classList.add('flip');
                setTimeout(() => {
                    tile.classList.remove('flip');
                    tile.classList.add(marks[i]);
                }, 120);
            }, i * 120);

            paintKey(guess[i], marks[i]);
        }

        if (guess === target) {
            gameOver = true;
            updateStatus('You win! The word was ' + target + '.');
            return;
        }

        row += 1;
        col = 0;

        if (row >= MAX_ROWS) {
            gameOver = true;
            updateStatus('Game over. The word was ' + target + '.');
            return;
        }

        updateStatus('Try ' + (row + 1) + ' of ' + MAX_ROWS);
    }

    function addLetter(letter) {
        if (gameOver || col >= MAX_COLS || row >= MAX_ROWS) {
            return;
        }

        const tile = getTile(row, col);
        if (!tile) {
            return;
        }

        tile.textContent = letter;
        tile.classList.add('filled');

        guesses[row][col] = letter;
        col += 1;
    }

    function removeLetter() {
        if (gameOver || col <= 0) {
            return;
        }

        col -= 1;
        guesses[row][col] = '';

        const tile = getTile(row, col);
        if (!tile) {
            return;
        }

        tile.textContent = '';
        tile.classList.remove('filled');
    }

    function onInput(key) {
        if (key === 'ENTER') {
            submitGuess();
            return;
        }

        if (key === 'DEL' || key === 'BACKSPACE') {
            removeLetter();
            return;
        }

        if (/^[A-Z]$/.test(key)) {
            addLetter(key);
        }
    }

    function onPhysicalKeyboard(event) {
        const key = event.key.toUpperCase();

        if (key === 'ENTER' || key === 'BACKSPACE') {
            event.preventDefault();
            onInput(key);
            return;
        }

        if (/^[A-Z]$/.test(key)) {
            onInput(key);
        }
    }

    function initGame() {
        target = randomWord();
        row = 0;
        col = 0;
        gameOver = false;
        guesses = Array.from({ length: MAX_ROWS }, () => Array(MAX_COLS).fill(''));

        buildBoard();
        buildKeyboard();
        updateStatus('Try 1 of ' + MAX_ROWS);
    }

    restartBtn.addEventListener('click', initGame);
    document.addEventListener('keydown', onPhysicalKeyboard);

    initGame();
})();
</script>
@endpush
