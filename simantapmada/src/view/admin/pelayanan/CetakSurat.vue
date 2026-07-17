<template>
  <div class="cetak-page">
    <!-- Toolbar (hilang saat print) -->
    <div class="toolbar no-print">
      <button class="btn-kembali" @click="kembali">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </button>
      <div class="toolbar-info">
        <i class="fa-solid fa-file-lines"></i> Pratinjau Surat
      </div>
      <button class="btn-cetak" @click="cetak">
        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
      </button>
    </div>

    <div v-if="loading" class="loading-state no-print">
      <i class="fa-solid fa-spinner fa-spin"></i> Memuat data surat...
    </div>
    <div v-else-if="error" class="error-state no-print">
      <i class="fa-solid fa-triangle-exclamation"></i> {{ error }}
    </div>

    <!-- ===== AREA SURAT ===== -->
    <div v-else id="surat" class="surat-a4">
      <!-- KOP -->
      <div class="kop">
        <div class="kop-logo-slot">
          <img v-if="logoDesa" :src="logoDesa" class="kop-logo" alt="Logo" />
        </div>
        <div class="kop-teks">
          <h3>{{ kop.kabupaten }}</h3>
          <h3>{{ kop.kecamatan }}</h3>
          <h1>{{ kop.desa }}</h1>
          <p>{{ kop.alamat }}</p>
          <p class="email">Email. {{ kop.email }}</p>
        </div>
      </div>
      <div class="kop-garis"></div>

      <!-- JUDUL + NOMOR -->
      <div class="judul-surat">
        <h2>{{ judulSurat }}</h2>
        <p class="nomor">NOMOR : {{ data.nomor_surat || "................" }}</p>
      </div>

      <!-- ISI (render per jenis) -->
      <div class="isi-surat" v-html="isiSurat"></div>

      <!-- TANDA TANGAN -->
      <div class="ttd-area">
        <div class="ttd-box">
          <p>Macanan, {{ tglSurat }}</p>
          <p>{{ jabatanKades }}</p>
          <div class="ttd-ruang">
            <img v-if="ttdKades" :src="ttdKades" class="ttd-gambar" alt="TTD" />
          </div>
          <p class="nama-kades">{{ namaKades }}</p>
          <p v-if="nipKades" class="nip-kades">NIP. {{ nipKades }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const BASE = `${import.meta.env.VITE_API_URL}/api`;

const loading = ref(true);
const error = ref("");
const data = ref({});

const logoDesa = ref("");
const namaKades = ref("MOHAMAT SOIB, S.Pd.");
const jabatanKades = ref("KEPALA DESA MACANAN");
const nipKades = ref("");
const ttdKades = ref("");
const kop = ref({
  kabupaten: "PEMERINTAH KABUPATEN NGANJUK",
  kecamatan: "KECAMATAN LOCERET",
  desa: "DESA MACANAN",
  alamat: "JL. PANGLIMA SUDIRMAN NO. 15 TELP. ___ KODE POS 64471",
  email: "macanan117@gmail.com",
  kode_surat: "411.508.21",
});

// Ambil pengaturan (nama/TTD/kop Kades) — cache lokal dulu, lalu sinkron server.
// Jadi ganti Kades cukup lewat halaman Pengaturan, tanpa ubah kode.
const muatPengaturanKades = () => {
  try {
    const p = JSON.parse(localStorage.getItem("pengaturan_kades") || "{}");
    if (p.nama) namaKades.value = p.nama;
    if (p.jabatan) jabatanKades.value = p.jabatan;
    if (p.nip) nipKades.value = p.nip;
    if (p.ttd) ttdKades.value = p.ttd;
    if (p.logo) logoDesa.value = p.logo;
    if (p.kop) kop.value = { ...kop.value, ...p.kop };
  } catch (e) {
    /* abaikan */
  }
  const ST = `${import.meta.env.VITE_API_URL}/storage/`;
  axios
    .get(`${BASE}/pengaturan`)
    .then((res) => {
      const d = res.data?.data;
      if (!d) return;
      if (d.nama_kades) namaKades.value = d.nama_kades;
      if (d.jabatan_kades) jabatanKades.value = d.jabatan_kades;
      if (d.nip_kades) nipKades.value = d.nip_kades;
      if (d.ttd_kades) ttdKades.value = ST + d.ttd_kades;
      if (d.logo_desa) logoDesa.value = ST + d.logo_desa;
      kop.value = {
        kabupaten: d.nama_kabupaten || kop.value.kabupaten,
        kecamatan: d.nama_kecamatan || kop.value.kecamatan,
        desa: d.nama_desa || kop.value.desa,
        alamat: d.alamat_desa || kop.value.alamat,
        email: d.email_desa || kop.value.email,
        kode_surat: d.kode_surat || kop.value.kode_surat,
      };
    })
    .catch(() => {});
};

// ====== HELPER ======
const bulanID = [
  "Januari",
  "Februari",
  "Maret",
  "April",
  "Mei",
  "Juni",
  "Juli",
  "Agustus",
  "September",
  "Oktober",
  "November",
  "Desember",
];
const tglIndo = (iso) => {
  if (!iso) return "-";
  const d = new Date(iso);
  if (isNaN(d)) return iso;
  return `${d.getDate()} ${bulanID[d.getMonth()]} ${d.getFullYear()}`;
};
const formatTTL = (tempat, tgl) => {
  if (!tempat && !tgl) return "-";
  return `${tempat || "-"}, ${tglIndo(tgl)}`;
};
const up = (s) => (s ? String(s).toUpperCase() : "-");
const val = (s) => (s !== undefined && s !== null && s !== "" ? s : "-");
const rupiah = (n) => {
  const angka = parseInt(String(n || "0").replace(/[^0-9]/g, ""), 10) || 0;
  return "Rp. " + angka.toLocaleString("id-ID") + ",-";
};

// ====== PARSE DATA ======
// Ambil objek data_pengajuan (sudah ter-parse), pisahkan data_pemohon (p) & detail isi (i)
const parsed = computed(() => {
  let d = data.value.data_pengajuan;
  if (typeof d === "string") {
    try {
      d = JSON.parse(d);
    } catch (e) {
      d = {};
    }
  }
  if (!d || typeof d !== "object") d = {};
  // kalau ada wrapper "detail", masuk ke dalamnya
  if (d.detail && typeof d.detail === "object") d = d.detail;

  const p =
    d.data_pemohon && typeof d.data_pemohon === "object" ? d.data_pemohon : d;
  return { i: d, p };
});

// jenis surat (lowercase, normalisasi)
const jenis = computed(() =>
  String(data.value.jenis_surat || "")
    .toLowerCase()
    .trim(),
);

// ====== JUDUL per jenis ======
const judulMap = {
  // Kependudukan & identitas
  domisili: "SURAT KETERANGAN DOMISILI",
  domisili_sekolah: "SURAT KETERANGAN DOMISILI",
  kelahiran: "SURAT KETERANGAN KELAHIRAN",
  kematian: "SURAT KETERANGAN KEMATIAN",
  pindah: "SURAT KETERANGAN PINDAH PENDUDUK",
  skip: "SURAT KETERANGAN IDENTITAS PENDUDUK (SKIP)",
  beda_nama: "SURAT KETERANGAN",
  // Perkawinan
  nikah: "SURAT KETERANGAN UNTUK NIKAH",
  numpang_nikah: "SURAT KETERANGAN NUMPANG NIKAH",
  belum_menikah: "SURAT KETERANGAN BELUM MENIKAH",
  nikah_siri: "SURAT KETERANGAN NIKAH SIRI",
  janda_duda: "SURAT KETERANGAN JANDA / DUDA",
  ket_menikah: "SURAT KETERANGAN TELAH MENIKAH",
  cerai_goib: "SURAT KETERANGAN GHAIB",
  wali: "SURAT KETERANGAN WALI",
  ahli_waris: "SURAT KETERANGAN WARIS",
  // Ekonomi & pekerjaan
  usaha: "SURAT KETERANGAN",
  penghasilan: "SURAT KETERANGAN PENGHASILAN ORANG TUA",
  sktm: "SURAT KETERANGAN TIDAK MAMPU",
  ket_pekerjaan: "SURAT KETERANGAN",
  pengalaman_kerja: "SURAT KETERANGAN PENGALAMAN KERJA",
  // Aset & transaksi
  jual_beli: "SURAT KETERANGAN JUAL BELI",
  milik_kendaraan: "SURAT KETERANGAN KEPEMILIKAN KENDARAAN",
  milik_tanah: "SURAT KETERANGAN KEPEMILIKAN TANAH",
  tanah_sengketa: "SURAT KETERANGAN TIDAK SENGKETA",
  pinjam_tanah: "SURAT KETERANGAN",
  // Kuasa
  kuasa: "SURAT KUASA",
  kuasa_bpkb: "SURAT KUASA",
  // Lain-lain
  skck: "SURAT KETERANGAN KELAKUAN BAIK",
  kehilangan: "SURAT KETERANGAN KEHILANGAN",
  keramaian: "SURAT KETERANGAN IZIN KERAMAIAN",
  bepergian: "SURAT KETERANGAN BEPERGIAN",
  tidak_dirumah: "SURAT KETERANGAN",
  ket_umum: "SURAT KETERANGAN",
  izin_sekolah: "SURAT KETERANGAN IZIN",
  permohonan_izin: "SURAT PERMOHONAN IZIN",
};
const judulSurat = computed(() => {
  const j = jenis.value;
  if (judulMap[j]) return judulMap[j];
  // kalau jenis_surat sudah berupa kalimat panjang, pakai apa adanya
  const asli = String(data.value.jenis_surat || "Surat Keterangan");
  return asli.toUpperCase().startsWith("SURAT")
    ? asli.toUpperCase()
    : "SURAT KETERANGAN " + asli.toUpperCase();
});

// ====== Baris biodata standar (reusable) ======
const barisBio = (p) => `
  <table class="tabel-bio">
    <tr><td class="label-bio">Nama Lengkap</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(p.nama)}</td></tr>
    ${p.nik ? `<tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(p.nik)}</td></tr>` : ""}
    <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(p.tempat_lahir, p.tgl_lahir)}</td></tr>
    <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(p.jk)}</td></tr>
    ${p.agama ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(p.agama)}</td></tr>` : ""}
    ${p.pekerjaan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(p.pekerjaan)}</td></tr>` : ""}
    <tr><td class="label-bio">Alamat Lengkap</td><td class="titik">:</td><td class="value-bio">${val(p.alamat)}</td></tr>
  </table>`;

