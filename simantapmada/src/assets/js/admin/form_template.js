// FILE: src/assets/js/admin/form_template.js

// 1. DATA MASTER KOTA
export const masterKota = [
  "Nganjuk",
  "Kediri",
  "Jombang",
  "Madiun",
  "Bojonegoro",
  "Ngawi",
  "Magetan",
  "Ponorogo",
  "Pacitan",
  "Trenggalek",
  "Tulungagung",
  "Blitar",
  "Malang",
  "Surabaya",
  "Sidoarjo",
  "Gresik",
  "Mojokerto",
  "Banyuwangi",
  "Tuban",
  "Lamongan",
  "Lumajang",
  "Probolinggo",
  "Pasuruan",
  "Jember",
  "Bondowoso",
  "Situbondo",
  "Bangkalan",
  "Sampang",
  "Pamekasan",
  "Sumenep",
  "Jakarta",
  "Bandung",
  "Semarang",
  "Yogyakarta",
  "Surakarta",
  "Denpasar",
  "Medan",
  "Makassar",
  "Palembang",
  "Balikpapan",
  "Samarinda",
  "Banjarmasin",
  "Pontianak",
  "Batam",
  "Padang",
  "Pekanbaru",
  "Bandar Lampung",
  "Ambon",
  "Jayapura",
  "Mataram",
  "Kupang",
  "Tangerang",
  "Bekasi",
  "Depok",
  "Bogor",
].sort();

export const datalistKota =
  `<datalist id="daftar_kota">` +
  masterKota.map((kota) => `<option value="${kota}"></option>`).join("") +
  `</datalist>`;

// 2. KOMPONEN KERANGKA FORM (BIODATA)
export const bioLengkap = `
    <div class="section-label"><i class="fa-solid fa-address-card"></i> Data Pemohon (Biodata Lengkap)</div>
    <div class="form-grid">
        <div class="form-group"><label>NIK (Sesuai KTP)</label><input type="number" name="detail[data_pemohon][nik]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Lengkap</label><input type="text" name="detail[data_pemohon][nama]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir</label><input type="text" name="detail[data_pemohon][tempat_lahir]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required>${datalistKota}</div>
        <div class="form-group"><label>Tanggal Lahir <span class="asterisk">*</span></label><div class="input-wrapper"><input type="date" name="detail[data_pemohon][tgl_lahir]" class="input-detail" required onclick="this.showPicker()" style="cursor:pointer"></div></div>
        <div class="form-group"><label>Jenis Kelamin</label><select name="detail[data_pemohon][jk]" class="input-detail" required><option>Laki-Laki</option><option>Perempuan</option></select></div>
        <div class="form-group"><label>Kewarganegaraan</label><input type="text" name="detail[data_pemohon][kewarganegaraan]" class="input-detail" value="Indonesia" required></div>
        <div class="form-group"><label>Agama</label><select name="detail[data_pemohon][agama]" class="input-detail" required><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select></div>
        <div class="form-group"><label>Pekerjaan</label><input type="text" name="detail[data_pemohon][pekerjaan]" class="input-detail" required></div>
        <div class="form-group"><label>Nomor HP / WA</label><input type="number" name="detail[data_pemohon][hp]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap</label><input type="text" name="detail[data_pemohon][alamat]" class="input-detail" placeholder="Jalan, RT/RW, Dusun, Desa" required></div>
    </div>
    <div class="divider"></div>
    <div class="section-label"><i class="fa-solid fa-file-pen"></i> Detail Form Pengajuan</div>
    <div class="form-grid">
`;

export const bioSingkat = `
    <div class="section-label"><i class="fa-solid fa-address-card"></i> Data Pemohon (Biodata Singkat)</div>
    <div class="form-grid">
        <div class="form-group"><label>Nama Lengkap</label><input type="text" name="detail[data_pemohon][nama]" class="input-detail" required></div>
        <div class="form-group"><label>Jenis Kelamin</label><select name="detail[data_pemohon][jk]" class="input-detail" required><option>Laki-Laki</option><option>Perempuan</option></select></div>
        <div class="form-group"><label>Kewarganegaraan</label><input type="text" name="detail[data_pemohon][kewarganegaraan]" class="input-detail" value="Indonesia" required></div>
        <div class="form-group"><label>Nomor HP / WA</label><input type="number" name="detail[data_pemohon][hp]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap</label><input type="text" name="detail[data_pemohon][alamat]" class="input-detail" placeholder="Jalan, RT/RW, Dusun, Desa" required></div>
    </div>
    <div class="divider"></div>
    <div class="section-label"><i class="fa-solid fa-file-pen"></i> Detail Form Pengajuan</div>
    <div class="form-grid">
`;
export const tutupGrid = `</div>`;

export const bioLengkapStatus = `
    <div class="section-label"><i class="fa-solid fa-address-card"></i> Data Pemohon (Biodata Lengkap)</div>
    <div class="form-grid">
        <div class="form-group"><label>NIK (Sesuai KTP)</label><input type="number" name="detail[data_pemohon][nik]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Lengkap</label><input type="text" name="detail[data_pemohon][nama]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir</label><input type="text" name="detail[data_pemohon][tempat_lahir]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required>${datalistKota}</div>
        <div class="form-group"><label>Tanggal Lahir <span class="asterisk">*</span></label><div class="input-wrapper"><input type="date" name="detail[data_pemohon][tgl_lahir]" class="input-detail" required onclick="this.showPicker()" style="cursor:pointer"></div></div>
        <div class="form-group"><label>Jenis Kelamin</label><select name="detail[data_pemohon][jk]" class="input-detail" required><option>Laki-Laki</option><option>Perempuan</option></select></div>
        <div class="form-group"><label>Agama</label><select name="detail[data_pemohon][agama]" class="input-detail" required><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select></div>
        <div class="form-group"><label>Kewarganegaraan</label><input type="text" name="detail[data_pemohon][kewarganegaraan]" class="input-detail" value="Indonesia" required></div>
        <div class="form-group"><label>Status Pernikahan</label><select name="detail[data_pemohon][status_kawin]" class="input-detail" required><option>Belum Kawin</option><option>Kawin</option><option>Cerai Hidup</option><option>Cerai Mati</option></select></div>
        <div class="form-group"><label>Pekerjaan</label><input type="text" name="detail[data_pemohon][pekerjaan]" class="input-detail" required></div>
        <div class="form-group"><label>Nomor HP / WA</label><input type="number" name="detail[data_pemohon][hp]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap</label><input type="text" name="detail[data_pemohon][alamat]" class="input-detail" placeholder="Jalan, RT/RW, Dusun, Desa" required></div>
    </div>
    <div class="divider"></div>
    <div class="section-label"><i class="fa-solid fa-file-pen"></i> Detail Form Pengajuan</div>
    <div class="form-grid">
`;

