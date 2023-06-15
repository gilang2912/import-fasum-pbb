<?php
require_once __DIR__ . "/vendor/autoload.php";
require "db/connection.php";

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$msg = '';
if (isset($_POST['import'])) {
    $mimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    if (!empty($_FILES['file']['name'] && in_array($_FILES['file']['type'], $mimes))) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $reader = new Xlsx();
            $sheet = $reader->load($_FILES['file']['tmp_name']);
            $worksheet = $sheet->getActiveSheet();
            $sheetArr = $worksheet->toArray();
            $params = [];
            $sql  = "UPDATE PBB.DAT_OP_BUMI SET JNS_BUMI = 4 WHERE 
                    KD_PROPINSI = :kd_prop AND
                    KD_DATI2 = :kd_dati2 AND
                    KD_KECAMATAN = :kd_kec AND
                    KD_KELURAHAN = :kd_kel AND
                    KD_BLOK = :kd_blok AND
                    NO_URUT = :no_urut AND
                    KD_JNS_OP = :kd_jns_op";
            foreach ($sheetArr as $row) {
                $params[] = [
                    'kd_prop' => $row[0],
                    'kd_dati2' => $row[1],
                    'kd_kec' => $row[2],
                    'kd_kel' => $row[3],
                    'kd_blok' => $row[4],
                    'no_urut' => $row[5],
                    'kd_jns_op' => $row[6]
                ];
            }

            $db->beginTransaction();

            try {
                $stmt = $db->prepare($sql);
                $stmt->Execute($params);
            } catch (Exception $e) {
                $db->rollBack();
                die($e->getMessage());
            }

            $db->commit();

            // print_r($params);
            // die;
        } else {
            $msg = "Gagal import file.";
        }
    } else {
        $msg = "Upload file yang dengan extensi .xlsx";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data Fasum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>

    <nav class="navbar bg-light shadow-sm navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <img src="logo.png" alt="Bootstrap" height="32">
                Import Data Fasum PBB |
                Bapenda Luwu Timur
            </span>
        </div>
    </nav>

    <main>
        <div class="container py-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <?php if (!empty($msg)) : ?>
                        <div class="alert alert-warning">
                            <?= $msg ?>
                        </div>
                    <?php endif; ?>
                    <div class="card">
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="file" class="form-label">
                                        Import Data Fasum
                                    </label>
                                    <input type="file" class="form-control mb-2" id="file" name="file">
                                    <small><em>Import data dengan extensi .xlsx</em></small>
                                </div>
                                <button type="submit" name="import" class="btn btn-primary">
                                    Import & Fasumkan data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <div class="card">
                        <div class="card-header">
                            Data NOP yang di fasumkan
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered border-secondary">
                                <thead>
                                    <tr>
                                        <th>Provinsi</th>
                                        <th>Dati2</th>
                                        <th>Kecamatan</th>
                                        <th>Kelurahan</th>
                                        <th>Blok</th>
                                        <th>No. Urut</th>
                                        <th>Jenis Op</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_POST['import']) && !empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $mimes)) {
                                        foreach ($sheetArr as $row) {
                                            echo '<tr>
                                                    <td>' . $row[0] . '</td>
                                                    <td>' . $row[1] . '</td>
                                                    <td>' . $row[2] . '</td>
                                                    <td>' . $row[3] . '</td>
                                                    <td>' . $row[4] . '</td>
                                                    <td>' . $row[5] . '</td>
                                                    <td>' . $row[6] . '</td>
                                                </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>