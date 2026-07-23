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

        <!-- 🏞️ PEMANDANGAN DESA (susunan yang disetujui, ikut fase waktu) -->
        <g class="siluet-desa" :class="{ malam: tema.bintang > 0 }"
           transform="translate(0,130) scale(0.5)">

          <path class="gn-jauh" d="M0 500 L300 190 L560 500 Z"/>
          <path class="gn-jauh" d="M1010 500 L1360 200 L1600 500 Z"/>
          <path class="gn-dekat" d="M340 500 L800 110 L1200 500 Z"/>
          <path class="gn-puncak" d="M756 148 L800 110 L844 148 L800 178 Z"/>

          <rect class="tanah" x="0" y="500" width="1600" height="120"/>
          <path class="galur" d="M0 596 Q 400 586 800 596 T 1600 596"/>

          <g transform="translate(392,268.4) scale(0.4)">
            
  <polygon class="utama" points="95,456 440,296 760,296 1105,456"/>
  <rect class="utama" x="425" y="288" width="350" height="14" rx="7"/>
  <rect class="utama" x="160" y="456" width="880" height="240"/>
  <rect class="putih" x="305" y="452" width="590" height="14"/>
  <rect class="utama" x="305" y="466" width="590" height="44"/>
  <rect class="dalam" x="330" y="510" width="540" height="186"/>
  <rect class="putih" x="392" y="566" width="26" height="100" rx="3"/>
  <rect class="putih" x="428" y="566" width="26" height="100" rx="3"/>
  <rect class="putih" x="746" y="566" width="26" height="100" rx="3"/>
  <rect class="putih" x="782" y="566" width="26" height="100" rx="3"/>
  <rect class="putih" x="556" y="548" width="88" height="148" rx="4"/>
  <rect class="dalam" x="597" y="548" width="6" height="148"/>
  <rect class="utama" x="344" y="510" width="28" height="186"/>
  <rect class="utama" x="470" y="510" width="28" height="186"/>
  <rect class="utama" x="702" y="510" width="28" height="186"/>
  <rect class="utama" x="828" y="510" width="28" height="186"/>
  <rect class="putih" x="200" y="556" width="26" height="104" rx="3"/>
  <rect class="putih" x="240" y="556" width="26" height="104" rx="3"/>
  <rect class="putih" x="934" y="556" width="26" height="104" rx="3"/>
  <rect class="putih" x="974" y="556" width="26" height="104" rx="3"/>
  <rect class="utama" x="312" y="696" width="576" height="16"/>
  <rect class="utama" x="284" y="712" width="632" height="16"/>
  <rect class="utama" x="256" y="728" width="688" height="16"/>
          </g>
          <g transform="translate(898,342.2) scale(0.3)">
            
  <polygon class="utama" points="105,462 385,366 550,244 650,244 815,366 1095,462"/>
  <rect class="utama" x="535" y="236" width="130" height="14" rx="7"/>
  <rect class="putih" x="175" y="462" width="850" height="12"/>
  <rect class="utama" x="175" y="474" width="850" height="42"/>
  <rect class="dalam" x="192" y="516" width="816" height="174"/>
  <rect class="utama" x="205" y="516" width="30" height="174"/>
  <rect class="utama" x="357" y="516" width="30" height="174"/>
  <rect class="utama" x="509" y="516" width="30" height="174"/>
  <rect class="utama" x="661" y="516" width="30" height="174"/>
  <rect class="utama" x="813" y="516" width="30" height="174"/>
  <rect class="utama" x="965" y="516" width="30" height="174"/>
  <rect class="utama" x="199" y="652" width="42" height="38"/>
  <rect class="utama" x="351" y="652" width="42" height="38"/>
  <rect class="utama" x="503" y="652" width="42" height="38"/>
  <rect class="utama" x="655" y="652" width="42" height="38"/>
  <rect class="utama" x="807" y="652" width="42" height="38"/>
  <rect class="utama" x="959" y="652" width="42" height="38"/>
  <rect class="utama" x="172" y="690" width="856" height="20"/>
  <rect class="utama" x="148" y="710" width="904" height="18"/>
  <rect class="utama" x="124" y="728" width="952" height="18"/>
          </g>
          <g v-if="tema.bintang > 0" transform="translate(805,320.6) scale(0.26)">
            <defs>
  <clipPath id="klipDM"><path d="M 258,78 Q 229,438 258,798 L 276,798 Q 247,438 276,78 Z"/></clipPath>
  <clipPath id="klipBM"><path d="M 343,78 Q 372,438 343,798 L 325,798 Q 354,438 325,78 Z"/></clipPath>
  <filter id="glowBesarM" x="-120%" y="-30%" width="340%" height="160%"><feGaussianBlur stdDeviation="26"/></filter>
  <filter id="glowKecilM" x="-80%" y="-20%" width="260%" height="140%"><feGaussianBlur stdDeviation="9"/></filter>
