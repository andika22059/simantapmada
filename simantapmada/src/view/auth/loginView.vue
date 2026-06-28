<template>
  <div class="login-container">
    <Transition name="cinematic">
      <div v-if="isLoading" class="loading-overlay" @click="skipLoading">
        <div class="loading-content">
          <div class="logo-glow-wrapper">
            <img
              src="../../assets/img/logoSiMANTAPMADA.png"
              alt="Logo"
              class="loading-logo"
              @error="$event.target.style.display = 'none'"
            />
          </div>
          <div class="loading-text">
            <span
              v-for="(char, i) in 'SiMANTAP MADA'"
              :key="i"
              class="l-char"
              :class="{ spasi: char === ' ' }"
              :style="{ animationDelay: `${0.08 * i}s` }"
            >
              {{ char }}
            </span>
          </div>
          <div class="loading-bar">
            <div class="progress"></div>
          </div>
          <p class="loading-hint">
            <i class="fa-solid fa-hand-pointer"></i> Ketuk layar untuk melewati
          </p>
        </div>
      </div>
    </Transition>

    <!-- 🔥 BACKGROUND SILUET DESA JAWA DINAMIS (warna ikut waktu) -->
    <div class="village-bg">
      <svg
        class="bg-svg"
        viewBox="0 0 800 440"
        preserveAspectRatio="xMidYMid slice"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <linearGradient id="langit" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" :stop-color="tema.s0" />
            <stop offset="55%" :stop-color="tema.s1" />
            <stop offset="100%" :stop-color="tema.s2" />
          </linearGradient>
          <radialGradient id="glowGrad" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="#fff7e0" />
            <stop offset="45%" stop-color="#ffe08a" />
            <stop offset="100%" stop-color="#f5b54a" stop-opacity="0" />
          </radialGradient>
        </defs>

        <rect width="800" height="440" fill="url(#langit)" />

        <!-- Bintang (muncul saat malam) -->
        <g class="bintang" :style="{ opacity: tema.bintang }">
          <circle cx="120" cy="70" r="1.6" fill="#fff">
            <animate
              attributeName="opacity"
              values="0.2;1;0.2"
              dur="3s"
              repeatCount="indefinite"
            />
          </circle>
          <circle cx="640" cy="60" r="1.4" fill="#fff">
            <animate
              attributeName="opacity"
              values="1;0.2;1"
              dur="4s"
              repeatCount="indefinite"
            />
          </circle>
          <circle cx="710" cy="120" r="1.8" fill="#fff">
            <animate
              attributeName="opacity"
              values="0.3;1;0.3"
              dur="2.5s"
              repeatCount="indefinite"
            />
          </circle>
          <circle cx="200" cy="120" r="1.4" fill="#fff">
            <animate
              attributeName="opacity"
              values="1;0.3;1"
              dur="3.5s"
              repeatCount="indefinite"
            />
          </circle>
          <circle cx="480" cy="80" r="1.5" fill="#fff">
            <animate
              attributeName="opacity"
              values="0.4;1;0.4"
              dur="3s"
              repeatCount="indefinite"
            />
          </circle>
          <circle cx="380" cy="50" r="1.2" fill="#fff">
            <animate
              attributeName="opacity"
              values="1;0.4;1"
              dur="4.5s"
              repeatCount="indefinite"
            />
          </circle>
        </g>

        <!-- Matahari + glow (naik-turun & ganti warna sesuai waktu) -->
        <circle
          cx="400"
          :cy="tema.sunY"
          r="150"
          fill="url(#glowGrad)"
          :style="{ opacity: tema.glow }"
        />
        <circle cx="400" :cy="tema.sunY" r="42" :fill="tema.sun" />

        <!-- Bulan (muncul saat malam) -->
        <g class="bulan" :style="{ opacity: tema.bulan }">
          <circle cx="620" cy="100" r="34" fill="#f1f5f9" />
          <circle cx="610" cy="92" r="7" fill="#cbd5e1" opacity="0.6" />
          <circle cx="630" cy="108" r="5" fill="#cbd5e1" opacity="0.5" />
          <circle cx="625" cy="90" r="4" fill="#cbd5e1" opacity="0.5" />
        </g>

        <!-- Gunung (siluet) -->
        <path
          d="M0 250 L 180 140 L 280 240 L 360 175 L 470 250 Z"
          fill="#1f5f57"
          opacity="0.5"
        />
        <path
          d="M330 250 L 520 130 L 640 215 L 800 160 L 800 250 Z"
          fill="#1a544c"
          opacity="0.6"
        />

        <!-- Bukit sawah -->
        <path
          d="M0 270 Q 200 240 400 268 T 800 262 L 800 440 L 0 440 Z"
          fill="#16463f"
          opacity="0.7"
        />

        <!-- Rumah joglo + masjid (siluet) -->
        <g fill="#0d2e2a">
          <path d="M0 320 L 800 320 L 800 440 L 0 440 Z" />
          <!-- joglo kiri -->
          <rect x="150" y="292" width="46" height="30" />
          <path d="M138 292 L 173 268 L 208 292 Z" fill="#0d2e2a" />
          <path
            d="M132 292 L 173 274 L 214 292 Z"
            fill="#0a2421"
            opacity="0.8"
          />
          <!-- joglo kanan -->
          <rect x="600" y="290" width="50" height="32" />
          <path d="M587 290 L 625 264 L 663 290 Z" fill="#0d2e2a" />
          <path
            d="M581 290 L 625 270 L 669 290 Z"
            fill="#0a2421"
            opacity="0.8"
          />
          <!-- masjid (kubah) tengah -->
          <rect x="396" y="248" width="8" height="74" />
          <path d="M380 256 L 400 232 L 420 256 Z" fill="#0d2e2a" />
          <path d="M384 274 L 400 254 L 416 274 Z" fill="#0d2e2a" />
          <circle cx="400" cy="232" r="4" fill="#0d2e2a" />
        </g>

        <!-- Sawah terdepan + tanaman padi -->
        <g fill="#08211e">
          <path d="M0 370 L 800 370 L 800 440 L 0 440 Z" />
          <g opacity="0.55" stroke="#08211e" stroke-width="2">
            <line x1="50" y1="392" x2="50" y2="372" />
            <line x1="95" y1="394" x2="95" y2="374" />
            <line x1="140" y1="392" x2="140" y2="372" />
            <line x1="660" y1="393" x2="660" y2="373" />
            <line x1="705" y1="392" x2="705" y2="372" />
            <line x1="750" y1="394" x2="750" y2="374" />
          </g>
        </g>

        <!-- Burung terbang (siang/pagi) -->
        <g class="burung" :style="{ opacity: tema.burung }">
          <path
            d="M250 160 q 6 -6 12 0 q 6 -6 12 0"
            fill="none"
            stroke="#0d2e2a"
            stroke-width="2"
          />
          <path
            d="M285 175 q 5 -5 10 0 q 5 -5 10 0"
            fill="none"
            stroke="#0d2e2a"
            stroke-width="2"
          />
          <animateTransform
            attributeName="transform"
            type="translate"
            values="-40 0; 560 -30"
            dur="16s"
            repeatCount="indefinite"
          />
        </g>
      </svg>
    </div>

    <div class="login-wrapper" :class="{ 'is-visible': !isLoading }">
      <div class="login-card">
        <div class="login-header">
          <img
            src="../../assets/img/logoSiMANTAPMADA.png"
            alt="Logo"
            class="header-logo"
            @error="$event.target.style.display = 'none'"
          />
          <h2>{{ sapaan }}</h2>
          <p>Sistem Manajemen Arsip & Pelayanan Desa</p>
        </div>

        <div v-if="alertMsg" class="alert-box" :class="alertClass">
          <i class="fa-solid" :class="alertIcon"></i> {{ alertMsg }}
        </div>

        <form @submit.prevent="handleLogin">
          <div class="input-group">
            <label>Username</label>
            <div class="input-field">
              <i class="fa-solid fa-user icon-left"></i>
              <input
                type="text"
                v-model="username"
                placeholder="Masukkan username"
                autocomplete="off"
                autofocus
              />
            </div>
          </div>

          <div class="input-group">
            <label>Password</label>
            <div class="input-field">
              <i class="fa-solid fa-lock icon-left"></i>
              <input
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                placeholder="Masukkan password"
              />
              <i
                class="fa-regular toggle-password"
                :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                @click="showPassword = !showPassword"
              ></i>
            </div>
          </div>

          <button
            type="submit"
            class="btn-login"
            style="position: relative; z-index: 99"
          >
            Masuk Aplikasi <i class="fa-solid fa-arrow-right"></i>
          </button>
        </form>

        <div class="login-footer">
          <p>&copy; {{ new Date().getFullYear() }} Pemerintah Desa Macanan</p>
          <p class="footer-univ">Universitas Negeri Surabaya</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
