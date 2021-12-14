<?php 

// mengatur datetime
date_default_timezone_set('Asia/Jakarta');
$localtime_assoc = localtime(time(), true);
setlocale(LC_ALL, 'id-ID', 'id_ID');

function get_CURL($url){
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);     //true untuk mengembalikan dalam bentuk text/json
    $result = curl_exec($curl);
    curl_close($curl);

    return json_decode($result,true);
}

// konek api data.covid19.go.id
// hasil
$result_covid = get_CURL("https://data.covid19.go.id/public/api/update.json");

// data update terbaru
$update_total = $result_covid['update']['total'];
// waktu update terakhir
$waktu_update = $result_covid['update']['penambahan']['created'];

$awal  = date_create($waktu_update);
$akhir = date_create(); // waktu sekarang
$diff  = date_diff( $awal, $akhir );
$selisih_update_terakhir = $diff->h." jam ". $diff->i." menit yang lalu";

// ambil data 30 hari terakhir
$jumlah_data_harian = count($result_covid['update']['harian']);
$data_30_hari_terakhir = [];
$count = 0;
for($i=$jumlah_data_harian-1;$i>=$jumlah_data_harian-31;$i--){
    $get_tanggal = strtok($result_covid['update']['harian'][$i]['key_as_string'], 'T');

    $data_30_hari_terakhir[$count]['tanggal'] = $get_tanggal ;
    $data_30_hari_terakhir[$count]['jumlah_positif'] = $result_covid['update']['harian'][$i]['jumlah_positif']['value'];
    $data_30_hari_terakhir[$count]['jumlah_sembuh'] = $result_covid['update']['harian'][$i]['jumlah_sembuh']['value'];
    $data_30_hari_terakhir[$count]['jumlah_meninggal'] = $result_covid['update']['harian'][$i]['jumlah_meninggal']['value'];
    $data_30_hari_terakhir[$count]['jumlah_dirawat'] = $result_covid['update']['harian'][$i]['jumlah_dirawat']['value'];
    $count++;
}

$data_30_hari_terakhir = array_reverse($data_30_hari_terakhir);

// ambil data tiap bulan
$data_perbulan = [];
$count = 0;
$kemarin = date('Y-m-d',strtotime("-1 days"));
for($i=0; $i<$jumlah_data_harian;$i++){
    // menagmbil data pada tangggal 1 tiap bulan dan hari kemarin
    if(strftime("%d",strtotime(strtok($result_covid['update']['harian'][$i]['key_as_string'], 'T')))==1 || date("Y-m-d",strtotime(strtok($result_covid['update']['harian'][$i]['key_as_string'], 'T')))==$kemarin){
        $get_tanggal = strtok($result_covid['update']['harian'][$i]['key_as_string'], 'T');

        $data_perbulan[$count]['tanggal'] = $get_tanggal;
        $data_perbulan[$count]['jumlah_positif'] = $result_covid['update']['harian'][$i]['jumlah_positif_kum']['value'];
        $data_perbulan[$count]['jumlah_sembuh'] = $result_covid['update']['harian'][$i]['jumlah_sembuh_kum']['value'];
        $data_perbulan[$count]['jumlah_meninggal'] = $result_covid['update']['harian'][$i]['jumlah_meninggal_kum']['value'];
        $data_perbulan[$count]['jumlah_dirawat'] = $result_covid['update']['harian'][$i]['jumlah_dirawat_kum']['value'];
    }
    $count++;
}

// end api data covid

// start konek api rs rujukan
// hasil
$result_rs = get_CURL("https://dekontaminasi.com/api/id/covid19/hospitals");

$no = 1;
// end api rs rujukan



?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="./src/css/style.css">
    <link rel="shortcut icon" href="./src/images/NEWLOGOWHITEBOLDENOUTLINE.png" type="image/x-icon">
    <meta name="description" content="Informasi Seputar COVID-19, Update Data Terbaru Penyebaran COVID-19 di Indonesia, Tips Pencegahan Penularan COVID-19, Rumah Sakit Rujukan COVID-19">
    <meta name="keywords" content="data terbaru covid, pencegahan covid, rs rujukan covid">
    <meta name="author" content="Ruly Adhika MH">
    <title>SARS CoV - Informasi Mengenai COVID-19</title>
</head>

