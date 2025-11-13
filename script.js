/* Générateur de kitsch: MagicBertrand partout */
(function () {
  const CHAOS_COUNT = 64; // densité de base
  const MAX_NODES = 200; // coupe-circuit perf
  const WORDS = [
    "MAGICBERTRAND",
    "MagicBertrand",
    "M4G1C-B3RTR4ND",
    "✨ Magic ✨ Bertrand ✨",
    "MAGIC • BERTRAND",
    "MB",
    "MAGIC! BERTRAND!",
  ];

  const COLORS = [
    "#ffffff",
    "#ff00ff",
    "#00ffff",
    "#ffff00",
    "#ff6600",
    "#00ff66",
    "#8800ff",
  ];

  const chaos = document.getElementById("chaos");
  if (!chaos) return;

  const rand = (min, max) => Math.random() * (max - min) + min;
  const choice = (arr) => arr[Math.floor(Math.random() * arr.length)];

  function makeTag(i) {
    const base = document.createElement("div");
    base.className = "mb-tag";

    const spin = document.createElement("span");
    spin.className = "spin";

    const pulse = document.createElement("span");
    pulse.className = "pulse";

    pulse.textContent = choice(WORDS);

    const size = rand(12, 96); // px (utilisé comme vw/px mix)
    const x = rand(-10, 90); // vw
    const y = rand(-10, 90); // vh
    const baseRot = Math.floor(rand(-180, 180));
    const hueRotate = Math.floor(rand(0, 360));
    const durationSpin = rand(2.5, 12).toFixed(2) + "s";
    const durationPulse = rand(0.8, 2.8).toFixed(2) + "s";
    const durationBlink = rand(0.3, 1.2).toFixed(2) + "s";

    base.style.left = x + "vw";
    base.style.top = y + "vh";

    pulse.style.fontSize = `min(${size}vw, ${size * 1.5}px)`;
    pulse.style.color = choice(COLORS);

    base.style.transform = `rotate(${baseRot}deg)`;
    base.style.filter = `hue-rotate(${hueRotate}deg) saturate(1.8)`;

    spin.style.animation = `spin ${durationSpin} linear infinite`;
    pulse.style.animation = [
      `pulse ${durationPulse} ease-in-out infinite alternate`,
      `blink ${durationBlink} steps(2,end) infinite`,
      `rainbow ${rand(1.5, 6).toFixed(2)}s linear infinite`,
    ].join(", ");

    // structure imbriquée
    spin.appendChild(pulse);
    base.appendChild(spin);

    return base;
  }

  function populate(n) {
    const target = Math.min(n, MAX_NODES);
    const frag = document.createDocumentFragment();
    for (let i = 0; i < target; i++) frag.appendChild(makeTag(i));
    chaos.appendChild(frag);
  }

  // Ajout lent et progressif pour encore plus de kitsch en continu
  function dripFeed() {
    if (chaos.childElementCount >= MAX_NODES) return;
    chaos.appendChild(makeTag(chaos.childElementCount));
    setTimeout(dripFeed, rand(150, 650));
  }

  // responsive: ajuster la densité en fonction de la taille de l'écran
  const area = window.innerWidth * window.innerHeight;
  const scale = Math.min(1.6, Math.max(0.5, area / (1280 * 720)));
  populate(Math.floor(CHAOS_COUNT * scale));
  dripFeed();
})();