</defs>
<path d="M 258,78 Q 229,438 258,798 L 276,798 Q 247,438 276,78 Z" fill="#FFE9A8" opacity="0.9" filter="url(#glowBesarM)"/>
<path d="M 258,78 Q 229,438 258,798 L 276,798 Q 247,438 276,78 Z" fill="#FFF6D8" opacity="0.9" filter="url(#glowKecilM)"/>
<path d="M 343,78 Q 372,438 343,798 L 325,798 Q 354,438 325,78 Z" fill="#FFE9A8" opacity="0.9" filter="url(#glowBesarM)"/>
<path d="M 343,78 Q 372,438 343,798 L 325,798 Q 354,438 325,78 Z" fill="#FFF6D8" opacity="0.9" filter="url(#glowKecilM)"/>
<rect fill="#1E2A3A" x="272" y="810" width="56" height="100"/>
<rect fill="#1E2A3A" x="212" y="906" width="176" height="38" rx="6"/>
<path d="M 276,56 L 326,56 Q 346,56 347,78 Q 379,438 347,798 Q 346,820 326,820 L 276,820 Q 256,820 254,798 Q 222,438 254,78 Q 256,56 276,56 Z" fill="#1E2A3A"/>
<path d="M 258,78 Q 229,438 258,798 L 276,798 Q 247,438 276,78 Z" fill="#FFFDF2"/>
<path d="M 343,78 Q 372,438 343,798 L 325,798 Q 354,438 325,78 Z" fill="#FFFDF2"/>
<g clip-path="url(#klipDM)">
  <rect x="243.5" y="282" width="24" height="44" fill="#27408B" opacity="0.95"/>
  <rect x="242.3" y="332" width="24" height="44" fill="#27408B" opacity="0.95"/>
  <rect x="241.6" y="382" width="24" height="44" fill="#1E8E3E" opacity="0.95"/>
  <rect x="241.5" y="432" width="24" height="44" fill="#E5C11A" opacity="0.95"/>
  <rect x="242.0" y="482" width="24" height="44" fill="#FFFFFF" opacity="0.95"/>
  <rect x="243.0" y="532" width="24" height="44" fill="#B3261E" opacity="0.95"/>
  <rect x="244.6" y="582" width="24" height="44" fill="#27408B" opacity="0.95"/>
  <rect x="246.7" y="632" width="24" height="44" fill="#27408B" opacity="0.95"/>
</g>
<g clip-path="url(#klipBM)">
  <rect x="335.5" y="282" width="24" height="44" fill="#27408B" opacity="0.95"/>
  <rect x="336.7" y="332" width="24" height="44" fill="#27408B" opacity="0.95"/>
  <rect x="337.4" y="382" width="24" height="44" fill="#1E8E3E" opacity="0.95"/>
  <rect x="337.5" y="432" width="24" height="44" fill="#E5C11A" opacity="0.95"/>
  <rect x="337.0" y="482" width="24" height="44" fill="#FFFFFF" opacity="0.95"/>
  <rect x="336.0" y="532" width="24" height="44" fill="#B3261E" opacity="0.95"/>
  <rect x="334.4" y="582" width="24" height="44" fill="#27408B" opacity="0.95"/>
  <rect x="332.3" y="632" width="24" height="44" fill="#27408B" opacity="0.95"/>
