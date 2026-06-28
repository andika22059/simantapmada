<template>
  <Topbar />
  <div class="dev-body">
    <div class="dev-wrapper">
      <!-- HEADER NAVY DOTTED -->
      <div class="page-header-box fade-up">
        <div class="header-text">
          <h2><i class="fa-solid fa-terminal"></i> Pemantauan Sistem</h2>
          <p>
            {{ sapaan }}, <strong>{{ namaUser }}</strong
            >. Pantau keseluruhan data dan aktivitas sistem.
          </p>
        </div>
        <span class="badge-mode">
          <i class="fa-solid fa-shield-halved"></i> Akses Pengembang
        </span>
      </div>

      <!-- STATISTIK MODUL UTAMA -->
      <div class="section-title fade-up delay-1">
        <i class="fa-solid fa-database"></i> Ringkasan Data ({{ tahun }})
      </div>
      <div class="stat-grid fade-up delay-1">
        <div class="stat-box">
          <div class="sb-icon icon-emerald">
            <i class="fa-solid fa-inbox"></i>
          </div>
          <div class="sb-info">
            <h3>{{ data.surat_masuk }}</h3>
            <span>Surat Masuk</span>
          </div>
        </div>
        <div class="stat-box">
          <div class="sb-icon icon-teal">
            <i class="fa-solid fa-paper-plane"></i>
          </div>
          <div class="sb-info">
            <h3>{{ data.surat_keluar }}</h3>
            <span>Surat Keluar</span>
          </div>
        </div>
        <div class="stat-box">
          <div class="sb-icon icon-navy">
            <i class="fa-solid fa-box-archive"></i>
          </div>
          <div class="sb-info">
            <h3>{{ data.arsip }}</h3>
            <span>Arsip Digital</span>
          </div>
        </div>
        <div class="stat-box">
          <div class="sb-icon icon-amber">
            <i class="fa-solid fa-warehouse"></i>
          </div>
          <div class="sb-info">
            <h3>{{ data.aset }}</h3>
            <span>Inventaris Aset</span>
          </div>
        </div>
      </div>

      <!-- STATUS USER SISTEM (per role) -->
      <div class="section-title fade-up delay-1">
        <i class="fa-solid fa-users-gear"></i> Status Akun Pengguna (Total:
        {{ dataUser.total }})
      </div>
      <div class="user-grid fade-up delay-1">
        <div class="user-box ub-admin">
          <div class="ub-icon"><i class="fa-solid fa-user-gear"></i></div>
          <h3>{{ dataUser.admin }}</h3>
          <span>Admin</span>
        </div>
        <div class="user-box ub-sekdes">
          <div class="ub-icon"><i class="fa-solid fa-user-pen"></i></div>
          <h3>{{ dataUser.sekdes }}</h3>
          <span>Sekretaris Desa</span>
        </div>
        <div class="user-box ub-kades">
          <div class="ub-icon"><i class="fa-solid fa-user-tie"></i></div>
          <h3>{{ dataUser.kades }}</h3>
          <span>Kepala Desa</span>
        </div>
        <div class="user-box ub-warga">
          <div class="ub-icon"><i class="fa-solid fa-users"></i></div>
          <h3>{{ dataUser.warga }}</h3>
          <span>Warga</span>
        </div>
        <div class="user-box ub-dev">
          <div class="ub-icon"><i class="fa-solid fa-code"></i></div>
          <h3>{{ dataUser.developer }}</h3>
          <span>Developer</span>
        </div>
      </div>

      <!-- ALUR PENGAJUAN & DISPOSISI -->
      <div class="dual-grid fade-up delay-2">
        <!-- Status Pengajuan -->
        <div class="panel">
          <div class="panel-head">
            <h3>
              <i class="fa-solid fa-file-lines icon-emerald-text"></i> Status
              Pengajuan Surat
            </h3>
          </div>
          <div class="panel-body">
            <div class="flow-item">
              <span class="flow-dot dot-amber"></span>
              <span class="flow-label">Diajukan</span>
              <span class="flow-val">{{ alur.menunggu_verifikasi }}</span>
            </div>
            <div class="flow-item">
              <span class="flow-dot dot-blue"></span>
              <span class="flow-label">Diverifikasi (menunggu ACC)</span>
              <span class="flow-val">{{ alur.sudah_diverifikasi }}</span>
            </div>
            <div class="flow-item">
              <span class="flow-dot dot-red"></span>
              <span class="flow-label">Dikembalikan</span>
              <span class="flow-val">{{ alur.dikembalikan }}</span>
            </div>
            <div class="flow-item">
              <span class="flow-dot dot-green"></span>
              <span class="flow-label">Selesai</span>
              <span class="flow-val">{{ alur.selesai }}</span>
            </div>
          </div>
        </div>

        <!-- Status Disposisi -->
        <div class="panel">
          <div class="panel-head">
            <h3>
              <i class="fa-solid fa-share-from-square icon-teal-text"></i>
              Status Disposisi Surat
            </h3>
          </div>
          <div class="panel-body">
            <div class="flow-item">
              <span class="flow-dot dot-amber"></span>
              <span class="flow-label">Belum Didisposisi</span>
              <span class="flow-val">{{ alur.menunggu_disposisi }}</span>
            </div>
            <div class="flow-item">
              <span class="flow-dot dot-teal"></span>
              <span class="flow-label">Sudah Didisposisi</span>
              <span class="flow-val">{{ alur.sudah_disposisi }}</span>
            </div>
            <div class="flow-divider"></div>
            <div class="flow-note">
              <i class="fa-solid fa-circle-info"></i>
              Data alur diambil dari statistik Sekretaris Desa.
            </div>
          </div>
        </div>
      </div>

      <!-- LOG AKTIVITAS -->
      <div class="panel fade-up delay-3" style="margin-top: 24px">
        <div class="panel-head">
          <h3>
            <i class="fa-solid fa-clock-rotate-left icon-navy-text"></i> Riwayat
            Aktivitas Sistem
          </h3>
          <button class="btn-refresh" @click="fetchSemua">
            <i class="fa-solid fa-rotate"></i> Muat Ulang
          </button>
        </div>
        <div class="panel-body">
          <div v-if="loadingLog" class="log-empty">
            <i class="fa-solid fa-spinner fa-spin"></i> Memuat log...
          </div>
          <div v-else-if="logAktivitas.length === 0" class="log-empty">
            <i class="fa-solid fa-inbox"></i> Belum ada aktivitas tercatat.
          </div>
          <ul v-else class="log-list">
            <li v-for="log in logAktivitas" :key="log.id" class="log-item">
              <div class="log-icon">
                <i class="fa-solid fa-circle-dot"></i>
              </div>
              <div class="log-content">
                <strong>{{ log.modul || "Sistem" }}</strong>
                <p>{{ log.deskripsi }}</p>
                <span class="log-time">{{ formatWaktu(log.created_at) }}</span>
              </div>
              <span v-if="log.nomor" class="log-nomor">{{ log.nomor }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

const BASE = `${import.meta.env.VITE_API_URL}/api`;

const userData = ref(null);
const namaUser = computed(() => userData.value?.nama_lengkap || "Developer");
const tahun = new Date().getFullYear();
const sapaan = computed(() => {
  const jam = new Date().getHours();
  if (jam >= 5 && jam < 11) return "Selamat pagi";
  if (jam >= 11 && jam < 15) return "Selamat siang";
  if (jam >= 15 && jam < 18) return "Selamat sore";
  return "Selamat malam";
});

// Data ringkasan modul (dari statistik admin)
const data = ref({
  surat_masuk: 0,
  surat_keluar: 0,
  arsip: 0,
  aset: 0,
});

// Data alur (dari statistik sekdes)
const alur = ref({
  menunggu_verifikasi: 0,
  sudah_diverifikasi: 0,
  dikembalikan: 0,
  selesai: 0,
  menunggu_disposisi: 0,
  sudah_disposisi: 0,
});

const logAktivitas = ref([]);
const loadingLog = ref(true);

// Data user per role (statistik developer)
const dataUser = ref({
  total: 0,
  admin: 0,
  sekdes: 0,
  kades: 0,
  warga: 0,
  developer: 0,
});

const fetchStatistikAdmin = async () => {
  try {
    const res = await axios.get(`${BASE}/dashboard/statistik`);
    const d = res.data?.data || {};
    data.value = {
      surat_masuk: d.surat_masuk ?? 0,
      surat_keluar: d.surat_keluar ?? 0,
      arsip: d.arsip ?? 0,
      aset: d.aset ?? 0,
    };
  } catch (e) {
    console.error("Gagal statistik admin:", e);
  }
};

const fetchStatistikSekdes = async () => {
  try {
    const res = await axios.get(`${BASE}/dashboard/statistik-sekdes`);
    if (res.data?.data) alur.value = { ...alur.value, ...res.data.data };
  } catch (e) {
    console.error("Gagal statistik sekdes:", e);
  }
};

const fetchLog = async () => {
  loadingLog.value = true;
  try {
    const res = await axios.get(`${BASE}/dashboard/aktivitas`);
    logAktivitas.value = res.data?.data || [];
  } catch (e) {
    console.error("Gagal log:", e);
    logAktivitas.value = [];
  } finally {
    loadingLog.value = false;
  }
};

const fetchStatistikUser = async () => {
  try {
    const res = await axios.get(`${BASE}/dashboard/statistik-user`);
    if (res.data?.data)
      dataUser.value = { ...dataUser.value, ...res.data.data };
  } catch (e) {
    console.error("Gagal statistik user:", e);
  }
};

const fetchSemua = () => {
  fetchStatistikAdmin();
  fetchStatistikSekdes();
  fetchStatistikUser();
  fetchLog();
};

const formatWaktu = (iso) => {
  if (!iso) return "-";
  return new Date(iso).toLocaleString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

onMounted(() => {
  const raw = localStorage.getItem("user");
  if (raw) {
    try {
      userData.value = JSON.parse(raw);
    } catch (e) {
      userData.value = null;
    }
  }
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  fetchSemua();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.dev-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.dev-wrapper {
  max-width: 1150px;
  margin: 0 auto;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-up {
  animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.delay-1 {
  animation-delay: 0.1s;
  opacity: 0;
}
.delay-2 {
  animation-delay: 0.2s;
  opacity: 0;
}
.delay-3 {
  animation-delay: 0.3s;
  opacity: 0;
}

/* HEADER */
.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 28px;
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  padding: 28px 35px;
  border-radius: 20px;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
}
.header-text h2 {
  margin: 0 0 5px 0;
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 12px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  margin: 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}
.header-text p strong {
  color: #e2e8f0;
}
.badge-mode {
  background: rgba(52, 211, 153, 0.15);
  color: #34d399;
  border: 1px solid rgba(52, 211, 153, 0.3);
  padding: 8px 16px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

/* SECTION TITLE */
.section-title {
  font-size: 14px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.section-title i {
  color: var(--primary, #059669);
}

/* STAT GRID */
.user-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}
.user-box {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 16px;
  padding: 18px 14px;
  text-align: center;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
  position: relative;
  overflow: hidden;
}
.user-box:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
}
.user-box .ub-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 10px;
  font-size: 18px;
  color: #fff;
}
.user-box h3 {
  margin: 0;
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
}
.user-box span {
  font-size: 12px;
  color: #64748b;
  font-weight: 600;
}
.ub-admin .ub-icon {
  background: linear-gradient(135deg, #10b981, #059669);
}
.ub-sekdes .ub-icon {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
}
.ub-kades .ub-icon {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}
.ub-warga .ub-icon {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}
.ub-dev .ub-icon {
  background: linear-gradient(135deg, #0f172a, #1e293b);
}

.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  margin-bottom: 28px;
}
.stat-box {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: 0.3s;
}
.stat-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 25px rgba(15, 23, 42, 0.07);
}
.sb-icon {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 21px;
  flex-shrink: 0;
}
.icon-emerald {
  background: #ecfdf5;
  color: #059669;
}
.icon-teal {
  background: #f0fdfa;
  color: #0f766e;
}
.icon-navy {
  background: #f1f5f9;
  color: #1e293b;
}
.icon-amber {
  background: #fffbeb;
  color: #d97706;
}
.sb-info h3 {
  margin: 0;
  font-size: 26px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  line-height: 1;
}
.sb-info span {
  font-size: 12px;
  color: var(--text-muted, #64748b);
  font-weight: 600;
}

/* DUAL GRID */
.dual-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

/* PANEL */
.panel {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 18px;
  overflow: hidden;
}
.panel-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 24px;
  border-bottom: 1px solid #f1f5f9;
}
.panel-head h3 {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  color: var(--navy, #0f172a);
  display: flex;
  align-items: center;
  gap: 10px;
}
.icon-emerald-text {
  color: #059669;
}
.icon-teal-text {
  color: #0f766e;
}
.icon-navy-text {
  color: #1e293b;
}
.panel-body {
  padding: 20px 24px;
}

/* FLOW ITEM */
.flow-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f8fafc;
}
.flow-item:last-child {
  border-bottom: none;
}
.flow-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex-shrink: 0;
}
.dot-amber {
  background: #f59e0b;
}
.dot-blue {
  background: #3b82f6;
}
.dot-red {
  background: #ef4444;
}
.dot-green {
  background: #10b981;
}
.dot-teal {
  background: #14b8a6;
}
.flow-label {
  flex: 1;
  font-size: 14px;
  color: var(--text-body, #334155);
  font-weight: 600;
}
.flow-val {
  font-size: 18px;
  font-weight: 800;
  color: var(--navy, #0f172a);
}
.flow-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 8px 0;
}
.flow-note {
  font-size: 12px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
}

/* LOG */
.btn-refresh {
  background: var(--primary-soft, #ecfdf5);
  color: var(--primary-dark, #047857);
  border: none;
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  transition: 0.2s;
  font-family: inherit;
}
.btn-refresh:hover {
  background: var(--primary, #059669);
  color: #fff;
}
.log-empty {
  text-align: center;
  padding: 30px;
  color: #94a3b8;
  font-weight: 500;
}
.log-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.log-item {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  padding: 14px;
  border-radius: 12px;
  transition: background 0.2s;
}
.log-item:hover {
  background: #f8fafc;
}
.log-icon {
  color: var(--primary, #059669);
  font-size: 11px;
  margin-top: 5px;
}
.log-content {
  flex: 1;
}
.log-content strong {
  font-size: 13px;
  color: var(--primary-dark, #047857);
  display: block;
  margin-bottom: 2px;
}
.log-content p {
  margin: 0;
  font-size: 14px;
  color: var(--text-body, #334155);
  line-height: 1.4;
}
.log-time {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 600;
}
.log-nomor {
  font-family: "Courier New", monospace;
  font-size: 11px;
  font-weight: 700;
  color: #047857;
  background: #ecfdf5;
  padding: 3px 8px;
  border-radius: 6px;
  white-space: nowrap;
  height: fit-content;
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .header-actions {
    flex-wrap: wrap;
    gap: 10px;
  }
}

@media (max-width: 900px) {
  .stat-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .user-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .dual-grid {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 480px) {
  .dev-body {
    padding: 90px 14px 30px;
  }
  .header-text h2 {
    font-size: 20px;
  }
  .stat-grid {
    grid-template-columns: 1fr;
  }
  .user-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px;
  }
}
</style>
