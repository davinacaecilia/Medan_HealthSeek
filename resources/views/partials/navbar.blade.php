<nav class="navbar">
    <a href="{{ route('rumahSakit.home') }}" class="logo">
        <img src="{{ asset('logoo.png') }}" alt="HealthSeek Logo">
    </a>

    <div class="floating-icons">
        <span class="icon">💊</span>
        <span class="icon">🩺</span>
        <span class="icon">💉</span>
        <span class="icon">🏥</span>
    </div>

    <!-- Tombol Beranda -->
    <a href="{{ route('rumahSakit.home') }}" class="btn-beranda">Beranda</a>
</nav>

<style>
    :root {
  --primary: #4c8547ff;
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
.logo {
  margin-left: 20px;
  margin-top: 10px;
  display: flex;
  align-items: center;
  z-index: 2;
}

.logo img {
  height: 80px;
  cursor: pointer;
  transition: 0.2s;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.logo img:hover {
  transform: scale(1.05);
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
  background: white;
  padding: 6px 18px;
  border-radius: 25px;
  font-weight: 600;
  color: var(--primary);
  border: 2px solid white;
  text-decoration: none;
  font-size: 1rem;

  position: absolute;
  right: 25px;
  top: 15px;

  transition: 0.3s;
  z-index: 2;
}

.btn-beranda:hover {
  transform: scale(1.05);
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}
</style>
