<template>
  <div class="pengaturan-body">
    <div class="pengaturan-wrapper">
      <div class="page-header-box fade-up">
        <div class="header-text">
          <h2><i class="fa-solid fa-gear"></i> Pengaturan Surat</h2>
          <p>
            Atur kop surat, nama &amp; tanda tangan Kepala Desa. Dipakai
            otomatis di semua surat — ganti Kades cukup di sini.
          </p>
        </div>
        <button class="btn-back-glass" @click="router.push('/admin')">
          <i class="fa-solid fa-arrow-left"></i> Dashboard
        </button>
      </div>

      <form class="kartu fade-up delay-1" @submit.prevent="simpan">
        <h3 class="sub"><i class="fa-solid fa-id-card"></i> Kepala Desa</h3>
        <div class="grid">
          <div class="grup">
            <label>Nama Kepala Desa</label>
            <input v-model="form.nama_kades" type="text" placeholder="MOHAMAT SOIB, S.Pd." />
          </div>
          <div class="grup">
            <label>Jabatan</label>
            <input v-model="form.jabatan_kades" type="text" placeholder="KEPALA DESA MACANAN" />
          </div>
          <div class="grup">
            <label>NIP (opsional)</label>
            <input v-model="form.nip_kades" type="text" placeholder="-" />
          </div>
        </div>

        <div class="grid">
          <div class="grup">
            <label>Tanda Tangan Kepala Desa
              <small>(PNG latar transparan disarankan)</small></label>
            <label class="upload-box cek-transparan">
              <img v-if="previewTtd" :src="previewTtd" alt="Tanda tangan" />
              <div v-else class="upload-empty">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Klik untuk unggah tanda tangan</span>
                <small>Format PNG / JPG</small>
              </div>
              <span v-if="previewTtd" class="upload-ganti">
                <i class="fa-solid fa-pen"></i> Ganti
              </span>
              <input type="file" accept="image/*" @change="pilihTtd" hidden />
            </label>
          </div>

          <div class="grup">
            <label>Logo Desa <small>(opsional)</small></label>
            <label class="upload-box">
              <img v-if="previewLogo" :src="previewLogo" alt="Logo desa" />
              <div v-else class="upload-empty">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Klik untuk unggah logo</span>
                <small>Format PNG / JPG</small>
              </div>
              <span v-if="previewLogo" class="upload-ganti">
                <i class="fa-solid fa-pen"></i> Ganti
              </span>
              <input type="file" accept="image/*" @change="pilihLogo" hidden />
            </label>
          </div>
        </div>

        <h3 class="sub"><i class="fa-solid fa-building-columns"></i> Kop Surat</h3>
        <div class="grid">
          <div class="grup">
            <label>Baris 1 (Kabupaten)</label>
            <input v-model="form.nama_kabupaten" type="text" />
          </div>
          <div class="grup">
            <label>Baris 2 (Kecamatan)</label>
            <input v-model="form.nama_kecamatan" type="text" />
          </div>
          <div class="grup">
            <label>Baris 3 (Desa)</label>
            <input v-model="form.nama_desa" type="text" />
          </div>
          <div class="grup full">
            <label>Alamat</label>
            <input v-model="form.alamat_desa" type="text" />
          </div>
          <div class="grup">
            <label>Email</label>
            <input v-model="form.email_desa" type="text" />
          </div>
          <div class="grup">
            <label>Kode Surat (mis. 411.508.21)</label>
            <input v-model="form.kode_surat" type="text" />
          </div>
        </div>

        <div class="aksi">
          <button type="submit" class="btn-simpan" :disabled="loading">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ loading ? "Menyimpan..." : "Simpan Pengaturan" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const BASE = `${import.meta.env.VITE_API_URL}/api`;
const STORAGE = `${import.meta.env.VITE_API_URL}/storage/`;

const loading = ref(false);
const form = ref({
  nama_kades: "",
  jabatan_kades: "",
  nip_kades: "",
  nama_kabupaten: "",
  nama_kecamatan: "",
  nama_desa: "",
  alamat_desa: "",
  email_desa: "",
  kode_surat: "",
});
const fileTtd = ref(null);
const fileLogo = ref(null);
const previewTtd = ref("");
const previewLogo = ref("");

const pilihTtd = (e) => {
  const f = e.target.files[0];
  if (f) {
    fileTtd.value = f;
    previewTtd.value = URL.createObjectURL(f);
  }
};
const pilihLogo = (e) => {
  const f = e.target.files[0];
  if (f) {
    fileLogo.value = f;
    previewLogo.value = URL.createObjectURL(f);
  }
};

const muat = async () => {
  try {
    const res = await axios.get(`${BASE}/pengaturan`);
    const d = res.data?.data || {};
    Object.keys(form.value).forEach((k) => {
      if (d[k] !== undefined && d[k] !== null) form.value[k] = d[k];
    });
    if (d.ttd_kades) previewTtd.value = STORAGE + d.ttd_kades;
    if (d.logo_desa) previewLogo.value = STORAGE + d.logo_desa;
  } catch (e) {
    console.error("Gagal memuat pengaturan:", e);
  }
};

