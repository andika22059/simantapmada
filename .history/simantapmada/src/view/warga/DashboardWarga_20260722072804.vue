<template>
  <div class="warga-body">
    <!-- NAV -->
    <div class="warga-nav">
      <a class="warga-brand">
        <img src="../../assets/img/logoSiMANTAPMADA.png" alt="logo" class="brand-logo" onerror="this.style.display='none'" />
        <span>SiMANTAP<b>MADA</b></span>
      </a>
      <div class="nav-right">
        <div class="nav-user">
          <div class="nav-ava"><i class="fa-solid fa-user"></i></div>
          <span>{{ namaLengkap }}</span>
        </div>
        <button class="nav-logout" title="Keluar" @click="logout">
          <i class="fa-solid fa-right-from-bracket"></i>
        </button>
      </div>
    </div>

    <div class="container-warga">
      <!-- HERO -->
      <div class="hero-warga fade-up">
        <div class="hero-text">
          <h1>{{ sapaan }}, {{ namaPanggilan }}!</h1>
          <p>
            Selamat datang di layanan administrasi digital Desa Macanan. Ajukan
            surat dengan mudah dan pantau prosesnya.
          </p>
          <div class="hero-date">
            <i class="fa-regular fa-calendar-days"></i> {{ tanggalHariIni }}
          </div>
        </div>
        <i class="fa-solid fa-fingerprint hero-bg"></i>
      </div>

      <!-- CTA -->
      <div class="cta-card fade-up delay-1" @click="infoPengajuan">
        <div class="cta-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
        <div class="cta-text">
          <h3>Ajukan Surat Baru</h3>
          <p>Lihat cara mengurus surat administrasi desa.</p>
        </div>
        <i class="fa-solid fa-chevron-right cta-arrow"></i>
      </div>

      <!-- LAYANAN TERSEDIA -->
      <h4 class="section-lbl">
        <i class="fa-solid fa-list-check"></i> Layanan Surat Tersedia
      </h4>
      <div class="layanan-grid fade-up delay-2">
        <div
          v-for="l in layanan"
          :key="l.judul"
          class="layanan-card"
          @click="infoPengajuan"
        >
          <div class="lc-icon" :style="{ background: l.bg, color: l.warna }">
            <i class="fa-solid" :class="l.ikon"></i>
          </div>
          <div class="lc-body">
            <strong>{{ l.judul }}</strong>
            <span>{{ l.ket }}</span>
          </div>
        </div>
      </div>

      <!-- ALUR PENGAJUAN -->
      <h4 class="section-lbl">
        <i class="fa-solid fa-route"></i> Alur Pengurusan Surat
      </h4>
      <div class="alur-grid fade-up delay-3">
        <div v-for="(a, i) in alur" :key="i" class="alur-step">
          <div class="alur-num">{{ i + 1 }}</div>
          <div class="alur-body">
            <strong>{{ a.judul }}</strong>
            <span>{{ a.ket }}</span>
          </div>
        </div>
      </div>

      <!-- INFO DESA -->
      <div class="info-desa fade-up delay-3">
        <div class="id-icon"><i class="fa-solid fa-building-columns"></i></div>
        <div class="id-text">
          <strong>Kantor Desa Macanan</strong>
          <span
            >Kecamatan Loceret, Kabupaten Nganjuk · Jam pelayanan 08.00–15.00
            WIB</span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

// Data pengguna dari sesi login (asli, bukan dummy)
const userData = ref(null);
try {
  userData.value = JSON.parse(localStorage.getItem("user") || "null");
} catch (e) {
  userData.value = null;
}
const namaLengkap = computed(() => userData.value?.nama_lengkap || "Warga");
const namaPanggilan = computed(() => namaLengkap.value.split(" ")[0]);

const sapaan = computed(() => {
  const j = new Date().getHours();
  if (j < 11) return "Selamat pagi";
  if (j < 15) return "Selamat siang";
  if (j < 18) return "Selamat sore";
  return "Selamat malam";
});

const tanggalHariIni = computed(() =>
  new Date().toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  }),
);

// Daftar layanan surat yang tersedia (informasi nyata)
const layanan = [
  { judul: "Keterangan Domisili", ket: "Bukti tempat tinggal", ikon: "fa-house-user", bg: "#ecfdf5", warna: "#059669" },
  { judul: "Surat Keterangan Usaha", ket: "Untuk pelaku usaha (SKU)", ikon: "fa-store", bg: "#eff6ff", warna: "#2563eb" },
  { judul: "Keterangan Tidak Mampu", ket: "SKTM untuk bantuan/beasiswa", ikon: "fa-hand-holding-heart", bg: "#fef2f2", warna: "#dc2626" },
  { judul: "Pengantar Nikah", ket: "Persyaratan menikah (N1-N4)", ikon: "fa-ring", bg: "#fdf4ff", warna: "#a21caf" },
  { judul: "Keterangan Kelahiran", ket: "Bukti kelahiran anak", ikon: "fa-baby", bg: "#fff7ed", warna: "#ea580c" },
  { judul: "Surat Pindah", ket: "Perpindahan penduduk", ikon: "fa-truck-arrow-right", bg: "#f0fdfa", warna: "#0d9488" },
];

