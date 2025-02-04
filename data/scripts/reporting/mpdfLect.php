<p>Page still under development.</p>
<a href="javascript:history.back()">back</a>
<?php
require_once __DIR__ . '../../../../vendor/autoload.php';
include '../dbconn.php';
$stmt = $conn->prepare("SELECT * FROM lecturers ORDER BY npp DESC");
$stmt->execute();
$result = $stmt->get_result();
$mpdf = new \Mpdf\Mpdf();
$filename = 'report-' . date("Y-m-d_H:i:s") . '.pdf';
$i = 1;
$data = '';
while ($row = $result->fetch_assoc()) {
    $data .=
        '<tr>
            <td style="width: 50px; border: 1px solid black;">' . $i . '</td>
            <td style="width: 50px; border: 1px solid black;">' . $row['npp'] . '</td>
            <td style="width: 50px; border: 1px solid black;">' . $row['name'] . '</td>
            <td style="width: 50px; border: 1px solid black;">' . $row['homebase'] . '</td>
        </tr>';
    $i++;
}
$html = '
<!DOCTYPE html>
<html>
<head>
<style>
body {
    font-family: sans-serif;
}

.container {
    width: 800px;
    margin: 0 auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
}

.logo-left {
    height: 100px; 
}

.logo-right {
    height: 100px; 
}

.title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
}

.tabledata {
    border: 1px solid black;
    border-collapse: collapse;
    width: 100%; 
}

th, td {
    border: none;
    padding: 8px;
}

thead tr {
    border: 1px solid black;
    background-color:rgb(107, 174, 255);
    color: #ffffff;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
    <table width="100%" style="vertical-align: middle; font-size: 9pt; color: #000000; border: none;">
      <tr>
          <td width="20%" style="text-align: left;">
            <img src="./../../assets/logo_dinus_new.png" alt="Logo Left" class="logo-left">
          </td>
          <td width="60%" style="text-align: center;">
            <div class="title">
                <div>UNIVERSITAS DIAN NUSWANTORO</div>
                <div>Fakultas Ilmu Komputer</div>
                <div style="font-size: 12px; font-weight: lighter;">
                    Jl. Nakula No. 5-11, Pendirikan Kidul, Semarang
                </div>
                <div style="font-size: 12px; font-weight: lighter;">
                    Telp. (024) 351 7261, Email: Udinus@dinus.ac.id
                </div>
            </div>
          </td>
          <td width="20%" style="text-align: right;">
            <img src="./../../assets/dinus_unggul.png" alt="Logo Right" class="logo-right">
        </td>
      </tr>
    </table>
    </div>
    <hr>
    <div style="text-align: center; font-size: 24px; font-weight: bold;">
        Laporan Data Dosen
    </div>
    <br>
    <table class="tabledata">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 150px;">NPP</th>
                <th style="width: 500px;">Name</th>
                <th style="width: 130px;">Homebase</th>
            </tr>
        </thead>
        <tbody>' . $data . '</tbody>
    </table>
    <p style="font-size: 11px; font-style: italic;">generated at ' . date("Y-m-d_H:i:s") . '</p>
</div>

</body>
</html>
';
$mpdf->WriteHTML($html);
$mpdf->Output($filename, 'I');
echo $html;