// 🔥 TYPO UDAH GUE BENERIN DI SINI:
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const route = useRoute();
const router = useRouter();

const isLoading = ref(true);
const showPassword = ref(false);
const username = ref("");
const password = ref("");

// 🔥 PRESET WARNA & ELEMEN BACKGROUND SESUAI WAKTU
const presetWaktu = {
  pagi: {
    s0: "#1a3a52",
    s1: "#e88d5a",
    s2: "#f5c98a",
    sun: "#fff3cf",
    glow: 0.85,
    sunY: 200,
    bintang: 0,
    bulan: 0,
    burung: 0.8,
    sapa: "Selamat Pagi",
  },
  siang: {
    s0: "#3b8fd4",
    s1: "#7ec0e8",
    s2: "#cfe9f5",
    sun: "#fffbe0",
    glow: 0.6,
    sunY: 90,
    bintang: 0,
    bulan: 0,
    burung: 0.8,
    sapa: "Selamat Siang",
  },
  sore: {
    s0: "#5b3a6b",
    s1: "#e86a4a",
    s2: "#f5a85a",
    sun: "#ffd98a",
    glow: 0.95,
    sunY: 250,
    bintang: 0,
    bulan: 0,
    burung: 0.6,
    sapa: "Selamat Sore",
  },
  malam: {
    s0: "#0a1428",
    s1: "#16243f",
    s2: "#2a3a5c",
    sun: "#0a1428",
    glow: 0,
    sunY: -100,
    bintang: 1,
    bulan: 1,
    burung: 0,
    sapa: "Selamat Malam",
  },
};

