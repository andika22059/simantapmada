<template>
  <div class="vue-aset-tambah-body">
    <div class="main-wrapper">
      <div class="page-header-box">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-boxes-stacked text-emerald"></i> Input Aset
            Baru
          </h2>
          <p>
            Registrasi inventaris aset desa sesuai Permendagri No. 1 Tahun 2016
            jo. No. 3 Tahun 2024.
          </p>
        </div>
        <button
          class="btn-back-white"
          @click="router.push('/admin/aset')"
          type="button"
        >
          <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
      </div>

      <div class="form-card">
        <form @submit.prevent="submitForm">
          <!-- ============ IDENTITAS UMUM ============ -->
          <div class="form-section-title">
            <i class="fa-solid fa-cube"></i> Identitas Aset (Buku Inventaris)
          </div>

          <div class="form-grid">
            <div class="input-group">
              <label
                >Nomor Register Aset
                <span class="hint"
                  >(Kosongkan untuk Generate Otomatis)</span
                ></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-barcode"></i>
                <input
                  type="text"
                  v-model="form.nomor_aset"
                  placeholder="Contoh: 01.02.03.04.05"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Kodefikasi Barang</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-hashtag"></i>
                <input
                  type="text"
                  v-model="form.kodefikasi"
                  placeholder="Kode sesuai pedoman kodefikasi aset desa"
                />
              </div>
            </div>

            <div class="input-group">
              <label
                >Nama Barang / Aset <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-tag"></i>
                <input
                  type="text"
                  v-model="form.nama_aset"
                  required
                  placeholder="Contoh: Laptop ASUS / Mobil Siaga"
                />
              </div>
            </div>

            <div class="input-group">
              <label>NUP (Nomor Urut Pendaftaran)</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-list-ol"></i>
                <input
                  type="text"
                  v-model="form.nup"
                  placeholder="Contoh: 0001"
                />
              </div>
            </div>

            <div class="input-group">
              <label
                >Kategori Jenis Aset <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-layer-group"></i>
                <select v-model="form.jenis_aset" required>
                  <option value="">-- Pilih Jenis Aset --</option>
                  <option value="Tanah">Tanah</option>
                  <option value="Gedung">Gedung / Bangunan</option>
                  <option value="Kendaraan">Kendaraan Mesin</option>
                  <option value="Peralatan">Peralatan / Mesin</option>
                  <option value="Lainnya">Aset Tetap Lainnya</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label>Merk / Type</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-trademark"></i>
                <input
                  type="text"
                  v-model="form.merk_type"
                  placeholder="Contoh: ASUS ROG / Suzuki APV"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Tahun Perolehan <span class="text-danger">*</span></label>
              <div class="input-wrapper">
                <i class="fa-regular fa-calendar"></i>
                <input
                  type="number"
                  v-model="form.tahun_perolehan"
                  required
                  min="1900"
                  max="2099"
                  placeholder="Contoh: 2024"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Bukti Kepemilikan</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-file-contract"></i>
                <input
                  type="text"
                  v-model="form.bukti_kepemilikan"
                  placeholder="Contoh: Sertifikat HM / BPKB / Kuitansi"
                />
              </div>
            </div>

            <div class="input-group">
              <label
                >Kondisi Fisik Saat Ini
                <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-heart-pulse"></i>
                <select v-model="form.kondisi" required>
                  <option value="Baik">Baik</option>
                  <option value="Rusak Ringan">Rusak Ringan</option>
                  <option value="Rusak Berat">Rusak Berat</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label
                >Lokasi Penempatan <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-location-dot"></i>
                <input
                  type="text"
                  v-model="form.lokasi"
                  required
                  placeholder="Contoh: Ruang Kepala Desa"
                />
              </div>
            </div>

            <div class="input-group">
              <label>Sumber Dana</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-coins"></i>
                <select v-model="form.sumber_dana">
                  <option value="Dana Desa">Dana Desa</option>
                  <option value="ADD">ADD (Alokasi Dana Desa)</option>
                  <option value="Bantuan Provinsi">Bantuan Provinsi</option>
                  <option value="Bantuan Pusat">Bantuan Pusat</option>
                  <option value="Swadaya">Swadaya Masyarakat</option>
                  <option value="Kekayaan Asli Desa">Kekayaan Asli Desa</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label>Penanggung Jawab</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-user-shield"></i>
                <input
                  type="text"
                  v-model="form.penanggung_jawab"
                  placeholder="Contoh: Kaur Umum"
                />
              </div>
            </div>
          </div>

          <!-- ============ DETAIL DINAMIS PER JENIS ASET ============ -->
          <div v-if="form.jenis_aset" class="dynamic-section">
            <div
              class="form-section-title section-dynamic"
              style="margin-top: 0"
            >
              <i :class="iconJenis"></i> Detail Spesifik: {{ labelJenis }}
            </div>

            <!-- TANAH -->
            <div v-if="form.jenis_aset === 'Tanah'" class="form-grid">
              <div class="input-group">
                <label>Luas Tanah (m²)</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-ruler-combined"></i>
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.ukuran"
                    placeholder="Contoh: 500"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Sertifikat / Letter C</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-stamp"></i>
                  <input
                    type="text"
                    v-model="detail.no_sertifikat"
                    placeholder="Contoh: HM 1234"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Status Hak Tanah</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-scale-balanced"></i>
                  <select v-model="detail.status_hak">
                    <option value="">-- Pilih Status --</option>
                    <option value="Tanah Kas Desa">Tanah Kas Desa</option>
                    <option value="Hak Milik">Hak Milik (HM)</option>
                    <option value="Hak Pakai">Hak Pakai (HP)</option>
                    <option value="Hak Guna Bangunan">
                      Hak Guna Bangunan (HGB)
                    </option>
                    <option value="Tanah Ulayat">Tanah Ulayat</option>
                    <option value="Belum Bersertifikat">
                      Belum Bersertifikat
                    </option>
                  </select>
                </div>
              </div>
              <div class="input-group">
                <label>Atas Nama</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-user"></i>
                  <input
                    type="text"
                    v-model="detail.atas_nama"
                    placeholder="Contoh: Pemerintah Desa Macanan"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Penggunaan / Peruntukan</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-seedling"></i>
                  <input
                    type="text"
                    v-model="detail.penggunaan"
                    placeholder="Contoh: Bengkok / Lapangan / Kantor"
                  />
                </div>
              </div>
              <div class="input-group full-width">
                <label>Letak & Batas Tanah</label>
                <div class="input-wrapper align-top">
                  <i class="fa-solid fa-map"></i>
                  <textarea
                    v-model="detail.letak_batas"
                    rows="2"
                    placeholder="Tuliskan letak dan batas-batas tanah (utara, selatan, timur, barat)..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- GEDUNG / BANGUNAN -->
            <div v-else-if="form.jenis_aset === 'Gedung'" class="form-grid">
              <div class="input-group">
                <label>Luas Bangunan (m²)</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-vector-square"></i>
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.ukuran"
                    placeholder="Contoh: 120"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Luas Lahan (m²)</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-ruler-combined"></i>
                  <input
                    type="number"
                    step="0.01"
                    v-model="detail.luas_lahan"
                    placeholder="Contoh: 300"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Jumlah Lantai</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-building"></i>
                  <input
                    type="number"
                    v-model="detail.jumlah_lantai"
                    placeholder="Contoh: 2"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Konstruksi</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-trowel-bricks"></i>
                  <select v-model="detail.konstruksi">
                    <option value="">-- Pilih Konstruksi --</option>
                    <option value="Permanen">Permanen</option>
                    <option value="Semi Permanen">Semi Permanen</option>
                    <option value="Darurat">Darurat / Sementara</option>
                  </select>
                </div>
              </div>
              <div class="input-group">
                <label>Nomor IMB / PBG</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-file-lines"></i>
                  <input
                    type="text"
                    v-model="detail.no_imb"
                    placeholder="Nomor Izin Mendirikan Bangunan"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Kondisi Struktur</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-helmet-safety"></i>
                  <input
                    type="text"
                    v-model="detail.kondisi_struktur"
                    placeholder="Contoh: Atap perlu perbaikan"
                  />
                </div>
              </div>
            </div>

            <!-- KENDARAAN -->
            <div v-else-if="form.jenis_aset === 'Kendaraan'" class="form-grid">
              <div class="input-group">
                <label>Nomor Polisi</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-id-card"></i>
                  <input
                    type="text"
                    v-model="detail.no_polisi"
                    placeholder="Contoh: AG 1234 XY"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Rangka</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-barcode"></i>
                  <input
                    type="text"
                    v-model="detail.no_rangka"
                    placeholder="Nomor rangka (chassis)"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Mesin</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-gear"></i>
                  <input
                    type="text"
                    v-model="detail.no_mesin"
                    placeholder="Nomor mesin"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Tahun Pembuatan</label>
                <div class="input-wrapper">
                  <i class="fa-regular fa-calendar"></i>
                  <input
                    type="number"
                    min="1900"
                    max="2099"
                    v-model="detail.tahun_pembuatan"
                    placeholder="Contoh: 2023"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Warna</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-palette"></i>
                  <input
                    type="text"
                    v-model="detail.warna"
                    placeholder="Contoh: Hitam Metalik"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Bahan Bakar</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-gas-pump"></i>
                  <select v-model="detail.bahan_bakar">
                    <option value="">-- Pilih --</option>
                    <option value="Bensin">Bensin</option>
                    <option value="Solar">Solar</option>
                    <option value="Listrik">Listrik</option>
                    <option value="Hybrid">Hybrid</option>
                  </select>
                </div>
              </div>
              <div class="input-group">
                <label>Nomor BPKB</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-book"></i>
                  <input
                    type="text"
                    v-model="detail.no_bpkb"
                    placeholder="Nomor BPKB"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor STNK</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-file-invoice"></i>
                  <input
                    type="text"
                    v-model="detail.no_stnk"
                    placeholder="Nomor STNK"
                  />
                </div>
              </div>
            </div>

            <!-- PERALATAN / MESIN -->
            <div v-else-if="form.jenis_aset === 'Peralatan'" class="form-grid">
              <div class="input-group">
                <label>Tipe / Model</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-microchip"></i>
                  <input
                    type="text"
                    v-model="detail.tipe_model"
                    placeholder="Contoh: ROG Strix G15"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Seri / Serial Number</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-barcode"></i>
                  <input
                    type="text"
                    v-model="detail.no_seri"
                    placeholder="Serial number perangkat"
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Jumlah Unit</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-cubes"></i>
                  <input
                    type="number"
                    min="1"
                    v-model="detail.jumlah_unit"
                    placeholder="Contoh: 1"
                  />
                </div>
              </div>
              <div class="input-group full-width">
                <label>Spesifikasi / Ukuran</label>
                <div class="input-wrapper align-top">
                  <i class="fa-solid fa-list-check"></i>
                  <textarea
                    v-model="detail.spesifikasi"
                    rows="2"
                    placeholder="Tuliskan spesifikasi teknis: prosesor, RAM, dimensi, dll..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- LAINNYA -->
            <div v-else-if="form.jenis_aset === 'Lainnya'" class="form-grid">
              <div class="input-group full-width">
                <label>Spesifikasi / Rincian Detail</label>
                <div class="input-wrapper align-top">
                  <i class="fa-solid fa-circle-info"></i>
                  <textarea
                    v-model="detail.spesifikasi_bebas"
                    rows="3"
                    placeholder="Tuliskan rincian detail aset ini..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- ============ NILAI & KETERANGAN ============ -->
          <div class="form-section-title" style="margin-top: 35px">
            <i class="fa-solid fa-file-invoice-dollar"></i> Rincian Nilai &
            Keterangan
          </div>

          <div class="form-grid">
            <div class="input-group full-width">
              <label
                >Nilai Perolehan / Harga (Rp)
                <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-rupiah-sign"></i>
                <input
                  type="text"
                  v-model="form.nilai_perolehan_formatted"
                  @input="onRupiahInput"
                  required
                  placeholder="Masukkan nominal harga..."
                />
                <input type="hidden" v-model="form.nilai_perolehan" />
              </div>
            </div>

            <div class="input-group full-width">
              <label>Keterangan Tambahan</label>
              <div class="input-wrapper align-top">
                <i class="fa-solid fa-align-left"></i>
                <textarea
                  v-model="form.keterangan"
                  rows="3"
                  placeholder="Tuliskan keterangan, asal usul perolehan, atau catatan lain..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- ============ FOTO ============ -->
          <div class="form-section-title" style="margin-top: 35px">
            <i class="fa-solid fa-camera"></i> Dokumentasi Fisik Aset
          </div>

          <div class="input-group full-width">
            <div class="upload-area" @click="triggerFileInput">
              <input
                type="file"
                ref="fileInputRef"
                @change="handleFileChange"
                accept=".jpg,.jpeg,.png"
                multiple
                style="display: none"
              />

              <div v-if="uploadedPhotos.length === 0" class="upload-content">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <h4>Klik di sini untuk upload Foto Aset</h4>
                <p>Mendukung pilih banyak foto (JPG/PNG). Opsional.</p>
              </div>

              <div v-else class="file-preview-container">
                <div class="preview-header">
                  <i class="fa-solid fa-circle-check"></i>
                  {{ uploadedPhotos.length }} Foto Berhasil Dimuat:
                </div>

                <div class="file-list">
                  <div
                    v-for="(file, index) in uploadedPhotos"
                    :key="index"
                    class="file-item"
                  >
                    <i class="fa-solid fa-image"></i>
                    <span class="file-name">{{ file.name }}</span>
                    <span class="file-size">({{ formatSize(file.size) }})</span>
                  </div>
                </div>
                <div
                  style="
                    margin-top: 15px;
                    font-size: 12px;
                    color: #10b981;
                    text-decoration: underline;
                  "
                >
                  Klik area ini jika ingin mengganti foto
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
              {{ isSaving ? "Menyimpan..." : "Simpan Data Aset" }}
            </button>
          </div>
        </form>
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
const isSaving = ref(false);

