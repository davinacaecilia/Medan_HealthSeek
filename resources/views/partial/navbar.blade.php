<nav class="navbar">
    <div class="logo">
        <img src="{{ asset('logoo.png') }}" alt="Medan HealthSeek Logo">
    </div>

    <div class="floating-icons">
        <span class="icon">💊</span>
        <span class="icon">🩺</span>
        <span class="icon">💉</span>
        <span class="icon">🏥</span>
    </div>

    <!-- Tombol Beranda -->
    <a href="{{ route('home') }}" class="btn-beranda">Beranda</a>
</nav>

<style>
    :root {
  --primary: #7fb77e;
  --secondary: #f9f9f9;
  --dark: #1f2937;
  --light: #ffffff;
  --shadow: rgba(0, 0, 0, 0.1);
  --radius: 10px;
}

/* NAVBAR */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 10vh;
  background: linear-gradient(135deg, var(--primary), #9cd89c);
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: var(--light);
  text-align: center;
  overflow: visible;
  z-index: 1000;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

/* Logo Tengah */
.logo img {
  height: 80px;
  margin-left: 20px;
  margin-top: 10px;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

/* Floating Icons */
.floating-icons {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
  z-index: 0;
}

.floating-icons .icon {
  position: absolute;
  font-size: 1.8rem;
  opacity: 0.25;
  animation: floatIcon 5s ease-in-out infinite;
}

.floating-icons .icon:nth-child(1) { top: 25%; left: 15%; animation-duration: 4.7s; }
.floating-icons .icon:nth-child(2) { top: 35%; left: 40%; animation-duration: 5.2s; }
.floating-icons .icon:nth-child(3) { top: 28%; right: 30%; animation-duration: 5.5s; }
.floating-icons .icon:nth-child(4) { top: 11%; right: 10%; animation-duration: 4.9s; }

@keyframes floatIcon {
  0% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0); }
}

/* Tombol Beranda */
.btn-beranda {
  color: white;
  text-decoration: none;
  font-weight: 600;
  font-size: 1rem;
  transition: 0.3s;
  position: absolute;
  right: 20px;
  top: 20px;
}

.btn-beranda:hover,
.btn-beranda.active {
  color: #e9f7e9;
  text-shadow: 0 0 8px rgba(255,255,255,0.6);
}
    </style>
