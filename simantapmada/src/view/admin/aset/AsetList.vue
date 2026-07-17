<template>
  <div class="vue-aset-list-body">
    <div class="main-container">
      <div class="page-header-box">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-server text-emerald"></i> Daftar Aset Desa
          </h2>
          <p>
            Pengelolaan dan rekapitulasi seluruh daftar kekayaan fisik desa.
          </p>
        </div>
        <div class="header-actions">
          <button class="btn-back-outline" @click="kembaliDashboard">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
          </button>
        </div>
      </div>

      <div class="action-bar">
        <div class="action-left">
          <button
            v-if="bisaEdit"
            class="btn-primary"
            @click="router.push('/admin/aset/tambah')"
          >
            <i class="fa-solid fa-plus"></i> Input Aset Baru
          </button>
          <button class="btn-outline-print" @click="cetakRekap">
            <i class="fa-solid fa-print"></i> Cetak Rekap KIB
          </button>
        </div>

        <div class="action-right">
          <div class="input-icon">
            <i class="fa-solid fa-layer-group"></i>
            <select v-model="filterKategori" class="filter-select">
              <option value="">Semua Kategori</option>
              <option value="Tanah">Tanah</option>
              <option value="Gedung">Gedung / Bangunan</option>
              <option value="Kendaraan">Kendaraan Mesin</option>
              <option value="Peralatan">Peralatan / Elektronik</option>
              <option value="Lainnya">Aset Lainnya</option>
            </select>
          </div>

          <div class="input-icon">
            <i class="fa-solid fa-heart-pulse"></i>
            <select v-model="filterKondisi" class="filter-select">
              <option value="">Semua Kondisi</option>
              <option value="Baik">Kondisi Baik</option>
              <option value="Rusak Ringan">Rusak Ringan</option>
              <option value="Rusak Berat">Rusak Berat</option>
            </select>
          </div>

          <div class="input-icon search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari Kode atau Nama Aset..."
            />
          </div>
        </div>
      </div>

      <div class="table-card">
        <div class="table-scroll">
          <table class="custom-table">
            <thead>
              <tr>
                <th class="col-no">NO</th>
                <th>NOMOR REGISTER</th>
                <th>NAMA INVENTARIS / BARANG</th>
                <th>KATEGORI</th>
                <th>TAHUN</th>
                <th>KONDISI FISIK</th>
                <th style="text-align: center">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="isLoading">
                <tr v-for="n in 8" :key="'skeleton-' + n" class="skeleton-row">
                  <td v-for="c in 7" :key="c">
                    <span class="skeleton-bar"></span>
                  </td>
                </tr>
              </template>

              <tr v-else-if="filteredAset.length === 0">
                <td colspan="7" class="empty-state">
                  <i class="fa-solid fa-box-open"></i>
                  <p>Tidak ada data aset yang sesuai dengan pencarian Anda.</p>
                </td>
              </tr>

              <tr v-for="(row, index) in pagedAset" :key="row.id">
                <td class="col-no">
                  {{ (currentPage - 1) * perPage + index + 1 }}
                </td>
                <td>
                  <span class="kode-badge">{{ row.nomor_aset }}</span>
                </td>
                <td class="text-judul">{{ row.nama_aset }}</td>
                <td>
                  <span class="category-badge">{{ row.jenis_aset }}</span>
                </td>
                <td class="text-bold">{{ row.tahun_perolehan }}</td>
                <td>
                  <span :class="['badge', getKondisiBadge(row.kondisi)]">{{
                    row.kondisi
                  }}</span>
                </td>
                <td style="text-align: center">
                  <div class="action-group">
                    <button
                      class="btn-icon btn-view"
                      title="Lihat Detail KIB"
                      @click="bukaModal(row)"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </button>
                    <button
                      v-if="bisaEdit"
                      class="btn-icon btn-edit"
                      title="Edit Aset"
                      @click="router.push('/admin/aset/edit/' + row.id)"
                    >
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-container">
          <span class="pg-info">
            Menampilkan {{ infoMulai }}–{{ infoAkhir }} dari
            {{ filteredAset.length }} aset
          </span>
          <div class="pg-controls" v-if="totalPages > 1">
            <button
              class="pg-btn"
              :disabled="currentPage === 1"
              @click="prevPage"
              title="Sebelumnya"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button
              v-for="p in nomorHalaman"
              :key="p"
              class="pg-btn"
              :class="{ active: p === currentPage }"
              @click="gotoPage(p)"
            >
              {{ p }}
            </button>
            <button
              class="pg-btn"
              :disabled="currentPage === totalPages"
              @click="nextPage"
              title="Selanjutnya"
            >
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal-overlay"
      :class="{ show: isModalOpen }"
      @click.self="tutupModal"
    >
      <div class="modal-box">
        <div class="modal-header">
          <h3>
            <i class="fa-solid fa-box-open text-emerald"></i> Rincian Aset Desa
            (KIB)
          </h3>
          <button class="btn-close" @click="tutupModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-content">
          <div class="aset-2col">
            <div class="col-foto">
              <div class="preview-top">
                <img
                  v-if="selectedAset.foto && selectedAset.foto.length > 0"
                  :src="selectedAset.foto[0]"
                  alt="Foto Aset"
                  style="width: 100%; height: 100%; object-fit: cover"
                />
                <div v-else class="placeholder-foto">
                  <i class="fa-solid fa-image"></i>
                  <span>Foto Aset Akan Ditampilkan Di Sini</span>
                </div>
              </div>
              <div class="qr-area-box">
                <img :src="qrUrl" class="qr-img" alt="QR Label Aset" />
                <span class="qr-label">SCAN UNTUK VERIFIKASI</span>
                <button class="btn-print-qr" @click="cetakLabel">
                  <i class="fa-solid fa-print"></i> Cetak Stiker Label
                </button>
              </div>
            </div>
            <div class="col-detail">
              <div class="info-side">
              <div style="margin-bottom: 25px">
                <span class="detail-label">Nama Inventaris Aset</span>
                <div class="detail-value text-emerald" style="font-size: 22px">
                  {{ selectedAset.nama_aset || "-" }}
                </div>
              </div>

              <div class="detail-group">
                <div class="detail-item">
                  <span class="detail-label">Nomor Register Aset</span>
                  <span class="detail-value text-mono" style="color: #3b82f6">{{
                    selectedAset.nomor_aset
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Kategori Jenis</span>
                  <span class="detail-value">{{
                    selectedAset.jenis_aset
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tahun Perolehan</span>
                  <span class="detail-value">{{
                    selectedAset.tahun_perolehan
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Kondisi Fisik</span>
                  <span
                    :class="['badge', getKondisiBadge(selectedAset.kondisi)]"
                    style="margin-top: 4px"
                    >{{ selectedAset.kondisi }}</span
                  >
                </div>
                <div class="detail-item">
                  <span class="detail-label">Sumber Dana</span>
                  <span class="detail-value">{{
                    selectedAset.sumber_dana || "Dana Desa"
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Penanggung Jawab</span>
                  <span class="detail-value">{{
                    selectedAset.penanggung_jawab || "Kaur Umum"
                  }}</span>
                </div>
                <div
                  v-if="selectedAset.ukuran && Number(selectedAset.ukuran) > 0"
                  class="detail-item"
                >
                  <span class="detail-label">Ukuran / Luas</span>
                  <span class="detail-value"
                    >{{ Number(selectedAset.ukuran) }} m²</span
                  >
                </div>
              </div>

              <div class="lokasi-nilai-box">
                <span class="detail-label">Penempatan & Estimasi Nilai</span>
                <div class="lokasi-nilai-flex">
                  <span class="lokasi-text"
                    ><i class="fa-solid fa-map-pin text-emerald"></i>
                    {{ selectedAset.lokasi || "-" }}</span
                  >
                  <span class="nilai-text">{{
                    formatRupiahTampilan(selectedAset.nilai_perolehan)
                  }}</span>
                </div>
              </div>

              <!-- 🔥 DETAIL SPESIFIK PER JENIS ASET -->
              <div v-if="detailEntries.length > 0" class="detail-spesifik-box">
                <span class="detail-label">
                  <i class="fa-solid fa-circle-info text-emerald"></i>
                  Detail Spesifik {{ selectedAset.jenis_aset }}
                </span>
                <div class="detail-spesifik-grid">
                  <div
                    v-for="item in detailEntries"
                    :key="item.label"
                    class="spesifik-item"
                  >
                    <span class="spesifik-label">{{ item.label }}</span>
                    <span class="spesifik-value">{{ item.value }}</span>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </div>

          <div class="action-bar-modal">
            <button class="btn-modal-action" @click="cetakLabel">
              <i class="fa-solid fa-qrcode"></i> Cetak Label QR
            </button>
            <button
              v-if="bisaEdit"
              class="btn-modal-action"
              style="background: #ef4444"
              @click="hapusAset(selectedAset.id)"
            >
              <i class="fa-solid fa-trash-can"></i> Hapus Aset
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== AREA CETAK REKAP KIB (tersembunyi, muncul saat print) ===== -->
    <div id="area-cetak-aset" class="area-cetak-aset">
      <div class="kop-cetak-aset">
        <h2>PEMERINTAH DESA MACANAN</h2>
        <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
        <h3>REKAP KARTU INVENTARIS BARANG (KIB)</h3>
        <hr />
      </div>

      <div class="info-cetak">
        <span v-if="filterKategori"
          >Kategori: <b>{{ filterKategori }}</b></span
        >
        <span v-if="filterKondisi"
          >Kondisi: <b>{{ filterKondisi }}</b></span
        >
        <span
          >Total: <b>{{ filteredAset.length }} unit aset</b></span
        >
      </div>

      <table class="tabel-cetak-aset">
        <thead>
          <tr>
            <th>No</th>
            <th>Nomor Register</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Tahun</th>
            <th>Kondisi</th>
            <th class="ta-right">Nilai Perolehan</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, i) in filteredAset" :key="row.id">
            <td>{{ i + 1 }}</td>
            <td>{{ row.nomor_aset }}</td>
            <td>{{ row.nama_aset }}</td>
            <td>{{ row.jenis_aset }}</td>
            <td>{{ row.tahun_perolehan }}</td>
            <td>{{ row.kondisi }}</td>
            <td class="ta-right">
              {{ formatRupiahTampilan(row.nilai_perolehan) }}
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="baris-total">
            <td colspan="6">TOTAL NILAI ASET</td>
            <td class="ta-right">{{ formatRupiahTampilan(totalNilaiAset) }}</td>
          </tr>
        </tfoot>
      </table>

      <div class="ttd-cetak-aset">
        <p>Nganjuk, {{ tglCetak }}</p>
        <p>Mengetahui,</p>
        <div class="ttd-space-aset"></div>
        <p class="ttd-nama-aset">( ........................... )</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

// === Deteksi role: hanya admin/developer yang bisa tambah/edit/hapus (sekdes & kades read-only) ===
const roleUser = (() => {
  try {
    return JSON.parse(localStorage.getItem("user"))?.role || "";
  } catch (e) {
    return "";
  }
})();
const bisaEdit = computed(
  () => roleUser === "admin" || roleUser === "developer",
);

// Kembali ke dashboard sesuai role yang login
const kembaliDashboard = () => {
  const tujuan = {
    admin: "/admin/aset",
    sekdes: "/sekdes",
    kades: "/kades",
    developer: "/dev",
  };
  router.push(tujuan[roleUser] || "/admin/aset");
};

// ==========================================
// STATE & DATA
// ==========================================
const searchQuery = ref("");
const filterKategori = ref("");
const filterKondisi = ref("");
const isModalOpen = ref(false);
const selectedAset = ref({});
const isLoading = ref(true);

// 🔥 DATA ASLI DARI DATABASE (BUKAN DUMMY LAGI)
const dataAset = ref([]);

// Coba ambil data dengan beberapa kali percobaan (mengatasi API yang
// kadang lambat/gagal sesaat), tanpa mengosongkan data lama saat gagal.
const fetchAset = async () => {
  isLoading.value = true;
  const maksPercobaan = 3;
  for (let percobaan = 1; percobaan <= maksPercobaan; percobaan++) {
    try {
      const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/aset`);
      dataAset.value = res.data?.data || [];
      isLoading.value = false;
      return; // sukses, berhenti
    } catch (error) {
      console.error(`Gagal memuat data aset (percobaan ${percobaan}):`, error);
      if (percobaan < maksPercobaan) {
        await new Promise((r) => setTimeout(r, 500 * percobaan));
      }
    }
  }
  // Semua percobaan gagal: jangan kosongkan data yang sudah tampil.
  if (!Array.isArray(dataAset.value)) dataAset.value = [];
  isLoading.value = false;
};

onMounted(fetchAset);

// 🔥 Format angka mentah dari DB jadi "Rp 15.000.000" untuk ditampilkan
const formatRupiahTampilan = (angka) => {
  if (!angka) return "Rp 0";
  return (
    "Rp " + Number(angka).toLocaleString("id-ID", { maximumFractionDigits: 0 })
  );
};

// Total nilai semua aset sesuai filter aktif
const totalNilaiAset = computed(() =>
  filteredAset.value.reduce(
    (sum, item) => sum + Number(item.nilai_perolehan || 0),
    0,
  ),
);

// Tanggal cetak
const tglCetak = computed(() =>
  new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }),
);

// Cetak rekap KIB (ikut filter aktif) — jendela baru, output bersih (anti "muter-muter")
const cetakRekap = () => {
  if (filteredAset.value.length === 0) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada Data",
      text: "Tidak ada aset untuk dicetak sesuai filter saat ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }

  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  const baris = filteredAset.value
    .map(
      (row, i) => `
      <tr>
        <td style="text-align:center">${i + 1}</td>
        <td>${esc(row.nomor_aset)}</td>
        <td>${esc(row.nama_aset)}</td>
        <td>${esc(row.jenis_aset)}</td>
        <td style="text-align:center">${esc(row.tahun_perolehan)}</td>
        <td style="text-align:center">${esc(row.kondisi)}</td>
        <td style="text-align:right">${formatRupiahTampilan(row.nilai_perolehan)}</td>
      </tr>`,
    )
    .join("");
  const infoFilter = [
    filterKategori.value ? `Kategori: <b>${esc(filterKategori.value)}</b>` : "",
    filterKondisi.value ? `Kondisi: <b>${esc(filterKondisi.value)}</b>` : "",
    `Total: <b>${filteredAset.value.length} unit aset</b>`,
  ]
    .filter(Boolean)
    .join(" &nbsp;|&nbsp; ");
  const inner = `
    <div class="kop">
      <h2>PEMERINTAH DESA MACANAN</h2>
      <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
      <h3>REKAP KARTU INVENTARIS BARANG (KIB)</h3>
    </div>
    <hr class="garis" />
    <p class="info">${infoFilter}</p>
    <table>
      <thead>
        <tr>
          <th style="width:5%">No</th>
          <th style="width:16%">Nomor Register</th>
          <th>Nama Barang</th>
          <th style="width:14%">Kategori</th>
          <th style="width:8%">Tahun</th>
          <th style="width:11%">Kondisi</th>
          <th style="width:16%">Nilai Perolehan</th>
        </tr>
      </thead>
      <tbody>${baris}</tbody>
      <tfoot>
        <tr class="total">
          <td colspan="6" style="text-align:right">TOTAL NILAI ASET</td>
          <td style="text-align:right">${formatRupiahTampilan(totalNilaiAset.value)}</td>
        </tr>
      </tfoot>
    </table>
    <div class="ttd">
      <p>Nganjuk, ${tglCetak.value}</p>
      <p>Mengetahui,</p>
      <div class="ttd-space"></div>
      <p class="nama">( ........................... )</p>
    </div>`;

  const w = window.open("", "_blank", "width=1100,height=760");
  if (!w) {
    Swal.fire({
      icon: "warning",
      title: "Popup Diblokir",
      text: "Izinkan popup untuk mencetak rekap aset.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Rekap KIB Aset Desa Macanan</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Times New Roman", serif; color: #000; margin: 20px 26px; }
      .kop { text-align: center; line-height: 1.25; }
      .kop h2 { margin: 0; font-size: 17pt; }
      .kop h3 { margin: 6px 0 0; font-size: 13pt; text-decoration: underline; }
      .kop p { margin: 2px 0 0; font-size: 10pt; }
      .garis { border: none; border-top: 3px double #000; margin: 8px 0 12px; }
      .info { font-size: 10pt; margin: 0 0 10px; }
      table { width: 100%; border-collapse: collapse; font-size: 10pt; table-layout: fixed; }
      th, td { border: 1px solid #000; padding: 5px 7px; vertical-align: top; word-wrap: break-word; }
      thead th { background: #e5e7eb; text-align: center; }
      tfoot .total td { font-weight: bold; background: #f1f5f9; }
      .ttd { width: 280px; margin: 26px 0 0 auto; text-align: center; font-size: 10pt; }
      .ttd-space { height: 64px; }
      .ttd .nama { font-weight: bold; }
      @page { size: A4 landscape; margin: 12mm; }
    </style></head>
    <body onload="window.focus(); window.print();">${inner}
    <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

// ==========================================
// LOGIKA FILTER PENCARIAN (REAKTIF VUE)
// ==========================================
const filteredAset = computed(() => {
  return dataAset.value.filter((item) => {
    const q = searchQuery.value.toLowerCase();
    const matchSearch =
      (item.nama_aset || "").toLowerCase().includes(q) ||
      (item.nomor_aset || "").toLowerCase().includes(q);
    const matchKat =
      filterKategori.value === "" || item.jenis_aset === filterKategori.value;
    const matchKondisi =
      filterKondisi.value === "" || item.kondisi === filterKondisi.value;

    return matchSearch && matchKat && matchKondisi;
  });
});

// 🔥 PAGINATION
const currentPage = ref(1);
const perPage = ref(10);

// Total halaman berdasarkan hasil filter
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredAset.value.length / perPage.value)),
);

// Data yang ditampilkan di halaman aktif (dipotong per halaman)
const pagedAset = computed(() => {
  const mulai = (currentPage.value - 1) * perPage.value;
  return filteredAset.value.slice(mulai, mulai + perPage.value);
});

// Info "Menampilkan X–Y dari Z"
const infoMulai = computed(() =>
  filteredAset.value.length === 0
    ? 0
    : (currentPage.value - 1) * perPage.value + 1,
);
const infoAkhir = computed(() =>
  Math.min(currentPage.value * perPage.value, filteredAset.value.length),
);

// Navigasi halaman
const gotoPage = (p) => {
  if (p >= 1 && p <= totalPages.value) currentPage.value = p;
};
const nextPage = () => gotoPage(currentPage.value + 1);
const prevPage = () => gotoPage(currentPage.value - 1);

// Kalau filter/pencarian berubah, balik ke halaman 1
watch([searchQuery, filterKategori, filterKondisi], () => {
  currentPage.value = 1;
});

// Kalau halaman aktif melebihi total (mis. data berkurang), koreksi
watch(totalPages, (t) => {
  if (currentPage.value > t) currentPage.value = t;
});

// Deret nomor halaman yang ditampilkan (maks 5, berpusat di halaman aktif)
const nomorHalaman = computed(() => {
  const total = totalPages.value;
  const cur = currentPage.value;
  const arr = [];
  let mulai = Math.max(1, cur - 2);
  let akhir = Math.min(total, mulai + 4);
  mulai = Math.max(1, akhir - 4);
  for (let i = mulai; i <= akhir; i++) arr.push(i);
  return arr;
});

// Helper Warna Badge Kondisi
const getKondisiBadge = (kondisi) => {
  if (kondisi === "Baik") return "badge-baik";
  if (kondisi === "Rusak Ringan") return "badge-ringan";
  if (kondisi === "Rusak Berat") return "badge-berat";
  return "badge-baik";
};

// 🔥 Ubah object 'detail' jadi list {label, value} dengan label rapi
const labelDetailMap = {
  no_sertifikat: "No. Sertifikat / Letter C",
  status_hak: "Status Hak",
  atas_nama: "Atas Nama",
  penggunaan: "Penggunaan",
  letak_batas: "Letak & Batas",
  luas_bangunan: "Luas Bangunan (m²)",
  luas_lahan: "Luas Lahan (m²)",
  jumlah_lantai: "Jumlah Lantai",
  konstruksi: "Konstruksi",
  no_imb: "No. IMB / PBG",
  kondisi_struktur: "Kondisi Struktur",
  no_polisi: "No. Polisi",
  no_rangka: "No. Rangka",
  no_mesin: "No. Mesin",
  tahun_pembuatan: "Tahun Pembuatan",
  warna: "Warna",
  bahan_bakar: "Bahan Bakar",
  no_bpkb: "No. BPKB",
  no_stnk: "No. STNK",
  tipe_model: "Tipe / Model",
  no_seri: "No. Seri",
  jumlah_unit: "Jumlah Unit",
  spesifikasi: "Spesifikasi",
  spesifikasi_bebas: "Spesifikasi",
};

// URL halaman scan publik (dibuka warga tanpa login setelah scan QR)
const scanUrl = computed(() => {
  const a = selectedAset.value || {};
  return `${window.location.origin}/scan/aset/${a.id || ""}`;
});

// QR aset — isinya URL halaman scan publik, jadi saat di-scan langsung
// membuka detail aset di browser warga (tanpa perlu login).
const qrUrl = computed(
  () =>
    `https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data=${encodeURIComponent(
      scanUrl.value,
    )}`,
);

// Cetak label/stiker QR aset (jendela baru, siap tempel di barang fisik)
const cetakLabel = () => {
  const a = selectedAset.value || {};
  const w = window.open("", "_blank", "width=520,height=620");
  if (!w) {
    Swal.fire({
      icon: "warning",
      title: "Popup Diblokir",
      text: "Izinkan popup untuk mencetak label QR.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Label QR ${esc(a.nomor_aset)}</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; padding: 18px; color: #0f172a; }
      .label { border: 2px solid #0f172a; border-radius: 14px; padding: 16px; text-align: center; width: 320px; margin: 0 auto; }
      .label h2 { margin: 0 0 2px; font-size: 13px; }
      .label .sub { font-size: 10px; color: #475569; margin-bottom: 10px; }
      .label img { width: 200px; height: 200px; }
      .label .nama { font-weight: 800; font-size: 14px; margin: 10px 0 2px; }
      .label .nomor { font-family: monospace; font-size: 13px; color: #1d4ed8; }
      .label .ket { font-size: 9px; color: #64748b; margin-top: 8px; }
      @page { margin: 8mm; }
    </style></head>
    <body onload="window.focus(); window.print();">
      <div class="label">
        <h2>PEMERINTAH DESA MACANAN</h2>
        <div class="sub">Inventaris / Aset Desa</div>
        <img src="${qrUrl.value}" alt="QR" />
        <div class="nama">${esc(a.nama_aset)}</div>
        <div class="nomor">${esc(a.nomor_aset)}</div>
        <div class="ket">Scan untuk verifikasi & detail aset</div>
      </div>
      <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

const detailEntries = computed(() => {
  const d = selectedAset.value?.detail;
  if (!d || typeof d !== "object") return [];
  return Object.keys(d)
    .filter((key) => d[key] !== null && d[key] !== "" && d[key] !== undefined)
    .map((key) => ({
      label: labelDetailMap[key] || key,
      value: d[key],
    }));
});

// Logika Modal
const bukaModal = (row) => {
  selectedAset.value = row;
  isModalOpen.value = true;
};

const tutupModal = () => {
  isModalOpen.value = false;
};

// Hapus Aset
const hapusAset = async (id) => {
  const konfirmasi = await Swal.fire({
    icon: "warning",
    title: "Hapus Data Aset?",
    text: "Data dan foto aset ini akan dihapus permanen dari database!",
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#64748b",
  });

  if (!konfirmasi.isConfirmed) return;

  try {
    await axios.delete(`${import.meta.env.VITE_API_URL}/api/aset/${id}`);
    tutupModal();
    await fetchAset();
    Swal.fire({
      icon: "success",
      title: "Terhapus!",
      text: "Data aset berhasil dihapus dari database.",
      confirmButtonColor: "#10b981",
      timer: 2500,
      timerProgressBar: true,
    });
  } catch (error) {
    console.error("Gagal menghapus aset:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal Menghapus",
      text: "Terjadi kesalahan saat menghapus data. Cek koneksi server/database.",
      confirmButtonColor: "#10b981",
    });
  }
};
</script>

<style>
/* ==================================================
   CSS DATABASE ASET - ULTRA PREMIUM EMERALD SAAS
   DITANAM LANGSUNG BIAR AMAN DARI BUG PATH
   ================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

/* Scaffold cetak lama disembunyikan — cetak kini via jendela terpisah */
.area-cetak-aset {
  display: none !important;
}

.vue-aset-list-body {
  background-color: #f8fafc;
  background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
  background-size: 24px 24px;
  padding: 40px 20px 60px;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #0f172a;
  min-height: 100vh;
  box-sizing: border-box;
}

.main-container {
  max-width: 95%;
  margin: 0 auto;
  animation: premiumFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes premiumFadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* === UTILITIES === */
.text-emerald {
  color: #10b981;
}
.text-muted {
  color: #94a3b8;
}
.text-bold {
  font-weight: 800;
  color: #1e293b;
}

/* === HEADER PREMIUM (GLASSMORPHISM) === */
.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  padding: 28px 35px;
  border-radius: 20px;
  border: none;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
  position: relative;
  overflow: hidden;
}

.header-text {
  position: relative;
  z-index: 2;
}
.header-text h2 {
  margin: 0 0 5px 0;
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  letter-spacing: -0.5px;
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

.btn-back-outline {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: 0.3s;
  position: relative;
  z-index: 2;
}
.btn-back-outline:hover {
  background: #ffffff;
  color: #1e293b;
  border-color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

/* === ACTION BAR (FILTER & SEARCH) === */
.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
  background: white;
  padding: 20px 30px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
  border: 1px solid #e2e8f0;
  margin-bottom: 25px;
}

.action-left,
.action-right {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.btn-primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 14px 28px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.25);
  transition: 0.3s;
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
}

.btn-outline-print {
  background: white;
  color: #1e293b;
  border: 2px solid #e2e8f0;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
}
.btn-outline-print:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-2px);
}

.input-icon {
  position: relative;
}
.input-icon i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #10b981;
  font-size: 15px;
}

.filter-select,
.input-icon input {
  padding: 12px 16px 12px 42px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: 0.3s;
  font-family: inherit;
  box-sizing: border-box;
}
.filter-select {
  appearance: none;
  cursor: pointer;
  padding-right: 35px;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310b981' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 14px;
}
.search-box input {
  width: 280px;
}

.filter-select:focus,
.input-icon input:focus {
  background: white;
  border-color: #10b981;
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

/* === TABEL ASET === */
.table-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: calc(100vh - 280px);
  min-height: 300px;
}
.table-scroll {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}
.table-scroll::-webkit-scrollbar {
  width: 8px;
}
.table-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
}
.custom-table th {
  background: #f8fafc;
  color: #0f172a;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  padding: 18px 20px;
  text-align: left;
  border-bottom: 2px solid #e2e8f0;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 2;
}
.custom-table td {
  padding: 18px 20px;
  font-size: 14px;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-weight: 500;
}
.custom-table tbody tr {
  transition:
    background 0.2s ease,
    transform 0.2s ease;
  animation: rowFadeIn 0.4s ease forwards;
}
.custom-table tbody tr:hover {
  background: var(--primary-soft, #ecfdf5);
  transform: translateX(3px);
}

/* Teks Kolom */
.col-no {
  width: 5%;
  text-align: center;
  font-weight: 800;
  color: #94a3b8 !important;
}
.kode-badge {
  font-family: monospace;
  font-size: 14px;
  font-weight: 800;
  color: #3b82f6;
  background: #eff6ff;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px dashed #bfdbfe;
  letter-spacing: 0.5px;
}
.text-judul {
  font-weight: 800;
  color: #0f172a;
  font-size: 15px;
}
.category-badge {
  font-weight: 700;
  color: #475569;
  background: #f1f5f9;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  border: 1px solid #e2e8f0;
}

/* Badge Kondisi */
.badge {
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
}
.badge-baik {
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
}
.badge-ringan {
  background: #fff7ed;
  color: #ea580c;
  border: 1px solid #fed7aa;
}
.badge-berat {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fca5a5;
}

/* Kosong */
.empty-state {
  text-align: center;
  padding: 50px 20px !important;
  color: #94a3b8;
}
.empty-state i {
  font-size: 45px;
  color: #cbd5e1;
  margin-bottom: 15px;
}
.empty-state p {
  font-size: 15px;
  font-weight: 600;
  margin: 0;
}

/* Tombol Aksi Tabel */
.action-group {
  display: flex;
  justify-content: center;
  gap: 8px;
}
.btn-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: 0.2s;
}
.btn-view {
  background: #ecfdf5;
  color: #10b981;
  border: 1px solid #a7f3d0;
}
.btn-view:hover {
  background: #10b981;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
}
.btn-edit {
  background: #f1f5f9;
  color: #64748b;
  border: 1px solid #e2e8f0;
}
.btn-edit:hover {
  background: #64748b;
  color: white;
  transform: translateY(-2px);
}

.pagination-container {
  padding: 15px 25px;
  background: #ffffff;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}
.pg-info {
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
}
.pg-controls {
  display: flex;
  gap: 6px;
  align-items: center;
}
.pg-btn {
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  color: #475569;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.pg-btn:hover:not(:disabled) {
  background: var(--primary-soft, #ecfdf5);
  border-color: var(--primary, #059669);
  color: var(--primary-dark, #047857);
}
.pg-btn.active {
  background: var(--primary, #059669);
  border-color: var(--primary, #059669);
  color: #fff;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
}
.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* === MODAL POPUP MASTER (KIB) === */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 92px 20px 24px;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
}
.modal-overlay.show {
  opacity: 1;
  visibility: visible;
}

.modal-box {
  background: white;
  width: 1080px;
  max-width: 95%;
  max-height: calc(100vh - 116px);
  border-radius: 24px;
  overflow: hidden auto;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  transform: scale(0.95) translateY(20px);
  transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-overlay.show .modal-box {
  transform: scale(1) translateY(0);
}

.modal-header {
  padding: 20px 30px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
}
.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: #0f172a;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.btn-close {
  background: #f1f5f9;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #94a3b8;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s;
}
.btn-close:hover {
  background: #fee2e2;
  color: #ef4444;
  transform: rotate(90deg);
}

.modal-content {
  display: flex;
  flex-direction: column;
  background: #f8fafc;
}

/* Layout 2 kolom: foto + QR di kiri, detail di kanan */
.aset-2col {
  display: grid;
  grid-template-columns: 360px 1fr;
  align-items: stretch;
}
.col-foto {
  display: flex;
  flex-direction: column;
  background: #0f172a;
  border-right: 1px solid #1e293b;
}
.col-detail {
  min-width: 0;
}
@media (max-width: 820px) {
  .aset-2col {
    grid-template-columns: 1fr;
  }
}

.preview-top {
  flex: 1;
  min-height: 220px;
  max-height: 240px;
  background: #1e293b;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* ===== SKELETON LOADING (baris tabel saat memuat) ===== */
.skeleton-row td {
  padding: 14px 16px;
}
.skeleton-bar {
  display: block;
  height: 14px;
  border-radius: 7px;
  background: linear-gradient(90deg, #eef2f7 25%, #e2e8f0 37%, #eef2f7 63%);
  background-size: 400% 100%;
  animation: skeletonShimmer 1.4s ease infinite;
}
@keyframes skeletonShimmer {
  0% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0 50%;
  }
}
.placeholder-foto {
  text-align: center;
  color: #64748b;
}
.placeholder-foto i {
  font-size: 50px;
  margin-bottom: 10px;
}
.placeholder-foto span {
  display: block;
  font-size: 13px;
  font-weight: 600;
}

.action-bar-modal {
  padding: 15px 30px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn-modal-action {
  background: #0f172a;
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
}
.btn-modal-action:hover {
  background: #1e293b;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.info-side {
  padding: 30px;
}
.detail-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 25px;
}
.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-label {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.detail-value {
  font-size: 15px;
  color: #1e293b;
  font-weight: 700;
}
.text-mono {
  font-family: monospace;
  font-size: 16px !important;
  letter-spacing: 0.5px;
}

.lokasi-nilai-box {
  background: #f0fdf4;
  padding: 20px;
  border-radius: 12px;
  border: 1px dashed #bbf7d0;
}
.lokasi-nilai-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
}
.lokasi-text {
  font-weight: 700;
  color: #0f172a;
  font-size: 15px;
}
.nilai-text {
  font-weight: 800;
  color: #10b981;
  font-size: 18px;
}

/* === DETAIL SPESIFIK PER JENIS === */
.detail-spesifik-box {
  margin-top: 20px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 18px 20px;
}
.detail-spesifik-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px 20px;
  margin-top: 12px;
}
.spesifik-item {
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.spesifik-label {
  font-size: 10px;
  color: #94a3b8;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.spesifik-value {
  font-size: 14px;
  color: #1e293b;
  font-weight: 700;
  word-break: break-word;
}

.qr-area-box {
  background: rgba(255, 255, 255, 0.04);
  border-top: 1px dashed #334155;
  padding: 22px 20px 26px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.qr-img {
  width: 150px;
  height: 150px;
  background: white;
  border: 6px solid white;
  border-radius: 16px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}
.btn-print-qr {
  margin-top: 14px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.25s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-print-qr:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(16, 185, 129, 0.3);
}
.qr-label {
  font-size: 11px;
  font-weight: 800;
  color: #94a3b8;
  margin-top: 15px;
  letter-spacing: 0.5px;
}

/* === MOBILE RESPONSIVE MASTER === */
@media (max-width: 900px) {
  .info-qr-grid {
    grid-template-columns: 1fr;
  }
  .qr-area-box {
    border-left: none;
    border-top: 1px dashed #e2e8f0;
    padding: 25px;
  }
}

@media (max-width: 768px) {
  .vue-aset-list-body {
    padding: 30px 15px 40px !important;
    overflow-x: hidden !important;
  }
  .main-container {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
  }

  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
  }
  .btn-back-outline {
    width: 100%;
    justify-content: center;
  }

  .action-bar {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
    gap: 15px;
  }
  .action-left,
  .action-right {
    flex-direction: column;
    width: 100%;
    align-items: stretch;
  }
  .btn-primary,
  .btn-outline-print {
    width: 100%;
    justify-content: center;
  }
  .input-icon,
  .filter-select,
  .search-box input {
    width: 100% !important;
    box-sizing: border-box;
  }

  .table-card {
    border-radius: 16px;
    max-height: calc(100vh - 230px);
  }
  .table-scroll {
    overflow-x: auto;
  }
  .custom-table {
    min-width: 800px;
  }

  .modal-box {
    max-height: 90vh;
    overflow-y: auto;
  }
  .detail-group {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  .lokasi-nilai-flex {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}

/* === HP KECIL === */
@media (max-width: 480px) {
  .header-text h2,
  .page-header-box h2 {
    font-size: 20px !important;
  }
  .header-text p {
    font-size: 12px !important;
  }
  .custom-table th {
    font-size: 11px !important;
    padding: 12px 13px !important;
  }
  .custom-table td {
    font-size: 13px !important;
    padding: 12px 13px !important;
  }
  .pagination-container {
    flex-direction: column;
    gap: 10px;
    padding: 12px 15px;
  }
  .pg-info {
    font-size: 12px;
    text-align: center;
  }
  .pg-btn {
    min-width: 32px;
    height: 32px;
    font-size: 12px;
  }
}

/* === HP SUPER KECIL (<=360px) === */
@media (max-width: 360px) {
  .header-text h2,
  .page-header-box h2 {
    font-size: 18px !important;
  }
  .pg-btn {
    min-width: 28px;
    height: 28px;
    font-size: 11px;
    padding: 0 6px;
  }
}

/* === ANIMASI PELENGKAP (SELARAS THEME) === */
@keyframes rowFadeIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
/* ===== AREA CETAK REKAP KIB ===== */
.area-cetak-aset {
  display: none;
}

.kop-cetak-aset {
  text-align: center;
  margin-bottom: 14px;
}
.kop-cetak-aset h2 {
  margin: 0;
  font-size: 18px;
}
.kop-cetak-aset p {
  margin: 2px 0;
  font-size: 13px;
}
.kop-cetak-aset h3 {
  margin: 10px 0 6px;
  font-size: 15px;
}
.kop-cetak-aset hr {
  border: none;
  border-top: 2px solid #000;
}
.info-cetak {
  display: flex;
  gap: 20px;
  font-size: 12px;
  margin-bottom: 10px;
}
.tabel-cetak-aset {
  width: 100%;
  border-collapse: collapse;
}
.tabel-cetak-aset th,
.tabel-cetak-aset td {
  border: 1px solid #333;
  padding: 6px 8px;
  font-size: 12px;
  text-align: left;
}
.tabel-cetak-aset th {
  background: #e2e8f0;
  font-weight: 700;
}
.tabel-cetak-aset .ta-right {
  text-align: right;
}
.baris-total td {
  font-weight: 800;
  background: #f1f5f9;
}
.ttd-cetak-aset {
  margin-top: 30px;
  width: 250px;
  margin-left: auto;
  text-align: center;
  font-size: 13px;
}
.ttd-space-aset {
  height: 60px;
}
.ttd-nama-aset {
  border-top: 1px solid #000;
  padding-top: 4px;
  font-weight: 700;
}

@media print {
  @page {
    size: A4 portrait;
    margin: 12mm;
  }
  body.printing-aset > #app {
    display: none !important;
  }
  body.printing-aset .area-cetak-aset {
    display: block !important;
  }
  .tabel-cetak-aset {
    width: 100%;
    border-collapse: collapse;
  }
  .tabel-cetak-aset thead {
    display: table-header-group;
  }
  .tabel-cetak-aset tfoot {
    display: table-footer-group;
  }
  .tabel-cetak-aset tr {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
  .ttd-cetak-aset {
    page-break-inside: avoid !important;
  }
}
</style>