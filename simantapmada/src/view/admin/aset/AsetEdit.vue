<template>
  <div class="vue-aset-edit-body">
    <div class="main-wrapper">
      <div class="page-header-box">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-pen-to-square text-amber"></i> Perbarui Data
            Aset
          </h2>
          <p>Ubah rincian, kondisi, atau perbarui foto inventaris desa.</p>
        </div>
        <button
          class="btn-back-white"
          @click="router.push('/admin/aset/list')"
          type="button"
        >
          <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
      </div>

      <div class="form-card">
        <div v-if="isLoadingData" style="text-align: center; padding: 60px">
          <i
            class="fa-solid fa-spinner fa-spin"
            style="font-size: 36px; color: var(--primary-light)"
          ></i>
          <p style="margin-top: 15px; color: #64748b; font-weight: 600">
            Memuat data aset...
          </p>
        </div>

        <form v-else @submit.prevent="submitForm">
          <!-- ============ IDENTITAS UMUM ============ -->
          <div class="form-section-title">
            <i class="fa-solid fa-cube"></i> Identitas Aset (Buku Inventaris)
          </div>

          <div class="form-grid">
            <div class="input-group">
              <label
                >Nomor Register Aset
                <span class="hint">(Generate Otomatis/Manual)</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-barcode"></i>
                <input type="text" v-model="form.nomor_aset" />
              </div>
            </div>

            <div class="input-group">
              <label>Kodefikasi Barang</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-hashtag"></i>
                <input type="text" v-model="form.kodefikasi" />
              </div>
            </div>

            <div class="input-group">
              <label
                >Nama Barang / Aset <span class="text-danger">*</span></label
              >
              <div class="input-wrapper">
                <i class="fa-solid fa-tag"></i>
                <input type="text" v-model="form.nama_aset" required />
              </div>
            </div>

            <div class="input-group">
              <label>NUP (Nomor Urut Pendaftaran)</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-list-ol"></i>
                <input type="text" v-model="form.nup" />
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
                <input type="text" v-model="form.merk_type" />
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
                />
              </div>
            </div>

            <div class="input-group">
              <label>Bukti Kepemilikan</label>
              <div class="input-wrapper">
                <i class="fa-solid fa-file-contract"></i>
                <input type="text" v-model="form.bukti_kepemilikan" />
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
                <input type="text" v-model="form.lokasi" required />
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
                <input type="text" v-model="form.penanggung_jawab" />
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
                  <input type="number" step="0.01" v-model="form.ukuran" />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Sertifikat / Letter C</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-stamp"></i>
                  <input type="text" v-model="detail.no_sertifikat" />
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
                  <input type="text" v-model="detail.atas_nama" />
                </div>
              </div>
              <div class="input-group">
                <label>Penggunaan / Peruntukan</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-seedling"></i>
                  <input type="text" v-model="detail.penggunaan" />
                </div>
              </div>
              <div class="input-group full-width">
                <label>Letak & Batas Tanah</label>
                <div class="input-wrapper align-top">
                  <i class="fa-solid fa-map"></i>
                  <textarea v-model="detail.letak_batas" rows="2"></textarea>
                </div>
              </div>
            </div>

            <!-- GEDUNG / BANGUNAN -->
            <div v-else-if="form.jenis_aset === 'Gedung'" class="form-grid">
              <div class="input-group">
                <label>Luas Bangunan (m²)</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-vector-square"></i>
                  <input type="number" step="0.01" v-model="form.ukuran" />
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
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Jumlah Lantai</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-building"></i>
                  <input type="number" v-model="detail.jumlah_lantai" />
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
                  <input type="text" v-model="detail.no_imb" />
                </div>
              </div>
              <div class="input-group">
                <label>Kondisi Struktur</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-helmet-safety"></i>
                  <input type="text" v-model="detail.kondisi_struktur" />
                </div>
              </div>
            </div>

            <!-- KENDARAAN -->
            <div v-else-if="form.jenis_aset === 'Kendaraan'" class="form-grid">
              <div class="input-group">
                <label>Nomor Polisi</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-id-card"></i>
                  <input type="text" v-model="detail.no_polisi" />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Rangka</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-barcode"></i>
                  <input type="text" v-model="detail.no_rangka" />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Mesin</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-gear"></i>
                  <input type="text" v-model="detail.no_mesin" />
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
                  />
                </div>
              </div>
              <div class="input-group">
                <label>Warna</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-palette"></i>
                  <input type="text" v-model="detail.warna" />
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
                  <input type="text" v-model="detail.no_bpkb" />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor STNK</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-file-invoice"></i>
                  <input type="text" v-model="detail.no_stnk" />
                </div>
              </div>
            </div>

            <!-- PERALATAN / MESIN -->
            <div v-else-if="form.jenis_aset === 'Peralatan'" class="form-grid">
              <div class="input-group">
                <label>Tipe / Model</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-microchip"></i>
                  <input type="text" v-model="detail.tipe_model" />
                </div>
              </div>
              <div class="input-group">
                <label>Nomor Seri / Serial Number</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-barcode"></i>
                  <input type="text" v-model="detail.no_seri" />
                </div>
              </div>
              <div class="input-group">
                <label>Jumlah Unit</label>
                <div class="input-wrapper">
                  <i class="fa-solid fa-cubes"></i>
                  <input type="number" min="1" v-model="detail.jumlah_unit" />
                </div>
              </div>
              <div class="input-group full-width">
                <label>Spesifikasi / Ukuran</label>
                <div class="input-wrapper align-top">
                  <i class="fa-solid fa-list-check"></i>
                  <textarea v-model="detail.spesifikasi" rows="2"></textarea>
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
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- ============ NILAI ============ -->
          <div class="form-section-title" style="margin-top: 35px">
            <i class="fa-solid fa-file-invoice-dollar"></i> Rincian Nilai
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
                />
                <input type="hidden" v-model="form.nilai_perolehan" />
              </div>
            </div>
          </div>

          <!-- ============ FOTO ============ -->
          <div class="form-section-title" style="margin-top: 35px">
            <i class="fa-solid fa-image"></i> Pembaruan Foto Aset
          </div>

          <div class="foto-grid">
            <div class="foto-sekarang-box">
              <span class="foto-label">Foto Saat Ini</span>
              <div class="img-wrapper">
                <img v-if="fotoSekarang" :src="fotoSekarang" alt="Foto Aset" />
                <div v-else class="no-img">
                  <i class="fa-solid fa-image-slash"></i> Belum ada foto
                </div>
              </div>
            </div>

            <div
              class="upload-box"
              :class="{ 'has-file': previewBaru }"
              @click="triggerFileInput"
            >
              <span class="foto-label">Upload Foto Baru (Opsional)</span>

              <div v-if="!previewBaru" class="upload-content">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span class="upload-title">Pilih Foto Baru</span>
                <span class="upload-subtitle">Format: JPG/PNG (Max 2MB)</span>
              </div>

              <div v-else class="preview-content" @click.stop>
                <img :src="previewBaru" class="img-preview" alt="Preview" />
                <button
                  type="button"
                  class="btn-hapus-foto"
                  @click="hapusFotoBaru"
                >
                  <i class="fa-solid fa-trash"></i> Hapus
                </button>
              </div>

              <input
                type="file"
                ref="fileInputRef"
                accept=".jpg,.jpeg,.png"
                hidden
                @change="handleFileChange"
              />
            </div>
          </div>

          <div class="input-group full-width" style="margin-top: 25px">
            <label>Keterangan / Catatan Admin</label>
            <div class="input-wrapper align-top">
              <i class="fa-solid fa-align-left"></i>
              <textarea
                v-model="form.keterangan"
                rows="3"
                placeholder="Tuliskan catatan perubahan atau info lainnya..."
              ></textarea>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-save" :disabled="isSaving">
              <i
                :class="
                  isSaving ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-save'
                "
              ></i>
              {{ isSaving ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const route = useRoute();

const isLoadingData = ref(true);
const isSaving = ref(false);

// State Form Data (field umum, diisi dari API saat halaman dibuka)
const form = ref({
  id: route.params.id,
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

// 🔥 State terpisah untuk field detail dinamis
const detail = ref({});

// State Foto
const fotoSekarang = ref(null);
const previewBaru = ref(null);
const fileBaru = ref(null);
const fileInputRef = ref(null);

// Label & ikon judul section dinamis
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

// Format angka mentah jadi "15.000.000" buat ngisi input pertama kali
const initRupiahFormat = (rawValue) => {
  if (!rawValue) return "";
  const intPart = Math.round(Number(rawValue)).toString();
  return intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

// 🔥 Ambil data aset asli dari database berdasarkan ID di URL
const fetchAsetDetail = async () => {
  isLoadingData.value = true;
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_API_URL}/api/aset/${route.params.id}`,
    );
    const data = res.data?.data;

    if (data) {
      form.value.id = data.id;
      form.value.nomor_aset = data.nomor_aset || "";
      form.value.kodefikasi = data.kodefikasi || "";
      form.value.nup = data.nup || "";
      form.value.nama_aset = data.nama_aset || "";
      form.value.jenis_aset = data.jenis_aset || "";
      form.value.merk_type = data.merk_type || "";
      form.value.bukti_kepemilikan = data.bukti_kepemilikan || "";
      form.value.tahun_perolehan =
        data.tahun_perolehan || new Date().getFullYear();
      form.value.ukuran = data.ukuran || "";
      form.value.kondisi = data.kondisi || "Baik";
      form.value.lokasi = data.lokasi || "";
      form.value.sumber_dana = data.sumber_dana || "Dana Desa";
      form.value.penanggung_jawab = data.penanggung_jawab || "";
      form.value.nilai_perolehan = data.nilai_perolehan || "";
      form.value.nilai_perolehan_formatted = initRupiahFormat(
        data.nilai_perolehan,
      );
      form.value.keterangan = data.keterangan || "";

      // 🔥 detail dinamis: dari DB sudah berupa object (model cast 'array')
      detail.value = data.detail || {};

      fotoSekarang.value =
        data.foto && data.foto.length > 0 ? data.foto[0] : null;
    }
  } catch (error) {
    console.error("Gagal memuat data aset:", error);
    Swal.fire({
      icon: "error",
      title: "Data Tidak Ditemukan",
      text: "Gagal memuat data aset. Aset mungkin sudah dihapus.",
      confirmButtonColor: "var(--primary-light)",
    }).then(() => router.push("/admin/aset/list"));
  } finally {
    isLoadingData.value = false;
  }
};

onMounted(fetchAsetDetail);

// Format Rupiah saat user mengetik
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

// Logika Foto
const triggerFileInput = () => {
  if (!previewBaru.value) fileInputRef.value.click();
};

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    previewBaru.value = URL.createObjectURL(file);
    fileBaru.value = file;
  }
};

const hapusFotoBaru = () => {
  previewBaru.value = null;
  fileBaru.value = null;
  if (fileInputRef.value) fileInputRef.value.value = null;
};

// Eksekusi Simpan
const submitForm = async () => {
  const formData = new FormData();
  formData.append("_method", "PUT"); // spoofing biar Laravel proses sebagai PUT tapi file kebaca
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
  formData.append("detail", JSON.stringify(detail.value));

  if (fileBaru.value) {
    formData.append("foto_baru[]", fileBaru.value);
  }

  isSaving.value = true;
  try {
    await axios.post(
      `${import.meta.env.VITE_API_URL}/api/aset/${form.value.id}`,
      formData,
      { headers: { "Content-Type": "multipart/form-data" } },
    );

    await Swal.fire({
      icon: "success",
      title: "Perubahan Disimpan!",
      text: "Data aset berhasil diperbarui di database.",
      confirmButtonText: "Lihat Database Aset",
      confirmButtonColor: "var(--primary-light)",
      timer: 4000,
      timerProgressBar: true,
    });

    router.push("/admin/aset/list");
  } catch (error) {
    console.error("Gagal memperbarui aset:", error);
    const pesan =
      error.response?.data?.message ||
      "Terjadi kesalahan saat menyimpan perubahan. Cek koneksi server/database.";
    Swal.fire({
      icon: "error",
      title: "Gagal Menyimpan",
      text: pesan,
      confirmButtonColor: "var(--primary-light)",
    });
  } finally {
    isSaving.value = false;
  }
};
</script>

<style>
/* ==================================================
   CSS EDIT ASET - ULTRA PREMIUM AMBER/GOLD EDITION
   ================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-aset-edit-body {
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

.text-amber {
  color: var(--primary-light);
}

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
  border-bottom: 4px solid var(--primary-light);
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
  box-shadow: 0 15px 40px rgba(5, 150, 105, 0.06);
  border: 1px solid #e2e8f0;
  border-top: none;
}

.form-section-title {
  font-size: 14px;
  font-weight: 800;
  color: var(--primary-dark);
  background: #ecfdf5;
  border: 1px solid #6ee7b7;
  padding: 12px 20px;
  border-radius: 10px;
  margin-bottom: 25px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.form-section-title.section-dynamic {
  color: #047857;
  background: #ecfdf5;
  border-color: #6ee7b7;
}

.dynamic-section {
  animation: premiumFadeInUp 0.4s ease forwards;
  border: 1px dashed #6ee7b7;
  background: #f0fdf4;
  border-radius: 16px;
  padding: 25px 25px 5px 25px;
  margin-top: 35px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px 25px;
}
.full-width {
  grid-column: span 2;
}

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
  color: var(--primary-dark);
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
  border-color: var(--primary-light);
  box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
}

.input-wrapper select {
  appearance: none;
  cursor: pointer;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23d97706' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 14px;
}

/* === ZONA FOTO SPLIT (LAMA VS BARU) === */
.foto-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
  margin-top: 10px;
}
.foto-label {
  display: block;
  font-size: 12px;
  font-weight: 800;
  color: #475569;
  text-transform: uppercase;
  margin-bottom: 10px;
  letter-spacing: 0.5px;
}
.foto-sekarang-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.img-wrapper {
  width: 100%;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  background: #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #cbd5e1;
}
.img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.no-img {
  color: #94a3b8;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.no-img i {
  font-size: 30px;
}

.upload-box {
  background: #ecfdf5;
  border: 2px dashed #6ee7b7;
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.3s;
  position: relative;
  height: 100%;
  box-sizing: border-box;
}
.upload-box:hover:not(.has-file) {
  border-color: var(--primary-light);
  background: #d1fae5;
}
.upload-content {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}
.upload-content i {
  font-size: 40px;
  color: var(--primary-light);
  margin-bottom: 10px;
  transition: 0.3s;
}
.upload-box:hover .upload-content i {
  transform: translateY(-5px) scale(1.1);
}
.upload-title {
  font-weight: 800;
  color: var(--primary-dark);
  font-size: 15px;
  margin-bottom: 5px;
}
.upload-subtitle {
  color: var(--primary-dark);
  font-size: 12px;
}

.preview-content {
  position: relative;
  width: 100%;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #6ee7b7;
}
.img-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.btn-hapus-foto {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  transition: 0.3s;
}
.btn-hapus-foto:hover {
  background: #dc2626;
  transform: scale(1.05);
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
  background: linear-gradient(
    135deg,
    var(--primary-light) 0%,
    var(--primary-dark) 100%
  );
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
  box-shadow: 0 12px 25px rgba(5, 150, 105, 0.4);
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
  .vue-aset-edit-body {
    padding: 95px 15px 20px !important;
    overflow-x: hidden !important;
  }
  .main-wrapper {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
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
    border-top: 4px solid var(--primary-light);
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
  .foto-grid {
    grid-template-columns: 1fr !important;
    gap: 20px !important;
  }
  .upload-box {
    min-height: 200px;
    padding: 10px;
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
