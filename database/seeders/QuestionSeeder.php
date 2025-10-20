<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $questionsData = [
            // ===================== 1. OOP (10 Soal) =====================
            [
                'tag' => 'OOP',
                'question_text' => 'Apa yang dimaksud dengan konsep Polymorphism dalam OOP?',
                'options' => [
                    'Kemampuan menyembunyikan data',
                    'Konsep pewarisan antar kelas',
                    'Membuat banyak instance dari satu variabel',
                    'Kemampuan objek memiliki banyak bentuk' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Apa fungsi dari keyword $this dalam OOP (PHP)?',
                'options' => [
                    'Membuat variabel global',
                    'Menentukan tipe data',
                    'Menghapus objek',
                    'Mengacu pada instance saat ini' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Apa perbedaan yang paling mendasar antara class dan object?',
                'options' => [
                    'Object dan class adalah hal yang sama',
                    'Object menyimpan fungsi, class menyimpan data',
                    'Class hanya digunakan untuk variabel global',
                    'Class adalah blueprint, object adalah instance-nya' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Konsep Encapsulation dalam OOP digunakan untuk?',
                'options' => [
                    'Menggabungkan dua class',
                    'Membuat variabel publik',
                    'Menjalankan perintah dari luar kelas',
                    'Menyembunyikan detail implementasi' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Inheritance dalam OOP memungkinkan?',
                'options' => [
                    'Sebuah objek dapat berubah tipe',
                    'Penyembunyian atribut data',
                    'Membuat class baru dari nol',
                    'Sebuah class mewarisi sifat class lain' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Apa manfaat utama dari penggunaan Interface dalam OOP?',
                'options' => [
                    'Menghubungkan dua database',
                    'Mengatur visibilitas variabel',
                    'Menentukan tipe data numerik',
                    'Menentukan kontrak metode untuk class lain' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Apa perbedaan paling krusial antara Abstract Class dan Interface?',
                'options' => [
                    'Interface bisa punya constructor',
                    'Keduanya identik',
                    'Abstract class tidak bisa diwariskan',
                    'Abstract class bisa punya implementasi metode, interface tidak' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Kapan Destructor dipanggil dalam OOP?',
                'options' => [
                    'Saat objek dibuat',
                    'Saat program dimulai',
                    'Setelah semua variabel dideklarasikan',
                    'Saat objek dihapus dari memori' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Apa itu Method Overloading?',
                'options' => [
                    'Mewarisi class dari class lain',
                    'Mengubah nilai variabel global',
                    'Menjalankan dua proses bersamaan',
                    'Mendefinisikan beberapa metode dengan nama sama tapi parameter berbeda' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OOP',
                'question_text' => 'Keyword static digunakan untuk membuat properti atau metode yang sifatnya:',
                'options' => [
                    'Membuat objek baru',
                    'Menghapus instance class',
                    'Menjalankan perulangan',
                    'Milik class, bukan objek' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 2. DATABASE / SQL (10 Soal) =====================
            [
                'tag' => 'Database',
                'question_text' => 'Apa fungsi dari Primary Key dalam tabel database?',
                'options' => [
                    'Menghubungkan dua tabel',
                    'Menyimpan data duplikat',
                    'Menentukan tipe kolom',
                    'Sebagai identitas unik setiap record' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Apa itu Foreign Key?',
                'options' => [
                    'Kunci utama tabel',
                    'Kolom yang menyimpan data unik',
                    'Atribut sementara',
                    'Kolom yang menghubungkan tabel satu dengan lainnya' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Perbedaan INNER JOIN dan LEFT JOIN adalah:',
                'options' => [
                    'LEFT JOIN hanya menampilkan data tabel kanan',
                    'INNER JOIN menampilkan semua data',
                    'Keduanya identik',
                    'INNER JOIN menampilkan data yang cocok di kedua tabel, LEFT JOIN menampilkan semua data tabel kiri' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Apa itu normalisasi dalam database?',
                'options' => [
                    'Proses menambah redundansi data',
                    'Proses enkripsi data',
                    'Proses backup data',
                    'Proses mengurangi duplikasi data' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'SQL kepanjangan dari?',
                'options' => [
                    'Sequential Query Logic',
                    'Simple Query List',
                    'Structured Question Language',
                    'Structured Query Language' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Perintah SQL untuk mengambil data dari tabel adalah?',
                'options' => [
                    'Menambahkan data',
                    'Menghapus tabel',
                    'Membuat indeks baru',
                    'SELECT' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'DDL digunakan untuk struktur database, sedangkan DML digunakan untuk:',
                'options' => [
                    'Keduanya untuk menampilkan data',
                    'DML untuk membuat tabel baru',
                    'DDL digunakan untuk perhitungan',
                    'Manipulasi data' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Apa fungsi dari indeks dalam database?',
                'options' => [
                    'Menambah duplikasi data',
                    'Menghapus data lama',
                    'Menurunkan performa query',
                    'Meningkatkan kecepatan pencarian data' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Apa itu subquery atau nested query?',
                'options' => [
                    'Query untuk menghapus data',
                    'Query khusus untuk tabel utama',
                    'Query yang menghasilkan error',
                    'Query di dalam query lain' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Database',
                'question_text' => 'Perintah SQL untuk menghapus tabel dan isinya secara permanen adalah?',
                'options' => [
                    'DELETE TABLE',
                    'REMOVE TABLE',
                    'ERASE TABLE',
                    'DROP TABLE' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 3. NETWORKING (10 Soal) =====================
            [
                'tag' => 'Networking',
                'question_text' => 'Protokol apa yang digunakan untuk mengirim email dari klien ke server?',
                'options' => [
                    'FTP',
                    'HTTP',
                    'SSH',
                    'SMTP' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Apa fungsi dari IP address?',
                'options' => [
                    'Menyimpan data pengguna',
                    'Mengatur file sistem',
                    'Menjalankan proses enkripsi',
                    'Identifikasi unik setiap perangkat di jaringan' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'TCP bersifat connection-oriented, sedangkan UDP bersifat:',
                'options' => [
                    'Lebih aman dari TCP',
                    'Lebih cepat dari TCP',
                    'Keduanya identik',
                    'Connectionless' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Apa itu DNS?',
                'options' => [
                    'Sistem yang menyimpan data user',
                    'Sistem manajemen keamanan',
                    'Protokol pengiriman data',
                    'Sistem yang menerjemahkan nama domain menjadi IP' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Port default untuk HTTP adalah?',
                'options' => [
                    '21',
                    '25',
                    '443',
                    '80' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Port default untuk HTTPS (aman) adalah?',
                'options' => [
                    '80',
                    '21',
                    '110',
                    '443' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Perangkat jaringan yang berfungsi menghubungkan beberapa jaringan yang berbeda (misalnya LAN ke WAN) adalah:',
                'options' => [
                    'Menyimpan file pengguna',
                    'Menjalankan aplikasi',
                    'Menentukan alamat MAC',
                    'Router' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Apa itu firewall?',
                'options' => [
                    'Perangkat penyimpanan data',
                    'Protokol email',
                    'Aplikasi web',
                    'Sistem keamanan untuk menyaring lalu lintas jaringan' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Apa fungsi DHCP?',
                'options' => [
                    'Menyimpan password jaringan',
                    'Mengamankan koneksi HTTPS',
                    'Mengontrol bandwidth jaringan',
                    'Memberikan IP address otomatis ke perangkat' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Networking',
                'question_text' => 'Protokol apa yang digunakan untuk transfer file secara tradisional?',
                'options' => [
                    'SMTP',
                    'HTTP',
                    'POP3',
                    'FTP' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 4. DATA STRUCTURES & ALGORITHMS (DSA) (10 Soal) =====================
            [
                'tag' => 'DSA',
                'question_text' => 'Struktur data mana yang mengikuti prinsip LIFO (Last-In, First-Out)?',
                'options' => [
                    'Queue',
                    'Linked List',
                    'Array',
                    'Stack' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Kompleksitas waktu rata-rata (Average Case) untuk pencarian biner (Binary Search) adalah:',
                'options' => [
                    'O(1)',
                    'O(n)',
                    'O(n²)',
                    'O(log n)' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Dalam struktur data Tree, node yang tidak memiliki anak disebut sebagai:',
                'options' => [
                    'Root',
                    'Parent',
                    'Internal Node',
                    'Leaf' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Algoritma Graph mana yang digunakan untuk mencari jalur terpendek dari satu node ke semua node lainnya?',
                'options' => [
                    'Depth First Search (DFS)',
                    'Breadth First Search (BFS)',
                    'Prim\'s Algorithm',
                    'Dijkstra\'s Algorithm' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Struktur data Queue mengikuti prinsip apa?',
                'options' => [
                    'LIFO (Last-In, First-Out)',
                    'LILO (Last-In, Last-Out)',
                    'FILO (First-In, Last-Out)',
                    'FIFO (First-In, First-Out)' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Apa nama umum untuk algoritma yang membandingkan setiap pasangan elemen yang berdekatan dan menukarnya jika urutannya salah?',
                'options' => [
                    'Quick Sort',
                    'Heap Sort',
                    'Insertion Sort',
                    'Bubble Sort' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Apa yang diukur dengan notasi Big O?',
                'options' => [
                    'Ukuran memori program',
                    'Keakuratan program',
                    'Waktu kompilasi program',
                    'Efisiensi waktu dan/atau ruang algoritma' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Sebuah Linked List yang setiap node-nya memiliki pointer ke node berikutnya dan node sebelumnya adalah:',
                'options' => [
                    'Singly Linked List',
                    'Circular Linked List',
                    'Array Linked List',
                    'Doubly Linked List' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Manakah dari Sorting Algorithm berikut yang memiliki kompleksitas waktu kasus terburuk O(n log n)?',
                'options' => [
                    'Bubble Sort',
                    'Insertion Sort',
                    'Selection Sort',
                    'Merge Sort' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'DSA',
                'question_text' => 'Apa yang terjadi pada Linked List saat Anda menyisipkan elemen di awal (Head)?',
                'options' => [
                    'O(n)',
                    'O(log n)',
                    'O(n log n)',
                    'O(1)' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 5. OPERATING SYSTEMS (OS) (10 Soal) =====================
            [
                'tag' => 'OS',
                'question_text' => 'Bagian inti dari sistem operasi yang bertanggung jawab mengelola resource sistem adalah:',
                'options' => [
                    'Shell',
                    'Compiler',
                    'Utility',
                    'Kernel' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Kondisi saat dua atau lebih proses saling menunggu sumber daya yang dipegang oleh proses lain, sehingga tidak ada yang dapat melanjutkan, disebut:',
                'options' => [
                    'Starvation',
                    'Race Condition',
                    'Context Switching',
                    'Deadlock' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Metode manajemen memori di mana memori fisik dibagi menjadi blok-blok berukuran tetap dan program dibagi menjadi blok-blok berukuran sama disebut:',
                'options' => [
                    'Segmentation',
                    'Swapping',
                    'Partitioning',
                    'Paging' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Algoritma penjadwalan CPU mana yang menggunakan konsep Time Slice (Quantum)?',
                'options' => [
                    'First-Come, First-Served (FCFS)',
                    'Shortest Job Next (SJN)',
                    'Priority Scheduling',
                    'Round Robin' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Apa fungsi utama dari File System?',
                'options' => [
                    'Mengatur transfer data melalui jaringan',
                    'Menjalankan aplikasi secara paralel',
                    'Mengalokasikan memori virtual',
                    'Mengontrol bagaimana data disimpan dan diambil dari disk' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Apa yang dimaksud dengan Process dalam Sistem Operasi?',
                'options' => [
                    'Program yang hanya disimpan dalam memori',
                    'Kumpulan instruksi yang tidak aktif',
                    'Sebuah unit memori fisik',
                    'Program yang sedang dieksekusi' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Mekanisme OS menyimpan data yang tidak sering digunakan dari RAM ke disk (Hard Drive) disebut:',
                'options' => [
                    'Thrashing',
                    'Caching',
                    'Buffering',
                    'Swapping/Virtual Memory' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Mode yang memiliki hak istimewa penuh dan berinteraksi langsung dengan hardware adalah:',
                'options' => [
                    'User Mode',
                    'Debug Mode',
                    'Safe Mode',
                    'Kernel Mode' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Istilah untuk proses di mana sebuah proses baru dibuat dari proses yang sudah ada (misalnya, di Unix/Linux) adalah:',
                'options' => [
                    'Exec',
                    'Wait',
                    'Kill',
                    'Fork' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'OS',
                'question_text' => 'Apa yang dimaksud dengan Context Switching?',
                'options' => [
                    'Mengganti RAM dengan Disk',
                    'Mengubah File System',
                    'Membuat Process baru',
                    'Proses menyimpan state satu proses dan me-load state proses lain untuk eksekusi CPU' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 6. WEB DEVELOPMENT (10 Soal) =====================
            [
                'tag' => 'WebDev',
                'question_text' => 'Tag HTML mana yang digunakan untuk membuat tautan (hyperlink)?',
                'options' => [
                    '<html>',
                    '<link>',
                    '<href>',
                    '<a>' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Properti CSS mana yang digunakan untuk mengubah warna latar belakang sebuah elemen?',
                'options' => [
                    'color',
                    'text-color',
                    'bgcolor',
                    'background-color' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Bahasa pemrograman di sisi klien (client-side) yang bertanggung jawab untuk membuat halaman web menjadi interaktif adalah:',
                'options' => [
                    'PHP',
                    'Python',
                    'Ruby',
                    'JavaScript' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Apa kepanjangan dari DOM (Document Object Model)?',
                'options' => [
                    'Standar untuk menentukan layout halaman',
                    'Protokol untuk transfer data aman',
                    'Sebuah framework CSS',
                    'Representasi terstruktur dari dokumen HTML sebagai pohon objek' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'HTTP Method mana yang paling sesuai untuk mengambil data dari server tanpa mengubah keadaan server?',
                'options' => [
                    'POST',
                    'PUT',
                    'DELETE',
                    'GET' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Untuk membuat daftar item tanpa urutan tertentu (unordered list), tag HTML yang digunakan adalah:',
                'options' => [
                    '<ol>',
                    '<list>',
                    '<dl>',
                    '<ul>' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Apa kepanjangan dari API dalam konteks web service?',
                'options' => [
                    'Application Program Integration',
                    'Advanced Protocol Interface',
                    'Automated Process Instruction',
                    'Application Programming Interface' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Apa fungsi utama dari Responsive Design?',
                'options' => [
                    'Agar website merespons input pengguna dengan cepat',
                    'Untuk menggunakan teknologi server-side terbaru',
                    'Untuk memuat halaman secepat mungkin',
                    'Agar website terlihat baik di berbagai ukuran layar' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Apa perbedaan antara Local Storage dan Session Storage di browser?',
                'options' => [
                    'Keduanya identik',
                    'Session Storage menyimpan data di server',
                    'Local Storage hanya mendukung string',
                    'Local Storage menyimpan data tanpa batas waktu, Session Storage dihapus saat tab ditutup' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'WebDev',
                'question_text' => 'Apa itu AJAX?',
                'options' => [
                    'Framework CSS',
                    'Bahasa pemrograman server-side',
                    'Protokol jaringan',
                    'Teknik untuk melakukan komunikasi asinkron antara browser dan server' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 7. SOFTWARE ENGINEERING / SDLC (10 Soal) =====================
            [
                'tag' => 'SE',
                'question_text' => 'Model SDLC (Software Development Life Cycle) mana yang bersifat linier dan berurutan?',
                'options' => [
                    'Agile',
                    'Spiral',
                    'Iterative',
                    'Waterfall' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Proses mendokumentasikan, menganalisis, dan memprioritaskan persyaratan (requirements) disebut:',
                'options' => [
                    'Code Review',
                    'Deployment',
                    'Quality Assurance',
                    'Requirement Engineering' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Apa tujuan utama dari Unit Testing?',
                'options' => [
                    'Menguji integrasi seluruh modul',
                    'Menguji persyaratan bisnis pengguna akhir',
                    'Menguji performa sistem di bawah beban berat',
                    'Menguji fungsionalitas terkecil (method atau class) secara terpisah' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Manakah yang merupakan prinsip kunci dari metodologi Agile?',
                'options' => [
                    'Dokumentasi yang lengkap sebelum pengkodean dimulai',
                    'Proses yang kaku dan perubahan harus dihindari',
                    'Mengerjakan semua fitur sekaligus dalam satu fase panjang',
                    'Iterasi singkat dan kolaborasi dengan klien' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Diagram UML mana yang menunjukkan struktur statis sistem, yaitu kelas-kelas dan hubungannya?',
                'options' => [
                    'Sequence Diagram',
                    'Use Case Diagram',
                    'Activity Diagram',
                    'Class Diagram' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Apa yang dimaksud dengan Technical Debt?',
                'options' => [
                    'Utang untuk membeli lisensi software',
                    'Biaya untuk perbaikan bug setelah deployment',
                    'Jumlah jam kerja yang terutang kepada pengembang',
                    'Konsekuensi dari memilih solusi pengkodean cepat daripada solusi yang solid' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Proses mengidentifikasi dan memperbaiki error atau bug dalam kode program adalah:',
                'options' => [
                    'Compilation',
                    'Execution',
                    'Refactoring',
                    'Debugging' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Dalam Git, apa fungsi dari perintah git pull?',
                'options' => [
                    'Mengirim perubahan lokal ke repositori jarak jauh',
                    'Membuat cabang (branch) baru',
                    'Melihat status file yang diubah',
                    'Mengambil dan menggabungkan (fetch dan merge) perubahan dari repositori jarak jauh' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Fase terakhir dalam model Waterfall adalah:',
                'options' => [
                    'Design',
                    'Implementation',
                    'Testing',
                    'Maintenance' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'SE',
                'question_text' => 'Teknik pengujian di mana tester tidak memiliki pengetahuan tentang struktur internal kode disebut:',
                'options' => [
                    'White Box Testing',
                    'Grey Box Testing',
                    'User Acceptance Testing',
                    'Black Box Testing' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 8. ARTIFICIAL INTELLIGENCE / ML (10 Soal) =====================
            [
                'tag' => 'AI/ML',
                'question_text' => 'Bidang AI yang memungkinkan komputer untuk belajar dari data tanpa diprogram secara eksplisit adalah:',
                'options' => [
                    'Expert Systems',
                    'Robotics',
                    'Natural Language Processing (NLP)',
                    'Machine Learning' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Jenis pembelajaran di mana model dilatih menggunakan data yang telah diberi label (labeled data) disebut:',
                'options' => [
                    'Unsupervised Learning',
                    'Reinforcement Learning',
                    'Deep Learning',
                    'Supervised Learning' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Apa istilah untuk model yang terlalu kompleks dan bekerja sangat baik pada data pelatihan, tetapi buruk pada data baru?',
                'options' => [
                    'Underfitting',
                    'Bias',
                    'Variance',
                    'Overfitting' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Jaringan Saraf Tiruan (Neural Network) dengan lebih dari satu lapisan tersembunyi (hidden layer) disebut:',
                'options' => [
                    'Shallow Network',
                    'Expert System',
                    'Bayesian Network',
                    'Deep Learning' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Metrik evaluasi yang mengukur proporsi prediksi positif yang benar di antara semua kasus yang diprediksi positif adalah:',
                'options' => [
                    'Recall',
                    'F1-Score',
                    'Accuracy',
                    'Precision' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Jenis pembelajaran yang melibatkan agen yang belajar melalui hadiah (reward) atau hukuman (punishment) adalah:',
                'options' => [
                    'Supervised Learning',
                    'Unsupervised Learning',
                    'Semi-supervised Learning',
                    'Reinforcement Learning' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Algoritma clustering (pengelompokan) yang membagi data menjadi K kelompok berdasarkan mean terdekat adalah:',
                'options' => [
                    'Hierarchical Clustering',
                    'DBSCAN',
                    'Principal Component Analysis (PCA)',
                    'K-Means' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Apa yang dilakukan oleh NLP (Natural Language Processing)?',
                'options' => [
                    'Memproses gambar',
                    'Membuat robot bergerak secara otonom',
                    'Mengoptimalkan algoritma pencarian data',
                    'Memungkinkan komputer memahami dan menghasilkan bahasa manusia' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Apa itu Feature Engineering dalam ML?',
                'options' => [
                    'Proses memilih model',
                    'Proses pelatihan model',
                    'Proses memvalidasi model',
                    'Proses transformasi data mentah menjadi fitur yang dapat dipahami model' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'AI/ML',
                'question_text' => 'Dalam konteks Regresi Linear, metrik yang mengukur seberapa jauh poin data tersebar dari garis regresi disebut:',
                'options' => [
                    'Accuracy',
                    'Precision',
                    'Recall',
                    'Residuals' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 9. CYBER SECURITY (10 Soal) =====================
            [
                'tag' => 'Cybersec',
                'question_text' => 'Tujuan utama dari Enkripsi adalah untuk menjamin properti keamanan data yang disebut:',
                'options' => [
                    'Availability (Ketersediaan)',
                    'Integrity (Integritas)',
                    'Non-repudiation (Non-penyangkalan)',
                    'Confidentiality (Kerahasiaan)' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Apa yang dimaksud dengan Phishing?',
                'options' => [
                    'Upaya untuk membanjiri server dengan permintaan berlebihan',
                    'Teknik mengenkripsi data',
                    'Proses otentikasi biometrik',
                    'Upaya penipuan untuk mendapatkan informasi sensitif dengan menyamar sebagai entitas tepercaya' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Serangan di mana penyerang mencoba membanjiri sistem target dari banyak sumber untuk mengganggu layanannya adalah:',
                'options' => [
                    'SQL Injection',
                    'XSS (Cross-Site Scripting)',
                    'Man-in-the-Middle (MITM)',
                    'DDoS (Distributed Denial of Service)' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Apa fungsi utama dari Hash kriptografi (misalnya, SHA-256)?',
                'options' => [
                    'Untuk mengenkripsi data yang dapat didekripsi kembali',
                    'Untuk menghasilkan kunci publik dan privat',
                    'Untuk membatasi akses pengguna',
                    'Untuk memverifikasi integritas data (memastikan data tidak diubah)' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Model keamanan informasi yang mencakup Confidentiality, Integrity, dan Availability dikenal sebagai:',
                'options' => [
                    'Defense in Depth',
                    'Zero Trust',
                    'Access Control List (ACL)',
                    'CIA Triad' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Jenis malware yang mengenkripsi file korban dan menuntut tebusan adalah:',
                'options' => [
                    'Trojan',
                    'Virus',
                    'Worm',
                    'Ransomware' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Mekanisme keamanan yang memverifikasi identitas pengguna (siapa Anda?) disebut:',
                'options' => [
                    'Authorization',
                    'Auditing',
                    'Access Control',
                    'Authentication' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Serangan web application di mana penyerang menyuntikkan kode berbahaya ke dalam database melalui input pengguna adalah:',
                'options' => [
                    'Buffer Overflow',
                    'DDoS',
                    'Cross-Site Request Forgery (CSRF)',
                    'SQL Injection' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Two-Factor Authentication (2FA) meningkatkan keamanan dengan memerlukan:',
                'options' => [
                    'Dua password berbeda',
                    'Otentikasi oleh dua administrator',
                    'Koneksi jaringan yang aman dan terenkripsi',
                    'Dua faktor dari kategori "tahu", "miliki", atau "adalah"' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'Cybersec',
                'question_text' => 'Sertifikat digital SSL/TLS menjamin properti keamanan apa?',
                'options' => [
                    'Availability dan Integrity',
                    'Integrity dan Non-repudiation',
                    'Confidentiality dan Availability',
                    'Confidentiality dan Integrity' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],

            // ===================== 10. DISCRETE MATH & LOGIC (10 Soal) =====================
            [
                'tag' => 'MathLogic',
                'question_text' => 'Operasi logika Boolean yang menghasilkan TRUE hanya jika kedua input berbeda (salah satunya TRUE, yang lain FALSE) adalah:',
                'options' => [
                    'NOT',
                    'AND',
                    'OR',
                    'XOR' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Sebuah proposisi yang selalu bernilai TRUE, terlepas dari nilai kebenaran komponennya, disebut:',
                'options' => [
                    'Kontradiksi',
                    'Kontingensi',
                    'Argumen',
                    'Tautologi' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Dalam Teori Graf (Graph Theory), simpul (vertex) yang memiliki derajat (degree) nol disebut:',
                'options' => [
                    'Root',
                    'Leaf',
                    'Pendant Vertex',
                    'Isolated Vertex' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Manakah notasi standar untuk menyatakan bahwa himpunan A adalah subset (bagian) dari himpunan B?',
                'options' => [
                    'A ∈ B',
                    'A = B',
                    'A ∩ B',
                    'A ⊆ B' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Urutan elemen yang teratur, di mana pengulangan diperbolehkan dan urutan penting, adalah definisi dari:',
                'options' => [
                    'Kombinasi',
                    'Himpunan (Set)',
                    'Multiset',
                    'Permutasi' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Sebuah relasi R pada himpunan A disebut Simetris jika:',
                'options' => [
                    '(a, a) ∈ R untuk setiap a ∈ A',
                    'Jika (a, b) ∈ R dan (b, c) ∈ R, maka (a, c) ∈ R',
                    'Jika (a, b) ∈ R dan (b, a) ∈ R, maka a=b',
                    'Jika (a, b) ∈ R, maka (b, a) ∈ R' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Berapa banyak bit (binary digit) yang dibutuhkan untuk merepresentasikan 32 nilai yang berbeda?',
                'options' => [
                    '4 bit',
                    '6 bit',
                    '8 bit',
                    '5 bit' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Dalam aljabar Boolean, hukum P ∨ (Q ∧ R) ≡ (P ∨ Q) ∧ (P ∨ R) disebut Hukum:',
                'options' => [
                    'Asosiatif',
                    'Komutatif',
                    'Identitas',
                    'Distributif' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Sebuah fungsi f: A → B disebut Bijektif jika fungsi tersebut:',
                'options' => [
                    'Hanya Injektif',
                    'Hanya Surjektif',
                    'Tidak Injektif dan tidak Surjektif',
                    'Injektif dan Surjektif' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
            [
                'tag' => 'MathLogic',
                'question_text' => 'Apa istilah untuk graph di mana setiap pasangan simpul yang berbeda terhubung oleh edge yang unik?',
                'options' => [
                    'Disconnected Graph',
                    'Bipartite Graph',
                    'Weighted Graph',
                    'Complete Graph' // Jawaban Benar
                ],
                'correct_answer' => 'd',
            ],
        ];

        // Memasukkan data ke database
        foreach ($questionsData as $q) {
            Question::create([
                'question_text' => $q['question_text'], // Kolom diperbaiki
                'option_a' => $q['options'][0],
                'option_b' => $q['options'][1],
                'option_c' => $q['options'][2],
                'option_d' => $q['options'][3],
                'correct_answer' => $q['correct_answer'], // Diisi dengan 'a', 'b', 'c', atau 'd'
                'tag' => $q['tag'],
            ]);
        }
    }
}
