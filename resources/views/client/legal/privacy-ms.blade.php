<x-client.layouts.legal
    title="Notis Privasi"
    heading="Notis <em>Privasi</em>"
    intro="Bagaimana NRH Intelligence mengumpul, menggunakan dan melindungi data peribadi dalam KYRO, di bawah Akta Perlindungan Data Peribadi 2010 Malaysia."
    locale="ms"
    :languages="[
        ['locale' => 'en', 'label' => 'English', 'url' => route('client.privacy')],
        ['locale' => 'ms', 'label' => 'Bahasa Malaysia', 'url' => route('client.privacy.ms')],
    ]"
>

    <p style="margin-top:20px;">Notis ini juga tersedia dalam <a href="{{ route('client.privacy') }}" hreflang="en">Bahasa Inggeris</a>. Sekiranya terdapat perbezaan antara kedua-dua versi, versi Bahasa Inggeris akan diguna pakai.</p>

    <nav class="lg-toc" aria-label="Kandungan">
        <div class="lg-toc-title">Kandungan</div>
        <ol>
            <li><a href="#who">Siapa kami</a></li>
            <li><a href="#scope">Siapa yang dilindungi notis ini</a></li>
            <li><a href="#collect">Data yang kami kumpul</a></li>
            <li><a href="#sources">Dari mana data diperoleh</a></li>
            <li><a href="#purposes">Mengapa kami menggunakannya</a></li>
            <li><a href="#sharing">Dengan siapa kami berkongsi</a></li>
            <li><a href="#transfers">Pemindahan ke luar Malaysia</a></li>
            <li><a href="#security">Bagaimana kami melindunginya</a></li>
            <li><a href="#retention">Tempoh penyimpanan</a></li>
            <li><a href="#rights">Hak anda</a></li>
            <li><a href="#cookies">Kuki</a></li>
            <li><a href="#changes">Perubahan kepada notis ini</a></li>
            <li><a href="#contact">Hubungi kami</a></li>
        </ol>
    </nav>

    <h2 id="who"><span class="num">01</span>Siapa kami</h2>
    <p>KYRO dikendalikan oleh <b>{{ config('app.company.name') }}</b> (No. Syarikat {{ config('app.company.reg_no') }}), {{ config('app.company.address') }} ("<b>NRH Intelligence</b>", "<b>kami</b>"). Kami menyediakan perkhidmatan saringan latar belakang kepada syarikat ("<b>Pelanggan</b>").</p>

    <h2 id="scope"><span class="num">02</span>Siapa yang dilindungi notis ini</h2>
    <ul>
        <li><b>Pengguna:</b> individu yang log masuk ke KYRO bagi pihak Pelanggan.</li>
        <li><b>Calon:</b> individu yang diminta oleh Pelanggan untuk kami saring, biasanya pemohon kerja atau pekerja.</li>
    </ul>
    <p>Bagi data Calon, Pelanggan menentukan siapa yang disaring dan atas tujuan apa, serta mendapatkan persetujuan Calon. Kami memproses data tersebut untuk menjalankan saringan dan turut memikul tanggungjawab kami sendiri ke atasnya di bawah Akta Perlindungan Data Peribadi 2010 ("<b>APDP</b>"). Jika anda seorang Calon, majikan yang meminta persetujuan anda sepatutnya juga telah memberikan notis privasinya sendiri kepada anda.</p>

    <h2 id="collect"><span class="num">03</span>Data yang kami kumpul</h2>
    <h3>Mengenai Pengguna</h3>
    <ul>
        <li>Nama, e-mel kerja, nombor telefon, jawatan dan Pelanggan yang anda wakili.</li>
        <li>Rekod log masuk: tarikh dan masa, alamat IP, pelayar, kaedah pengesahan dua faktor dan cubaan yang gagal.</li>
        <li>Rekod tindakan yang anda lakukan dalam KYRO, seperti menghantar permohonan, memuat turun laporan dan mengurus pengguna.</li>
        <li>Butiran pengebilan dan slip pembayaran yang dimuat naik untuk akaun Pelanggan anda.</li>
    </ul>
    <h3>Mengenai Calon</h3>
    <ul>
        <li>Butiran pengenalan: nama penuh, nombor kad pengenalan atau pasport, tarikh lahir, kewarganegaraan dan butiran perhubungan.</li>
        <li>Dokumen yang dimuat naik oleh Pelanggan, seperti resume, sijil, salinan dokumen pengenalan dan borang persetujuan yang ditandatangani.</li>
        <li>Keputusan semakan yang dipesan, yang mungkin merangkumi sejarah pekerjaan, pendidikan, kelayakan profesional, kedudukan kredit atau kewangan, serta rekod jenayah atau mahkamah.</li>
        <li>Rekod persetujuan: bila persetujuan diberikan, teks persetujuan yang dipaparkan dan bukti yang dikemukakan.</li>
    </ul>
    <p>Sebahagian daripada data ini, terutamanya maklumat mengenai kesalahan jenayah dan prosiding mahkamah, adalah <b>data peribadi sensitif</b> di bawah APDP. Kami hanya memprosesnya dengan persetujuan nyata Calon.</p>

    <h2 id="sources"><span class="num">04</span>Dari mana data diperoleh</h2>
    <ul>
        <li>Daripada Pengguna, semasa anda mendaftar, log masuk dan menggunakan KYRO.</li>
        <li>Daripada Pelanggan, yang mengemukakan butiran dan dokumen Calon.</li>
        <li>Daripada sumber yang kami hubungi untuk mengesahkan maklumat, seperti bekas majikan, universiti dan kolej, badan profesional, agensi pelaporan kredit, mahkamah dan pendaftaran awam yang lain.</li>
    </ul>

    <h2 id="purposes"><span class="num">05</span>Mengapa kami menggunakannya</h2>
    <ul>
        <li>Untuk menjalankan saringan yang dipesan oleh Pelanggan dan dipersetujui oleh Calon, serta mengeluarkan laporan.</li>
        <li>Untuk mengurus akaun Pengguna: log masuk, pengesahan dua faktor, pemberitahuan dan sokongan.</li>
        <li>Untuk mengeluarkan invois kepada Pelanggan dan memproses pembayaran.</li>
        <li>Untuk memastikan KYRO selamat, mengesan penyalahgunaan dan menyiasat insiden.</li>
        <li>Untuk mematuhi kewajipan undang-undang dan peraturan kami, termasuk penyimpanan rekod cukai dan permintaan sah daripada pihak berkuasa.</li>
    </ul>
    <p>Kami tidak menjual data peribadi dan tidak menggunakannya untuk pengiklanan.</p>

    <h2 id="sharing"><span class="num">06</span>Dengan siapa kami berkongsi</h2>
    <ul>
        <li><b>Pelanggan</b> yang memesan saringan akan menerima laporan Calon.</li>
        <li><b>Sumber pengesahan</b> hanya menerima butiran yang diperlukan untuk mengesahkan sesuatu rekod. Contohnya, bekas majikan menerima nama Calon dan tarikh perkhidmatannya.</li>
        <li><b>Penyedia perkhidmatan</b> yang membantu kami mengendalikan KYRO, di bawah kontrak dan hanya untuk tujuan tersebut: Cloudflare (keselamatan dan penghantaran rangkaian), Microsoft (e-mel) dan penyedia pengehosan e-mel kami.</li>
        <li><b>Pihak berkuasa</b>, apabila dikehendaki oleh undang-undang.</li>
    </ul>

    <h2 id="transfers"><span class="num">07</span>Pemindahan ke luar Malaysia</h2>
    <p>Aplikasi dan pangkalan data KYRO beroperasi di pelayan yang diuruskan oleh NRH Intelligence. Sesetengah penyedia perkhidmatan, seperti Cloudflare dan Microsoft, mengendalikan rangkaian global, jadi data yang melalui mereka mungkin diproses di luar Malaysia. Dalam keadaan sedemikian, kami bergantung kepada penyedia yang terikat dengan komitmen perlindungan data secara kontrak, sebagaimana yang dibenarkan oleh APDP. Semakan saringan di negara lain, seperti semakan pendidikan di luar negara, juga memerlukan butiran berkaitan dihantar kepada sumber di negara tersebut.</p>

    <h2 id="security"><span class="num">08</span>Bagaimana kami melindunginya</h2>
    <ul>
        <li>Semua sambungan ke KYRO disulitkan semasa penghantaran (HTTPS).</li>
        <li>Fail dan laporan yang dimuat naik disulitkan semasa disimpan menggunakan AES-256, dan nombor pengenalan disimpan dalam bentuk sulit.</li>
        <li>Setiap log masuk memerlukan kata laluan dan faktor kedua, dan cubaan gagal yang berulang akan mengunci akaun.</li>
        <li>Setiap Pelanggan hanya boleh melihat datanya sendiri, dan akses dalam sesebuah Pelanggan mengikut peranan yang ditetapkan oleh pengurusnya.</li>
        <li>Log masuk dan tindakan penting direkodkan, dan akses kakitangan kepada data Calon dihadkan kepada mereka yang memerlukannya.</li>
    </ul>
    <p>Jika berlaku pelanggaran data yang berkemungkinan menyebabkan kemudaratan yang ketara, kami akan memaklumkan Pesuruhjaya Perlindungan Data Peribadi dan individu yang terjejas sebagaimana yang dikehendaki oleh undang-undang.</p>

    <h2 id="retention"><span class="num">09</span>Tempoh penyimpanan</h2>
    <table class="lg-table">
        <tr><th>Rekod</th><th>Disimpan selama</th><th>Sebab</th></tr>
        <tr><td>Data dan laporan Calon</td><td>7 tahun dari tarikh permohonan</td><td>Untuk menjawab pertikaian dan pertanyaan mengenai sesuatu saringan</td></tr>
        <tr><td>Rekod persetujuan</td><td>10 tahun</td><td>Bukti bahawa saringan telah dibenarkan</td></tr>
        <tr><td>Invois dan rekod pembayaran</td><td>7 tahun</td><td>Peraturan penyimpanan rekod cukai Malaysia</td></tr>
        <tr><td>Log audit dan log masuk</td><td>7 tahun</td><td>Siasatan keselamatan dan peraturan</td></tr>
    </table>
    <p>Apabila sesuatu tempoh tamat, data peribadi Calon akan disunting secara automatik. Perjanjian perkhidmatan Pelanggan boleh menetapkan tempoh yang lebih singkat.</p>

    <h2 id="rights"><span class="num">10</span>Hak anda</h2>
    <p>Di bawah APDP, anda boleh:</p>
    <ul>
        <li><b>mengakses</b> data peribadi yang kami simpan mengenai anda dan mendapatkan salinannya;</li>
        <li><b>membetulkan</b> data yang tidak tepat, tidak lengkap atau tidak terkini;</li>
        <li><b>menarik balik persetujuan</b>, walaupun saringan yang telah selesai tidak boleh dibatalkan dan kami perlu menyimpan rekod yang disenaraikan di atas; dan</li>
        <li><b>mengehadkan</b> cara data anda digunakan, atau membantah pemprosesan yang berkemungkinan menyebabkan kerosakan atau kesusahan kepada anda.</li>
    </ul>
    <p>Pengguna boleh mengemas kini kebanyakan butiran mereka sendiri di bawah Tetapan. Calon boleh menghubungi majikan yang meminta saringan, atau terus menghubungi kami. Kami akan memberi maklum balas dalam tempoh 21 hari, dan mungkin perlu mengesahkan identiti anda terlebih dahulu. APDP membenarkan fi yang kecil bagi salinan data anda. Kami akan memaklumkan anda sebelum mengenakannya.</p>
    <p>Jika anda tidak berpuas hati dengan maklum balas kami, anda boleh membuat aduan kepada Pesuruhjaya Perlindungan Data Peribadi Malaysia.</p>

    <h2 id="cookies"><span class="num">11</span>Kuki</h2>
    <p>KYRO hanya menggunakan kuki yang diperlukan untuk berfungsi:</p>
    <ul>
        <li><b>kuki sesi</b> yang mengekalkan anda dalam keadaan log masuk dan tamat apabila sesi anda berakhir;</li>
        <li><b>token keselamatan</b> yang melindungi borang daripada pemalsuan permintaan rentas tapak; dan</li>
        <li><b>kuki ingat peranti</b>, hanya jika anda menanda "Trust this device", selama 30 hari.</li>
    </ul>
    <p>Pilihan tema cerah atau gelap anda disimpan dalam pelayar anda. Kami tidak menggunakan kuki analitik, pengiklanan atau penjejakan.</p>

    <h2 id="changes"><span class="num">12</span>Perubahan kepada notis ini</h2>
    <p>Kami mungkin mengemas kini notis ini. Tarikh berkuat kuasa di bahagian atas menunjukkan bila ia kali terakhir diubah. Jika sesuatu perubahan itu ketara, kami akan memaklumkan Pelanggan melalui e-mel atau di dalam portal.</p>

    <h2 id="contact"><span class="num">13</span>Hubungi kami</h2>
    <p>Untuk pertanyaan privasi atau untuk melaksanakan hak anda, hubungi:</p>
    <div class="lg-contact">
        <p><b>Pegawai Perlindungan Data, {{ config('app.company.name') }}</b></p>
        <p>{{ config('app.company.address') }}</p>
        <p>E-mel: <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a> · Telefon: {{ config('app.company.phone') }}</p>
    </div>

</x-client.layouts.legal>
