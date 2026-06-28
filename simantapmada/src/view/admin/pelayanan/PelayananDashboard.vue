<template>
  <div class="vue-dashboard-body">
    <div class="container">
      <div class="page-header">
        <div class="header-title">
          <h1>
            <i
              class="fa-solid fa-envelope-open-text"
              style="color: #10b981"
            ></i>
            Pelayanan Masyarakat
          </h1>
          <p>
            Halo, <strong>Admin / Pegawai</strong>. Pilih jenis surat
            pengajuan untuk memulai proses.
          </p>
        </div>

        <div class="header-buttons-wrapper">
          <button
            @click="router.push('/admin/pelayanan/list')"
            class="btn-list-pelayanan"
            type="button"
          >
            <i class="fa-solid fa-table-list"></i> List Pengajuan
          </button>

          <button @click="router.push('/admin')" class="btn-back" type="button">
            <i class="fa-solid fa-house"></i> Menu Utama
          </button>
        </div>
      </div>

      <div class="search-card">
        <h3>Tersedia {{ daftarLayanan.length }} Jenis Layanan</h3>
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Cari layanan (Misal: Domisili, SKCK)..."
          />
        </div>
      </div>

      <div class="scrollable-layanan-box">
        <div class="grid-layanan" id="layananGrid">
          <div
            v-for="(item, index) in filteredLayanan"
            :key="item.slug"
            class="service-item"
            @click="goToForm(item.slug)"
          >
            <div class="service-icon">{{ item.icon }}</div>
            <div class="service-text">
              <h3>{{ item.t }}</h3>
              <p>{{ item.d }}</p>
            </div>
          </div>

          <div
            v-if="filteredLayanan.length === 0"
            style="
              grid-column: 1 / -1;
              text-align: center;
              padding: 40px;
              color: #64748b;
            "
          >
            <i
              class="fa-solid fa-folder-open"
              style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px"
            ></i>
            <p>Layanan surat tidak ditemukan.</p>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="recent-log-container">
        <h3>
          <i class="fa-solid fa-clock-rotate-left"></i> Pengajuan Terakhir Anda
        </h3>
        <div class="log-wrapper">
          <div
            v-if="isLoadingLog"
            style="text-align: center; color: #64748b; padding: 20px"
          >
            <i class="fa-solid fa-spinner fa-spin"></i> Memuat data...
          </div>

          <div
            v-else-if="logPermohonan.length === 0"
            style="text-align: center; color: #64748b; padding: 20px"
          >
            Belum ada riwayat pengajuan surat.
          </div>

          <div
            v-else
            v-for="(log, index) in logPermohonan"
            :key="index"
            class="log-item"
          >
            <div class="log-left">
              <div class="log-icon">
                <i class="fa-solid fa-file-lines"></i>
              </div>
              <div class="log-text">
                <h4
                  style="
                    margin: 0;
                    font-size: 15px;
                    font-weight: 600;
                    color: #0f172a;
                  "
                >
                  {{ log.jenis }}
                </h4>
                <span>Diajukan: {{ log.tgl }}</span>
              </div>
            </div>
            <div class="log-right">
              <span :class="['log-status', log.status.toLowerCase()]">{{
                log.status
              }}</span>
              <button @click="openModal(log)" class="btn-lacak" type="button">
                Lacak <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal-overlay-simple"
      :class="{ active: isModalOpen }"
      @click.self="closeModal"
    >
      <div class="modal-box-simple">
        <div class="modal-header-simple">
          <div>
            <h3 style="margin: 0; font-size: 18px; font-weight: 700">
              Pelacakan Surat
            </h3>
            <p
              style="
                margin: 3px 0 0;
                font-size: 13px;
                color: #64748b;
                font-weight: 400;
              "
            >
              ID Pengajuan: #<span style="font-weight: 700; color: #10b981">{{
                selectedPermohonan.id
              }}</span>
            </p>
          </div>
          <button @click="closeModal" class="close-btn-simple" type="button">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-body-grid">
          <div class="modal-left">
            <div class="info-group">
              <label>Tanggal Pengajuan</label>
              <p>{{ selectedPermohonan.tgl }}</p>
            </div>
            <div class="info-group">
              <label>Jenis Surat</label>
              <p>{{ selectedPermohonan.jenis }}</p>
            </div>
            <div class="info-group">
              <label>Status Terkini</label>
              <p>
                <span
                  :class="[
                    'status-badge',
                    selectedPermohonan.status?.toLowerCase(),
                  ]"
                  >{{ selectedPermohonan.status }}</span
                >
              </p>
            </div>
          </div>

          <div class="modal-right">
            <div class="timeline-container">
              <div class="timeline-step active">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                  <h5>Pengajuan Masuk</h5>
                  <p>Data telah diterima sistem.</p>
                </div>
              </div>

              <div class="timeline-step" :class="{ active: isStep2Active }">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                  <h5>Diproses Desa</h5>
                  <p>Sedang diverifikasi petugas.</p>
                </div>
              </div>

              <div class="timeline-step" :class="{ active: isStep3Active }">
                <div class="timeline-dot" :style="step3DotStyle"></div>
                <div class="timeline-content">
                  <h5 :style="step3TitleStyle">{{ step3Title }}</h5>
                  <p>{{ step3Desc }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const searchQuery = ref("");
const isModalOpen = ref(false);
const selectedPermohonan = ref({});

// Base URL backend Laravel
const API_BASE_URL = `${import.meta.env.VITE_API_URL}`;

const logPermohonan = ref([]);
const isLoadingLog = ref(true);

// 35 LIST LAYANAN MASTER
const daftarLayanan = ref([
  {
    icon: "🏠",
    slug: "domisili",
    t: "Ket. Domisili",
    d: "Keterangan tempat tinggal sementara.",
  },
  {
    icon: "👶",
    slug: "kelahiran",
    t: "Ket. Kelahiran",
    d: "Pengantar pembuatan Akta Lahir.",
  },
  {
    icon: "🕊️",
    slug: "kematian",
    t: "Ket. Kematian",
    d: "Pengantar pembuatan Akta Mati.",
  },
  {
    icon: "🚚",
    slug: "pindah",
    t: "Surat Pindah",
    d: "Pengantar pindah ke luar daerah.",
  },
  {
    icon: "📝",
    slug: "beda_nama",
    t: "Beda Identitas",
    d: "Perbaikan kesalahan data KTP.",
  },
  {
    icon: "🪪",
    slug: "skip",
    t: "Ket. Identitas (SKIP)",
    d: "Pengganti sementara KTP/identitas diri.",
  },
  {
    icon: "💍",
    slug: "nikah",
    t: "Pengantar Nikah",
    d: "Formulir N1, N2, N4 (KUA).",
  },
  {
    icon: "💌",
    slug: "numpang_nikah",
    t: "Numpang Nikah",
    d: "Pengantar nikah di luar domisili asli.",
  },
  {
    icon: "👤",
    slug: "belum_menikah",
    t: "Belum Menikah",
    d: "Status lajang untuk kerja/TNI.",
  },
  {
    icon: "🤲",
    slug: "nikah_siri",
    t: "Ket. Nikah Siri",
    d: "Pernyataan nikah secara agama (siri).",
  },
  {
    icon: "💔",
    slug: "janda_duda",
    t: "Janda / Duda",
    d: "Status pasca perceraian/wafat.",
  },
  {
    icon: "⚖️",
    slug: "cerai_goib",
    t: "Ket. Cerai Goib",
    d: "Cerai tanpa diketahui keberadaan suami.",
  },
  {
    icon: "🤝",
    slug: "wali",
    t: "Keterangan Wali",
    d: "Untuk wali nikah atau hak asuh.",
  },
  {
    icon: "📜",
    slug: "ahli_waris",
    t: "Ahli Waris",
    d: "Susunan ahli waris yang sah.",
  },
  {
    icon: "👫",
    slug: "ket_menikah",
    t: "Keterangan Menikah",
    d: "Keterangan Status Pernikahan",
  },
  {
    icon: "🏪",
    slug: "usaha",
    t: "Ket. Usaha (SKU)",
    d: "Izin usaha mikro & kredit bank.",
  },
  {
    icon: "📉",
    slug: "sktm",
    t: "Tidak Mampu",
    d: "Syarat Bansos, KIP, BPJS.",
  },
  {
    icon: "💰",
    slug: "penghasilan",
    t: "Ket. Penghasilan",
    d: "Slip gaji sektor non-formal.",
  },
  {
    icon: "👷",
    slug: "bekerja",
    t: "Keterangan Bekerja",
    d: "Bukti sedang aktif bekerja di instansi.",
  },
  {
    icon: "💼",
    slug: "pengalaman_kerja",
    t: "Pengalaman Kerja",
    d: "Bukti pengalaman bekerja (Paklaring).",
  },
  {
    icon: "🤝",
    slug: "jual_beli",
    t: "Pernyataaan Jual Beli",
    d: "Kesepakatan transaksi barang/tanah.",
  },
  {
    icon: "🗺️",
    slug: "milik_tanah",
    t: "Kepemilikan Tanah",
    d: "Bukti kepemilikan aset tanah desa.",
  },
  {
    icon: "🚗",
    slug: "milik_kendaraan",
    t: "Milik Kendaraan",
    d: "Bukti kepemilikan motor/mobil.",
  },
  {
    icon: "🏞️",
    slug: "tanah_sengketa",
    t: "Tanah Aman",
    d: "Ket. tanah tidak dalam sengketa.",
  },
  {
    icon: "🚓",
    slug: "skck",
    t: "Pengantar SKCK",
    d: "Berkelakuan baik (Polsek).",
  },
  {
    icon: "🔍",
    slug: "kehilangan",
    t: "Ket. Kehilangan",
    d: "Lapor barang/surat hilang (Polisi).",
  },
  {
    icon: "🏍️",
    slug: "kuasa_bpkb",
    t: "Kuasa BPKB/STNK",
    d: "Kuasa pengurusan surat kendaraan hilang.",
  },
  {
    icon: "✍️",
    slug: "kuasa",
    t: "Surat Kuasa Umum",
    d: "Pemberian wewenang ke pihak lain.",
  },
  {
    icon: "🎉",
    slug: "keramaian",
    t: "Izin Keramaian",
    d: "Izin pesta, hajatan, acara warga.",
  },
  {
    icon: "✈️",
    slug: "bepergian",
    t: "Ket. Bepergian",
    d: "Izin dinas/pergi ke luar kota.",
  },
  {
    icon: "🚪",
    slug: "tidak_dirumah",
    t: "Tidak Dirumah",
    d: "Ket. sedang bepergian/tidak di tempat.",
  },
  {
    icon: "📄",
    slug: "umum",
    t: "Keterangan Umum",
    d: "Keperluan administrasi lain.",
  },
  {
    icon: "🏫",
    slug: "izin_sekolah",
    t: "Izin Tidak Sekolah",
    d: "Keterangan absen untuk siswa sekolah.",
  },
  {
    icon: "🎓",
    slug: "domisili_sekolah",
    t: "Domisili Sekolah",
    d: "Keterangan alamat untuk PPDB/Zonasi.",
  },
  {
    icon: "⛺",
    slug: "pinjam_tanah",
    t: "Pinjam Tanah",
    d: "Izin pinjam pakai lahan sekolah/desa.",
  },
]);

// Ambil data pengajuan terbaru langsung dari database (5 terakhir)
const fetchLogPermohonan = async () => {
  isLoadingLog.value = true;
  try {
    const response = await axios.get(`${API_BASE_URL}/api/pelayanan`);
    const data = response.data.data || [];

    logPermohonan.value = data.slice(0, 5).map((item) => {
      let tgl = "-";
      if (item.created_at) {
        tgl = new Date(item.created_at).toLocaleDateString("id-ID", {
          day: "2-digit",
          month: "short",
          year: "numeric",
        });
      }

      return {
        id: item.id,
        tgl,
        jenis: item.jenis_surat ? item.jenis_surat.toUpperCase() : "-",
        status: item.status || "Pending",
        nomor_surat: item.nomor_surat || null,
      };
    });
  } catch (error) {
    console.error("Gagal mengambil data pengajuan terakhir:", error);
    logPermohonan.value = [];
  } finally {
    isLoadingLog.value = false;
  }
};

onMounted(() => {
  fetchLogPermohonan();
});

const filteredLayanan = computed(() => {
  if (!searchQuery.value) return daftarLayanan.value;
  const q = searchQuery.value.toLowerCase();
  return daftarLayanan.value.filter(
    (item) =>
      item.t.toLowerCase().includes(q) || item.d.toLowerCase().includes(q),
  );
});

const goToForm = (slug) => {
  router.push({ path: "/admin/pelayanan/tambah", query: { layanan: slug } });
};

const openModal = (log) => {
  selectedPermohonan.value = log;
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};

const isStep2Active = computed(() =>
  ["Diproses", "Selesai", "Diserahkan", "Ditolak"].includes(
    selectedPermohonan.value?.status,
  ),
);
const isStep3Active = computed(() =>
  ["Selesai", "Diserahkan", "Ditolak"].includes(
    selectedPermohonan.value?.status,
  ),
);
const step3Title = computed(() =>
  selectedPermohonan.value?.status === "Ditolak"
    ? "Permohonan Ditolak"
    : "Selesai & Dapat Diambil",
);
const step3Desc = computed(() =>
  selectedPermohonan.value?.status === "Ditolak"
    ? "Silakan cek pesan atau buat ulang."
    : "Surat telah ditandatangani Kepala Desa.",
);
const step3TitleStyle = computed(() =>
  selectedPermohonan.value?.status === "Ditolak" ? { color: "#ef4444" } : {},
);
const step3DotStyle = computed(() =>
  selectedPermohonan.value?.status === "Ditolak"
    ? { borderColor: "#ef4444", backgroundColor: "#ef4444" }
    : {},
);
</script>

<style>
@import "../../../assets/css/admin/pelayanan.css";

@media (max-width: 1100px) {
  .search-card {
    flex-wrap: wrap;
    gap: 15px;
  }
  .search-box {
    flex: 1 1 200px;
    width: auto;
  }
}

@media (max-width: 900px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    padding: 25px 20px;
  }
  .header-buttons-wrapper {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .btn-list-pelayanan,
  .btn-back {
    flex: 1 1 auto;
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .search-card {
    flex-direction: column;
    align-items: stretch;
    gap: 15px;
    padding: 15px 20px;
  }
  .search-box {
    width: 100%;
  }
  .header-title h1 {
    font-size: 22px;
  }
}

@media (max-width: 480px) {
  .page-header {
    padding: 18px 15px;
  }
  .header-title h1 {
    font-size: 20px;
  }
  .header-title p {
    font-size: 12px;
  }
  .service-item {
    padding: 15px;
  }
}

@media (max-width: 360px) {
  .header-title h1 {
    font-size: 18px;
  }
  .btn-list-pelayanan,
  .btn-back {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }
}
</style>

<style scoped>
.modal-overlay-simple {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.25s ease;
}
.modal-overlay-simple.active {
  opacity: 1;
  visibility: visible;
}
.modal-box-simple {
  background: #ffffff;
  width: 92%;
  max-width: 620px;
  border-radius: 16px;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
  max-height: 85vh;
  overflow-y: auto;
}
.modal-header-simple {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
}
.close-btn-simple {
  background: #f1f5f9;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  color: #64748b;
  font-size: 14px;
  cursor: pointer;
  flex-shrink: 0;
  transition: 0.2s;
}
.close-btn-simple:hover {
  background: #e2e8f0;
  color: #1e293b;
}
.modal-body-grid {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 24px;
  padding: 24px;
}
@media (max-width: 640px) {
  .modal-body-grid {
    grid-template-columns: 1fr;
  }
}
.modal-left {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.info-group label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #94a3b8;
  margin-bottom: 4px;
}
.info-group p {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}
.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: capitalize;
}
.status-badge.pending {
  background: #dbeafe;
  color: #1d4ed8;
}
.status-badge.diproses {
  background: #fef3c7;
  color: #b45309;
}
.status-badge.selesai {
  background: #dcfce7;
  color: #15803d;
}
.status-badge.diserahkan {
  background: #ede9fe;
  color: #6d28d9;
}
.status-badge.ditolak {
  background: #fee2e2;
  color: #b91c1c;
}
.timeline-container {
  position: relative;
  padding-left: 24px;
}
.timeline-step {
  position: relative;
  padding-bottom: 24px;
  opacity: 0.4;
}
.timeline-step:last-child {
  padding-bottom: 0;
}
.timeline-step.active {
  opacity: 1;
}
.timeline-step::before {
  content: "";
  position: absolute;
  left: -19px;
  top: 16px;
  bottom: -8px;
  width: 2px;
  background: #e2e8f0;
}
.timeline-step:last-child::before {
  display: none;
}
.timeline-step.active::before {
  background: #10b981;
}
.timeline-dot {
  position: absolute;
  left: -24px;
  top: 2px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #e2e8f0;
  border: 2px solid #ffffff;
  box-shadow: 0 0 0 2px #e2e8f0;
}
.timeline-step.active .timeline-dot {
  background: #10b981;
  box-shadow: 0 0 0 2px #10b981;
}
.timeline-content h5 {
  margin: 0 0 4px;
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}
.timeline-content p {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}
.log-status {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  text-transform: capitalize;
}
.log-status.pending {
  background: #dbeafe;
  color: #1d4ed8;
}
.log-status.diproses {
  background: #fef3c7;
  color: #b45309;
}
.log-status.selesai {
  background: #dcfce7;
  color: #15803d;
}
.log-status.diserahkan {
  background: #ede9fe;
  color: #6d28d9;
}
.log-status.ditolak {
  background: #fee2e2;
  color: #b91c1c;
}
</style>