const pembuka = `<p class="paragraf">Yang Bertanda tangan dibawah ini Kepala Desa Macanan Kecamatan Loceret Kabupaten Nganjuk, Dengan ini Menerangkan bahwa :</p>`;
const penutupStd = `<p class="paragraf">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan seperlunya.</p>`;

// ============================================================
// Gaya 1:1 (inline, self-contained) — sesuai template docx asli desa.
// Dipakai template yang sudah disamakan persis. Helper ini bikin baris
// label : value yang lurus (kolom label 4.5cm, titik dua 0.5cm).
// ============================================================
const L1 = "width:4.5cm;white-space:nowrap;vertical-align:top;padding:1px 0;";
const S1 = "width:0.5cm;vertical-align:top;padding:1px 0;";
const V1 = "vertical-align:top;padding:1px 0;";
const VJ1 = "vertical-align:top;padding:5px 0;text-align:justify;";
const TBL1 =
  "border-collapse:collapse;margin-left:1.27cm;table-layout:fixed;width:calc(100% - 1.27cm);";
const pembuka1 = `<p style="margin:16px 0 10px;text-align:justify;text-indent:1.27cm;">Yang bertanda tangan dibawah ini Kepala Desa Macanan Kecamatan Loceret Kabupaten Nganjuk, Menerangkan dengan sebenarnya bahwa :</p>`;
const bio1 = (rows) =>
  `<table style="${TBL1}">` +
  rows
    .filter((r) => r)
    .map(
      ([lab, v]) =>
        `<tr><td style="${L1}">${lab}</td><td style="${S1}">:</td><td style="${V1}">${v}</td></tr>`,
    )
    .join("") +
  `</table>`;
const blok1 = (rows) =>
  `<table style="${TBL1}margin-top:10px;">` +
  rows
    .filter((r) => r)
    .map(
      ([lab, v]) =>
        `<tr><td style="${L1}">${lab}</td><td style="${S1}">:</td><td style="${VJ1}">${v}</td></tr>`,
    )
    .join("") +
  `</table>`;
const penutup1 = (txt) =>
  `<p style="text-align:justify;margin-top:12px;text-indent:1.27cm;">${txt}</p>`;