// Tentukan waktu sekarang dari jam perangkat
const tentukanWaktu = () => {
  const jam = new Date().getHours();
  if (jam >= 5 && jam < 11) return "pagi";
  if (jam >= 11 && jam < 15) return "siang";
  if (jam >= 15 && jam < 18) return "sore";
  return "malam";
};

const tema = ref(presetWaktu[tentukanWaktu()]);
const sapaan = computed(() => tema.value.sapa);

const alertData = computed(() => {
  const pesan = route.query.pesan;
  if (pesan === "gagal")
    return {
      class: "error",
      icon: "fa-circle-xmark",
      msg: "Username atau password salah!",
    };
  if (pesan === "logout")
    return {
      class: "success",
      icon: "fa-circle-check",
      msg: "Berhasil logout.",
    };
  if (pesan === "belum_login")
    return {
      class: "warning",
      icon: "fa-triangle-exclamation",
      msg: "Silakan login terlebih dahulu.",
    };
  return null;
});

const alertMsg = computed(() => alertData.value?.msg);
const alertClass = computed(() => alertData.value?.class);
const alertIcon = computed(() => alertData.value?.icon);

const skipLoading = () => (isLoading.value = false);

onMounted(() => {
  setTimeout(() => (isLoading.value = false), 3000);
});

const handleLogin = async () => {
  if (!username.value || !password.value) {
    Swal.fire({
      icon: "warning",
      title: "Tunggu Dulu...",
      text: "Username dan Password nggak boleh kosong!",
      confirmButtonColor: "#f8bb86",
    });
    return;
  }

  Swal.fire({
    title: "Lagi Ngecek Data...",
    text: "Tunggu bentar ya bang, server lagi mikir...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_URL}/api/login`,
      {
        username: username.value,
        password: password.value,
      },
      { timeout: 5000 },
    );

    const user = response.data.user;
    const token = response.data.token;

    // 🔥 Simpan token & data user biar bisa dipakai di seluruh aplikasi
    localStorage.setItem("token", token);
    localStorage.setItem("user", JSON.stringify(user));
    // Penanda sesi-hidup per-tab. sessionStorage hilang saat tab/browser ditutup,
    // jadi membuka kembali (atau tab bookmark baru) akan dipaksa login ulang.
    sessionStorage.setItem("sesi_aktif", String(Date.now()));
    // Pasang token ke axios biar semua request berikutnya otomatis terautentikasi
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    await Swal.fire({
      icon: "success",
      title: "Login Berhasil!",
      text: `Selamat datang, ${user.nama_lengkap}`,
      showConfirmButton: false,
      timer: 1500,
    });

    // 🔥 Arahkan ke dashboard sesuai role
    const tujuan = {
      admin: "/admin",
      sekdes: "/sekdes",
      kades: "/kades",
      warga: "/warga",
      developer: "/dev",
    };
    router.push(tujuan[user.role] || "/admin");
  } catch (error) {
    console.error("Error Asli:", error);

    // Tutup popup loading dulu biar notif error bisa muncul
    Swal.close();

    // jeda singkat memastikan loading benar-benar tertutup sebelum notif baru
    await new Promise((r) => setTimeout(r, 200));

    if (
      error.code === "ECONNABORTED" ||
      error.message === "Network Error" ||
      !error.response
    ) {
      Swal.fire({
        icon: "error",
        title: "Server Tidak Merespon",
        text: "Pastikan MySQL (XAMPP) sudah Start, dan 'php artisan serve' sedang berjalan.",
        confirmButtonColor: "#059669",
      });
      return;
    }

    const pesanError =
      error.response?.data?.message || "Username atau Password salah!";
    Swal.fire({
      icon: "error",
      title: "Login Gagal",
      text: pesanError,
      confirmButtonColor: "#059669",
    });
  }
};
</script>

<style scoped src="../../assets/css/auth/login.css"></style>

<style>
/* SweetAlert harus di atas .login-container (z-index 9999) & loading-overlay (99999) */
.swal2-container {
  z-index: 999999 !important;
}
</style>