// 3. DATA INPUT KHUSUS SEMUA LAYANAN (DARI LO MURNI)
export const customFields = {
  // === 1. KEPENDUDUKAN & IDENTITAS ===
  domisili:
    bioLengkapStatus +
    `
        <div class="form-group"><label>Golongan Darah</label>
            <select name="detail[gol_darah]" class="input-detail">
                <option value="-">-</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
            </select>
        </div>
        <div class="form-group"><label>Kewarganegaraan</label><input type="text" name="detail[kewarganegaraan]" class="input-detail" value="WNI/Indonesia" required></div>
        <div class="form-group full-width"><label>Alamat Domisili Saat Ini</label><input type="text" name="detail[alamat_domisili]" class="input-detail" placeholder="Contoh: Dusun Gilis RT. 004 RW. 001 Desa Macanan Kec.Loceret Kab.Nganjuk" required></div>
        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Untuk Persyaratan Pembuatan Akte Kematian" required></div>
    ` +
    tutupGrid,

  kelahiran:
    bioLengkap +
    `
        <div class="form-group full-width"><label>Nama Bayi</label><input type="text" name="detail[nama_bayi]" class="input-detail" required></div>
        <div class="form-group"><label>Jenis Kelamin Bayi</label><select name="detail[jk_bayi]" class="input-detail"><option>Laki-laki</option><option>Perempuan</option></select></div>
        
        <div class="form-group"><label>Tempat Lahir Bayi</label>
            <input type="text" name="detail[tempat_lahir_bayi]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required>
            ${datalistKota}
        </div>
        <div class="form-group"><label>Tanggal Lahir Bayi</label><input type="date" name="detail[tgl_lahir_bayi]" class="input-detail" required></div>
        
        <div class="form-group"><label>Nama Ayah</label><input type="text" name="detail[nama_ayah]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Ibu</label><input type="text" name="detail[nama_ibu]" class="input-detail" required></div>` +
    tutupGrid,

  kematian:
    bioLengkap +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Orang yang Meninggal (Jenazah)</div>
        <div class="form-grid">
        <div class="form-group"><label>NIK Jenazah</label><input type="number" name="detail[nik_jenazah]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Lengkap Jenazah</label><input type="text" name="detail[nama_jenazah]" class="input-detail" required></div>
        
        <div class="form-group"><label>Tempat Lahir Jenazah</label><input type="text" name="detail[tempat_lahir_jenazah]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Jenazah</label><input type="date" name="detail[tgl_lahir_jenazah]" class="input-detail" required></div>
        
        <div class="form-group"><label>Jenis Kelamin Jenazah</label><select name="detail[jk_jenazah]" class="input-detail"><option>Laki-laki</option><option>Perempuan</option></select></div>
        <div class="form-group"><label>Agama Jenazah</label><select name="detail[agama_jenazah]" class="input-detail"><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select></div>
        
        <div class="form-group full-width">
            <label style="display: flex; justify-content: space-between; align-items: center;">
                Alamat Lengkap Jenazah 
                <span style="font-size:12px; color:#f97316; cursor:pointer; font-weight:600;">
                    <input type="checkbox" onchange="toggleAlamat('alamat_jenazah', this)" style="cursor:pointer; transform: scale(1.2); margin-right: 5px;"> Samakan dgn Pelapor
                </span>
            </label>
            <input type="text" name="detail[alamat_jenazah]" class="input-detail" placeholder="Jalan, RT/RW, Dusun, Desa" required>
        </div>

        </div> <div class="divider"></div> <div class="section-label">Detail Kematian & Pelapor</div>
        <div class="form-grid">
        
        <div class="form-group"><label>Tanggal Meninggal</label><input type="date" name="detail[tgl_meninggal]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Meninggal</label><input type="text" name="detail[tempat_meninggal]" class="input-detail" placeholder="Contoh: Di Rumah / RSUD Nganjuk" required></div>
        <div class="form-group"><label>Penyebab Kematian</label><input type="text" name="detail[penyebab]" class="input-detail" placeholder="Contoh: Sakit / Kecelakaan" required></div>
        
        <div class="form-group full-width"><label>Hubungan Pelapor dgn Jenazah</label><input type="text" name="detail[hubungan]" class="input-detail" placeholder="Contoh: Anak Kandung / Ibu Kandung" required></div>` +
    tutupGrid,

  pindah:
    bioLengkapStatus +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Kepindahan</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Alasan Pindah</label><input type="text" name="detail[alasan_pindah]" class="input-detail" placeholder="Contoh: Mengikuti Suami / Pekerjaan / dll" required></div>
        <div class="form-group full-width"><label>Alamat Tujuan (Jalan / RT / RW)</label><input type="text" name="detail[alamat_tujuan]" class="input-detail" placeholder="Contoh: Jl. Merdeka No. 10 RT 01 RW 02" required></div>
        <div class="form-group"><label>Desa / Kelurahan Tujuan</label><input type="text" name="detail[desa_tujuan]" class="input-detail" required></div>
        <div class="form-group"><label>Kecamatan Tujuan</label><input type="text" name="detail[kec_tujuan]" class="input-detail" required></div>
        <div class="form-group"><label>Kabupaten / Kota Tujuan</label><input type="text" name="detail[kab_tujuan]" class="input-detail" required></div>
        <div class="form-group"><label>Provinsi Tujuan</label><input type="text" name="detail[prov_tujuan]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Jumlah Keluarga yang Ikut (Pengikut)</label><input type="number" name="detail[jumlah_pengikut]" class="input-detail" value="0" required></div>
    ` +
    tutupGrid,

  beda_nama:
    bioLengkapStatus +
    `
        <div class="form-group full-width"><label>Perbedaan Pada Data (Contoh: Nama / Tanggal Lahir)</label><input type="text" name="detail[jenis_perbedaan]" class="input-detail" required></div>
        
        <div class="form-group"><label>Tertulis di Dokumen (Salah)</label><input type="text" name="detail[dokumen_salah]" class="input-detail" placeholder="Contoh: KTP" required></div>
        <div class="form-group"><label>Data yang Salah</label><input type="text" name="detail[data_salah]" class="input-detail" placeholder="Contoh: 20-06-1982" required></div>
        
        <div class="form-group"><label>Tertulis di Dokumen (Benar)</label><input type="text" name="detail[dokumen_benar]" class="input-detail" placeholder="Contoh: KK dan Buku Nikah" required></div>
        <div class="form-group"><label>Data yang Benar</label><input type="text" name="detail[data_benar]" class="input-detail" placeholder="Contoh: 20-07-1982" required></div>` +
    tutupGrid,

  skip:
    bioLengkapStatus +
    `
        <div class="form-group full-width"><label>Keperluan Pembuatan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" value="Sebagai identitas penduduk sementara" required></div>` +
    tutupGrid,

  // === 2. PERNIKAHAN & KELUARGA ===
  nikah:
    bioLengkapStatus +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Calon Pasangan</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Nama Calon Pasangan</label><input type="text" name="detail[nama_pasangan]" class="input-detail" placeholder="Contoh: AMANDA FEBRIANA" required></div>
        <div class="form-group"><label>Tempat Lahir Pasangan</label><input type="text" name="detail[tempat_lahir_pasangan]" class="input-detail" required></div>
        <div class="form-group"><label>Tanggal Lahir Pasangan</label><input type="date" name="detail[tgl_lahir_pasangan]" class="input-detail" required></div>
        <div class="form-group"><label>Agama Pasangan</label><input type="text" name="detail[agama_pasangan]" class="input-detail" value="Islam" required></div>
        <div class="form-group"><label>Pekerjaan Pasangan</label><input type="text" name="detail[pekerjaan_pasangan]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap Pasangan</label><input type="text" name="detail[alamat_pasangan]" class="input-detail" placeholder="Contoh: Ds. Cerme Kec. Pace Kab. Nganjuk" required></div>
        
        </div> <div class="divider"></div> <div class="section-label">Rencana Pelaksanaan</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Tempat Pelaksanaan Nikah</label><input type="text" name="detail[tempat_nikah]" class="input-detail" placeholder="Contoh: KUA Kecamatan Loceret" required></div>
    ` +
    tutupGrid,

  numpang_nikah:
    bioLengkap +
    `
        <div class="form-group"><label>Nomor KK Pemohon</label><input type="number" name="detail[no_kk]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Calon Pasangan</label><input type="text" name="detail[nama_pasangan]" class="input-detail" placeholder="Contoh: AMANDA FEBRIANA KETRIN" required></div>
        <div class="form-group full-width"><label>Alamat Tujuan Numpang Nikah</label><input type="text" name="detail[alamat_tujuan]" class="input-detail" placeholder="Contoh: Jl. Semerus Rt/Rw : 002/003 Desa Cerme Kec Pace Kab Nganjuk" required></div>` +
    tutupGrid,

  belum_menikah:
    bioLengkapStatus +
    `
        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Persyaratan Melamar Pekerjaan"></div>
    ` +
    tutupGrid,

  nikah_siri:
    bioLengkapStatus +
    `
        <div class="form-group"><label>Nama Ayah Kandung Pemohon</label><input type="text" name="detail[ayah_pemohon]" class="input-detail" placeholder="Contoh: MUJIONO" required></div>
        <div class="form-group"><label>Status Pemohon</label>
            <select name="detail[status_pemohon]" class="input-detail" required>
                <option value="binti">Binti (Jika Perempuan)</option>
                <option value="bin">Bin (Jika Laki-laki)</option>
            </select>
        </div>
        
        </div> <div class="divider"></div> <div class="section-label">Data Pihak Kedua (Pasangan)</div>
        <div class="form-grid">
        <div class="form-group"><label>Nama Pasangan</label><input type="text" name="detail[nama_pasangan]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Ayah Pasangan</label><input type="text" name="detail[ayah_pasangan]" class="input-detail" placeholder="Contoh: NASRUN" required></div>
        <div class="form-group"><label>Status Pasangan</label>
            <select name="detail[status_pasangan]" class="input-detail" required>
                <option value="bin">Bin (Laki-laki)</option>
                <option value="binti">Binti (Perempuan)</option>
            </select>
        </div>
        <div class="form-group"><label>Tempat Lahir Pasangan</label><input type="text" name="detail[tempat_lahir_pasangan]" class="input-detail" value="Tuban" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Pasangan</label><input type="date" name="detail[tgl_lahir_pasangan]" class="input-detail" required></div>
        <div class="form-group"><label>Agama Pasangan</label>
            <select name="detail[agama_pasangan]" class="input-detail" required>
                <option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option>
            </select>
        </div>
        <div class="form-group"><label>Pekerjaan Pasangan</label><input type="text" name="detail[pekerjaan_pasangan]" class="input-detail" required></div>
        <div class="form-group"><label>Status Perkawinan Pasangan</label>
            <select name="detail[status_kawin_pasangan]" class="input-detail" required>
                <option>Belum Kawin</option><option>Kawin</option><option>Cerai Hidup</option><option>Cerai Mati</option>
            </select>
        </div>
        <div class="form-group full-width"><label>Alamat Lengkap Pasangan</label><input type="text" name="detail[alamat_pasangan]" class="input-detail" required></div>

        </div> <div class="divider"></div> <div class="section-label">Detail Pelaksanaan & Saksi</div>
        <div class="form-grid">
        <div class="form-group"><label>Tanggal Pelaksanaan Nikah</label><input type="date" name="detail[tgl_pelaksanaan]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Penghulu / Wali</label><input type="text" name="detail[nama_penghulu]" class="input-detail" placeholder="Contoh: ACHMAD SURURI" required></div>
        <div class="form-group"><label>Nama Saksi 1</label><input type="text" name="detail[saksi_1]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Saksi 2</label><input type="text" name="detail[saksi_2]" class="input-detail" required></div>` +
    tutupGrid,

  janda_duda:
    bioLengkapStatus +
    `
        </div> <div class="divider"></div> <div class="section-label">Keterangan Status</div>
        <div class="form-grid">
        <div class="form-group"><label>Status Saat Ini</label>
            <select name="detail[status_janda_duda]" class="input-detail" required>
                <option value="Janda (Cerai Mati)">Janda (Cerai Mati)</option>
                <option value="Janda (Cerai Hidup)">Janda (Cerai Hidup)</option>
                <option value="Duda (Cerai Mati)">Duda (Cerai Mati)</option>
                <option value="Duda (Cerai Hidup)">Duda (Cerai Hidup)</option>
            </select>
        </div>
        <div class="form-group"><label>Nama Mantan Pasangan / Almarhum</label><input type="text" name="detail[nama_pasangan]" class="input-detail" placeholder="Sebutkan namanya..." required></div>
        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Mengurus Pensiun / Persyaratan Nikah" required></div>
    ` +
    tutupGrid,

  ket_menikah:
    bioLengkapStatus +
    `
        <div class="form-group full-width"><label>Nama Pasangan (Suami/Istri)</label><input type="text" name="detail[nama_pasangan]" class="input-detail" required></div>
        
        <div class="form-group"><label>Tempat Lahir Pasangan</label><input type="text" name="detail[tempat_lahir_pasangan]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Pasangan</label><input type="date" name="detail[tgl_lahir_pasangan]" class="input-detail" required></div>
        
        <div class="form-group full-width"><label>Pekerjaan Pasangan</label><input type="text" name="detail[pekerjaan_pasangan]" class="input-detail" required></div>
        
        </div> <div class="divider"></div> <div class="section-label">Detail Pencatatan Nikah</div>
        <div class="form-grid"> 
        <div class="form-group"><label>Tanggal Pernikahan</label><input type="date" name="detail[tgl_nikah]" class="input-detail" required></div>
        <div class="form-group"><label>Lokasi / KUA Pernikahan</label><input type="text" name="detail[lokasi_nikah]" class="input-detail" placeholder="Contoh: KUA Loceret" required></div>
        <div class="form-group"><label>No Register Nikah</label><input type="text" name="detail[no_register]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Wali Nikah</label><input type="text" name="detail[nama_wali]" class="input-detail" required></div>
        ` +
    tutupGrid,

  cerai_goib:
    bioLengkap +
    `
        </div> <div class="divider"></div>
        <div class="section-label">Data Pasangan yang Hilang (Goib)</div>
        <div class="form-grid"> 
        <div class="form-group"><label>Pasangan yang Hilang Adalah</label>
            <select name="detail[status_pasangan_hilang]" class="input-detail" required>
                <option value="Suami">Suami (Pemohon adalah Istri)</option>
                <option value="Istri">Istri (Pemohon adalah Suami)</option>
            </select>
        </div>

        <div class="form-group"><label>Nama Pasangan</label><input type="text" name="detail[nama_pasangan]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir Pasangan</label><input type="text" name="detail[tempat_lahir_pasangan]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Pasangan</label><input type="date" name="detail[tgl_lahir_pasangan]" class="input-detail" required></div>
        
        <div class="form-group"><label>Agama Pasangan</label>
            <select name="detail[agama_pasangan]" class="input-detail" required>
                <option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option>
            </select>
        </div>
        <div class="form-group"><label>Pekerjaan Pasangan</label><input type="text" name="detail[pekerjaan_pasangan]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Terakhir Pasangan</label><input type="text" name="detail[alamat_pasangan]" class="input-detail" required></div>

        </div> <div class="divider"></div>
        <div class="section-label">Keterangan Kepergian</div>
        <div class="form-grid"> 
        <div class="form-group"><label>Bulan Pergi</label><input type="text" name="detail[bulan_pergi]" class="input-detail" placeholder="Contoh: Maret" required></div>
        <div class="form-group"><label>Tahun Pergi</label><input type="number" name="detail[tahun_pergi]" class="input-detail" placeholder="Contoh: 2025" required></div>
        <div class="form-group full-width"><label>Lama Meninggalkan</label><input type="text" name="detail[lama_pergi]" class="input-detail" placeholder="Contoh: 12 bulan / 2 tahun" required></div>
        ` +
    tutupGrid,

  wali:
    bioLengkapStatus +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Anak / Perwalian</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Nama Anak / Yang Diwali</label><input type="text" name="detail[nama_anak]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir Anak</label><input type="text" name="detail[tempat_lahir_anak]" class="input-detail" required></div>
        <div class="form-group"><label>Tanggal Lahir Anak</label><input type="date" name="detail[tgl_lahir_anak]" class="input-detail" required></div>
        <div class="form-group"><label>Jenis Kelamin Anak</label><select name="detail[jk_anak]" class="input-detail"><option>Laki-laki</option><option>Perempuan</option></select></div>
        <div class="form-group"><label>Hubungan Keluarga</label><input type="text" name="detail[hubungan]" class="input-detail" placeholder="Contoh: Paman / Kakek / Kakak Kandung" required></div>
        <div class="form-group full-width"><label>Keperluan Perwalian</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Persyaratan Pendaftaran TNI / Pendaftaran Sekolah / Nikah" required></div>
    ` +
    tutupGrid,

  ahli_waris:
    bioLengkap +
    `
        <div class="form-group full-width"><label>Nama Pewaris (Almarhum)</label><input type="text" name="detail[nama_pewaris]" class="input-detail" required></div>
        <div class="form-group"><label>Tanggal Meninggal</label><input type="date" name="detail[tgl_meninggal]" class="input-detail" required></div>
        <div class="form-group"><label>Jml Ahli Waris Ditinggalkan</label><input type="number" name="detail[jumlah_waris]" class="input-detail" required></div>` +
    tutupGrid,

  // === 3. EKONOMI, ASET, & PEKERJAAN ===
  usaha:
    bioLengkap +
    `
        <div class="form-group full-width"><label>Jenis / Nama Usaha</label><input type="text" name="detail[jenis_usaha]" class="input-detail" placeholder="Contoh: Dagang / Bengkel / Toko Kelontong" required></div>
        <div class="form-group"><label>Tahun Mulai Usaha</label><input type="number" name="detail[tahun_usaha]" class="input-detail" placeholder="Contoh: 2000" required></div>
        <div class="form-group full-width"><label>Lokasi Usaha</label><input type="text" name="detail[lokasi_usaha]" class="input-detail" placeholder="Contoh: Desa Macanan Kec Loceret Kab. Nganjuk" required></div>` +
    tutupGrid,

  penghasilan:
    bioLengkap +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Anak / Siswa</div>
        <div class="form-grid">
        <div class="form-group"><label>Nama Anak</label><input type="text" name="detail[nama_anak]" class="input-detail" required></div>
        <div class="form-group"><label>NIK Anak</label><input type="number" name="detail[nik_anak]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir Anak</label><input type="text" name="detail[tempat_lahir_anak]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Anak</label><input type="date" name="detail[tgl_lahir_anak]" class="input-detail" required></div>
        <div class="form-group"><label>Jenis Kelamin Anak</label>
            <select name="detail[jk_anak]" class="input-detail" required>
                <option>Laki-laki</option><option>Perempuan</option>
            </select>
        </div>

        </div> <div class="divider"></div> <div class="section-label">Rincian Penghasilan Orang Tua</div>
        <div class="form-grid">
        
        <div class="form-group full-width"><label>Nama Ayah</label><input type="text" name="detail[nama_ayah]" class="input-detail" required></div>
        <div class="form-group"><label>Pekerjaan Ayah</label><input type="text" name="detail[kerja_ayah]" class="input-detail" required></div>
        <div class="form-group"><label>Penghasilan Ayah (Rp)</label><input type="text" name="detail[gaji_ayah]" class="input-detail" placeholder="Ketik nominal angka ..." onkeyup="formatRupiah(this)" required></div>
        
        <div class="form-group full-width"><label>Nama Ibu</label><input type="text" name="detail[nama_ibu]" class="input-detail" required></div>
        <div class="form-group"><label>Pekerjaan Ibu</label><input type="text" name="detail[kerja_ibu]" class="input-detail" required></div>
        <div class="form-group"><label>Penghasilan Ibu (Rp)</label><input type="text" name="detail[gaji_ibu]" class="input-detail" placeholder="Ketik nominal angka (Isi 0 jika tidak ada)" onkeyup="formatRupiah(this)" required></div>

        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Persyaratan Pendaftaran KIP Kuliah" required></div>` +
    tutupGrid,

  sktm:
    bioLengkapStatus +
    `
        <div class="form-group"><label>Nomor KK</label><input type="number" name="detail[no_kk]" class="input-detail" required></div>
        
        <div class="form-group"><label>Penghasilan Ortu/Wali</label><input type="text" name="detail[gaji_ortu]" class="input-detail" placeholder="Masukan Nominal" onkeyup="formatRupiah(this)" required></div>
        
        <div class="form-group full-width"><label>Tujuan Penggunaan SKTM</label><input type="text" name="detail[tujuan_sktm]" class="input-detail" placeholder="Contoh: Persyaratan Beasiswa / Keringanan Biaya RS" required></div>
        
        </div> <div class="divider"></div> <div class="section-label">Khusus Keperluan Beasiswa (Kosongkan jika untuk RS)</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Nama Anak / Siswa</label><input type="text" name="detail[nama_anak]" class="input-detail" placeholder="Nama anak yang membutuhkan beasiswa"></div>
        <div class="form-group"><label>Nama Sekolah / Kampus</label><input type="text" name="detail[sekolah_anak]" class="input-detail" placeholder="Contoh: SMAN 1 Loceret"></div>
        <div class="form-group"><label>Kelas / Semester</label><input type="text" name="detail[kelas_anak]" class="input-detail" placeholder="Contoh: Kelas 10 / Semester 3"></div>
    ` +
    tutupGrid,

  jual_beli:
    bioLengkap +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Pihak II (Pembeli)</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Nama Pembeli</label><input type="text" name="detail[nama_pembeli]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir Pembeli</label><input type="text" name="detail[tempat_lahir_pembeli]" class="input-detail" value="Nganjuk" required></div>
        <div class="form-group"><label>Tanggal Lahir Pembeli</label><input type="date" name="detail[tgl_lahir_pembeli]" class="input-detail" required></div>
        <div class="form-group"><label>Pekerjaan Pembeli</label><input type="text" name="detail[kerja_pembeli]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap Pembeli</label><input type="text" name="detail[alamat_pembeli]" class="input-detail" required></div>
        </div>

        <div class="divider"></div> <div class="section-label">Obyek Jual Beli</div>
        <div class="form-grid">
        <div class="form-group full-width">
            <label>Pilih Jenis Jual Beli</label>
            <select name="detail[jenis_obyek]" class="input-detail" onchange="
                document.getElementById('blok_tanah').style.display = this.value == 'Tanah' ? 'grid' : 'none';
                document.getElementById('blok_kendaraan').style.display = this.value == 'Kendaraan' ? 'grid' : 'none';
                document.getElementById('blok_lainnya').style.display = this.value == 'Lainnya' ? 'block' : 'none';
            " required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Tanah">Tanah / Sawah / Bangunan</option>
                <option value="Kendaraan">Kendaraan (Motor/Mobil)</option>
                <option value="Lainnya">Barang Lainnya</option>
            </select>
        </div>
        </div>

        <div id="blok_tanah" class="form-grid" style="display:none; width:100%; margin-top:10px;">
            <div class="form-group full-width"><label>Asal Usul Tanah</label><input type="text" name="detail[asal_tanah]" class="input-detail" placeholder="Contoh: tahun 1990 dari Bapak SUBUR"></div>
            <div class="form-group"><label>Luas Tanah Dijual (M2)</label><input type="text" name="detail[luas_dijual]" class="input-detail" placeholder="Contoh: 2055"></div>
            <div class="form-group"><label>Jalan / Blok</label><input type="text" name="detail[blok_tanah]" class="input-detail" placeholder="Contoh: SAWAH NGLEJOK"></div>
            <div class="form-group full-width"><label>Nomor SHM & Surat Ukur</label><input type="text" name="detail[shm_tanah]" class="input-detail" placeholder="Contoh: 184 Luas 2731 Surat Ukur tgl 7-6-1990 No. 3325/Macanan/1998"></div>
            <div class="form-group full-width"><label>Data C Desa / Persil</label><input type="text" name="detail[c_desa]" class="input-detail" placeholder="Contoh: C Desa No. 123 Persil 45 Klas S.I Luas 2000 M2"></div>
            <div class="form-group"><label>Batas Utara</label><input type="text" name="detail[batas_utara]" class="input-detail"></div>
            <div class="form-group"><label>Batas Timur</label><input type="text" name="detail[batas_timur]" class="input-detail"></div>
            <div class="form-group"><label>Batas Selatan</label><input type="text" name="detail[batas_selatan]" class="input-detail"></div>
            <div class="form-group"><label>Batas Barat</label><input type="text" name="detail[batas_barat]" class="input-detail"></div>
        </div>

        <div id="blok_kendaraan" class="form-grid" style="display:none; width:100%; margin-top:10px;">
            <div class="form-group full-width"><label>Merk / Type Kendaraan</label><input type="text" name="detail[merk]" class="input-detail" placeholder="Contoh: HONDA / P5E02R22M1 M/T"></div>
            <div class="form-group"><label>Tahun Pembuatan</label><input type="text" name="detail[tahun]" class="input-detail" placeholder="Contoh: 2016"></div>
            <div class="form-group"><label>Nomor Polisi</label><input type="text" name="detail[nopol]" class="input-detail" placeholder="Contoh: AG 5340 UJ"></div>
            <div class="form-group"><label>Nomor Rangka</label><input type="text" name="detail[no_rangka]" class="input-detail"></div>
            <div class="form-group"><label>Nomor Mesin</label><input type="text" name="detail[no_mesin]" class="input-detail"></div>
            <div class="form-group"><label>No BPKB</label><input type="text" name="detail[no_bpkb]" class="input-detail"></div>
            <div class="form-group full-width"><label>Atas Nama (di BPKB/STNK)</label><input type="text" name="detail[atas_nama]" class="input-detail"></div>
        </div>

        <div id="blok_lainnya" style="display:none; width:100%; margin-top:10px;">
            <div class="form-group full-width"><label>Spesifikasi Barang</label><textarea name="detail[spek_lainnya]" class="input-detail" rows="3" placeholder="Sebutkan rincian barang yang dijual..."></textarea></div>
        </div>

        <div class="divider"></div> <div class="section-label">Harga & Saksi</div>
        <div class="form-grid">
        <div class="form-group"><label>Harga Jual (Rp)</label><input type="text" name="detail[harga_jual]" class="input-detail" placeholder="Ketik angka..." onkeyup="formatRupiah(this)" required></div>
        <div class="form-group"><label>Terbilang (Huruf)</label><input type="text" name="detail[terbilang_harga]" class="input-detail" placeholder="Contoh: Seratus Juta Rupiah" required></div>
        <div class="form-group"><label>Nama Saksi 1</label><input type="text" name="detail[saksi_1]" class="input-detail" required></div>
        <div class="form-group"><label>Nama Saksi 2</label><input type="text" name="detail[saksi_2]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Nama Saksi 3 (Opsional)</label><input type="text" name="detail[saksi_3]" class="input-detail" placeholder="Kosongkan jika hanya 2 saksi"></div>
    ` +
    tutupGrid,

  milik_kendaraan:
    bioLengkap +
    `
        <div class="form-group full-width"><label>Merk / Type Kendaraan</label><input type="text" name="detail[merk]" class="input-detail" placeholder="Contoh: HONDA / P5E02R22M1 M/T" required></div>
        <div class="form-group"><label>Tahun Pembuatan</label><input type="number" name="detail[tahun]" class="input-detail" placeholder="Contoh: 2016" required></div>
        <div class="form-group"><label>Isi Silinder</label><input type="text" name="detail[isi_silinder]" class="input-detail" placeholder="Contoh: 150 CC" required></div>
        <div class="form-group"><label>Nomor Rangka</label><input type="text" name="detail[no_rangka]" class="input-detail" required></div>
        <div class="form-group"><label>Nomor Mesin</label><input type="text" name="detail[no_mesin]" class="input-detail" required></div>
        <div class="form-group"><label>Nomor Polisi</label><input type="text" name="detail[nopol]" class="input-detail" placeholder="Contoh: AG 5340 UJ" required></div>
        <div class="form-group"><label>No BPKB</label><input type="text" name="detail[no_bpkb]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Nama (Sesuai BPKB / STNK)</label><input type="text" name="detail[atas_nama]" class="input-detail" required></div>` +
    tutupGrid,

  milik_tanah:
    bioLengkap +
    `
        <div class="form-group"><label>Nomor Sertifikat / NIB</label><input type="text" name="detail[no_sertifikat]" class="input-detail" placeholder="Contoh: 1093" required></div>
        <div class="form-group"><label>Gambar Situasi / Surat Ukur Tgl</label><input type="text" name="detail[surat_ukur]" class="input-detail" placeholder="Contoh: 00826/Macanan/2014/25-03-2014" required></div>
        
        <div class="form-group"><label>Sertifikat Atas Nama</label><input type="text" name="detail[nama_sertifikat]" class="input-detail" required></div>
        <div class="form-group"><label>Luas Tanah (m2)</label><input type="number" name="detail[luas_tanah]" class="input-detail" placeholder="Contoh: 266" required></div>
        
        </div> <div class="divider"></div> <div class="section-label">Batas-Batas Tanah</div>
        <div class="form-grid">
        <div class="form-group"><label>Batas Utara</label><input type="text" name="detail[batas_utara]" class="input-detail" placeholder="Contoh: MUHAMAD RONI" required></div>
        <div class="form-group"><label>Batas Timur</label><input type="text" name="detail[batas_timur]" class="input-detail" placeholder="Contoh: JALAN RAYA" required></div>
        <div class="form-group"><label>Batas Selatan</label><input type="text" name="detail[batas_selatan]" class="input-detail" placeholder="Contoh: SUYONO" required></div>
        <div class="form-group"><label>Batas Barat</label><input type="text" name="detail[batas_barat]" class="input-detail" placeholder="Contoh: PARNO" required></div>
        
        </div> <div class="divider"></div> <div class="section-label">Estimasi Harga (Taksiran Desa)</div>
        <div class="form-grid">
        <div class="form-group"><label>Taksiran Harga Tanah (Rp)</label><input type="text" name="detail[harga_tanah]" class="input-detail" placeholder="Ketik angka saja..." onkeyup="formatRupiah(this)" required></div>
        <div class="form-group"><label>Taksiran Harga Bangunan (Rp)</label><input type="text" name="detail[harga_bangunan]" class="input-detail" placeholder="Ketik angka saja (Isi 0 jika tidak ada)" onkeyup="formatRupiah(this)" required></div>` +
    tutupGrid,

  tanah_sengketa:
    bioLengkapStatus +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Tanah / Obyek</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Lokasi Tanah (Dusun / Jalan / Blok)</label><input type="text" name="detail[lokasi_tanah]" class="input-detail" placeholder="Contoh: Dsn. Gilis RT 01 RW 02" required></div>
        <div class="form-group"><label>Luas Tanah (M2)</label><input type="text" name="detail[luas_tanah]" class="input-detail" placeholder="Contoh: 1500" required></div>
        <div class="form-group"><label>Bukti Kepemilikan</label><input type="text" name="detail[bukti_kepemilikan]" class="input-detail" placeholder="Contoh: SHM No. 123 / Petok D / Leter C" required></div>
        <div class="form-group full-width"><label>Atas Nama (Pemilik Tanah)</label><input type="text" name="detail[atas_nama]" class="input-detail" placeholder="Nama yang tertera di surat tanah..." required></div>
        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Persyaratan Jual Beli / Agunan Bank / Balik Nama" required></div>
    ` +
    tutupGrid,

  ket_pekerjaan:
    bioLengkap +
    `
        <div class="form-group"><label>Kebangsaan</label><input type="text" name="detail[kewarganegaraan]" class="input-detail" value="WNI/Indonesia" required></div>
        <div class="form-group full-width"><label>Menerangkan Bekerja Sebagai</label><input type="text" name="detail[pekerjaan_baru]" class="input-detail" placeholder="Contoh: BURUH TANI" required></div>
        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Persyaratan Perubahan Pekerjaan di KK" required></div>
    ` +
    tutupGrid,

  pengalaman_kerja:
    bioLengkap +
    `
        </div> <div class="divider"></div> <div class="section-label">Detail Pengalaman Kerja</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Bekerja Sebagai (Jabatan/Posisi)</label><input type="text" name="detail[jabatan_kerja]" class="input-detail" placeholder="Contoh: Perangkat Desa (Kamituwo/Kepala Dusun)" required></div>
        <div class="form-group"><label>Mulai Bekerja Tanggal</label><input type="date" name="detail[tgl_mulai_kerja]" class="input-detail" required></div>
        <div class="form-group"><label>Selesai Bekerja Tanggal</label><input type="date" name="detail[tgl_selesai_kerja]" class="input-detail" required></div>` +
    tutupGrid,

  // === 4. KEAMANAN & KUASA ===
  skck:
    bioLengkap +
    `
        <div class="form-group"><label>Pendidikan Terakhir</label>
            <select name="detail[pendidikan]" class="input-detail" required>
                <option>SD/Sederajat</option>
                <option>SLTP/Sederajat</option>
                <option>SLTA/Sederajat</option>
                <option>D1/D2/D3</option>
                <option>S1/Diploma 4</option>
                <option>S2</option>
                <option>S3</option>
            </select>
        </div>
        <div class="form-group full-width"><label>Keperluan Pembuatan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Mengurus Persyaratan Administrasi SKCK" required></div>
        ` +
    tutupGrid,

  kehilangan:
    bioLengkap +
    `
        <div class="form-group"><label>Nama Barang yg Hilang</label><input type="text" name="detail[barang_hilang]" class="input-detail" placeholder="Contoh: KTP / Kartu ATM BRI" required></div>
        <div class="form-group"><label>Keterangan / Nomor Barang</label><input type="text" name="detail[rincian_barang]" class="input-detail" placeholder="Contoh: Dengan NO NIK : 35180411..." required></div>
        <div class="form-group full-width"><label>Kronologi / Lokasi Kejadian</label><input type="text" name="detail[kronologi]" class="input-detail" placeholder="Contoh: berada Perjalanan dari Kediri ke Tulungagung" required></div>
        <div class="form-group full-width"><label>Keperluan Pembuatan Surat</label><input type="text" name="detail[keperluan_surat]" class="input-detail" placeholder="Contoh: Untuk Persyaratan Mengurus KTP Baru" required></div>` +
    tutupGrid,

  kuasa_bpkb:
    bioLengkap +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Penerima Kuasa (Pihak Kedua)</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Nama Penerima Kuasa</label><input type="text" name="detail[nama_penerima]" class="input-detail" required></div>
        <div class="form-group"><label>Tempat Lahir Penerima</label><input type="text" name="detail[tempat_lahir_penerima]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Penerima</label><input type="date" name="detail[tgl_lahir_penerima]" class="input-detail" required></div>
        <div class="form-group"><label>Pekerjaan Penerima</label><input type="text" name="detail[pekerjaan_penerima]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap Penerima</label><input type="text" name="detail[alamat_penerima]" class="input-detail" required></div>

        </div> <div class="divider"></div> <div class="section-label">Keperluan & Data Kendaraan</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Tujuan / Keperluan Kuasa</label><input type="text" name="detail[tujuan_kuasa]" class="input-detail" placeholder="Contoh: Mengurus surat STNK Motor yang Hilang..." required></div>
        
        <div class="form-group full-width"><label>Merk / Type Kendaraan</label><input type="text" name="detail[merk]" class="input-detail" placeholder="Contoh: HONDA / P5E02R22M1 M/T" required></div>
        <div class="form-group"><label>Tahun Pembuatan</label><input type="number" name="detail[tahun]" class="input-detail" placeholder="Contoh: 2016" required></div>
        <div class="form-group"><label>Isi Silinder</label><input type="text" name="detail[isi_silinder]" class="input-detail" placeholder="Contoh: 150 CC" required></div>
        <div class="form-group"><label>Nomor Rangka</label><input type="text" name="detail[no_rangka]" class="input-detail" required></div>
        <div class="form-group"><label>Nomor Mesin</label><input type="text" name="detail[no_mesin]" class="input-detail" required></div>
        <div class="form-group"><label>Nomor Polisi</label><input type="text" name="detail[nopol]" class="input-detail" placeholder="Contoh: AG 5340 UJ" required></div>
        <div class="form-group"><label>No BPKB</label><input type="text" name="detail[no_bpkb]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Nama (Sesuai BPKB / STNK)</label><input type="text" name="detail[atas_nama]" class="input-detail" required></div>` +
    tutupGrid,

  kuasa:
    bioLengkap +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Penerima Kuasa (Pihak II)</div>
        <div class="form-grid">
        <div class="form-group"><label>Nama Penerima Kuasa</label><input type="text" name="detail[nama_penerima]" class="input-detail" required></div>
        <div class="form-group"><label>NIK Penerima Kuasa</label><input type="number" name="detail[nik_penerima]" class="input-detail" required></div>
        
        <div class="form-group"><label>Tempat Lahir Penerima</label><input type="text" name="detail[tempat_lahir_penerima]" class="input-detail" value="Nganjuk" list="daftar_kota" autocomplete="off" required></div>
        <div class="form-group"><label>Tanggal Lahir Penerima</label><input type="date" name="detail[tgl_lahir_penerima]" class="input-detail" required></div>
        
        <div class="form-group"><label>Pekerjaan Penerima</label><input type="text" name="detail[pekerjaan_penerima]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alamat Lengkap Penerima</label><input type="text" name="detail[alamat_penerima]" class="input-detail" required></div>

        </div> <div class="divider"></div> <div class="section-label">Tujuan Pemberian Kuasa</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Tujuan Pemberian Kuasa</label><input type="text" name="detail[tujuan_kuasa]" class="input-detail" placeholder="Contoh: Mengurus Pembaruan KK di Dukcapil Nganjuk" required></div>` +
    tutupGrid,

  keramaian:
    bioLengkapStatus +
    `
        </div> <div class="divider"></div> <div class="section-label">Detail Acara Keramaian</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Jenis Acara</label><input type="text" name="detail[jenis_acara]" class="input-detail" placeholder="Contoh: Hajatan Pernikahan / Sunatan / Pengajian" required></div>
        <div class="form-group"><label>Hari Pelaksanaan</label>
            <select name="detail[hari_acara]" class="input-detail" required>
                <option value="Senin">Senin</option><option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option><option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option>
                <option value="Minggu">Minggu</option>
            </select>
        </div>
        <div class="form-group"><label>Tanggal Pelaksanaan</label><input type="date" name="detail[tgl_acara]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Tempat Acara</label><input type="text" name="detail[tempat_acara]" class="input-detail" placeholder="Contoh: Rumah Kediaman Bapak Subur RT 01 RW 02" required></div>
    ` +
    tutupGrid,

  bepergian:
    bioLengkap +
    `
        <div class="form-group"><label>Kota/Daerah Tujuan</label><input type="text" name="detail[kota_tujuan]" class="input-detail" required></div>
        <div class="form-group"><label>Lama Bepergian</label><input type="text" name="detail[lama_pergi]" class="input-detail" placeholder="Contoh: 3 Hari / 1 Minggu" required></div>
        <div class="form-group full-width"><label>Keperluan Bepergian</label><input type="text" name="detail[keperluan_pergi]" class="input-detail" required></div>` +
    tutupGrid,

  tidak_dirumah:
    bioLengkapStatus +
    `
        <div class="form-group full-width"><label>Keberadaan Saat Ini</label><input type="text" name="detail[lokasi_sekarang]" class="input-detail" placeholder="Contoh: Luar Negeri (MALAYSIA)" required></div>
        <div class="form-group full-width"><label>Alasan Tidak Dirumah</label><input type="text" name="detail[alasan]" class="input-detail" value="sedang bekerja dan tidak bisa Pulang" required></div>
        <div class="form-group full-width"><label>Keperluan Surat</label><input type="text" name="detail[keperluan]" class="input-detail" placeholder="Contoh: Untuk Persyaratan Adminitrasi Bank" required></div>
    ` +
    tutupGrid,

  ket_umum:
    bioLengkapStatus +
    `
        <div class="form-group full-width"><label>Kebangsaan</label><input type="text" name="detail[kewarganegaraan]" class="input-detail" value="Indonesia" required></div>
        <div class="form-group full-width"><label>Isi Keterangan (Menerangkan Bahwa...)</label><textarea name="detail[isi_keterangan]" class="input-detail" rows="5" placeholder="Contoh: Orang tersebut diatas adalah Penduduk Ds. Macanan... Menerangkan bahwa orang tersebut Memiliki Sertipikat Tanah seluas 1.148 M2..." required></textarea></div>
    ` +
    tutupGrid,

  // === 5. PENDIDIKAN & SEKOLAH (Menggunakan bioSingkat - Tanpa NIK/Agama) ===
  izin_sekolah:
    bioSingkat +
    `
        <div class="form-group full-width"><label>Nama Sekolah</label><input type="text" name="detail[nama_sekolah]" class="input-detail" required></div>
        <div class="form-group"><label>Kelas</label><input type="text" name="detail[kelas]" class="input-detail" required></div>
        <div class="form-group"><label>Lama Izin (Hari)</label><input type="number" name="detail[lama_izin]" class="input-detail" required></div>
        <div class="form-group full-width"><label>Alasan Tidak Masuk</label>
            <select name="detail[alasan_izin]" class="input-detail" required>
                <option>Sakit</option>
                <option>Izin Keperluan Keluarga</option>
            </select>
        </div>` +
    tutupGrid,

  domisili_sekolah:
    bioSingkat +
    `
        <div class="form-group full-width"><label>Nama Sekolah yang Dituju</label><input type="text" name="detail[sekolah_tujuan]" class="input-detail" placeholder="Contoh: SMAN 1 Loceret (Keperluan PPDB Zonasi)" required></div>` +
    tutupGrid,

  pinjam_tanah:
    bioSingkat +
    `
        </div> <div class="divider"></div> <div class="section-label">Data Yayasan / Bangunan</div>
        <div class="form-grid">
        <div class="form-group full-width"><label>Nama Yayasan / Bangunan</label><input type="text" name="detail[nama_yayasan]" class="input-detail" placeholder="Contoh: KB AS-SHOFA" required></div>
        <div class="form-group full-width"><label>Alamat Yayasan / Bangunan</label><input type="text" name="detail[alamat_yayasan]" class="input-detail" placeholder="Contoh: Dsn.Gilis RT05 RW01 Desa Macanan Kecamatan Loceret..." required></div>
        <div class="form-group full-width"><label>Status Kepemilikan (Milik Siapa?)</label><input type="text" name="detail[kepemilikan]" class="input-detail" value="yayasan Desa" required></div>
        <div class="form-group"><label>Panjang Tanah (m)</label><input type="text" name="detail[panjang_tanah]" class="input-detail" placeholder="Contoh: 14,20" required></div>
        <div class="form-group"><label>Lebar Tanah (m)</label><input type="text" name="detail[lebar_tanah]" class="input-detail" placeholder="Contoh: 12,20" required></div>
        <div class="form-group"><label>Panjang Bangunan (m)</label><input type="text" name="detail[panjang_bangunan]" class="input-detail" placeholder="Contoh: 8" required></div>
        <div class="form-group"><label>Lebar Bangunan (m)</label><input type="text" name="detail[lebar_bangunan]" class="input-detail" placeholder="Contoh: 10" required></div>` +
    tutupGrid,

  // === 6. FALLBACK DEFAULT ===
  default:
    bioLengkap +
    `
        <div class="form-group full-width"><label>Keperluan Surat / Keterangan Tambahan</label>
        <textarea name="detail[keterangan]" rows="3" class="input-detail" placeholder="Jelaskan secara rinci tujuan pembuatan surat ini..." required></textarea></div>` +
    tutupGrid,
};

// 4. FUNGSI HELPER (DARI JS LO) EXPORT BIAR BISA DIPAKAI DI VUE
export function formatRupiah(obj) {
  let number_string = obj.value.replace(/[^,\d]/g, "").toString();
  let split = number_string.split(",");
  let sisa = split[0].length % 3;
  let rupiah = split[0].substr(0, sisa);
  let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
  if (ribuan) {
    let separator = sisa ? "." : "";
    rupiah += separator + ribuan.join(".");
  }
  rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
  obj.value = rupiah ? "Rp " + rupiah : "";
}

export function toggleAlamat(targetName, checkbox) {
  const alamatPelapor = document.querySelector(
    'input[name="detail[data_pemohon][alamat]"]',
  );
  const targetInput = document.querySelector(
    `input[name="detail[${targetName}]"]`,
  );
  if (alamatPelapor && targetInput) {
    if (checkbox.checked) {
      if (alamatPelapor.value.trim() === "") {
        alert(
          "Silakan isi Alamat Lengkap di bagian Biodata Pelapor terlebih dahulu!",
        );
        checkbox.checked = false;
      } else {
        targetInput.value = alamatPelapor.value;
        targetInput.setAttribute("readonly", true);
        targetInput.style.backgroundColor = "#e0f2fe";
      }
    } else {
      targetInput.value = "";
      targetInput.removeAttribute("readonly");
      targetInput.style.backgroundColor = "";
    }
  }
}