<body class="light">
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom bg-transparent fixed-top">
        <div class="container nav-container">
            <a class="navbar-brand ml-0 font-weight-bold text-danger" href="#">SARS CoV</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span >
                    <i class="fa fa-bars text-danger"></i>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link tentang-nav" href="javascript:void(0)">Tentang <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pencegahan-nav" href="javascript:void(0)">Pencegahan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link data-nav" href="javascript:void(0)">Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rujukan-nav mr-0" href="javascript:void(0)" >Rs Rujukan</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="main-container">
        <header>
            <div class="banner">
                <div class="text-area">
                    <div class="text mt-5 mt-sm-0">
                        <h1 class="font-weight-bold">
                            Update terbaru data penyebaran <span class="text-danger">covid-19</span> Indonesia
                            <img src="./src/images/indonesia-flag.png" style="width: 30px; border-radius: 15px;" class="shadow" alt="">
                        </h1>
                        <small class="font-italic mb-2 d-block pb-3 text-muted">
                            Data diupdate <?= $selisih_update_terakhir; ?> &#8226 sumber : data.covid19.go.id
                        </small>
                        <div class="row dislay-data justify-content-center">
                            <div class="col-6 col-md-3 mb-3 mb-lg-0 text-center border-right">
                                <h3 class="font-weight-bold text-danger" id="terkonfirmasi">0</h3>
                                <h6 class="mb-0 ">Terkonfirmasi</h6>
                            </div>
                            <div class="col-6 col-md-3 mb-3 mb-lg-0 text-center border-right">
                                <h3 class="font-weight-bold text-success" id="jml_sembuh">0</h3>
                                <h6 class="mb-0 ">Sembuh</h6>
                            </div>
                            <div class="col-6 col-md-3 mb-3 mb-lg-0 text-center border-right">
                                <h3 class="font-weight-bold text-danger" id="jml_dirawat">0</h3>
                                <h6 class="mb-0 ">Dirawat</h6>
                            </div>
                            <div class="col-6 col-md-3 text-center">
                                <h3 class="font-weight-bold text-danger" id="jml_meninggal">0</h3>
                                <h6 class="mb-0 ">Meninggal</h6>
                            </div>
                        </div>
                        <button class="more-detail-btn btn btn-danger mt-3 mt-md-2 mt-lg-4" style="border-radius: 18px;">Lihat Selengkapnya <i class="fa fa-chevron-circle-right mt-1 "></i></button>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-container mt-5 mt-sm-0">
            <section id="info-covid" class="row section-content">
                <div class="col-md-12 col-lg-7 my-auto">
                    <h2 class="font-weight-bold">Apa itu coronavirus?</h2>
                    <p class="">
                        Virus Corona atau severe acute respiratory syndrome coronavirus 2 (SARS-CoV-2) adalah virus yang menyerang sistem pernapasan. Penyakit karena infeksi virus ini disebut COVID-19. Virus Corona bisa menyebabkan gangguan ringan pada sistem pernapasan, infeksi paru-paru yang berat, hingga kematian.
                    </p>
                    <p>
                        Virus ini menular melalui percikan dahak (droplet) dari saluran pernapasan, misalnya ketika berada di ruang tertutup yang ramai dengan sirkulasi udara yang kurang baik atau kontak langsung dengan droplet.
                    </p>
                    <p>
                        Gejala awal infeksi virus Corona atau COVID-19 bisa menyerupai gejala flu, yaitu demam, pilek, batuk kering, sakit tenggorokan, dan sakit kepala. Setelah itu, gejala dapat hilang dan sembuh atau malah memberat. Penderita dengan gejala yang berat bisa mengalami demam tinggi, batuk berdahak bahkan berdarah, sesak napas, dan nyeri dada.
                    </p>
                </div>
                <div class="col-lg-5 d-flex justify-content-center align-items-start">
                    <img src="./src/images/covid.svg" class=" d-none d-lg-block" alt="">
                </div>
            </section>

            <section id="tips-pencegahan" class="row section-content">
                <div class="col-12 mb-3">
                    <h2 class="font-weight-bold">Tips cegah penularan</h2>
                    <h6 class="">Hal - hal yang bisa dilakukan untuk mencegah terkena penularan covid</h6>
                </div>
                <div class="col-12">
                    <ul class="content-pencegahan text-center">
                        <li data-aos="fade-up">
                            <img src="./src/images/Artboard 1.svg" alt="">
                            <h6 class="">Selalu Gunakan Masker</h6>
                        </li>
                        <li data-aos="fade-up" data-aos-delay="100">
                            <img src="./src/images/Artboard 2.svg" alt="">
                            <h6 class="">Rajin Cuci Tangan</h6>
                        </li>
                        <li data-aos="fade-up" data-aos-delay="200">
                            <img src="./src/images/Artboard 3.svg" alt="">
                            <h6 class="">Hindari Kontak Dengan Orang Sakit</h6>
                        </li>
                        <li data-aos="fade-up" data-aos-delay="300">
                            <img src="./src/images/Artboard 4.svg" alt="">
                            <h6 class="">Selalu Gunakan Hand Sanitizer</h6>
                        </li>
                        <li data-aos="fade-up" data-aos-delay="400">
                            <img src="./src/images/Artboard 5.svg" alt="">
                            <h6 class="">Tutup Mulut Saat Batuk Dan Bersin</h6>
                        </li>
                        <li data-aos="fade-up" data-aos-delay="500">
                            <img src="./src/images/Artboard 6.svg" alt="">
                            <h6 class="">Tetap Di Rumah</h6>
                        </li>
                    </ul>
                </div>
            </section>

            <section id="data-covid" class="section-content">
                <div class="judul-data-covid">
                    <h2 class="font-weight-bold">Data penyebaran covid-19</h2>
                </div>
                <div class="text-data-covid mb-2">
                    <p class=" ">Data penyebaran covid se-Indonesia</p>
                </div>
                <div class="graph-data-covid-harian mb-5 mb-md-0">
                    <canvas id="myChart1"></canvas>
                </div>
                <div class="graph-data-covid-bulanan mb-2">
                    <canvas id="myChart2"></canvas>
                </div>
                <div class="footer-data">
                    <small class=" font-italic">Source : data.covid19.go.id</small>
                </div>
            </section>

            <section id="rs-rujukan" class="row section-content">
                <div class="col-12 mb-3">
                    <h2 class="font-weight-bold">Daftar rumah sakit rujukan</h2>
                    <h6 class="">Daftar rumah sakit rujukan covid-19 di Indonesia</h6>
                </div>
                <div class="col-12 mb-3">
                    <div class="table-responsive">
                        <table id="data-rs" class="table table-striped" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Daerah</th>
                                    <th>Provinsi</th>
                                    <th>No. Telp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($result_rs as $data_rs) :?>
                                    <tr>
                                        <td class="text-center font-weight-bold"><?= $no; ?></td>
                                        <td><?= $data_rs['name']; ?></td>
                                        <td class="text-center"><?= ucwords(strtolower($data_rs['address'])); ?></td>
                                        <td class="text-center"><?= ucwords(strtolower($data_rs['region'])); ?></td>
                                        <td class="text-center"><?= ucwords(strtolower($data_rs['province'])); ?></td>
                                        <td class="text-center"><?= $data_rs['phone']; ?></td>
                                    </tr>
                                <?php $no++ ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <small class=" font-italic">Source : dekontaminasi.com</small>
                </div>
                
            </section>
        </div>

        <div class="theme-switch-btn d-flex flex-column">
            <button class="btn mb-2" id="dark" tabindex="0"  data-toggle="popover" data-trigger="hover" data-content="Toggle dark mode"><i class="far fa-moon"></i></button>
            <button class="btn" id="light" tabindex="0"  data-toggle="popover" data-trigger="hover" data-content="Toggle light mode"><i class="far fa-lightbulb p-auto"></i></button>
        </div>
    </main>
    <footer>
        <div class="footer-wrapper">
            <span>Made With <i class="fa fa-heart"></i> by Ruly Adhika</span>
        </div>
    </footer>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"
        integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="./src/js/script.js"></script>
    