// State Form Data (field umum / Buku Inventaris)
const form = ref({
  nomor_aset: "",
  kodefikasi: "",
  nup: "",
  nama_aset: "",
  jenis_aset: "",
  merk_type: "",
  bukti_kepemilikan: "",
  tahun_perolehan: new Date().getFullYear(),
  ukuran: "",
  kondisi: "Baik",
  lokasi: "",
  sumber_dana: "Dana Desa",
  penanggung_jawab: "",
  nilai_perolehan_formatted: "",
  nilai_perolehan: "",
  keterangan: "",
});

// 🔥 State terpisah untuk field detail dinamis (disimpan ke kolom JSON 'detail')
const detail = ref({});

// Label & ikon judul section dinamis sesuai jenis terpilih
const labelJenis = computed(() => {
  return (
    {
      Tanah: "Tanah",
      Gedung: "Gedung / Bangunan",
      Kendaraan: "Kendaraan Mesin",
      Peralatan: "Peralatan / Mesin",
      Lainnya: "Aset Lainnya",
    }[form.value.jenis_aset] || ""
  );
});

const iconJenis = computed(() => {
  return (
    {
      Tanah: "fa-solid fa-map-location-dot",
      Gedung: "fa-solid fa-building",
      Kendaraan: "fa-solid fa-car-side",
      Peralatan: "fa-solid fa-screwdriver-wrench",
      Lainnya: "fa-solid fa-box",
    }[form.value.jenis_aset] || "fa-solid fa-circle-info"
  );
});