</g>
<rect fill="#1E2A3A" x="239.5" y="240" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="314.5" y="240" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="235.7" y="470" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="318.8" y="470" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="242.3" y="676" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="311.5" y="676" width="52" height="16" rx="6"/>
          </g>
          <g v-else transform="translate(805,320.6) scale(0.26)">
            <defs>
  <clipPath id="klipDS"><path d="M 258,78 Q 229,438 258,798 L 276,798 Q 247,438 276,78 Z"/></clipPath>
  <clipPath id="klipBS"><path d="M 343,78 Q 372,438 343,798 L 325,798 Q 354,438 325,78 Z"/></clipPath>
</defs>
<rect fill="#1E2A3A" x="272" y="810" width="56" height="100"/>
<rect fill="#1E2A3A" x="212" y="906" width="176" height="38" rx="6"/>
<path d="M 276,56 L 326,56 Q 346,56 347,78 Q 379,438 347,798 Q 346,820 326,820 L 276,820 Q 256,820 254,798 Q 222,438 254,78 Q 256,56 276,56 Z" fill="#1E2A3A"/>
<path d="M 258,78 Q 229,438 258,798 L 276,798 Q 247,438 276,78 Z" fill="#DDE3EA"/>
<path d="M 343,78 Q 372,438 343,798 L 325,798 Q 354,438 325,78 Z" fill="#DDE3EA"/>
<g clip-path="url(#klipDS)">
  <rect x="243.5" y="282" width="24" height="44" fill="#27408B" opacity="0.40"/>
  <rect x="242.3" y="332" width="24" height="44" fill="#27408B" opacity="0.40"/>
  <rect x="241.6" y="382" width="24" height="44" fill="#1E8E3E" opacity="0.40"/>
  <rect x="241.5" y="432" width="24" height="44" fill="#E5C11A" opacity="0.40"/>
  <rect x="242.0" y="482" width="24" height="44" fill="#FFFFFF" opacity="0.40"/>
  <rect x="243.0" y="532" width="24" height="44" fill="#B3261E" opacity="0.40"/>
  <rect x="244.6" y="582" width="24" height="44" fill="#27408B" opacity="0.40"/>
  <rect x="246.7" y="632" width="24" height="44" fill="#27408B" opacity="0.40"/>
</g>
<g clip-path="url(#klipBS)">
  <rect x="335.5" y="282" width="24" height="44" fill="#27408B" opacity="0.40"/>
  <rect x="336.7" y="332" width="24" height="44" fill="#27408B" opacity="0.40"/>
  <rect x="337.4" y="382" width="24" height="44" fill="#1E8E3E" opacity="0.40"/>
  <rect x="337.5" y="432" width="24" height="44" fill="#E5C11A" opacity="0.40"/>
  <rect x="337.0" y="482" width="24" height="44" fill="#FFFFFF" opacity="0.40"/>
  <rect x="336.0" y="532" width="24" height="44" fill="#B3261E" opacity="0.40"/>
  <rect x="334.4" y="582" width="24" height="44" fill="#27408B" opacity="0.40"/>
  <rect x="332.3" y="632" width="24" height="44" fill="#27408B" opacity="0.40"/>
