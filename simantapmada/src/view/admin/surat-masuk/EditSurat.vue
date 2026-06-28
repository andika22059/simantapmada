<template>
  <div class="vue-tambah-sm-body">
    <div class="wrapper">
      <div class="page-header">
        <div class="header-text">
          <h2>Edit Surat Masuk</h2>
          <p>Form pencatatan arsip surat masuk instansi.</p>
        </div>
        <button class="btn-back-white" @click="router.back()">
          <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
      </div>

      <div class="form-card">
        <form @submit.prevent="handleSubmit">
          <div class="form-section-title">
            <i class="fa-solid fa-envelope-open-text text-primary"></i> Data
            Utama Surat
          </div>

          <div class="form-grid">
            <div class="input-group">
              <label>Nomor Surat <span class="asterisk">*</span></label>
              <div class="input-wrapper">
                <i class="fa-solid fa-hashtag"></i>
                <input
                  type="text"
                  v-model="form.nomor_surat"
                  required
                  placeholder="Contoh: 140/01/Bupati/2026"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Tanggal Surat <span class="asterisk">*</span></label>
              <div class="input-wrapper">
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

            <div class="input-group">
              <label>Tanggal Diterima <span class="asterisk">*</span></label>
              <div class="input-wrapper">
                <i class="fa-solid fa-calendar-check"></i>
                <input
                  type="date"
                  v-model="form.tanggal_terima"
                  required
                  @click="$event.target.showPicker()"
                  style="cursor: pointer"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Jenis Surat <span class="asterisk">*</span></label>
              <div class="input-wrapper">
                <i class="fa-solid fa-list"></i>
                <select v-model="form.jenis_surat" required>
                  <option value="">-- Pilih Jenis --</option>
                  <option value="Permohonan">Permohonan</option>
                  <option value="Undangan">Undangan</option>
                  <option value="Pemberitahuan">Pemberitahuan</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label>Sifat Surat</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-bolt"></i>
                <select v-model="form.sifat">
                  <option value="Biasa">Biasa</option>
                  <option value="Penting">Penting</option>
                  <option value="Rahasia">Rahasia</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label>Disposisi Ke</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-share-nodes"></i>
                <select v-model="form.disposisi_ke">
                  <option value="">-- Tidak Ada --</option>
                  <option value="Kepala Desa">Kepala Desa</option>
                  <option value="Sekretaris Desa">Sekretaris Desa</option>
                </select>
              </div>
            </div>

            <div class="input-group full-width">
              <label>Pengirim <span class="asterisk">*</span></label>
              <div class="input-wrapper">
                <i class="fa-solid fa-building-user"></i>
                <input
                  type="text"
                  v-model="form.pengirim"
                  required
                  placeholder="Nama Instansi/Orang"
                />
              </div>
            </div>

            <div class="input-group full-width">
              <label>Alamat Pengirim</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-map-location-dot"></i>
                <input
                  type="text"
                  v-model="form.alamat"
                  placeholder="Alamat lengkap pengirim..."
                />
              </div>
            </div>

            <div class="input-group full-width">
              <label>Perihal <span class="asterisk">*</span></label>
              <div class="input-wrapper">
                <i class="fa-solid fa-quote-left"></i>
                <input
                  type="text"
                  v-model="form.perihal"
                  required
                  placeholder="Perihal surat..."
                />
              </div>
            </div>

            <div class="input-group full-width">
              <label>Keterangan Tambahan</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-align-left" style="top: 18px"></i>
                <textarea
                  v-model="form.keterangan"
                  rows="3"
                  placeholder="Tambahkan keterangan jika ada..."
                ></textarea>
              </div>
            </div>

            <div class="input-group full-width">
              <label>File Surat (PDF/JPG/PNG)</label>
              <div class="file-upload-wrapper" @click="triggerFileInput">
                <input
                  type="file"
                  ref="fileInputRef"
                  @change="handleFileChange"
                  accept=".pdf,.jpg,.jpeg,.png"
                  style="display: none"
                />

                <div v-if="!selectedFileName">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <strong style="color: #10b981; font-size: 14px"
                    >Klik untuk memilih file dokumen</strong
                  >
                  <span class="file-hint">Maksimal 5MB (PDF/JPG/PNG)</span>
                </div>
                <div
                  v-else
                  style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 5px;
                  "
                >
                  <i
                    class="fa-solid fa-circle-check"
                    style="color: #10b981; font-size: 30px; margin: 0"
                  ></i>
                  <span
                    style="color: #059669; font-weight: 700; font-size: 15px"
                    >{{ selectedFileName }}</span
                  >
                </div>
              </div>
            </div>

            <div class="input-group full-width">
              <div class="toggle-box">
                <div style="display: flex; flex-direction: column; gap: 4px">
                  <strong style="color: #0f172a; font-size: 14px"
                    >Simpan ke E-Arsip?</strong
                  >
                  <span style="font-size: 12px; color: #64748b"
                    >Arsipkan surat secara otomatis ke brankas digital</span
                  >
                </div>
                <label class="switch">
                  <input type="checkbox" v-model="form.simpan_arsip" />
                  <span class="slider"></span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-save">
              <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const route = useRoute();