// Referensi DOM untuk upload file
const fileInputRef = ref(null);
const uploadedPhotos = ref([]);

// Format Rupiah Otomatis
const formatRupiah = (angka) => {
  let number_string = angka.replace(/[^,\d]/g, "").toString();
  let split = number_string.split(",");
  let sisa = split[0].length % 3;
  let rupiah = split[0].substr(0, sisa);
  let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

  if (ribuan) {
    let separator = sisa ? "." : "";
    rupiah += separator + ribuan.join(".");
  }

  return split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
};

const onRupiahInput = (e) => {
  let val = e.target.value;
  form.value.nilai_perolehan_formatted = formatRupiah(val);
  form.value.nilai_perolehan = form.value.nilai_perolehan_formatted.replace(
    /\./g,
    "",
  );
};

// Fitur Multi-Upload Foto
const triggerFileInput = () => {
  fileInputRef.value.click();
};

const handleFileChange = (e) => {
  const files = e.target.files;
  if (files && files.length > 0) {
    uploadedPhotos.value = Array.from(files);
  }
};

const formatSize = (bytes) => {
  return (bytes / (1024 * 1024)).toFixed(2) + " MB";
};

// Eksekusi Simpan
const submitForm = async () => {
  if (
    !form.value.nama_aset ||
    !form.value.jenis_aset ||
    !form.value.tahun_perolehan ||
    !form.value.nilai_perolehan
  ) {
    Swal.fire({
      icon: "warning",
      title: "Data Belum Lengkap",
      text: "Data wajib (Nama, Jenis, Tahun, Nilai) belum diisi dengan benar!",
      confirmButtonColor: "#10b981",
    });
    return;
  }

  const formData = new FormData();
  formData.append("nomor_aset", form.value.nomor_aset);
  formData.append("kodefikasi", form.value.kodefikasi);
  formData.append("nup", form.value.nup);
  formData.append("nama_aset", form.value.nama_aset);
  formData.append("jenis_aset", form.value.jenis_aset);
  formData.append("merk_type", form.value.merk_type);
  formData.append("bukti_kepemilikan", form.value.bukti_kepemilikan);
  formData.append("tahun_perolehan", form.value.tahun_perolehan);
  formData.append("ukuran", form.value.ukuran || "");
  formData.append("kondisi", form.value.kondisi);
  formData.append("lokasi", form.value.lokasi);
  formData.append("nilai_perolehan", form.value.nilai_perolehan);
  formData.append("sumber_dana", form.value.sumber_dana);
  formData.append("penanggung_jawab", form.value.penanggung_jawab);
  formData.append("keterangan", form.value.keterangan);
  // 🔥 field detail dinamis dikirim sebagai JSON string
  formData.append("detail", JSON.stringify(detail.value));

  uploadedPhotos.value.forEach((file) => {
    formData.append("foto[]", file);
  });

  isSaving.value = true;
  try {
    await axios.post(`${import.meta.env.VITE_API_URL}/api/aset`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    await Swal.fire({
      icon: "success",
      title: "Tersimpan!",
      text: "Data Inventaris Aset berhasil disimpan ke database.",
      confirmButtonText: "Lihat Database Aset",
      confirmButtonColor: "#10b981",
      timer: 4000,
      timerProgressBar: true,
    });

    router.push("/admin/aset/list");
  } catch (error) {
    console.error("Gagal menyimpan aset:", error);
    const pesan =
      error.response?.data?.message ||
      "Terjadi kesalahan saat menyimpan data aset. Cek koneksi server/database.";
    Swal.fire({
      icon: "error",
      title: "Gagal Menyimpan",
      text: pesan,
      confirmButtonColor: "#10b981",
    });
  } finally {
    isSaving.value = false;
  }
};
</script>

<style>
/* ==================================================
   CSS INPUT ASET BARU - ULTRA PREMIUM SAAS EDITION
   ================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-aset-tambah-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
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

.text-emerald {
  color: #10b981;
}

/* --- HEADER --- */
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
  gap: 15px;
  padding: 30px 40px;
  border-radius: 20px 20px 0 0;
  color: white;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
  border-bottom: 4px solid #10b981;
}
.header-text h2 {
  font-size: 24px;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0 0 6px 0;
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
.btn-back-white {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.18);
  color: white;
  padding: 12px 24px;
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
  border-color: #ffffff;
  color: #1e293b;
  transform: translateY(-2px);
}