</g>
<rect fill="#1E2A3A" x="239.5" y="240" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="314.5" y="240" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="235.7" y="470" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="318.8" y="470" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="242.3" y="676" width="52" height="16" rx="6"/>
<rect fill="#1E2A3A" x="311.5" y="676" width="52" height="16" rx="6"/>
          </g>

          <g transform="translate(348,566)">
            <rect x="-3" y="-210" width="6" height="210" class="tiang"/>
            <circle cx="0" cy="-214" r="6" class="tiang"/>
            <g class="bendera-desa">
              <rect x="4" y="-206" width="76" height="24" fill="#c1272d"/>
              <rect x="4" y="-182" width="76" height="24" fill="#f8fafc"/>
            </g>
          </g>

          <g transform="translate(105,574) scale(1.85)"><g class="pohon-desa">
              <rect x="-8" y="-58" width="16" height="62" class="btg"/>
              <circle cx="0" cy="-80" r="44" class="dn1"/>
              <circle cx="-32" cy="-58" r="32" class="dn2"/>
              <circle cx="32" cy="-58" r="32" class="dn2"/>
              <circle cx="0" cy="-108" r="28" class="dn3"/></g></g>
          <g transform="translate(262,574) scale(1.45)"><g class="pohon-desa">
              <rect x="-8" y="-58" width="16" height="62" class="btg"/>
              <circle cx="0" cy="-80" r="44" class="dn1"/>
              <circle cx="-32" cy="-58" r="32" class="dn2"/>
              <circle cx="32" cy="-58" r="32" class="dn2"/>
              <circle cx="0" cy="-108" r="28" class="dn3"/></g></g>
          <g transform="translate(1318,574) scale(1.8)"><g class="pohon-desa">
              <rect x="-8" y="-58" width="16" height="62" class="btg"/>
              <circle cx="0" cy="-80" r="44" class="dn1"/>
              <circle cx="-32" cy="-58" r="32" class="dn2"/>
              <circle cx="32" cy="-58" r="32" class="dn2"/>
              <circle cx="0" cy="-108" r="28" class="dn3"/></g></g>
          <g transform="translate(1487,574) scale(1.5)"><g class="pohon-desa">
              <rect x="-8" y="-58" width="16" height="62" class="btg"/>
              <circle cx="0" cy="-80" r="44" class="dn1"/>
              <circle cx="-32" cy="-58" r="32" class="dn2"/>
              <circle cx="32" cy="-58" r="32" class="dn2"/>
              <circle cx="0" cy="-108" r="28" class="dn3"/></g></g>

          <g class="kunang-grup" :style="{ opacity: tema.bintang }">
            <circle class="kunang" style="animation-delay:0s" cx="300" cy="470" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:1.3s" cx="420" cy="510" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:2.6s" cx="560" cy="440" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:3.9000000000000004s" cx="1080" cy="460" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:5.2s" cx="1220" cy="500" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:6.5s" cx="1340" cy="450" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:7.800000000000001s" cx="700" cy="520" r="5" fill="#FFE9A8"/>
            <circle class="kunang" style="animation-delay:9.1s" cx="980" cy="430" r="5" fill="#FFE9A8"/>
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

// Ubah jam (0-23) menjadi fase tema
const faseDariJam = (jam) => {
  if (jam >= 5 && jam < 11) return "pagi";
  if (jam >= 11 && jam < 15) return "siang";
  if (jam >= 15 && jam < 18) return "sore";
  return "malam";
};

// Apakah perangkat pengguna disetel mode gelap?
const perangkatModeGelap = () =>
  typeof window !== "undefined" &&
  typeof window.matchMedia === "function" &&
  window.matchMedia("(prefers-color-scheme: dark)").matches;

// Tentukan fase awal dari jam perangkat (dipakai sementara sebelum
// waktu server tiba, supaya tampilan tidak berkedip).
const tentukanWaktu = () => {
  if (perangkatModeGelap()) return "malam";
  return faseDariJam(new Date().getHours());
};

const tema = ref(presetWaktu[tentukanWaktu()]);