const simpan = async () => {
  loading.value = true;
  try {
    const fd = new FormData();
    Object.keys(form.value).forEach((k) => fd.append(k, form.value[k] ?? ""));
    if (fileTtd.value) fd.append("ttd_kades", fileTtd.value);
    if (fileLogo.value) fd.append("logo_desa", fileLogo.value);

    const res = await axios.post(`${BASE}/pengaturan`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    const d = res.data?.data || {};

    // Cache ke localStorage untuk dipakai cepat oleh halaman cetak
    localStorage.setItem(
      "pengaturan_kades",
      JSON.stringify({
        nama: d.nama_kades,
        jabatan: d.jabatan_kades,
        nip: d.nip_kades,
        ttd: d.ttd_kades ? STORAGE + d.ttd_kades : "",
        logo: d.logo_desa ? STORAGE + d.logo_desa : "",
        kop: {
          kabupaten: d.nama_kabupaten,
          kecamatan: d.nama_kecamatan,
          desa: d.nama_desa,
          alamat: d.alamat_desa,
          email: d.email_desa,
          kode_surat: d.kode_surat,
        },
      }),
    );

    await Swal.fire({
      icon: "success",
      title: "Tersimpan!",
      text: "Pengaturan surat berhasil diperbarui.",
      confirmButtonColor: "#059669",
      timer: 1600,
      showConfirmButton: false,
    });
  } catch (e) {
    console.error(e);
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: e.response?.data?.message || "Gagal menyimpan pengaturan.",
      confirmButtonColor: "#dc2626",
    });
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  muat();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.pengaturan-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: #f8fafc;
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.pengaturan-wrapper {
  max-width: 95%;
  margin: 0 auto;
}
.page-header-box {
  background: linear-gradient(135deg, #0f172a, #1e293b);
  border-radius: 18px;
  padding: 24px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  color: #fff;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
}
.header-text h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  margin: 6px 0 0;
  font-size: 13px;
  opacity: 0.85;
  max-width: 560px;
}
.btn-back-glass {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-family: inherit;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-back-glass:hover {
  background: rgba(255, 255, 255, 0.2);
}
.kartu {
  margin-top: 20px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 26px 28px;
  box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
}
.sub {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
  margin: 6px 0 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.sub i {
  color: #059669;
}
.sub:not(:first-child) {
  margin-top: 28px;
  border-top: 1px solid #f1f5f9;
  padding-top: 22px;
}
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 18px 20px;
  margin-bottom: 6px;
}
.grup {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.grup.full {
  grid-column: 1 / -1;
}
.grup label {
  font-size: 12px;
  font-weight: 700;
  color: #475569;
}
.grup input[type="text"] {
  padding: 11px 13px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-family: inherit;
  font-size: 13px;
  color: #0f172a;
  outline: none;
  transition: 0.2s;
}
.grup input[type="text"]:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}
.grup label small {
  font-weight: 500;
  color: #94a3b8;
}
/* Dropzone upload (TTD & Logo) — seluruh kotak bisa diklik */
.upload-box {
  position: relative;
  height: 165px;
  border: 1.5px dashed #cbd5e1;
  border-radius: 14px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: pointer;
  transition: 0.2s;
}
.upload-box:hover {
  border-color: #10b981;
  background: #f0fdf4;
}
.upload-box img {
  max-height: 140px;
  max-width: 92%;
  object-fit: contain;
}
/* Latar kotak-kotak untuk cek transparansi PNG tanda tangan */
.cek-transparan {
  background:
    repeating-conic-gradient(#eef2f7 0% 25%, #ffffff 0% 50%) 0 / 20px 20px;
}
.upload-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: #94a3b8;
  font-size: 13px;
  font-weight: 600;
  text-align: center;
  padding: 12px;
}
.upload-empty i {
  font-size: 32px;
  color: #cbd5e1;
  transition: 0.2s;
}
.upload-empty small {
  font-weight: 500;
  font-size: 11px;
}
.upload-box:hover .upload-empty i {
  color: #10b981;
}
.upload-ganti {
  position: absolute;
  bottom: 10px;
  right: 10px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  background: rgba(15, 23, 42, 0.78);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  backdrop-filter: blur(2px);
}
.aksi {
  margin-top: 26px;
  display: flex;
  justify-content: flex-end;
}
.btn-simpan {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  padding: 12px 24px;
  border: none;
  border-radius: 11px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  font-family: inherit;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-simpan:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
}
.btn-simpan:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.fade-up {
  animation: fadeUp 0.5s ease both;
}
.delay-1 {
  animation-delay: 0.08s;
}
@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@media (max-width: 768px) {
  .pengaturan-body {
    padding: 90px 12px 40px;
  }
  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
  }
  .kartu {
    padding: 20px 16px;
  }
}
</style>