// ====== TEMPLATE per jenis (batch 1) ======
const templates = {
  // 1. DOMISILI
  // 1:1 sesuai docx "SURAT KETERANGAN DOMISILI" (028 MU'ALIM)
  domisili(i, p) {
    return `
      ${pembuka1}
      ${bio1([
        ["Nama", up(p.nama)],
        p.nik ? ["NIK", val(p.nik)] : null,
        ["Tempat, Tgl Lahir", formatTTL(p.tempat_lahir, p.tgl_lahir)],
        ["Jenis Kelamin", val(p.jk)],
        ["Golongan Darah", val(i.gol_darah)],
        ["Kewarganegaraan", p.kewarganegaraan || "WNI/Indonesia"],
        p.status_kawin ? ["Status Perkawinan", val(p.status_kawin)] : null,
        p.agama ? ["Agama", val(p.agama)] : null,
        p.pekerjaan ? ["Pekerjaan", val(p.pekerjaan)] : null,
        ["Alamat Lengkap", val(p.alamat)],
      ])}
      ${blok1([
        [
          "Menerangkan",
          `Bahwa orang tersebut diatas Sekarang Benar-benar Berdomisili di ${up(i.alamat_domisili)}.`,
        ],
        ["Keperluan", val(i.keperluan)],
      ])}
      ${penutup1(
        "Demikian surat keterangan ini dibuat dengan sebenar - benarnya dan dapat digunakan sebagaimana mestinya.",
      )}`;
  },

  // 2. SKTM
  sktm(i, p) {
    let anak = "";
    if (i.nama_anak) {
      anak = `
        <p class="paragraf">Surat keterangan ini diberikan untuk keperluan Anak / Wali dari yang bersangkutan:</p>
        <table class="tabel-bio">
          <tr><td class="label-bio">Nama Anak</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_anak)}</td></tr>
          <tr><td class="label-bio">Sekolah / Kampus</td><td class="titik">:</td><td class="value-bio">${val(i.sekolah_anak)}</td></tr>
          <tr><td class="label-bio">Kelas / Semester</td><td class="titik">:</td><td class="value-bio">${val(i.kelas_anak)}</td></tr>
        </table>`;
    }
    return `
      ${pembuka}
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Pemohon</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(p.nama)}</td></tr>
        <tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(p.nik)}</td></tr>
        ${i.no_kk ? `<tr><td class="label-bio">No KK</td><td class="titik">:</td><td class="value-bio">${val(i.no_kk)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(p.tempat_lahir, p.tgl_lahir)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(p.jk)}</td></tr>
        <tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(p.agama)}</td></tr>
        <tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(p.pekerjaan)}</td></tr>
        ${i.gaji_ortu ? `<tr><td class="label-bio">Penghasilan (Bulan)</td><td class="titik">:</td><td class="value-bio">${rupiah(i.gaji_ortu)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat Lengkap</td><td class="titik">:</td><td class="value-bio">${val(p.alamat)}</td></tr>
      </table>
      <p class="paragraf" style="text-align:justify;">Yang tersebut di atas adalah benar-benar penduduk Desa Macanan Kecamatan Loceret. Selanjutnya diterangkan bahwa yang bersangkutan tergolong <strong>Masyarakat Tidak Mampu (Miskin)</strong>.</p>
      ${anak}
      <p class="paragraf">Surat keterangan ini dibuat sebagai <strong>${up(i.tujuan_sktm || "PERSYARATAN ADMINISTRASI")}</strong>.</p>
      ${penutupStd}`;
  },

  // 3. USAHA
  usaha(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Yang tersebut diatas adalah benar-benar penduduk Desa Macanan</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Yang memiliki usaha</td><td class="titik">:</td><td class="value-bio">${val(i.jenis_usaha)} .</td></tr>
        <tr><td class="label-bio">Usaha tersebut telah dilakukan sejak</td><td class="titik">:</td><td class="value-bio">Tahun ${val(i.tahun_usaha)} sampai sekarang masih berjalan</td></tr>
        <tr><td class="label-bio">Lokasi Usaha berada di</td><td class="titik">:</td><td class="value-bio">${val(i.lokasi_usaha)}</td></tr>
      </table>
      ${penutupStd}`;
  },

  // 4. KELAHIRAN
  kelahiran(i, p) {
    return `
      <p class="paragraf">Yang bertanda tangan di bawah ini Kepala Desa Macanan, Kecamatan Loceret, Kabupaten Nganjuk, menerangkan dengan sebenarnya bahwa telah lahir seorang anak ${String(i.jk_bayi || "").toLowerCase()}:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Bayi</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;font-size:14pt;">${up(i.nama_bayi)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${up(i.jk_bayi)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_bayi, i.tgl_lahir_bayi)}</td></tr>
      </table>
      <p class="paragraf">Anak tersebut adalah anak kandung dari pasangan suami istri:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Ayah</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_ayah)}</td></tr>
        <tr><td class="label-bio">Nama Ibu</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_ibu)}</td></tr>
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(p.alamat)}</td></tr>
      </table>
      ${penutupStd}`;
  },

  // 5. SKCK (Kelakuan Baik)
  skck(i, p) {
    return `
      ${pembuka}
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama</td><td class="titik">:</td><td class="value-bio">${up(p.nama)}</td></tr>
        ${p.nik ? `<tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(p.nik)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(p.tempat_lahir, p.tgl_lahir)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(p.jk)}</td></tr>
        <tr><td class="label-bio">Kewarganegaraan</td><td class="titik">:</td><td class="value-bio">WNI/Indonesia</td></tr>
        ${p.agama ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(p.agama)}</td></tr>` : ""}
        ${i.pendidikan ? `<tr><td class="label-bio">Pendidikan Terakhir</td><td class="titik">:</td><td class="value-bio">${val(i.pendidikan)}</td></tr>` : ""}
        ${p.pekerjaan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(p.pekerjaan)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(p.alamat)}</td></tr>
      </table>
      <p class="paragraf">Adalah benar-benar penduduk Desa Macanan dengan Nomor Register Penduduk: <strong>${data.value.nomor_surat || "................"}</strong>. Bahwa yang tersebut di atas <strong>berkelakuan baik</strong> selama menjadi penduduk Desa Macanan.</p>
      <p class="paragraf">Surat Keterangan Kelakuan Baik ini dipergunakan untuk: <strong>${val(i.keperluan || "Mengurus Persyaratan Administrasi SKCK")}</strong>, dan berlaku selama satu bulan sejak dikeluarkannya surat keterangan ini.</p>
      ${penutupStd}`;
  },

  // 6. KEMATIAN
  kematian(i, p) {
    return `
      ${pembuka}
      <p class="paragraf">Bahwa telah meninggal dunia seorang penduduk Desa Macanan dengan data sebagai berikut:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Jenazah</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_jenazah)}</td></tr>
        ${i.nik_jenazah ? `<tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(i.nik_jenazah)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_jenazah, i.tgl_lahir_jenazah)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(i.jk_jenazah)}</td></tr>
        ${i.agama_jenazah ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(i.agama_jenazah)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_jenazah)}</td></tr>
      </table>
      <p class="paragraf">Telah meninggal dunia pada:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Hari / Tanggal</td><td class="titik">:</td><td class="value-bio">${i.tgl_meninggal ? tglIndo(i.tgl_meninggal) : "-"}</td></tr>
        <tr><td class="label-bio">Tempat</td><td class="titik">:</td><td class="value-bio">${val(i.tempat_meninggal)}</td></tr>
        <tr><td class="label-bio">Disebabkan</td><td class="titik">:</td><td class="value-bio">${val(i.penyebab)}</td></tr>
      </table>
      <p class="paragraf">Keterangan kematian ini dilaporkan oleh <strong>${up(p.nama)}</strong>${i.hubungan ? ` selaku <strong>${val(i.hubungan)}</strong> dari almarhum/almarhumah` : ""}.</p>
      ${penutupStd}`;
  },

  // 7. PINDAH
  pindah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar penduduk Desa Macanan dan akan pindah tempat tinggal ke alamat:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Alamat Tujuan</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.alamat_tujuan)}</td></tr>
        <tr><td class="label-bio">Desa / Kelurahan</td><td class="titik">:</td><td class="value-bio">${val(i.desa_tujuan)}</td></tr>
        <tr><td class="label-bio">Kecamatan</td><td class="titik">:</td><td class="value-bio">${val(i.kec_tujuan)}</td></tr>
        <tr><td class="label-bio">Kabupaten / Kota</td><td class="titik">:</td><td class="value-bio">${val(i.kab_tujuan)}</td></tr>
        <tr><td class="label-bio">Provinsi</td><td class="titik">:</td><td class="value-bio">${val(i.prov_tujuan)}</td></tr>
        <tr><td class="label-bio">Alasan Pindah</td><td class="titik">:</td><td class="value-bio">${val(i.alasan_pindah)}</td></tr>
        ${i.jumlah_pengikut ? `<tr><td class="label-bio">Jumlah Pengikut</td><td class="titik">:</td><td class="value-bio">${val(i.jumlah_pengikut)} orang</td></tr>` : ""}
      </table>
      ${penutupStd}`;
  },

  // 8. BEDA NAMA
  beda_nama(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa benar terdapat perbedaan <strong>${val(i.jenis_perbedaan)}</strong> atas nama orang yang sama, dengan rincian sebagai berikut:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Pada Dokumen</td><td class="titik">:</td><td class="value-bio">${val(i.dokumen_salah)}</td></tr>
        <tr><td class="label-bio">Tertulis</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.data_salah)}</td></tr>
        <tr><td class="label-bio">Pada Dokumen</td><td class="titik">:</td><td class="value-bio">${val(i.dokumen_benar)}</td></tr>
        <tr><td class="label-bio">Seharusnya</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.data_benar)}</td></tr>
      </table>
      <p class="paragraf">Bahwa kedua data tersebut adalah benar milik orang yang sama.</p>
      ${penutupStd}`;
  },

  // 9. SKIP (Identitas Penduduk)
  skip(i, p) {
    return `
      ${pembuka}
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Lengkap</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(p.nama)}</td></tr>
        ${p.nik ? `<tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(p.nik)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(p.tempat_lahir, p.tgl_lahir)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(p.jk)}</td></tr>
        ${p.agama ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(p.agama)}</td></tr>` : ""}
        ${p.status_kawin ? `<tr><td class="label-bio">Status Perkawinan</td><td class="titik">:</td><td class="value-bio">${val(p.status_kawin)}</td></tr>` : ""}
        ${p.pekerjaan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(p.pekerjaan)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(p.alamat)}</td></tr>
      </table>
      <p class="paragraf">Adalah benar-benar penduduk Desa Macanan dengan Nomor Register Penduduk: <strong>${data.value.nomor_surat || "................"}</strong>.</p>
      <p class="paragraf">Surat Keterangan ini dibuat untuk keperluan: <strong>${val(i.keperluan)}</strong>, dan berlaku selama tiga bulan sejak dikeluarkannya surat keterangan ini.</p>
      ${penutupStd}`;
  },

  // 10. NIKAH (Pengantar Nikah)
  nikah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar akan melangsungkan pernikahan dengan:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Calon Pasangan</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_pasangan)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_pasangan, i.tgl_lahir_pasangan)}</td></tr>
        ${i.agama_pasangan ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(i.agama_pasangan)}</td></tr>` : ""}
        ${i.pekerjaan_pasangan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.pekerjaan_pasangan)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_pasangan)}</td></tr>
      </table>
      <p class="paragraf">Pernikahan tersebut rencananya akan dilaksanakan di <strong>${val(i.tempat_nikah)}</strong>.</p>
      ${penutupStd}`;
  },

  // 11. NUMPANG NIKAH
  numpang_nikah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      ${i.no_kk ? `<table class="tabel-bio"><tr><td class="label-bio">No. Kartu Keluarga</td><td class="titik">:</td><td class="value-bio">${val(i.no_kk)}</td></tr></table>` : ""}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan benar penduduk Desa Macanan dan akan melangsungkan pernikahan dengan <strong>${up(i.nama_pasangan)}</strong>, serta bermaksud menumpang nikah di wilayah <strong>${val(i.alamat_tujuan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 12. BELUM MENIKAH
  belum_menikah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa menurut pengetahuan kami, yang bersangkutan sampai dengan dikeluarkannya surat ini benar-benar <strong>BELUM PERNAH MENIKAH</strong> (berstatus belum kawin / lajang).</p>
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 13. NIKAH SIRI
  nikah_siri(i, p) {
    return `
      ${pembuka}
      <p class="paragraf"><strong>PIHAK SUAMI / PEMOHON:</strong></p>
      ${barisBio(p)}
      <table class="tabel-bio">
        ${i.ayah_pemohon ? `<tr><td class="label-bio">Nama Ayah</td><td class="titik">:</td><td class="value-bio">${up(i.ayah_pemohon)}</td></tr>` : ""}
        ${i.status_pemohon ? `<tr><td class="label-bio">Status</td><td class="titik">:</td><td class="value-bio">${val(i.status_pemohon)}</td></tr>` : ""}
      </table>
      <p class="paragraf"><strong>PIHAK ISTRI / PASANGAN:</strong></p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_pasangan)}</td></tr>
        ${i.ayah_pasangan ? `<tr><td class="label-bio">Nama Ayah</td><td class="titik">:</td><td class="value-bio">${up(i.ayah_pasangan)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_pasangan, i.tgl_lahir_pasangan)}</td></tr>
        ${i.agama_pasangan ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(i.agama_pasangan)}</td></tr>` : ""}
        ${i.pekerjaan_pasangan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.pekerjaan_pasangan)}</td></tr>` : ""}
        ${i.status_kawin_pasangan ? `<tr><td class="label-bio">Status</td><td class="titik">:</td><td class="value-bio">${val(i.status_kawin_pasangan)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_pasangan)}</td></tr>
      </table>
      <p class="paragraf" style="text-align:justify;">Bahwa kedua belah pihak telah melangsungkan pernikahan secara siri pada <strong>${i.tgl_pelaksanaan ? tglIndo(i.tgl_pelaksanaan) : "-"}</strong>, dengan penghulu <strong>${val(i.nama_penghulu)}</strong>, disaksikan oleh <strong>${val(i.saksi_1)}</strong> dan <strong>${val(i.saksi_2)}</strong>.</p>
      ${penutupStd}`;
  },

  // 14. JANDA / DUDA
  janda_duda(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa menurut pengetahuan kami, yang bersangkutan benar berstatus <strong>${up(i.status_janda_duda)}</strong>${i.nama_pasangan ? ` setelah berpisah dari pasangan atas nama <strong>${up(i.nama_pasangan)}</strong>` : ""}.</p>
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 15. KETERANGAN TELAH MENIKAH
  ket_menikah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar telah menikah dengan:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Pasangan</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_pasangan)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_pasangan, i.tgl_lahir_pasangan)}</td></tr>
        ${i.pekerjaan_pasangan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.pekerjaan_pasangan)}</td></tr>` : ""}
      </table>
      <p class="paragraf" style="text-align:justify;">Pernikahan tersebut dilangsungkan pada <strong>${i.tgl_nikah ? tglIndo(i.tgl_nikah) : "-"}</strong> di <strong>${val(i.lokasi_nikah)}</strong>${i.no_register ? `, dengan Nomor Register <strong>${val(i.no_register)}</strong>` : ""}${i.nama_wali ? `, dan wali nikah <strong>${up(i.nama_wali)}</strong>` : ""}.</p>
      ${penutupStd}`;
  },

  // 16. CERAI GHAIB
  cerai_goib(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa pasangan dari yang bersangkutan:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_pasangan)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_pasangan, i.tgl_lahir_pasangan)}</td></tr>
        ${i.agama_pasangan ? `<tr><td class="label-bio">Agama</td><td class="titik">:</td><td class="value-bio">${val(i.agama_pasangan)}</td></tr>` : ""}
        ${i.pekerjaan_pasangan ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.pekerjaan_pasangan)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_pasangan)}</td></tr>
      </table>
      <p class="paragraf" style="text-align:justify;">Telah pergi meninggalkan tempat tinggal (ghaib) sejak <strong>${val(i.bulan_pergi)} ${val(i.tahun_pergi)}</strong> selama kurang lebih <strong>${val(i.lama_pergi)}</strong> dan sampai saat ini tidak diketahui keberadaannya. Status: <strong>${val(i.status_pasangan_hilang)}</strong>.</p>
      ${penutupStd}`;
  },

  // 17. WALI
  wali(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar bertindak sebagai <strong>WALI</strong>${i.hubungan ? ` (selaku ${val(i.hubungan)})` : ""} dari:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Anak</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_anak)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_anak, i.tgl_lahir_anak)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(i.jk_anak)}</td></tr>
      </table>
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 18. AHLI WARIS
  ahli_waris(i, p) {
    return `
      ${pembuka}
      <p class="paragraf" style="text-align:justify;">Bahwa benar telah meninggal dunia seorang penduduk Desa Macanan atas nama <strong>${up(i.nama_pewaris)}</strong> pada <strong>${i.tgl_meninggal ? tglIndo(i.tgl_meninggal) : "-"}</strong>, dan meninggalkan ahli waris sejumlah <strong>${val(i.jumlah_waris)} orang</strong>.</p>
      <p class="paragraf">Keterangan ahli waris ini dilaporkan oleh:</p>
      ${barisBio(p)}
      ${penutupStd}`;
  },

  // 19. PENGHASILAN ORANG TUA
  penghasilan(i, p) {
    return `
      ${pembuka}
      <p class="paragraf">Data orang tua / wali:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Ayah</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_ayah)}</td></tr>
        <tr><td class="label-bio">Pekerjaan Ayah</td><td class="titik">:</td><td class="value-bio">${val(i.kerja_ayah)}</td></tr>
        <tr><td class="label-bio">Penghasilan Ayah</td><td class="titik">:</td><td class="value-bio">${i.gaji_ayah ? rupiah(i.gaji_ayah) + " / bulan" : "-"}</td></tr>
        <tr><td class="label-bio">Nama Ibu</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_ibu)}</td></tr>
        <tr><td class="label-bio">Pekerjaan Ibu</td><td class="titik">:</td><td class="value-bio">${val(i.kerja_ibu)}</td></tr>
        <tr><td class="label-bio">Penghasilan Ibu</td><td class="titik">:</td><td class="value-bio">${i.gaji_ibu ? rupiah(i.gaji_ibu) + " / bulan" : "-"}</td></tr>
      </table>
      <p class="paragraf">Adalah benar orang tua dari:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama Anak</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_anak)}</td></tr>
        ${i.nik_anak ? `<tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(i.nik_anak)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_anak, i.tgl_lahir_anak)}</td></tr>
        <tr><td class="label-bio">Jenis Kelamin</td><td class="titik">:</td><td class="value-bio">${val(i.jk_anak)}</td></tr>
      </table>
      <p class="paragraf">Surat keterangan penghasilan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 20. JUAL BELI
  jual_beli(i, p) {
    const tanah =
      i.asal_tanah || i.luas_dijual || i.shm_tanah || i.c_desa || i.blok_tanah
        ? `
      <p class="paragraf">Obyek berupa <strong>tanah</strong> dengan data:</p>
      <table class="tabel-bio">
        ${i.asal_tanah ? `<tr><td class="label-bio">Asal Tanah</td><td class="titik">:</td><td class="value-bio">${val(i.asal_tanah)}</td></tr>` : ""}
        ${i.luas_dijual ? `<tr><td class="label-bio">Luas Dijual</td><td class="titik">:</td><td class="value-bio">${val(i.luas_dijual)}</td></tr>` : ""}
        ${i.blok_tanah ? `<tr><td class="label-bio">Blok / Persil</td><td class="titik">:</td><td class="value-bio">${val(i.blok_tanah)}</td></tr>` : ""}
        ${i.shm_tanah ? `<tr><td class="label-bio">No. SHM</td><td class="titik">:</td><td class="value-bio">${val(i.shm_tanah)}</td></tr>` : ""}
        ${i.c_desa ? `<tr><td class="label-bio">No. C Desa</td><td class="titik">:</td><td class="value-bio">${val(i.c_desa)}</td></tr>` : ""}
        ${i.batas_utara ? `<tr><td class="label-bio">Batas Utara</td><td class="titik">:</td><td class="value-bio">${val(i.batas_utara)}</td></tr>` : ""}
        ${i.batas_timur ? `<tr><td class="label-bio">Batas Timur</td><td class="titik">:</td><td class="value-bio">${val(i.batas_timur)}</td></tr>` : ""}
        ${i.batas_selatan ? `<tr><td class="label-bio">Batas Selatan</td><td class="titik">:</td><td class="value-bio">${val(i.batas_selatan)}</td></tr>` : ""}
        ${i.batas_barat ? `<tr><td class="label-bio">Batas Barat</td><td class="titik">:</td><td class="value-bio">${val(i.batas_barat)}</td></tr>` : ""}
      </table>`
        : "";
    const kendaraan =
      i.merk || i.nopol || i.no_rangka || i.no_bpkb
        ? `
      <p class="paragraf">Obyek berupa <strong>kendaraan</strong> dengan data:</p>
      <table class="tabel-bio">
        ${i.merk ? `<tr><td class="label-bio">Merk / Tipe</td><td class="titik">:</td><td class="value-bio">${val(i.merk)}</td></tr>` : ""}
        ${i.tahun ? `<tr><td class="label-bio">Tahun</td><td class="titik">:</td><td class="value-bio">${val(i.tahun)}</td></tr>` : ""}
        ${i.nopol ? `<tr><td class="label-bio">No. Polisi</td><td class="titik">:</td><td class="value-bio">${val(i.nopol)}</td></tr>` : ""}
        ${i.no_rangka ? `<tr><td class="label-bio">No. Rangka</td><td class="titik">:</td><td class="value-bio">${val(i.no_rangka)}</td></tr>` : ""}
        ${i.no_mesin ? `<tr><td class="label-bio">No. Mesin</td><td class="titik">:</td><td class="value-bio">${val(i.no_mesin)}</td></tr>` : ""}
        ${i.no_bpkb ? `<tr><td class="label-bio">No. BPKB</td><td class="titik">:</td><td class="value-bio">${val(i.no_bpkb)}</td></tr>` : ""}
        ${i.atas_nama ? `<tr><td class="label-bio">Atas Nama</td><td class="titik">:</td><td class="value-bio">${val(i.atas_nama)}</td></tr>` : ""}
        ${i.spek_lainnya ? `<tr><td class="label-bio">Keterangan Lain</td><td class="titik">:</td><td class="value-bio">${val(i.spek_lainnya)}</td></tr>` : ""}
      </table>`
        : "";
    const saksi =
      i.saksi_1 || i.saksi_2 || i.saksi_3
        ? `<p class="paragraf">Saksi-saksi: ${[i.saksi_1, i.saksi_2, i.saksi_3].filter(Boolean).join(", ")}.</p>`
        : "";
    return `
      ${pembuka}
      <p class="paragraf"><strong>PIHAK PERTAMA (Penjual):</strong></p>
      ${barisBio(p)}
      <p class="paragraf"><strong>PIHAK KEDUA (Pembeli):</strong></p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_pembeli)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_pembeli, i.tgl_lahir_pembeli)}</td></tr>
        ${i.kerja_pembeli ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.kerja_pembeli)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_pembeli)}</td></tr>
      </table>
      <p class="paragraf">Telah terjadi jual beli atas obyek <strong>${val(i.jenis_obyek)}</strong> sebagai berikut:</p>
      ${tanah}
      ${kendaraan}
      <p class="paragraf">Dengan harga sebesar <strong>${i.harga_jual ? rupiah(i.harga_jual) : "-"}</strong>${i.terbilang_harga ? ` (<em>${val(i.terbilang_harga)}</em>)` : ""}.</p>
      ${saksi}
      ${penutupStd}`;
  },

  // 21. KEPEMILIKAN KENDARAAN
  milik_kendaraan(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar memiliki kendaraan dengan data sebagai berikut:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Merk / Tipe</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.merk)}</td></tr>
        <tr><td class="label-bio">Tahun Pembuatan</td><td class="titik">:</td><td class="value-bio">${val(i.tahun)}</td></tr>
        ${i.isi_silinder ? `<tr><td class="label-bio">Isi Silinder</td><td class="titik">:</td><td class="value-bio">${val(i.isi_silinder)}</td></tr>` : ""}
        <tr><td class="label-bio">No. Rangka</td><td class="titik">:</td><td class="value-bio">${val(i.no_rangka)}</td></tr>
        <tr><td class="label-bio">No. Mesin</td><td class="titik">:</td><td class="value-bio">${val(i.no_mesin)}</td></tr>
        <tr><td class="label-bio">No. Polisi</td><td class="titik">:</td><td class="value-bio">${val(i.nopol)}</td></tr>
        ${i.no_bpkb ? `<tr><td class="label-bio">No. BPKB</td><td class="titik">:</td><td class="value-bio">${val(i.no_bpkb)}</td></tr>` : ""}
        <tr><td class="label-bio">Atas Nama</td><td class="titik">:</td><td class="value-bio">${val(i.atas_nama)}</td></tr>
      </table>
      ${penutupStd}`;
  },

  // 22. KEPEMILIKAN TANAH
  milik_tanah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar memiliki sebidang tanah dengan data sebagai berikut:</p>
      <table class="tabel-bio">
        ${i.no_sertifikat ? `<tr><td class="label-bio">No. Sertifikat</td><td class="titik">:</td><td class="value-bio">${val(i.no_sertifikat)}</td></tr>` : ""}
        ${i.surat_ukur ? `<tr><td class="label-bio">Surat Ukur</td><td class="titik">:</td><td class="value-bio">${val(i.surat_ukur)}</td></tr>` : ""}
        <tr><td class="label-bio">Atas Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.nama_sertifikat)}</td></tr>
        <tr><td class="label-bio">Luas Tanah</td><td class="titik">:</td><td class="value-bio">${val(i.luas_tanah)}</td></tr>
        <tr><td class="label-bio">Batas Utara</td><td class="titik">:</td><td class="value-bio">${val(i.batas_utara)}</td></tr>
        <tr><td class="label-bio">Batas Timur</td><td class="titik">:</td><td class="value-bio">${val(i.batas_timur)}</td></tr>
        <tr><td class="label-bio">Batas Selatan</td><td class="titik">:</td><td class="value-bio">${val(i.batas_selatan)}</td></tr>
        <tr><td class="label-bio">Batas Barat</td><td class="titik">:</td><td class="value-bio">${val(i.batas_barat)}</td></tr>
        ${i.harga_tanah ? `<tr><td class="label-bio">Taksiran Harga Tanah</td><td class="titik">:</td><td class="value-bio">${rupiah(i.harga_tanah)}</td></tr>` : ""}
        ${i.harga_bangunan ? `<tr><td class="label-bio">Taksiran Harga Bangunan</td><td class="titik">:</td><td class="value-bio">${rupiah(i.harga_bangunan)}</td></tr>` : ""}
      </table>
      ${penutupStd}`;
  },

  // 23. TANAH TIDAK SENGKETA
  tanah_sengketa(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa sebidang tanah dengan data berikut benar-benar <strong>TIDAK DALAM SENGKETA</strong> dengan pihak manapun:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Lokasi Tanah</td><td class="titik">:</td><td class="value-bio">${val(i.lokasi_tanah)}</td></tr>
        <tr><td class="label-bio">Luas Tanah</td><td class="titik">:</td><td class="value-bio">${val(i.luas_tanah)}</td></tr>
        <tr><td class="label-bio">Bukti Kepemilikan</td><td class="titik">:</td><td class="value-bio">${val(i.bukti_kepemilikan)}</td></tr>
        <tr><td class="label-bio">Atas Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.atas_nama)}</td></tr>
      </table>
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 24. KETERANGAN PEKERJAAN
  ket_pekerjaan(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      ${p.kewarganegaraan ? `<table class="tabel-bio"><tr><td class="label-bio">Kewarganegaraan</td><td class="titik">:</td><td class="value-bio">${val(p.kewarganegaraan)}</td></tr></table>` : ""}
      <p class="paragraf" style="text-align:justify;">Bahwa benar yang bersangkutan bekerja sebagai <strong>${up(i.pekerjaan_baru)}</strong>.</p>
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 25. PENGALAMAN KERJA
  pengalaman_kerja(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan benar memiliki pengalaman kerja sebagai berikut:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Jabatan</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.jabatan_kerja)}</td></tr>
        <tr><td class="label-bio">Mulai Bekerja</td><td class="titik">:</td><td class="value-bio">${i.tgl_mulai_kerja ? tglIndo(i.tgl_mulai_kerja) : "-"}</td></tr>
        <tr><td class="label-bio">Selesai Bekerja</td><td class="titik">:</td><td class="value-bio">${i.tgl_selesai_kerja ? tglIndo(i.tgl_selesai_kerja) : "-"}</td></tr>
      </table>
      ${penutupStd}`;
  },

  // 26. KEHILANGAN
  kehilangan(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan benar telah kehilangan <strong>${val(i.barang_hilang)}</strong>${i.rincian_barang ? ` dengan rincian: ${val(i.rincian_barang)}` : ""}.</p>
      ${i.kronologi ? `<p class="paragraf" style="text-align:justify;">Kronologi kejadian: ${val(i.kronologi)}.</p>` : ""}
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan_surat)}</strong>.</p>
      ${penutupStd}`;
  },

  // 27. KUASA BPKB
  kuasa_bpkb(i, p) {
    return `
      <p class="paragraf">Yang bertanda tangan di bawah ini, selaku <strong>PEMBERI KUASA</strong>:</p>
      ${barisBio(p)}
      <p class="paragraf">Dengan ini memberikan kuasa kepada, selaku <strong>PENERIMA KUASA</strong>:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_penerima)}</td></tr>
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_penerima, i.tgl_lahir_penerima)}</td></tr>
        ${i.pekerjaan_penerima ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.pekerjaan_penerima)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_penerima)}</td></tr>
      </table>
      <p class="paragraf"><strong>KHUSUS</strong> untuk ${val(i.tujuan_kuasa || "pengurusan BPKB")} atas kendaraan:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Merk / Tipe</td><td class="titik">:</td><td class="value-bio">${val(i.merk)}</td></tr>
        ${i.tahun ? `<tr><td class="label-bio">Tahun</td><td class="titik">:</td><td class="value-bio">${val(i.tahun)}</td></tr>` : ""}
        ${i.isi_silinder ? `<tr><td class="label-bio">Isi Silinder</td><td class="titik">:</td><td class="value-bio">${val(i.isi_silinder)}</td></tr>` : ""}
        <tr><td class="label-bio">No. Rangka</td><td class="titik">:</td><td class="value-bio">${val(i.no_rangka)}</td></tr>
        <tr><td class="label-bio">No. Mesin</td><td class="titik">:</td><td class="value-bio">${val(i.no_mesin)}</td></tr>
        <tr><td class="label-bio">No. Polisi</td><td class="titik">:</td><td class="value-bio">${val(i.nopol)}</td></tr>
        ${i.no_bpkb ? `<tr><td class="label-bio">No. BPKB</td><td class="titik">:</td><td class="value-bio">${val(i.no_bpkb)}</td></tr>` : ""}
        <tr><td class="label-bio">Atas Nama</td><td class="titik">:</td><td class="value-bio">${val(i.atas_nama)}</td></tr>
      </table>
      <p class="paragraf">Demikian surat kuasa ini dibuat dengan sebenarnya untuk dapat dipergunakan seperlunya.</p>`;
  },

  // 28. KUASA (Umum)
  kuasa(i, p) {
    return `
      <p class="paragraf">Yang bertanda tangan di bawah ini, selaku <strong>PEMBERI KUASA</strong>:</p>
      ${barisBio(p)}
      <p class="paragraf">Dengan ini memberikan kuasa kepada, selaku <strong>PENERIMA KUASA</strong>:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Nama</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${up(i.nama_penerima)}</td></tr>
        ${i.nik_penerima ? `<tr><td class="label-bio">NIK</td><td class="titik">:</td><td class="value-bio">${val(i.nik_penerima)}</td></tr>` : ""}
        <tr><td class="label-bio">Tempat, Tgl Lahir</td><td class="titik">:</td><td class="value-bio">${formatTTL(i.tempat_lahir_penerima, i.tgl_lahir_penerima)}</td></tr>
        ${i.pekerjaan_penerima ? `<tr><td class="label-bio">Pekerjaan</td><td class="titik">:</td><td class="value-bio">${val(i.pekerjaan_penerima)}</td></tr>` : ""}
        <tr><td class="label-bio">Alamat</td><td class="titik">:</td><td class="value-bio">${val(i.alamat_penerima)}</td></tr>
      </table>
      <p class="paragraf" style="text-align:justify;"><strong>KHUSUS</strong> untuk: ${val(i.tujuan_kuasa)}.</p>
      <p class="paragraf">Demikian surat kuasa ini dibuat dengan sebenarnya untuk dapat dipergunakan seperlunya.</p>`;
  },

  // 29. IZIN KERAMAIAN
  keramaian(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf">Bahwa yang bersangkutan akan menyelenggarakan acara dengan rincian:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Jenis Acara</td><td class="titik">:</td><td class="value-bio" style="font-weight:bold;">${val(i.jenis_acara)}</td></tr>
        <tr><td class="label-bio">Hari</td><td class="titik">:</td><td class="value-bio">${val(i.hari_acara)}</td></tr>
        <tr><td class="label-bio">Tanggal</td><td class="titik">:</td><td class="value-bio">${i.tgl_acara ? tglIndo(i.tgl_acara) : "-"}</td></tr>
        <tr><td class="label-bio">Tempat</td><td class="titik">:</td><td class="value-bio">${val(i.tempat_acara)}</td></tr>
      </table>
      <p class="paragraf">Sehubungan dengan hal tersebut, kami tidak keberatan dan memberikan izin atas penyelenggaraan acara dimaksud.</p>
      ${penutupStd}`;
  },

  // 30. BEPERGIAN
  bepergian(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan benar penduduk Desa Macanan dan akan bepergian ke <strong>${up(i.kota_tujuan)}</strong> selama <strong>${val(i.lama_pergi)}</strong> dengan keperluan <strong>${val(i.keperluan_pergi)}</strong>.</p>
      ${penutupStd}`;
  },

  // 31. TIDAK DI RUMAH / TIDAK DI TEMPAT
  tidak_dirumah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan saat ini sedang tidak berada di tempat/rumah dikarenakan <strong>${val(i.alasan)}</strong>, dan saat ini berada di <strong>${val(i.lokasi_sekarang)}</strong>.</p>
      <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 32. KETERANGAN UMUM
  ket_umum(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      ${p.kewarganegaraan ? `<table class="tabel-bio"><tr><td class="label-bio">Kewarganegaraan</td><td class="titik">:</td><td class="value-bio">${val(p.kewarganegaraan)}</td></tr></table>` : ""}
      <p class="paragraf" style="text-align:justify;">${val(i.isi_keterangan)}</p>
      ${penutupStd}`;
  },

  // 33. IZIN SEKOLAH
  izin_sekolah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan adalah siswa/i <strong>${val(i.nama_sekolah)}</strong>${i.kelas ? ` kelas ${val(i.kelas)}` : ""}, dan mengajukan izin tidak masuk sekolah selama <strong>${val(i.lama_izin)}</strong> dikarenakan <strong>${val(i.alasan_izin)}</strong>.</p>
      ${penutupStd}`;
  },

  // 34. DOMISILI SEKOLAH
  domisili_sekolah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan benar berdomisili di Desa Macanan, Kecamatan Loceret, Kabupaten Nganjuk. Surat keterangan domisili ini dibuat untuk keperluan pendaftaran / administrasi di <strong>${val(i.sekolah_tujuan)}</strong>.</p>
      ${penutupStd}`;
  },

  // 35. PINJAM TANAH
  pinjam_tanah(i, p) {
    return `
      ${pembuka}
      ${barisBio(p)}
      <p class="paragraf" style="text-align:justify;">Bahwa yang bersangkutan ${val(i.kepemilikan)} atas sebidang tanah yang dipinjamkan/digunakan oleh <strong>${up(i.nama_yayasan)}</strong>${i.alamat_yayasan ? ` (${val(i.alamat_yayasan)})` : ""}, dengan ukuran sebagai berikut:</p>
      <table class="tabel-bio">
        <tr><td class="label-bio">Ukuran Tanah</td><td class="titik">:</td><td class="value-bio">${val(i.panjang_tanah)} x ${val(i.lebar_tanah)} m</td></tr>
        ${i.panjang_bangunan || i.lebar_bangunan ? `<tr><td class="label-bio">Ukuran Bangunan</td><td class="titik">:</td><td class="value-bio">${val(i.panjang_bangunan)} x ${val(i.lebar_bangunan)} m</td></tr>` : ""}
      </table>
      ${penutupStd}`;
  },
};