// Ambil jam dari SERVER (WIB) supaya tidak bisa dicurangi dengan
// mengubah jam perangkat. Kalau gagal, tetap pakai jam perangkat.
const selaraskanWaktuServer = async () => {
  // Preferensi mode gelap pengguna diutamakan.
  if (perangkatModeGelap()) return;
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/waktu`);
    const jam = res.data?.jam;
    if (typeof jam === "number" && jam >= 0 && jam <= 23) {
      tema.value = presetWaktu[faseDariJam(jam)];
    }
  } catch (e) {
    /* biarkan pakai jam perangkat */
  }
};
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
  selaraskanWaktuServer();
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
    // Hosting shared kadang lambat "bangun" (cold start), jadi percobaan
    // pertama bisa timeout. Kita coba beberapa kali dengan timeout lega
    // sebelum menyerah — supaya blip koneksi sesaat tidak langsung gagal.
    const kirimLogin = () =>
      axios.post(
        `${import.meta.env.VITE_API_URL}/api/login`,
        { username: username.value, password: password.value },
        { timeout: 15000 },
      );
    let response;
    let percobaan = 0;
    while (true) {
      percobaan++;
      try {
        response = await kirimLogin();
        break;
      } catch (err) {
        const gangguanJaringan =
          err.code === "ECONNABORTED" ||
          err.message === "Network Error" ||
          !err.response;
        if (gangguanJaringan && percobaan < 3) {
          await new Promise((r) => setTimeout(r, 800 * percobaan));
          continue; // coba lagi
        }
        throw err; // password salah / gagal beneran → lempar ke catch
      }
    }

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
        title: "Koneksi ke Server Gagal",
        text: "Tidak dapat terhubung ke server. Periksa koneksi internet Anda, lalu coba lagi. Bila masalah berlanjut, hubungi pengelola sistem.",
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

/* ===== PEMANDANGAN DESA (gunung, tanah, bangunan, pohon) ===== */
.siluet-desa .gn-jauh { fill: #8fb4d6; }
.siluet-desa .gn-dekat { fill: #a4c3e0; }
.siluet-desa .gn-puncak { fill: #cfe0f0; }
.siluet-desa .tanah { fill: #cfe3c6; }
.siluet-desa .galur { stroke: #c3d9b8; stroke-width: 5; fill: none; }
.siluet-desa .utama { fill: #1e2a3a; }
.siluet-desa .dalam { fill: #46586e; }
.siluet-desa .putih { fill: #ffffff; }
.siluet-desa .btg { fill: #6b4f3a; }
.siluet-desa .dn1 { fill: #3f7d4e; }
.siluet-desa .dn2 { fill: #46875a; }
.siluet-desa .dn3 { fill: #4f9160; }
.siluet-desa .tiang { fill: #334155; }

/* malam: langit gelap, lampu menyala */
.siluet-desa.malam .gn-jauh { fill: #22304a; }
.siluet-desa.malam .gn-dekat { fill: #2b3a58; }
.siluet-desa.malam .gn-puncak { fill: #3a4a6b; }
.siluet-desa.malam .tanah { fill: #1b2942; }
.siluet-desa.malam .galur { stroke: #22314c; }
.siluet-desa.malam .utama { fill: #0e1726; }
.siluet-desa.malam .dalam { fill: #263449; }
.siluet-desa.malam .putih { fill: #ffd98a; animation: napasLampu 5s ease-in-out infinite; }
.siluet-desa.malam .dn1,
.siluet-desa.malam .dn2,
.siluet-desa.malam .dn3 { fill: #1b2f2a; }
.siluet-desa.malam .btg { fill: #1d2436; }
.siluet-desa.malam .tiang { fill: #16203a; }

.pohon-desa { transform-box: fill-box; transform-origin: bottom center; animation: tiupAngin 6s ease-in-out infinite; }
.bendera-desa { transform-box: fill-box; transform-origin: left center; animation: kibarBendera 2.6s ease-in-out infinite; }
.kunang { animation: kunangTerbang 6s ease-in-out infinite; }

@keyframes tiupAngin { 0%,100% { transform: rotate(-1.2deg); } 50% { transform: rotate(1.2deg); } }
@keyframes kibarBendera { 0%,100% { transform: skewY(0deg) scaleY(1); } 50% { transform: skewY(-5deg) scaleY(0.93); } }
@keyframes napasLampu { 0%,100% { opacity: 1; } 50% { opacity: 0.72; } }
@keyframes kunangTerbang {
  0%,100% { opacity: 0; transform: translate(0,0); }
  25% { opacity: 1; }
  55% { opacity: 0.85; transform: translate(22px,-34px); }
  85% { opacity: 0.15; transform: translate(40px,-14px); }
}
@media (prefers-reduced-motion: reduce) {
  .pohon-desa, .bendera-desa, .kunang, .siluet-desa.malam .putih { animation: none; }
}
</style>
