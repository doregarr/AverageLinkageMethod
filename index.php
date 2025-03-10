<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clustering Kabupaten</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        input[type="file"], input[type="number"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            width: 80%;
            max-width: 300px;
            margin-bottom: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        @media (max-width: 768px) {
            .container {
                width: 80%;
            }
            input[type="file"], input[type="number"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📂 Upload File Excel untuk Clustering</h2>
        <form action="process.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <br>
            <input type="number" name="jumlah_cluster" placeholder="Jumlah Cluster" min="1" required>
            <br>
            <button type="submit">🚀 Upload & Proses</button>
        </form>
    </div>
</body>
</html>
