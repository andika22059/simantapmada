<template>
  <div class="vue-arsip-tambah-body">
    <div class="main-wrapper">
      <div class="page-header-box">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-box-archive text-emerald"></i> Registrasi
            Arsip Baru
          </h2>
          <p>
            Input dokumen desa sesuai standar ANRI. Mendukung multi-upload
            berkas digital.
          </p>
        </div>
        <button
          class="btn-back-white"
          @click="router.push('/admin/arsip')"
          type="button"
        >
          <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
      </div>

      <div class="form-card">
        <form @submit.prevent="handleSubmit">
          <div class="form-section-title">
            <i class="fa-solid fa-tags"></i> Klasifikasi Arsip
          </div>

          <div class="form-grid">
            <div class="input-group">
              <label
                >Nomor Klasifikasi / Arsip
                <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-hashtag"></i>
                <input
                  type="text"
                  v-model="form.nomor_arsip"
                  placeholder="Contoh: 470/01/Ds/2026"
                  required
                  autofocus
                />
              </div>
            </div>

            <div class="input-group">
              <label>Pencipta Arsip (Uploader)</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-user-shield"></i>
                <input
                  type="text"
                  v-model="form.pencipta"
                  readonly
                  class="bg-readonly"
                />
              </div>
            </div>

            <div class="input-group full-width">
              <label
                >Judul / Uraian Informasi
                <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-book"></i>
                <input
                  type="text"
                  v-model="form.judul"
                  placeholder="Ringkasan isi dokumen arsip..."
                  required
                />
              </div>
            </div>

            <div class="input-group">
              <label>Kategori Dokumen <span class="text-danger">*</span></label>
              <div class="input-wrapper">
                <i class="fa-solid fa-folder-tree"></i>
                <select v-model="form.kategori" required>
                  <option value="">-- Pilih Kategori --</option>
                  <option value="Surat Masuk">Surat Masuk</option>
                  <option value="Surat Keluar">Surat Keluar</option>
                  <option value="SK Kades">SK Kepala Desa</option>
                  <option value="Perdes">Peraturan Desa</option>
                  <option value="Lainnya">Dokumen Lainnya</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label
                >Lokasi Fisik <span class="hint">(Lemari/Rak/Boks)</span>
                <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-location-dot"></i>
                <input
                  type="text"
                  v-model="form.lokasi_fisik"
                  placeholder="Cth: Lemari A - Rak 2"
                  required
                />
              </div>
            </div>
          </div>

          <div class="form-section-title" style="margin-top: 35px">
            <i class="fa-solid fa-clock-rotate-left"></i> Jadwal Retensi Arsip
            (JRA)
          </div>

          <div class="retensi-box form-grid">
            <div class="input-group">
              <label>Status Arsip Awal</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-circle-check text-emerald"></i>
                <input
                  type="text"
                  v-model="form.status"
                  readonly
                  class="bg-readonly text-bold"
                />
              </div>
            </div>

            <div class="input-group">
              <label
                >Masa Simpan Aktif <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-regular fa-hourglass-half"></i>
                <select v-model="form.durasi_retensi" required>
                  <option value="">-- Tentukan Durasi --</option>
                  <option value="1">1 Tahun</option>
                  <option value="2">2 Tahun</option>
                  <option value="5">5 Tahun</option>
                  <option value="10">10 Tahun</option>
                  <option value="custom">Tentukan Manual...</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label
                >Tanggal Kedaluwarsa <span class="hint">(Oto/Manual)</span>
                <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-calendar-xmark text-danger"></i>
                <input
                  type="date"
                  v-model="form.tgl_retensi"
                  required
                  @click="$event.target.showPicker()"
                  style="cursor: pointer"
                />
              </div>
            </div>

            <div class="input-group">
              <label
                >Nasib Akhir Dokumen <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-gavel text-warning"></i>
                <select v-model="form.nasib_akhir" required>
                  <option value="Musnah">Dimusnahkan</option>
                  <option value="Permanen">
                    Permanen (Diserahkan ke ANRI/Pemda)
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section-title" style="margin-top: 35px">
            <i class="fa-solid fa-file-pdf"></i> Upload Dokumen Digital
          </div>

          <div class="input-group full-width">
            <div class="upload-area" @click="triggerFileInput">
              <input
                type="file"
                ref="fileInputRef"
                @change="handleFileChange"
                accept=".pdf,.jpg,.jpeg,.png"
                multiple
                style="display: none"
              />

              <div v-if="uploadedFiles.length === 0" class="upload-content">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <h4>Klik di sini untuk upload File Scan Arsip</h4>
                <p>Mendukung pilih banyak file sekaligus (Max 5MB per file)</p>
              </div>

              <div v-else class="file-preview-container">
                <div class="preview-header">
                  <i class="fa-solid fa-circle-check"></i>
                  {{ uploadedFiles.length }} Berkas Berhasil Dimuat:
                </div>

                <div class="file-list">
                  <div
                    v-for="(file, index) in uploadedFiles"
                    :key="index"
                    class="file-item"
                  >
                    <i class="fa-solid fa-file-lines"></i>
                    <span class="file-name">{{ file.name }}</span>
                    <span class="file-size">({{ formatSize(file.size) }})</span>
                  </div>
                </div>
                <div
                  style="
                    margin-top: 15px;
                    font-size: 12px;
                    color: #059669;
                    text-decoration: underline;
                  "
                >
                  Klik area ini jika ingin mengganti file
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-save" :disabled="isSaving">
              <i
                :class="
                  isSaving
                    ? 'fa-solid fa-spinner fa-spin'
                    : 'fa-solid fa-floppy-disk'
                "
              ></i>
              {{
                isSaving
                  ? "Menyimpan..."
                  : isEdit
                    ? "Perbarui Arsip"
                    : "Simpan Arsip ke Database"
              }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

const router = useRouter();
const route = useRoute();
const isSaving = ref(false);

// Mode edit (kalau ada :id di URL /admin/arsip/edit/:id)
const isEdit = ref(false);
const editId = ref(null);
const sedangMuat = ref(false); // cegah watch menimpa tgl_retensi saat prefill

// State Form Data
const form = ref({
  nomor_arsip: "",
  pencipta: "Admin Desa", // Mock data, di backend biasanya pakai session
  judul: "",
  kategori: "",
  lokasi_fisik: "",
  status: "Aktif",
  durasi_retensi: "",
  tgl_retensi: "",
  nasib_akhir: "Musnah",
});

// Referensi DOM untuk upload file
const fileInputRef = ref(null);
const uploadedFiles = ref([]);

// 🚨 LOGIKA PINTAR: Penghitung Tanggal Otomatis (Menggantikan JS lama) 🚨
watch(
  () => form.value.durasi_retensi,
  (newVal) => {
    if (sedangMuat.value) return; // jangan hitung ulang saat memuat data edit
    if (newVal && newVal !== "custom") {
      const today = new Date();
      // Tambah tahun sesuai durasi yang dipilih
      const futureDate = new Date(
        today.getFullYear() + parseInt(newVal),
        today.getMonth(),
        today.getDate(),
      );

      // Format ke string YYYY-MM-DD agar masuk ke input type="date"
      form.value.tgl_retensi = futureDate.toISOString().split("T")[0];
    } else {
      // Kosongkan jika pilih "Custom/Manual"
      form.value.tgl_retensi = "";
    }
  },
);

// Fitur Multi-Upload File
const triggerFileInput = () => {
  fileInputRef.value.click();
};

const handleFileChange = (e) => {
  const files = e.target.files;
  if (files && files.length > 0) {
    uploadedFiles.value = Array.from(files); // Ubah FileList jadi Array
  }
};

const formatSize = (bytes) => {
  return (bytes / (1024 * 1024)).toFixed(2) + " MB";
};

// Muat data arsip untuk mode edit
const muatArsip = async (id) => {
  sedangMuat.value = true;
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_API_URL}/api/arsip/${id}`,
    );
    const d = res.data?.data || res.data;
    if (!d || !d.id) throw new Error("not found");
    form.value = {
      nomor_arsip: d.nomor_arsip || "",
      pencipta: d.pencipta || "Admin Desa",
      judul: d.judul || "",
      kategori: d.kategori || "",
      lokasi_fisik: d.lokasi_fisik || "",
      status: d.status || "Aktif",
      durasi_retensi: d.durasi_retensi || "",
      tgl_retensi: d.tgl_retensi ? String(d.tgl_retensi).split("T")[0] : "",
      nasib_akhir: d.nasib_akhir || "Musnah",
    };
  } catch (e) {
    console.error("Gagal memuat arsip:", e);
    alert("Gagal memuat data arsip untuk diedit.");
    router.push("/admin/arsip/list");
  } finally {
    // beri jeda agar watch tidak langsung menimpa tgl_retensi
    setTimeout(() => (sedangMuat.value = false), 50);
  }
};

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true;
    editId.value = route.params.id;
    muatArsip(route.params.id);
  }
});

// Eksekusi Simpan
const handleSubmit = async () => {
  // Mode EDIT → perbarui metadata (tanpa wajib unggah file baru)
  if (isEdit.value) {
    isSaving.value = true;
    try {
      await axios.put(
        `${import.meta.env.VITE_API_URL}/api/arsip/${editId.value}`,
        { ...form.value },
      );
      alert("Data arsip berhasil diperbarui!");
      router.push("/admin/arsip/list");
    } catch (error) {
      console.error("Gagal memperbarui arsip:", error);
      alert(
        error.response?.data?.message ||
          "Terjadi kesalahan saat memperbarui arsip.",
      );
    } finally {
      isSaving.value = false;
    }
    return;
  }

  // Mode TAMBAH → wajib unggah file
  if (uploadedFiles.value.length === 0) {
    alert("Wajib mengunggah minimal 1 dokumen arsip!");
    return;
  }

  const formData = new FormData();
  formData.append("nomor_arsip", form.value.nomor_arsip);
  formData.append("pencipta", form.value.pencipta);
  formData.append("judul", form.value.judul);
  formData.append("kategori", form.value.kategori);
  formData.append("lokasi_fisik", form.value.lokasi_fisik);
  formData.append("status", form.value.status);
  formData.append("durasi_retensi", form.value.durasi_retensi);
  formData.append("tgl_retensi", form.value.tgl_retensi);
  formData.append("nasib_akhir", form.value.nasib_akhir);

  uploadedFiles.value.forEach((file) => {
    formData.append("files[]", file);
  });

  isSaving.value = true;
  try {
    await axios.post(`${import.meta.env.VITE_API_URL}/api/arsip`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    alert("Data Arsip Baru Berhasil Disimpan ke Database!");
    router.push("/admin/arsip/list");
  } catch (error) {
    console.error("Gagal menyimpan arsip:", error);
    const pesan =
      error.response?.data?.message ||
      "Terjadi kesalahan saat menyimpan arsip. Cek koneksi server/database.";
    alert(pesan);
  } finally {
    isSaving.value = false;
  }
};
</script>

<style>
/* ==================================================
   CSS INPUT ARSIP BARU - ULTRA PREMIUM SAAS EDITION
   (DITANAM LANGSUNG BIAR AMAN DARI BUG PATH)
   ================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-arsip-tambah-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
  color: #0f172a;
  min-height: 100vh;
  padding: 20px 20px 60px;
  box-sizing: border-box;
}

.main-wrapper {
  max-width: 95%;
  margin: 0 auto;
  padding-top: 30px;
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
  color: #34d399;
}
.text-danger {
  color: #ef4444;
}
.text-warning {
  color: #f59e0b;
}
.text-bold {
  font-weight: 800 !important;
  color: #1e293b !important;
}
.bg-readonly {
  background: #f1f5f9 !important;
  color: #64748b !important;
  cursor: not-allowed;
}

/* === HEADER PREMIUM (GLASSMORPHISM) === */
.page-header-box {
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  padding: 28px 35px;
  border-radius: 20px 20px 0 0;
  border: none;
  border-bottom: 4px solid #059669;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
  margin-bottom: 0; /* Menyatu dengan form card di bawahnya */
}

.header-text h2 {
  margin: 0 0 5px 0;
  font-size: 24px;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 10px;
  letter-spacing: -0.5px;
}

.header-text p {
  margin: 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}

.btn-back-white {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  transition: 0.3s;
  cursor: pointer;
}
.btn-back-white:hover {
  background: #ffffff;
  color: #1e293b;
  border-color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

/* === KARTU FORM === */
.form-card {
  background: white;
  border-radius: 0 0 20px 20px;
  padding: 40px;
  box-shadow: 0 15px 40px rgba(5, 150, 105, 0.06);
  border: 1px solid #e2e8f0;
  border-top: none;
}

/* Judul Bagian */
.form-section-title {
  font-size: 14px;
  font-weight: 800;
  color: #047857;
  background: #d1fae5;
  border: 1px solid #a7f3d0;
  padding: 12px 20px;
  border-radius: 10px;
  margin-bottom: 25px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Flex Grid */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px 25px;
}
.full-width {
  grid-column: span 2;
}

/* Input Layouts */
.input-group label {
  display: block;
  font-size: 12px;
  font-weight: 800;
  color: #475569;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.input-group label .hint {
  color: #94a3b8;
  font-weight: 600;
  font-size: 11px;
  text-transform: none;
  letter-spacing: 0;
}

.input-wrapper {
  position: relative;
  width: 100%;
}
.input-wrapper i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 16px;
  transition: 0.3s;
}
.input-wrapper:focus-within i {
  color: #059669;
}

.input-wrapper input,
.input-wrapper select {
  width: 100%;
  height: 48px;
  padding: 0 15px 0 45px;
  border-radius: 12px;
  border: 2px solid #e2e8f0;
  background: #f8fafc;
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  outline: none;
  transition: 0.3s;
  box-sizing: border-box;
}

.input-wrapper input:focus,
.input-wrapper select:focus {
  background: white;
  border-color: #059669;
  box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
}

.input-wrapper select {
  appearance: none;
  cursor: pointer;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2316a34a' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 14px;
}

/* === ZONA RETENSI (JRA) === */
.retensi-box {
  background: #ecfdf5;
  border: 2px dashed #a7f3d0;
  padding: 25px;
  border-radius: 16px;
  margin-top: 10px;
  margin-bottom: 10px;
}
.retensi-box .input-wrapper input:not(.bg-readonly),
.retensi-box .input-wrapper select {
  background: white;
  border-color: #a7f3d0;
}

/* === ZONA UPLOAD PDF MULTI-FILE === */
.upload-area {
  border: 2px dashed #86efac;
  border-radius: 16px;
  background: #ecfdf5;
  padding: 35px;
  text-align: center;
  cursor: pointer;
  transition: 0.3s ease;
}
.upload-content i {
  font-size: 40px;
  color: #059669;
  margin-bottom: 15px;
  transition: 0.3s;
}
.upload-area:hover .upload-content i {
  transform: translateY(-5px) scale(1.1);
}
.upload-content h4 {
  font-weight: 800;
  color: #047857;
  font-size: 16px;
  margin: 0 0 5px 0;
}
.upload-content p {
  color: #059669;
  font-size: 13px;
  margin: 0;
}

.upload-area:hover {
  border-color: #059669;
  background: #d1fae5;
}

/* Tampilan Multi-File Vue */
.file-preview-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}
.preview-header {
  font-weight: 800;
  color: #047857;
  font-size: 15px;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.file-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
  max-width: 600px;
}
.file-item {
  background: white;
  border: 1px solid #a7f3d0;
  padding: 12px 18px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 13px;
  color: #0f172a;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
}
.file-item i {
  color: #10b981;
  font-size: 18px;
}
.file-name {
  font-weight: 700;
  flex-grow: 1;
  text-align: left;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.file-size {
  color: #64748b;
  font-weight: 600;
}

/* === BUTTON ACTIONS === */
.form-actions {
  margin-top: 35px;
  padding-top: 30px;
  border-top: 2px dashed #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 15px;
}
.btn-save {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
  padding: 16px 32px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 800;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
  transition: 0.3s;
  position: relative;
  overflow: hidden;
}
.btn-save::after {
  content: "";
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: linear-gradient(
    120deg,
    transparent,
    rgba(255, 255, 255, 0.35),
    transparent
  );
  transform: skewX(-25deg);
  transition: left 0.7s ease;
}
.btn-save:hover::after {
  left: 130%;
}

.btn-save:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 25px rgba(5, 150, 105, 0.4);
}
.btn-save:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* ==========================================================
   === MOBILE RESPONSIVE MASTER (100% ANTI-HANCUR) ===
   ========================================================== */
@media (max-width: 900px) {
  .page-header-box {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 15px !important;
  }
  .btn-back-white {
    width: 100% !important;
    justify-content: center !important;
  }
}

@media (max-width: 768px) {
  .vue-arsip-tambah-body {
    padding: 95px 15px 20px !important;
    overflow-x: hidden !important;
  }
  .main-wrapper {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
    box-sizing: border-box !important;
  }

  .page-header-box {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 15px !important;
    border-radius: 16px !important;
    margin-bottom: 15px;
  }
  .btn-back-white {
    width: 100% !important;
    justify-content: center !important;
  }

  .form-card {
    padding: 25px 20px !important;
    border-radius: 16px !important;
    border-top: 4px solid #059669;
  }
  .form-grid {
    grid-template-columns: 1fr !important;
    gap: 15px !important;
  }
  .full-width {
    grid-column: span 1 !important;
  }

  .file-item {
    flex-direction: column;
    text-align: center;
  }
  .file-name {
    white-space: normal;
    text-align: center;
  }

  .form-actions {
    flex-direction: column !important;
    width: 100% !important;
    padding-top: 20px !important;
    margin-top: 20px !important;
  }
  .btn-save {
    width: 100% !important;
    justify-content: center !important;
    padding: 15px !important;
  }
}

/* === HP KECIL === */
@media (max-width: 480px) {
  .page-header-box {
    padding: 18px !important;
  }
  .header-text h2 {
    font-size: 19px !important;
  }
  .header-text p {
    font-size: 12px !important;
  }
  .form-card {
    padding: 18px 15px !important;
  }
  .form-section-title {
    font-size: 12px !important;
    padding: 10px 14px !important;
  }
  .input-group label {
    font-size: 11px !important;
  }
  .input-wrapper input,
  .input-wrapper select,
  .input-wrapper textarea {
    font-size: 13px !important;
    padding: 12px 14px 12px 40px !important;
  }
  .input-wrapper i {
    font-size: 14px !important;
    left: 13px !important;
  }
  .btn-save {
    font-size: 14px !important;
    padding: 14px !important;
    width: 100% !important;
    justify-content: center !important;
  }
}

/* === HP SUPER KECIL (<=360px) === */
@media (max-width: 360px) {
  .vue-arsip-tambah-body {
    padding: 85px 10px 15px !important;
  }
  .header-text h2 {
    font-size: 17px !important;
  }
  .form-card {
    padding: 14px 12px !important;
  }
  .btn-save {
    font-size: 13px !important;
    padding: 12px !important;
  }
}
</style>