const suratId = route.params.id;
const BASE = `${import.meta.env.VITE_API_URL}/api`;
const STORAGE = `${import.meta.env.VITE_API_URL}/storage/`;
const fileLama = ref(""); // path file lama (kalau tidak diganti)

// 🔥 TAMBAHAN BIAR FITUR UPLOAD JALAN DI HTML LU
const fileInputRef = ref(null);
const selectedFileName = ref("");

const triggerFileInput = () => {
  if (fileInputRef.value) {
    fileInputRef.value.click();
  }
};

const form = ref({
  nomor_surat: "",
  tanggal_surat: "",
  pengirim: "",
  perihal: "",
  file: null,
  tanggal_terima: "",
  jenis_surat: "",
  sifat: "Biasa",
  disposisi_ke: "",
  alamat: "",
  keterangan: "",
  simpan_arsip: false,
});

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire(
        "Peringatan",
        "Maksimal ukuran file dokumen adalah 5MB.",
        "warning",
      );
      event.target.value = "";
      return;
    }
    form.value.file = file;
    selectedFileName.value = file.name;
  }
};

const handleSubmit = async () => {
  // Saat edit, file opsional (kalau tidak ganti, pakai file lama)
  let fd = new FormData();
  fd.append("no_surat", form.value.nomor_surat);
  fd.append("pengirim", form.value.pengirim);
  fd.append("perihal", form.value.perihal);
  fd.append("tanggal_surat", form.value.tanggal_surat);
  if (form.value.file) {
    fd.append("file", form.value.file);
  }
  fd.append(
    "tanggal_terima",
    form.value.tanggal_terima || form.value.tanggal_surat,
  );
  fd.append("jenis_surat", form.value.jenis_surat || "-");
  fd.append("sifat", form.value.sifat || "-");
  fd.append("alamat", form.value.alamat || "-");
  fd.append("keterangan", form.value.keterangan || "-");

  try {
    Swal.fire({
      title: "Menyimpan Perubahan...",
      text: "Sedang memperbarui data surat...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    // POST ke endpoint update (POST karena ada kemungkinan upload file)
    await axios.post(`${BASE}/surat-masuk/${suratId}`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    Swal.fire("Berhasil!", "Surat masuk berhasil diperbarui.", "success").then(
      () => {
        router.push("/admin/surat-masuk");
      },
    );
  } catch (error) {
    console.error(error);
    Swal.fire(
      "Gagal",
      "Terjadi kesalahan saat menyimpan perubahan surat.",
      "error",
    );
  }
};

// Ambil data surat lama & isi form saat halaman dibuka
onMounted(async () => {
  try {
    const res = await axios.get(`${BASE}/surat-masuk/${suratId}`);
    const d = res.data?.data;
    if (!d) {
      Swal.fire("Tidak Ditemukan", "Data surat tidak ditemukan.", "error").then(
        () => router.push("/admin/surat-masuk"),
      );
      return;
    }
    // Surat yang sudah didisposisi tidak boleh diedit
    if (d.status_disposisi !== "Belum") {
      Swal.fire(
        "Tidak Bisa Diedit",
        "Surat yang sudah didisposisi tidak dapat diubah.",
        "info",
      ).then(() => router.push("/admin/surat-masuk"));
      return;
    }
    form.value.nomor_surat = d.no_surat || "";
    form.value.tanggal_surat = d.tanggal_surat || "";
    form.value.tanggal_terima = d.tanggal_terima || "";
    form.value.jenis_surat = d.jenis_surat || "";
    form.value.sifat = d.sifat || "Biasa";
    form.value.disposisi_ke = d.disposisi_ke || "";
    form.value.pengirim = d.pengirim || "";
    form.value.alamat = d.alamat || "";
    form.value.perihal = d.perihal || "";
    form.value.keterangan = d.keterangan || "";
    fileLama.value = d.file_surat || "";
    if (d.file_surat)
      selectedFileName.value = "File lama tersimpan (opsional ganti)";
  } catch (e) {
    console.error(e);
    Swal.fire("Gagal", "Gagal memuat data surat.", "error");
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-tambah-sm-body {
  background-color: transparent;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #1e293b;
  padding: 15px;
}

.wrapper {
  max-width: 100%;
  margin: 0 auto;
}

/* === HEADER (DARK SLATE EMERALD) === */
.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 24px 35px;
  border-radius: 16px;
  box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.12);
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.header-text h2 {
  color: #ffffff;
  font-size: 24px;
  font-weight: 800;
  margin: 0 0 6px 0;
}

.header-text p {
  color: #94a3b8;
  margin: 0;
  font-size: 13px;
  font-weight: 500;
}

.btn-back-white {
  background-color: transparent;
  border: 2px solid #334155;
  color: #cbd5e1;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.25s ease;
}

.btn-back-white:hover {
  background-color: rgba(255, 255, 255, 0.05);
  border-color: #64748b;
  color: #ffffff;
  transform: translateY(-1px);
}

/* === FORM CARD & TITLE === */
.form-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.03),
    0 2px 4px -1px rgba(0, 0, 0, 0.02);
  border: 1px solid #e2e8f0;
  padding: 35px 40px;
}

.form-section-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 10px;
  border-bottom: 2px solid #f1f5f9;
}

.text-primary {
  color: #10b981 !important;
}

.mt-4 {
  margin-top: 35px;
}

/* === FORM GRID & INPUTS === */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

.full-width {
  grid-column: span 2;
}

.input-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 8px;
}

.asterisk {
  color: #ef4444;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper i {
  position: absolute;
  left: 16px;
  color: #94a3b8;
  font-size: 14px;
  z-index: 2;
}

.input-wrapper input,
.input-wrapper select,
.input-wrapper textarea {
  width: 100%;
  padding: 12px 16px 12px 42px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  color: #1e293b;
  background: #f8fafc;
  outline: none;
  font-family: inherit;
  transition: all 0.2s ease;
}

.input-wrapper textarea {
  padding-top: 16px;
  resize: vertical;
}

.input-wrapper input:focus,
.input-wrapper select:focus,
.input-wrapper textarea:focus {
  border-color: #10b981;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* === FILE UPLOAD AREA (KLIKABLE) === */
.file-upload-wrapper {
  border: 2px dashed #cbd5e1;
  background: #f8fafc;
  border-radius: 14px;
  padding: 40px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.file-upload-wrapper:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.fa-cloud-arrow-up {
  font-size: 45px;
  color: #94a3b8;
  margin-bottom: 15px;
  transition: 0.3s ease;
}

.file-upload-wrapper:hover .fa-cloud-arrow-up {
  color: #10b981;
  transform: translateY(-5px);
}

.file-hint {
  display: block;
  font-size: 12px;
  color: #64748b;
  margin-top: 5px;
}

/* === CUSTOM TOGGLE SWITCH (IOS STYLE) === */
.toggle-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 15px 20px;
  border-radius: 10px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
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
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #10b981;
}

input:checked + .slider:before {
  transform: translateX(22px);
}

/* === BUTTON SIMPAN === */
.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 35px;
  padding-top: 25px;
  border-top: 1px solid #e2e8f0;
}

.btn-save {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #ffffff;
  border: none;
  padding: 12px 28px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
  transition: 0.3s;
}

.btn-save:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
}

/* === RESPONSIVE (MOBILE) === */
@media (max-width: 900px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .btn-back-white {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  .full-width {
    grid-column: span 1;
  }
  .form-card {
    padding: 25px 20px;
  }
  .btn-save {
    width: 100%;
    justify-content: center;
  }
  .search-box {
    width: 100% !important;
  }
}

@media (max-width: 480px) {
  .page-header {
    padding: 18px;
  }
  .header-text h2 {
    font-size: 19px;
  }
  .header-text p {
    font-size: 12px;
  }
  .form-card {
    padding: 18px 15px;
  }
  .input-group label {
    font-size: 12px;
  }
  .input-wrapper input,
  .input-wrapper select,
  .input-wrapper textarea {
    font-size: 13px;
    padding: 11px 14px 11px 40px;
  }
  .btn-save {
    font-size: 13px;
    padding: 12px;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px;
  }
  .form-card {
    padding: 14px 12px;
  }
}
</style>
