<?php
require 'vendor/autoload.php';  // Pastikan PhpSpreadsheet sudah terinstal

use PhpOffice\PhpSpreadsheet\IOFactory;

function calculateDistanceMatrix($data) {
    $rowCount = count($data);
    $distanceMatrix = array_fill(0, $rowCount, array_fill(0, $rowCount, 0));

    for ($i = 0; $i < $rowCount; $i++) {
        for ($j = $i + 1; $j < $rowCount; $j++) {
            $sum = 0;
            for ($k = 0; $k < count($data[$i]); $k++) {
                $sum += pow($data[$i][$k] - $data[$j][$k], 2);
            }
            $distanceMatrix[$i][$j] = sqrt($sum);
            $distanceMatrix[$j][$i] = $distanceMatrix[$i][$j];
        }
    }
    return $distanceMatrix;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $jumlahCluster = isset($_POST['jumlah_cluster']) ? intval($_POST['jumlah_cluster']) : 4; // Ambil input jumlah cluster dari form
    $targetDir = "uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filePath = $targetDir . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $filePath);

    try {
        $spreadsheet = IOFactory::load($filePath);
    } catch (Exception $e) {
        die("<h3 style='color:red;'>❌ File tidak valid! Pastikan Anda mengunggah file Excel.</h3>");
    }

    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    if (count($rows) < 2) {
        die("<h3 style='color:red;'>❌ Data dalam file Excel tidak cukup untuk clustering.</h3>");
    }

    $clusterLabels = [];
    $data = [];

    foreach ($rows as $index => $row) {
        if ($index == 0) continue;
        $clusterLabels[] = $row[0];
        $data[] = array_slice($row, 1);
    }

    $distanceMatrix = calculateDistanceMatrix($data);

    echo "<h2>🔹 Matriks Jarak Awal</h2>";
    displayMatrix($distanceMatrix, $clusterLabels);

    $iteration = 1;
    while (count($clusterLabels) > $jumlahCluster) {  // Menggunakan input jumlah cluster
        $minDist = INF;
        $minIdx1 = -1;
        $minIdx2 = -1;

        for ($i = 0; $i < count($distanceMatrix); $i++) {
            for ($j = $i + 1; $j < count($distanceMatrix); $j++) {
                if ($distanceMatrix[$i][$j] < $minDist) {
                    $minDist = $distanceMatrix[$i][$j];
                    $minIdx1 = $i;
                    $minIdx2 = $j;
                }
            }
        }

        if ($minIdx1 == -1 || $minIdx2 == -1) {
            break;
        }

        $newClusterName = "{$clusterLabels[$minIdx1]}-{$clusterLabels[$minIdx2]}";
        echo "<div class='iteration-container'>
                <div class='iteration-header'>🔹 Iterasi {$iteration} </div>
                <div class='iteration-content'>
                    <p><strong>Gabungan:</strong> '{$clusterLabels[$minIdx1]}' + '{$clusterLabels[$minIdx2]}'</p>
                    <p><strong>Jarak Minimum:</strong> $minDist</p>
                </div>
              </div>";

        for ($i = 0; $i < count($distanceMatrix); $i++) {
            if ($i != $minIdx1 && $i != $minIdx2) {
                $distanceMatrix[$minIdx1][$i] = ($distanceMatrix[$minIdx1][$i] + $distanceMatrix[$minIdx2][$i]) / 2;
                $distanceMatrix[$i][$minIdx1] = $distanceMatrix[$minIdx1][$i];
            }
        }

        array_splice($distanceMatrix, $minIdx2, 1);
        foreach ($distanceMatrix as &$row) {
            array_splice($row, $minIdx2, 1);
        }

        unset($clusterLabels[$minIdx2]);
        $clusterLabels = array_values($clusterLabels);
        $clusterLabels[$minIdx1] = $newClusterName;

        echo "<h3>🔹 Matriks Jarak Setelah Iterasi {$iteration}</h3>";
        displayMatrix($distanceMatrix, $clusterLabels);

        $iteration++;
    }

    echo "<h2>✅ Hasil Akhir: $jumlahCluster Cluster Terbentuk</h2>";
    echo "<p>" . implode(", ", $clusterLabels) . "</p>";
}

echo "<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        text-align: center;
        margin: 20px;
    }
    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        text-align: center;
        font-size: 14px;
        background-color: white;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #ddd;
    }
    .iteration-container {
        width: 80%;
        margin: 20px auto;
        padding: 15px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
    .iteration-header {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
    }
    .iteration-content {
        padding: 10px;
        font-size: 14px;
    }
</style>";

function displayMatrix($matrix, $labels) {
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>Cluster</th>";
    foreach ($labels as $label) {
        echo "<th>$label</th>";
    }
    echo "</tr>";

    foreach ($matrix as $i => $row) {
        echo "<tr><td><b>{$labels[$i]}</b></td>";
        foreach ($row as $value) {
            echo "<td>" . round($value, 2) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
}
?>