.form-card {
  background: white;
  border-radius: 0 0 20px 20px;
  padding: 40px;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.06);
  border: 1px solid #e2e8f0;
  border-top: none;
}

/* Judul Bagian */
.form-section-title {
  font-size: 14px;
  font-weight: 800;
  color: #059669;
  background: #ecfdf5;
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
/* Judul section dinamis: warna beda biar menonjol */
.form-section-title.section-dynamic {
  color: #b45309;
  background: #fffbeb;
  border-color: #fcd34d;
}

/* Wrapper section dinamis */
.dynamic-section {
  animation: premiumFadeInUp 0.4s ease forwards;
  border: 1px dashed #fcd34d;
  background: #fffdf5;
  border-radius: 16px;
  padding: 25px 25px 5px 25px;
  margin-top: 35px;
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
.text-danger {
  color: #ef4444;
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
.input-wrapper.align-top i {
  top: 20px;
  transform: none;
}
.input-wrapper:focus-within i {
  color: #10b981;
}

.input-wrapper input,
.input-wrapper select,
.input-wrapper textarea {
  width: 100%;
  padding: 14px 15px 14px 45px;
  border-radius: 12px;
  border: 2px solid #e2e8f0;
  background: #f8fafc;
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  outline: none;
  transition: 0.3s;
  box-sizing: border-box;
  font-family: inherit;
}
.input-wrapper select {
  height: 50px;
}
.input-wrapper textarea {
  resize: vertical;
  line-height: 1.5;
}

.input-wrapper input:focus,
.input-wrapper select:focus,
.input-wrapper textarea:focus {
  background: white;
  border-color: #10b981;
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

.input-wrapper select {
  appearance: none;
  cursor: pointer;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310b981' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 14px;
}

/* === ZONA UPLOAD FOTO MULTI-FILE === */
.upload-area {
  border: 2px dashed #6ee7b7;
  border-radius: 16px;
  background: #f0fdf4;
  padding: 35px;
  text-align: center;
  cursor: pointer;
  transition: 0.3s ease;
}
.upload-content i {
  font-size: 40px;
  color: #10b981;
  margin-bottom: 15px;
  transition: 0.3s;
}
.upload-area:hover .upload-content i {
  transform: translateY(-5px) scale(1.1);
}
.upload-content h4 {
  font-weight: 800;
  color: #059669;
  font-size: 16px;
  margin: 0 0 5px 0;
}
.upload-content p {
  color: #16a34a;
  font-size: 13px;
  margin: 0;
}
.upload-area:hover {
  border-color: #10b981;
  background: #dcfce7;
}

.file-preview-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}
.preview-header {
  font-weight: 800;
  color: #059669;
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
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
  transition: 0.3s;
}
.btn-save {
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
  box-shadow: 0 12px 25px rgba(16, 185, 129, 0.4);
}
.btn-save:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* === MOBILE RESPONSIVE === */
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
  .vue-aset-tambah-body {
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
    border-top: 4px solid #10b981;
  }
  .form-grid {
    grid-template-columns: 1fr !important;
    gap: 15px !important;
  }
  .full-width {
    grid-column: span 1 !important;
  }
  .dynamic-section {
    padding: 18px 15px 2px 15px;
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
  .input-wrapper select {
    font-size: 13px !important;
  }
  .btn-save {
    font-size: 14px !important;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px !important;
  }
  .form-card {
    padding: 14px 12px !important;
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
</style>