// Template generik (fallback kalau jenis belum dibuat)
const templateGenerik = (i, p) => `
  ${pembuka}
  ${barisBio(p)}
  <p class="paragraf">Surat keterangan ini dibuat untuk keperluan <strong>${val(i.keperluan || i.keperluan_surat || data.value.keterangan || "keperluan administrasi")}</strong>.</p>
  ${penutupStd}`;

const isiSurat = computed(() => {
  const { i, p } = parsed.value;
  const fn = templates[jenis.value];
  try {
    return fn ? fn(i, p) : templateGenerik(i, p);
  } catch (e) {
    return templateGenerik(i, p);
  }
});

const tglSurat = computed(() => {
  const iso = data.value.tgl_persetujuan || data.value.updated_at;
  return tglIndo(iso || new Date().toISOString());
});

const fetchSurat = async () => {
  loading.value = true;
  error.value = "";
  try {
    const id = route.params.id;
    const res = await axios.get(`${BASE}/pelayanan/${id}`);
    data.value = res.data?.data || res.data || {};
    if (!data.value || !data.value.id)
      error.value = "Data surat tidak ditemukan.";
  } catch (e) {
    console.error(e);
    error.value = "Gagal memuat data surat.";
  } finally {
    loading.value = false;
  }
};

const cetak = () => window.print();
const kembali = () => router.back();

