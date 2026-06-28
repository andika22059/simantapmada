<template>
  <div class="vue-tambah-body">
    <div class="main-wrapper">
      <div class="page-header-box">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-file-export"></i> Terbitkan Surat Keluar
          </h2>
          <p>Catat dan unggah arsip surat keluar resmi instansi desa.</p>
        </div>
        <div class="header-actions">
          <button
            @click="router.push('/admin/surat-keluar')"
            class="btn-back-outline"
            type="button"
          >
            <i class="fa-solid fa-arrow-left"></i> Kembali
          </button>
        </div>
      </div>

      <div class="form-card">
        <form @submit.prevent="submitForm">
          <div class="form-grid">
            <div class="input-group">
              <label
                >Nomor Surat Resmi <span class="text-danger">*</span></label
              >
              <div class="input-with-icon">
                <i class="fa-solid fa-hashtag"></i>
                <input
                  type="text"
                  v-model="form.nomor_surat"
                  required
                  placeholder="Contoh: 141/002/411.508/2026"
                />
              </div>
            </div>

            <div class="input-group">
              <label
                >Tanggal Dikeluarkan <span class="text-danger">*</span></label
              >
              <div class="input-with-icon">
                <i class="fa-solid fa-calendar-day"></i>
                <input
                  type="date"
                  v-model="form.tanggal_surat"
                  required
                  @click="$event.target.showPicker()"
                  style="cursor: pointer"
                />
              </div>
            </div>

            <div class="input-group full-width">
              <label>Tujuan Surat <span class="text-danger">*</span></label>
              <div class="input-with-icon">
                <i class="fa-solid fa-building-user"></i>
                <input
                  type="text"
                  v-model="form.tujuan"
                  required
                  placeholder="Contoh: Camat Loceret / Dinas Sosial"
                />
              </div>
            </div>

            <div class="input-group full-width">
              <label
                >Perihal / Isi Ringkas <span class="text-danger">*</span></label
              >
              <div class="input-with-icon align-top">
                <i class="fa-solid fa-quote-left"></i>
                <textarea
                  v-model="form.perihal"
                  rows="3"
                  required
                  placeholder="Tuliskan perihal atau isi ringkas surat keluar..."
                ></textarea>
              </div>
            </div>

            <div class="input-group full-width">
              <label>Keterangan Tambahan</label>
              <div class="input-with-icon align-top">
                <i class="fa-solid fa-align-left" style="top: 18px"></i>
                <textarea
                  v-model="form.keterangan"
                  rows="2"
                  placeholder="Tambahkan keterangan jika ada (opsional)..."
                ></textarea>
              </div>
            </div>
          </div>

          <div class="input-group full-width" style="margin-top: 25px">
            <label
              >Unggah Salinan (Scan PDF)
              <span class="text-danger">*</span></label
            >
            <div class="upload-area" @click="triggerFileInput">
              <input
                type="file"
                ref="fileInputRef"
                @change="handleFileUpload"
                accept=".pdf"
                required
                style="display: none"
              />

              <div v-if="!selectedFileName" class="upload-content">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <h4>Pilih File PDF Surat</h4>
                <p>Format yang didukung: .PDF (Maksimal 5 MB)</p>
              </div>

              <div v-else class="file-preview-box">
                <i class="fas fa-check-circle"></i>
                <span>{{ selectedFileName }}</span>
              </div>
            </div>
          </div>

          <div class="input-group full-width">
            <div class="toggle-box">
              <div class="toggle-text">
                <strong>Simpan ke E-Arsip?</strong>
                <span
                  >Klik untuk mengarsipkan surat keluar ini ke dalam brankas
                  digital.</span
                >
              </div>
              <label class="switch">
                <input type="checkbox" v-model="form.simpan_arsip" value="ya" />
                <span class="slider"></span>
              </label>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-save" :disabled="isSaving">
              <i
                :class="
                  isSaving ? 'fa-solid fa-spinner fa-spin' : 'fas fa-save'
                "
              ></i>
              {{ isSaving ? "Menyimpan..." : "Simpan Arsip Keluar" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const fileInputRef = ref(null);
const selectedFileName = ref("");
const selectedFile = ref(null);
const isSaving = ref(false);

const form = ref({
  nomor_surat: "",
  tanggal_surat: "",
  tujuan: "",
  perihal: "",
  keterangan: "", // 🔥 UDAH DITAMBAHIN DI STATE
  simpan_arsip: false,
});

const triggerFileInput = () => {
  fileInputRef.value.click();
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire(
        "Ukuran Terlalu Besar",
        "Maksimal file PDF 5MB ya bang!",
        "warning",
      );
      event.target.value = "";
      return;
    }
    selectedFileName.value = file.name;
    selectedFile.value = file;
  } else {
    selectedFileName.value = "";
    selectedFile.value = null;
  }
};