// Alur pengurusan surat
const alur = [
  { judul: "Datang ke Kantor Desa", ket: "Bawa dokumen persyaratan (KTP, KK, dll)." },
  { judul: "Pengajuan Diinput", ket: "Petugas memasukkan pengajuan Anda ke sistem." },
  { judul: "Verifikasi & Persetujuan", ket: "Diperiksa Sekretaris, lalu disetujui Kepala Desa." },
  { judul: "Surat Selesai", ket: "Surat resmi bernomor siap diambil/dicetak." },
];

const infoPengajuan = () => {
  Swal.fire({
    icon: "info",
    title: "Cara Mengajukan Surat",
    html: `Untuk mengurus surat, silakan <b>datang ke Kantor Desa Macanan</b>
      pada jam pelayanan (08.00–15.00 WIB) dengan membawa dokumen persyaratan.
      Petugas akan membantu memproses pengajuan Anda.`,
    confirmButtonColor: "#059669",
    confirmButtonText: "Mengerti",
  });
};

const logout = async () => {
  const konf = await Swal.fire({
    icon: "question",
    title: "Keluar dari akun?",
    showCancelButton: true,
    confirmButtonText: "Ya, Keluar",
    cancelButtonText: "Batal",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#64748b",
  });
  if (!konf.isConfirmed) return;
  try {
    await axios.post(`${import.meta.env.VITE_API_URL}/api/logout`);
  } catch (e) {
    /* tetap lanjut walau gagal */
  }
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  sessionStorage.removeItem("sesi_aktif");
  delete axios.defaults.headers.common["Authorization"];
  router.push({ path: "/login", query: { pesan: "logout" } });
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.warga-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: #f1f5f9;
  min-height: 100vh;
  color: #0f172a;
  padding-bottom: 40px;
}

/* NAV */
.warga-nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 22px;
}
.warga-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 800;
  font-size: 18px;
  color: #0f172a;
}
.warga-brand b {
  color: #059669;
}
.brand-logo {
  height: 32px;
}
.nav-right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.nav-user {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 14px;
  color: #334155;
}
.nav-ava {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #ecfdf5;
  color: #059669;
  display: flex;
  align-items: center;
  justify-content: center;
}
.nav-logout {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 1px solid #fee2e2;
  background: #fef2f2;
  color: #ef4444;
  cursor: pointer;
  font-size: 16px;
  transition: 0.2s;
}
.nav-logout:hover {
  background: #ef4444;
  color: #fff;
}

.container-warga {
  max-width: 720px;
  margin: 0 auto;
  padding: 22px 16px;
}
.fade-up {
  animation: fadeUp 0.5s ease both;
}
.delay-1 {
  animation-delay: 0.08s;
}
.delay-2 {
  animation-delay: 0.16s;
}
.delay-3 {
  animation-delay: 0.24s;
}
@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* HERO */
.hero-warga {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  border-radius: 20px;
  padding: 28px 26px;
  margin-bottom: 18px;
  box-shadow: 0 12px 30px rgba(5, 150, 105, 0.25);
}
.hero-warga h1 {
  margin: 0 0 6px;
  font-size: 24px;
  font-weight: 800;
}
.hero-warga p {
  margin: 0 0 14px;
  font-size: 14px;
  opacity: 0.95;
  max-width: 440px;
  line-height: 1.6;
}
.hero-date {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.18);
  padding: 8px 14px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
}
.hero-bg {
  position: absolute;
  right: -10px;
  bottom: -20px;
  font-size: 130px;
  opacity: 0.12;
}

/* CTA */
.cta-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 18px 20px;
  cursor: pointer;
  transition: 0.25s;
  margin-bottom: 26px;
}
.cta-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 25px rgba(15, 23, 42, 0.07);
  border-color: #a7f3d0;
}
.cta-icon {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.cta-text {
  flex: 1;
}
.cta-text h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 800;
}
.cta-text p {
  margin: 4px 0 0;
  font-size: 12px;
  color: #64748b;
}
.cta-arrow {
  color: #cbd5e1;
}

/* SECTION LABEL */
.section-lbl {
  margin: 0 0 14px 4px;
  font-size: 13px;
  font-weight: 800;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.section-lbl i {
  color: #059669;
}

/* LAYANAN GRID */
.layanan-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 30px;
}
.layanan-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 14px;
  cursor: pointer;
  transition: 0.2s;
}
.layanan-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
}
.lc-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  flex-shrink: 0;
}
.lc-body {
  min-width: 0;
}
.lc-body strong {
  display: block;
  font-size: 14px;
  color: #0f172a;
  line-height: 1.3;
}
.lc-body span {
  font-size: 11px;
  color: #94a3b8;
}

/* ALUR */
.alur-grid {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 26px;
}
.alur-step {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 14px 16px;
}
.alur-num {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #ecfdf5;
  color: #059669;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.alur-body strong {
  display: block;
  font-size: 14px;
  color: #0f172a;
}
.alur-body span {
  font-size: 12px;
  color: #64748b;
}

/* INFO DESA */
.info-desa {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #0f172a;
  border-radius: 16px;
  padding: 18px 20px;
  color: #fff;
}
.id-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(52, 211, 153, 0.15);
  color: #34d399;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.id-text strong {
  display: block;
  font-size: 15px;
}
.id-text span {
  font-size: 12px;
  color: #94a3b8;
}

@media (max-width: 480px) {
  .hero-warga h1 {
    font-size: 20px;
  }
  .layanan-grid {
    grid-template-columns: 1fr;
  }
  .nav-user span {
    display: none;
  }
}
</style>