// Pastikan token terpasang sebelum request apa pun.
const tokenAwal = localStorage.getItem("token");
if (tokenAwal)
  axios.defaults.headers.common["Authorization"] = `Bearer ${tokenAwal}`;

// Ambil data surat begitu komponen siap DAN setiap id berubah.
// immediate:true → langsung jalan di klik pertama, tak perlu refresh manual.
watch(
  () => route.params.id,
  (id) => {
    if (id) fetchSurat();
  },
  { immediate: true },
);

onMounted(() => {
  muatPengaturanKades();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.cetak-page {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: #e2e8f0;
  min-height: 100vh;
  padding: 90px 20px 40px;
  box-sizing: border-box;
}
.toolbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 64px;
  background: #1e293b;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  z-index: 100;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.toolbar-info {
  color: #e2e8f0;
  font-weight: 700;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.toolbar-info i {
  color: #34d399;
}
.btn-kembali,
.btn-cetak {
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: inherit;
  transition: 0.2s;
}
.btn-kembali {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}
.btn-kembali:hover {
  background: rgba(255, 255, 255, 0.2);
}
.btn-cetak {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
}
.btn-cetak:hover {
  transform: translateY(-1px);
}
.loading-state,
.error-state {
  text-align: center;
  padding: 60px;
  color: #475569;
  font-weight: 600;
}
.error-state i {
  color: #dc2626;
}

.surat-a4 {
  background: #fff;
  width: 100%;
  min-height: 14in;
  max-width: 8.5in;
  margin: 0 auto;
  padding: 0.69in 1in 1in 1in;
  box-sizing: border-box;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  color: #000;
  font-family: "Times New Roman", Times, serif;
  font-size: 12pt;
  line-height: 1.2;
}
.kop {
  position: relative;
  text-align: center;
  line-height: 1.05;
}
.kop-logo-slot {
  position: absolute;
  left: 0;
  top: 0;
  width: 90px;
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.kop-logo {
  width: 85px;
  height: 85px;
  object-fit: contain;
}
.kop-teks h3 {
  margin: 0;
  font-size: 18pt;
  font-weight: normal;
}
.kop-teks h1 {
  margin: 1px 0;
  font-size: 22pt;
  font-weight: normal;
  letter-spacing: 0.5px;
}
.kop-teks p {
  margin: 1px 0 0;
  font-size: 12pt;
}
.kop-teks .email {
  font-style: italic;
}
.kop-garis {
  border-bottom: 3px solid #000;
  margin: 8px 0 4px;
}
.kop-garis::after {
  content: "";
  display: block;
  border-bottom: 1px solid #000;
  margin-top: 2px;
}
.judul-surat {
  text-align: center;
  margin: 14px 0 16px;
}
.judul-surat h2 {
  margin: 0;
  font-size: 18pt;
  font-weight: bold;
  text-decoration: underline;
  letter-spacing: 0;
}
.judul-surat .nomor {
  margin: 2px 0 0;
  font-size: 12pt;
}
.isi-surat {
  text-align: justify;
}
/* Gaya isi surat 1:1 docx — berlaku untuk SEMUA template */
:deep(.paragraf) {
  margin: 8px 0;
  text-align: justify;
  text-indent: 1.27cm;
}
:deep(.tabel-bio) {
  margin: 4px 0 4px 1.27cm;
  border-collapse: collapse;
  table-layout: fixed;
  width: calc(100% - 1.27cm);
}
:deep(.tabel-bio td) {
  padding: 1px 0;
  vertical-align: top;
  font-size: 12pt;
}
:deep(.label-bio) {
  width: 4.5cm;
  white-space: nowrap;
}
:deep(.titik) {
  width: 0.5cm;
  text-align: left;
}
:deep(.value-bio) {
  font-weight: normal;
}
.ttd-area {
  display: flex;
  justify-content: flex-end;
  margin-top: 24px;
}
.ttd-box {
  text-align: center;
  width: 280px;
}
.ttd-box p {
  margin: 2px 0;
}
.ttd-ruang {
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.ttd-gambar {
  max-height: 90px;
  max-width: 200px;
  object-fit: contain;
}
.nama-kades {
  font-weight: 700;
  text-decoration: underline;
}
.nip-kades {
  font-weight: normal;
}

@media (max-width: 768px) {
  .toolbar {
    flex-direction: column;
    gap: 10px;
    padding: 12px 15px;
  }
  .btn-kembali,
  .btn-cetak {
    width: 100%;
    justify-content: center;
  }
  .surat-a4 {
    padding: 15mm 12mm;
  }
}

@media (max-width: 360px) {
  .kop-teks h1 {
    font-size: 14pt;
  }
  .kop-teks h3 {
    font-size: 10pt;
  }
}

@media print {
  @page {
    size: 8.5in 14in;
    margin: 0;
  }
  html,
  body {
    margin: 0 !important;
    padding: 0 !important;
    height: auto !important;
    background: #fff !important;
  }
  .no-print {
    display: none !important;
  }
  .cetak-page {
    padding: 0 !important;
    margin: 0 !important;
    min-height: 0 !important;
    background: #fff;
  }
  .surat-a4 {
    box-shadow: none;
    width: 100%;
    max-width: none;
    min-height: 0 !important;
    margin: 0;
    padding: 0.69in 1in 1in 1in;
    page-break-after: avoid;
    page-break-inside: avoid;
  }
}

@media (max-width: 480px) {
  .cetak-page {
    padding: 80px 8px 30px;
  }
  .surat-a4 {
    padding: 15mm 12mm;
    font-size: 11pt;
  }
  .kop-teks h1 {
    font-size: 16pt;
  }
  .kop-teks h3 {
    font-size: 11pt;
  }
  :deep(.label-bio) {
    width: 130px;
  }
  .toolbar {
    padding: 0 12px;
  }
  .toolbar-info {
    display: none;
  }
}
</style>
