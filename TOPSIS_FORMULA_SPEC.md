# 📐 TOPSIS FORMULA SPECIFICATION
## Dokumentasi Matematis Resmi — SPK Bantuan Alsintan Dinas Pertanian Kab. Subang

> **Versi:** 1.0.0 | **Tanggal:** 2026-11-04
> **Referensi Kode:** app/Services/TopsisCalculationService.php
> **Referensi Laporan:** Bab 3 (Metodologi) & Bab 4 (Implementasi)

> PENTING: Dokumen ini adalah KONTRAK MATEMATIS antara laporan TA (Bab 3)
> dan kode program (Bab 4). Setiap perubahan rumus di kode WAJIB diperbarui
> di dokumen ini, dan sebaliknya.

---

## OVERVIEW: Metode TOPSIS

TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)
adalah metode pengambilan keputusan multi-kriteria oleh Hwang dan Yoon (1981).

Prinsip: alternatif terbaik adalah yang jaraknya PALING DEKAT dari solusi
ideal positif DAN PALING JAUH dari solusi ideal negatif.

### Input Sistem
- Alternatif (A): Kelompok Tani yang mengajukan bantuan Alsintan
- Kriteria (C): Faktor-faktor penilaian kelayakan
- Bobot (W): Tingkat kepentingan setiap kriteria (total = 1.0)
- Tipe Kriteria: 'benefit' (lebih besar = lebih baik) | 'cost' (lebih kecil = lebih baik)

### Output Sistem
- Nilai Preferensi (Vi): Skor akhir 0–1 per alternatif
- Ranking: Urutan prioritas (Vi tertinggi = prioritas tertinggi)

---

## 6 LANGKAH ALGORITMA TOPSIS

---

### LANGKAH 1: Membangun Matriks Keputusan (Decision Matrix)

Susun matriks X berukuran m x n:
  m = jumlah alternatif (Poktan)
  n = jumlah kriteria

     X = | x11  x12  ...  x1n |
         | x21  x22  ...  x2n |
         |  .    .    .    .  |
         | xm1  xm2  ...  xmn |

Rumus: x_ij = nilai Poktan ke-i untuk Kriteria ke-j

Contoh Data:
| Poktan | Luas Lahan(C1) | Produktivitas(C2) | Kepemilikan Alat(C3) | Jml Anggota(C4) |
|--------|----------------|-------------------|----------------------|-----------------|
| A1     |       5        |         4         |           2          |       30        |
| A2     |       3        |         5         |           3          |       25        |
| A3     |       4        |         3         |           1          |       40        |

---

### LANGKAH 2: Normalisasi Matriks (Normalized Decision Matrix)

Rumus normalisasi Euclidean:

   r_ij = x_ij / sqrt( sum(x_ij^2) untuk semua i )

   Di mana:
   - r_ij  = nilai ternormalisasi alternatif i pada kriteria j
   - x_ij  = nilai asli dari matriks keputusan
   - sqrt( sum(x_ij^2) ) = Euclidean length kolom j

Tujuan: menyamakan skala semua kriteria (dimensionless).

---

### LANGKAH 3: Normalisasi Matriks Terbobot (Weighted Normalized Matrix)

Rumus:

   y_ij = w_j * r_ij

   Di mana:
   - y_ij = nilai matriks ternormalisasi terbobot
   - w_j  = bobot kriteria ke-j
   - r_ij = nilai ternormalisasi (dari Langkah 2)

   Syarat: sum(w_j) untuk semua j = 1.0

---

### LANGKAH 4: Solusi Ideal Positif (A+) dan Negatif (A-)

   A+ = { y_j+ | j = 1, 2, ..., n }
   A- = { y_j- | j = 1, 2, ..., n }

   Ketentuan per TIPE KRITERIA:

   | Tipe    | A+ (Ideal Positif) | A- (Ideal Negatif) |
   |---------|--------------------|--------------------|
   | benefit | max( y_ij )        | min( y_ij )        |
   | cost    | min( y_ij )        | max( y_ij )        |

---

### LANGKAH 5: Jarak Euclidean (D+ dan D-)

Jarak dari Solusi Ideal Positif:
   D+_i = sqrt( sum( (y_ij - y_j+)^2 ) untuk semua j )

Jarak dari Solusi Ideal Negatif:
   D-_i = sqrt( sum( (y_ij - y_j-)^2 ) untuk semua j )

---

### LANGKAH 6: Nilai Preferensi (Vi) dan Ranking

   V_i = D-_i / ( D+_i + D-_i )

   Interpretasi:
   - V_i mendekati 1 -> alternatif TERBAIK (dekat A+, jauh A-)
   - V_i mendekati 0 -> alternatif TERBURUK (jauh A+, dekat A-)
   - Ranking: urutkan V_i dari besar ke kecil (descending)

---

## ALUR KALKULASI LENGKAP

```
INPUT (Penilaian dari DB)
        |
        v
[1] Matriks Keputusan X (m x n)
    x_ij = nilai asli Poktan i pada Kriteria j
        |
        v
[2] Normalisasi Matriks R
    r_ij = x_ij / sqrt(Sum x_ij^2)
        |
        v
[3] Matriks Terbobot Y
    y_ij = w_j * r_ij
        |
        v
[4] Solusi Ideal A+ dan A-
    Benefit -> A+ = max(y_j), A- = min(y_j)
    Cost    -> A+ = min(y_j), A- = max(y_j)
        |
        v
[5] Jarak Euclidean D+ dan D-
    D+_i = sqrt(Sum (y_ij - y_j+)^2)
    D-_i = sqrt(Sum (y_ij - y_j-)^2)
        |
        v
[6] Nilai Preferensi V
    V_i = D-_i / (D+_i + D-_i)
        |
        v
OUTPUT: Ranking Kelayakan Poktan
        (V_i tertinggi = Prioritas tertinggi)
```

---

## CONTOH KRITERIA DEFAULT

| ID | Nama Kriteria            | Bobot | Tipe    | Keterangan                              |
|----|--------------------------|-------|---------|-----------------------------------------|
| C1 | Luas Lahan Poktan (Ha)   | 0.30  | benefit | Lahan lebih luas = lebih diutamakan     |
| C2 | Produktivitas (Ton/Ha/Th)| 0.25  | benefit | Produktivitas tinggi = prioritas lebih  |
| C3 | Kepemilikan Alat (unit)  | 0.20  | cost    | Sudah punya banyak = kurang diprioritaskan |
| C4 | Jumlah Anggota Aktif     | 0.15  | benefit | Poktan besar = lebih diutamakan         |
| C5 | Jarak ke Lahan (Km)      | 0.10  | cost    | Lahan jauh = kurang diutamakan          |

Total Bobot: 0.30 + 0.25 + 0.20 + 0.15 + 0.10 = 1.00 [VALID]

---

## REFERENSI

- Hwang, C.L., & Yoon, K. (1981). Multiple Attribute Decision Making:
  Methods and Applications. Springer-Verlag.
- Kusumadewi, S., et al. (2006). Fuzzy Multi-Attribute Decision Making
  (Fuzzy MADM). Graha Ilmu, Yogyakarta.