<script>
    $('#dark').popover({
        trigger: 'hover'
    })

    $('#light').popover({
        trigger: 'hover'
    })

    AOS.init({
        easing: 'ease-out-back',
        duration: 1000
    });

    var ctx1 = document.getElementById('myChart1').getContext('2d');
    var myChart1 = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: [<?php foreach($data_30_hari_terakhir as $data_kasus){
                        echo "'".strftime("%d %b",strtotime($data_kasus['tanggal']))."',";
                    };
                    ?>],
            datasets: [{
                label: 'Terkonfirmasi',
                data: [<?php foreach($data_30_hari_terakhir as $data_kasus){
                            echo $data_kasus['jumlah_positif'].",";
                        };
                       ?>],
                backgroundColor: [
                    'rgba(37,76,94,0.5)',
                ],
                borderColor: [
                    'rgba(37,76,94,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(37,76,94,1)',
                pointBorderColor :'rgba(37,76,94,1)'
            },
            {
                label: 'Sembuh',
                data: [<?php foreach($data_30_hari_terakhir as $data_kasus){
                            echo $data_kasus['jumlah_sembuh'].",";
                        };
                       ?>], 
                backgroundColor: [
                    'rgba(40,167,69,0.5)',
                ],
                borderColor: [
                    'rgba(40,167,69,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(40,167,69,1)',
                pointBorderColor :'rgba(40,167,69,1)'
            },
            {
                label: 'Dirawat',
                data: [<?php foreach($data_30_hari_terakhir as $data_kasus){
                            echo $data_kasus['jumlah_dirawat'].",";
                        };
                       ?>], 
                backgroundColor: [
                    'rgba(255,193,30,0.5)',
                ],
                borderColor: [
                    'rgba(255,193,30,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(255,193,30,1)',
                pointBorderColor :'rgba(255,193,30,1)'
            },{
                label: 'Meninggal',
                data: [<?php foreach($data_30_hari_terakhir as $data_kasus){
                            echo $data_kasus['jumlah_meninggal'].",";
                        };
                       ?>], 
                backgroundColor: [
                    'rgba(220,53,69,0.5)',
                ],
                borderColor: [
                    'rgba(220,53,69,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(220,53,69,1)',
                pointBorderColor :'rgba(220,53,69,1)'
            },]
        },
        options: {
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Penambahan jumlah selama 30 hari terakhir'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 10
                    },
                    // display: true,
                    // scaleLabel: {
                    //     display: true,
                    //     labelString: 'Tanggal'
                    // }
                }],
            }
        }
    });

    var ctx2 = document.getElementById('myChart2').getContext('2d');
    var myChart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: [<?php foreach($data_perbulan as $data_bulanan){
                        echo "'".strftime("%d %b",strtotime($data_bulanan['tanggal']))."',";
                    };
                    ?>],
            datasets: [{
                label: 'Terkonfirmasi',
                data: [<?php foreach($data_perbulan as $data_bulanan){
                        echo $data_bulanan['jumlah_positif'].",";
                    };
                    ?>],
                backgroundColor: [
                    'rgba(37,76,94,0.5)',
                ],
                borderColor: [
                    'rgba(37,76,94,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(37,76,94,1)',
                pointBorderColor :'rgba(37,76,94,1)'
            },
            {
                label: 'Sembuh',
                data: [<?php foreach($data_perbulan as $data_bulanan){
                        echo $data_bulanan['jumlah_sembuh'].",";
                    };
                    ?>], 
                backgroundColor: [
                    'rgba(40,167,69,0.5)',
                ],
                borderColor: [
                    'rgba(40,167,69,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(40,167,69,1)',
                pointBorderColor :'rgba(40,167,69,1)'
            },
            {
                label: 'Dirawat',
                data: [<?php foreach($data_perbulan as $data_bulanan){
                        echo $data_bulanan['jumlah_dirawat'].",";
                    };
                    ?>], 
                backgroundColor: [
                    'rgba(255,193,30,0.5)',
                ],
                borderColor: [
                    'rgba(255,193,30,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(255,193,30,1)',
                pointBorderColor :'rgba(255,193,30,1)'
            },{
                label: 'Meninggal',
                data: [<?php foreach($data_perbulan as $data_bulanan){
                        echo $data_bulanan['jumlah_meninggal'].",";
                    };
                    ?>], 
                backgroundColor: [
                    'rgba(220,53,69,0.5)',
                ],
                borderColor: [
                    'rgba(220,53,69,1)',
                ],
                borderWidth: 2,
                fill: 'false',
                pointBackgroundColor: 'rgba(220,53,69,1)',
                pointBorderColor :'rgba(220,53,69,1)'
            },]
        },
        options: {
            maintainAspectRatio: false,
            title: {
            display: true,
            text: 'Total kasus hingga saat ini'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
        }
    });

    $("document").ready( function () {
        $('#data-rs').DataTable({
            "columnDefs": [{
                    "orderable": false,
                    "targets": [5]
            }],
            "language":{
                    "emptyTable":"Maaf data tidak tersedia...",
                    "info":"Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    "infoEmpty":"Maaf, Data tidak tersedia..",
                    "infoFiltered": "",
                    "search":"Pencarian",
                    "lengthMenu":"Menampilkan _MENU_ data",
                    "zeroRecords":"Maaf, Data tidak tersedia.",
                    "paginate":{
                            "first":"Pertama",
                            "last":"Terakhir",
                            "next":"Berikutnya",
                            "previous":"Sebelumnya"
                        },
                    "searchPlaceholder" : "Masukan kata kunci"
            }
        });
    });


    </script>


    <script type="module">
        import { CountUp } from './src/js/countUp.min.js';
        const options = {
          duration: 4,
          separator: '.',
        };

        const jml_positif = new CountUp('terkonfirmasi', <?= $update_total['jumlah_positif']; ?>,options);
        const jml_dirawat = new CountUp('jml_dirawat', <?= $update_total['jumlah_dirawat']; ?>,options);
        const jml_meninggal = new CountUp('jml_meninggal', <?= $update_total['jumlah_meninggal']; ?>,options);
        const jml_sembuh = new CountUp('jml_sembuh', <?= $update_total['jumlah_sembuh']; ?>,options);
        jml_positif.start();
        setTimeout(()=>{
            jml_sembuh.start();
        },500);
        setTimeout(()=>{
            jml_dirawat.start();
        },1000);
        setTimeout(()=>{
            jml_meninggal.start();
        },1500);
    </script>

</body>

</html>