const submitForm = async () => {
  if (!selectedFile.value) {
    Swal.fire({
      icon: "warning",
      title: "Belum Ada File",
      text: "Silakan unggah salinan PDF surat terlebih dahulu.",
      confirmButtonColor: "var(--primary)",
    });
    return;
  }

  const formData = new FormData();
  formData.append("nomor_surat", form.value.nomor_surat);
  formData.append("tanggal_surat", form.value.tanggal_surat);
  formData.append("tujuan", form.value.tujuan);
  formData.append("perihal", form.value.perihal);

  // 🔥 INI DATANYA DIKIRIM KE CONTROLLER
  formData.append("keterangan", form.value.keterangan || "-");

  // 🔥 Kirim status toggle "Simpan ke E-Arsip" ke backend
  formData.append("simpan_arsip", form.value.simpan_arsip ? "1" : "0");

  formData.append("file_surat", selectedFile.value);

  isSaving.value = true;
  try {
    await axios.post(`${import.meta.env.VITE_API_URL}/api/surat-keluar`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    await Swal.fire({
      icon: "success",
      title: "Berhasil Diterbitkan!",
      html: `Surat <strong>${form.value.nomor_surat}</strong> berhasil disimpan ke database.${
        form.value.simpan_arsip
          ? "<br/><small>Surat ini juga otomatis tersimpan di E-Arsip.</small>"
          : ""
      }`,
      confirmButtonText: "Lihat Daftar Surat",
      confirmButtonColor: "var(--primary)",
      timer: 2500,
      timerProgressBar: true,
    });

    router.push("/admin/surat-keluar");
  } catch (error) {
    console.error("Gagal menyimpan surat keluar:", error);
    const pesan =
      error.response?.data?.message ||
      "Terjadi kesalahan saat menyimpan. Cek koneksi server/database.";
    Swal.fire({
      icon: "error",
      title: "Gagal Menyimpan",
      text: pesan,
      confirmButtonColor: "var(--primary)",
    });
  } finally {
    isSaving.value = false;
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-tambah-body {
  padding: 20px;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #0f172a;
  min-height: 100vh;
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

/* --- HEADER PREMIUM --- */
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
  padding: 30px 40px;
  border-radius: 20px 20px 0 0;
  color: white;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
  border-bottom: 4px solid var(--primary);
}

.header-text h2 {
  font-size: 24px;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0 0 6px 0;
  letter-spacing: -0.5px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  font-size: 14px;
  color: #94a3b8;
  font-weight: 500;
  margin: 0;
}

.btn-back-outline {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.18);
  color: white;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  transition: 0.3s;
  cursor: pointer;
}
.btn-back-outline:hover {
  background: #ffffff;
  border-color: #ffffff;
  color: #1e293b;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* --- KARTU FORM --- */
.form-card {
  background: white;
  border-radius: 0 0 20px 20px;
  padding: 45px;
  box-shadow: 0 20px 40px rgba(5, 150, 105, 0.05);
  border: 1px solid #d1fae5;
  border-top: none;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
}
.full-width {
  grid-column: 1 / -1;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.input-group label {
  font-size: 12px;
  font-weight: 800;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.text-danger {
  color: var(--primary);
}

.input-with-icon {
  position: relative;
  display: flex;
  align-items: center;
}
.input-with-icon.align-top {
  align-items: flex-start;
}
.input-with-icon i {
  position: absolute;
  left: 16px;
  color: #94a3b8;
  font-size: 16px;
  transition: 0.3s;
}
.input-with-icon.align-top i {
  top: 18px;
}
.input-with-icon:focus-within i {
  color: var(--primary);
}

.input-with-icon input,
.input-with-icon textarea {
  width: 100%;
  padding: 16px 16px 16px 45px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 14px;
  font-family: inherit;
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
  transition: 0.3s;
  box-sizing: border-box;
}
.input-with-icon textarea {
  resize: vertical;
  min-height: 110px;
  line-height: 1.5;
}
.input-with-icon input:focus,
.input-with-icon textarea:focus {
  outline: none;
  border-color: var(--primary);
  background: white;
  box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
}

/* --- AREA UPLOAD FILE --- */
.upload-area {
  border: 2px dashed #cbd5e1;
  border-radius: 16px;
  padding: 40px;
  text-align: center;
  background: #f8fafc;
  cursor: pointer;
  transition: 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.upload-area:hover {
  border-color: var(--primary);
  background: #ecfdf5;
}
.upload-content i {
  font-size: 45px;
  color: #94a3b8;
  margin-bottom: 15px;
  transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.upload-area:hover .upload-content i {
  color: var(--primary);
  transform: translateY(-5px) scale(1.1);
}
.upload-content h4 {
  font-size: 16px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 5px 0;
}
.upload-content p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.file-preview-box {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  padding: 20px 30px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 700;
  color: #166534;
  font-size: 15px;
  width: 100%;
  justify-content: center;
  animation: premiumFadeInUp 0.3s ease forwards;
}
.file-preview-box i {
  font-size: 24px;
  color: #10b981;
}

/* --- TOGGLE SWITCH --- */
.toggle-box {
  background: #ecfdf5;
  padding: 20px;
  border-radius: 16px;
  border: 1px solid #a7f3d0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 15px;
}
.toggle-text strong {
  display: block;
  color: var(--primary-dark);
  font-size: 15px;
  margin-bottom: 4px;
}
.toggle-text span {
  font-size: 13px;
  color: var(--primary);
  font-weight: 500;
}

.switch {
  position: relative;
  display: inline-block;
  width: 54px;
  height: 30px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: 0.4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
input:checked + .slider {
  background-color: var(--primary);
}
input:checked + .slider:before {
  transform: translateX(24px);
}

/* --- TOMBOL SIMPAN --- */
.form-actions {
  margin-top: 35px;
  border-top: 2px dashed #e2e8f0;
  display: flex;
  justify-content: flex-end;
  padding-top: 30px;
}
.btn-save {
  background: linear-gradient(
    135deg,
    var(--primary) 0%,
    var(--primary-dark) 100%
  );
  color: white;
  padding: 16px 32px;
  border-radius: 12px;
  border: none;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  transition: 0.3s;
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
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

/* --- RESPONSIVE FIX --- */
@media (max-width: 900px) {
  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    padding: 25px;
  }
  .header-actions {
    width: 100%;
  }
  .btn-back-outline {
    width: 100%;
    justify-content: center;
    padding: 14px;
  }
}

@media (max-width: 768px) {
  .page-header-box {
    border-radius: 20px;
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
    padding: 25px;
  }
  .header-actions {
    width: 100%;
  }
  .btn-back-outline {
    width: 100%;
    justify-content: center;
    padding: 14px;
  }
  .form-card {
    padding: 25px;
    border-radius: 20px;
    margin-top: 15px;
    border-top: 1px solid #d1fae5;
  }
  .form-grid {
    grid-template-columns: 1fr !important;
    gap: 20px !important;
  }
  .toggle-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .form-actions {
    flex-direction: column !important;
    width: 100% !important;
    padding-top: 20px !important;
    margin-top: 25px !important;
  }
  .btn-save {
    width: 100% !important;
    justify-content: center !important;
    box-sizing: border-box !important;
    padding: 16px !important;
  }
}

/* === HP KECIL (font & kotak lebih kecil biar pas) === */
@media (max-width: 480px) {
  .page-header-box {
    padding: 18px !important;
  }
  .header-text h2,
  .page-header-box h2 {
    font-size: 19px !important;
  }
  .page-header-box p {
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
  }
  .toggle-box {
    padding: 15px !important;
  }
}

@media (max-width: 360px) {
  .page-header-box {
    padding: 15px !important;
  }
  .header-text h2,
  .page-header-box h2 {
    font-size: 17px !important;
  }
  .form-card {
    padding: 14px 12px !important;
  }
}
</style>
