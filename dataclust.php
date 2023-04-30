<?php
    $db = mysqli_connect("localhost", "root", "", "dataset");

    $query = "SELECT * FROM hepat";
    $result = mysqli_query($db, $query);

    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
    }

    $kluster = 2;
    $iterasi = 0;
    $maks_iterasi = 100;
    $centroids = array();

    $centroids = array(  
         array("age" => 30,"steroid" => 1, "anorexia" => 2),
         array("age" => 50,"steroid" => 1, "anorexia" => 2)
       
    );
    // for ($i = 0; $i < $kluster; $i++) {
    // $rand_keys = array_rand($data);
    // $centroids[] = $data[$rand_keys];
    // }

    // print_r($centroids);


    while(true){
        $jarak = array();
        foreach ($data as $row) {
            $jarak_row = array();
            foreach ($centroids as $centroid) {
            $distance = sqrt(pow($row['age'] - $centroid['age'], 2) + pow($row['steroid'] - $centroid['steroid'], 2) + pow($row['anorexia'] - $centroid['anorexia'], 2));
            $jarak_row[] = $distance;
            }
            $jarak[] = $jarak_row;
        }

        // print_r($jarak);  

        $clusters = array_fill(0, $kluster, array());
        foreach ($jarak as $i => $row) {
            $min_jarak = min($row);
            $cluster_index = array_search($min_jarak, $row);
            $clusters[$cluster_index][] = $data[$i];
        }

        $new_centroids = array();
        foreach ($clusters as $cluster) {
            $cluster_size = count($cluster);
            $age_sum = 0;
            $steroid_sum = 0;
            $anorexia_sum = 0;
            foreach ($cluster as $data_point) {
            $age_sum += $data_point['age'];
            $steroid_sum += $data_point['steroid'];
            $anorexia_sum += $data_point['anorexia'];
            }
            $new_centroids[] = array('age' => $age_sum / $cluster_size, 'steroid' => $steroid_sum / $cluster_size, 'anorexia' => $anorexia_sum / $cluster_size);
        }

        // print_r($new_centroids);

        if ($centroids === $new_centroids) {
        break;
        } else {
        $centroids = $new_centroids;
        $iterasi++;
        }
    }
    $kunci = array_column($centroids, 'age'); 
    array_multisort($kunci, SORT_DESC, $centroids);
    // foreach($centroids as $i => $x){
    //     echo "Cluster".($i+1).": <br>";
    //     foreach($x as $y){
    //         echo $y."<br>";
    //     }
    // }
    // print_r($centroids);
    if(isset($_POST['simpan'])){
        $age = $_POST['age'];
        $steroid = $_POST['steroid'];
        $anorexia = $_POST['anorexia'];
        $sedikit = 1000000000;
        $ambil = 0;
        foreach($centroids as $p => $x){
            $htg = sqrt(pow($x['age'] - $age, 2) + pow($x['steroid'] - $steroid, 2) + pow($x['anorexia'] - $anorexia, 2));
            if($sedikit > $htg){
                $sedikit = $htg;
                $ambil = $p;
            }
        }
        // if($ambil == 0){
        //     echo "Anda menderita hepatitis berat dan berkemungkinan besar untuk meninggal";
        // }
        // else echo "Anda menderita hepatisis ringan atau tidak sama sekali";
        // mysqli_query($db, "insert into hepat values (null, $age, $anorexia, $steroid, ".($ambil+1).")");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
       <!-- navbar ini -->
       <nav class="nav nav-pills flex-column flex-sm-row">
    </nav>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <style>
        h3{
            color: black;
        }
        .table{
            background-color: white;
        }
    </style>
</head>
<body>
    <hr>
<div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kluster</th>
                            <th scope="col">AGE</th>
                            <th scope="col">Steroid</th>
                            <th scope="col">Anorexia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($centroids as $i => $x):?>
                            <tr>
                                <td><?php echo $i+1 ?></td>
                                <td><?php echo $x['age'] ?></td>
                                <td><?php echo $x['steroid'] ?></td>
                                <td><?php echo $x['anorexia'] ?></td>
                            </tr>
                    </tbody>
                    <tbody>
                    <?php endforeach?>
                    <h3>
                            <?php
                            if($ambil == 0){
                                echo '<span style="color:red;text-align:center;">Anda menderita hepatitis berat dan berkemungkinan besar untuk meninggal!</span>';
                            }
                            else  echo '<span style="color:green;text-align:center;">Anda menderita hepatitis ringan atau tidak sama sekali</span>';
                            mysqli_query($db, "insert into hepat values (null, $age, $anorexia, $steroid, ".($ambil+1).")");
                            ?>
                        </h3>
                    </tbody>
                    <a href="indexK.php" class="btn btn-danger">KEMBALI</a>
                </table>
</div>
</body>
</html>
