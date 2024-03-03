<?php


function redirect() {
    header("HTTP/1.1 302 Found");
    header("Location: /");
}

if(isset($_GET['id'])) {
    include 'localdb.php';
    
    $idToCheck = $_GET['id'];
    
    $sql = "SELECT id FROM api WHERE id = ?";
    $stmt = $localmysql->prepare($sql);
    
    $stmt->bind_param("s", $idToCheck);
    $stmt->execute();
    $stmt->bind_result($id);

    if ($stmt->fetch()) {
        include 'db.php';
        
        

        if(isset($_GET['anime_name'])) {
            $sql = "SELECT * FROM info WHERE name LIKE ? ORDER BY name ASC";
            $anime_name = $_GET['anime_name'];
            $stmtapi = $mysqli->prepare($sql);
            $stmtapi->bind_param("s", $anime_name);
        }
        else if(isset($_GET['anime_id'])) {
            $sql = "SELECT * FROM info WHERE id LIKE ?";
            $type = $_GET['anime_id'];
            $stmtapi = $mysqli->prepare($sql);
            $stmtapi->bind_param("s", $type);
        }
        else {
            $sql = "SELECT * FROM info ORDER BY name ASC";
            $stmtapi = $mysqli->prepare($sql);
        }

        
        
        $stmtapi->execute();
        $result = $stmtapi->get_result();
        $stmtapi->close();

        if ($result->num_rows > 0) {
            $data = array(); // Tableau pour stocker les données
        
            while ($row = $result->fetch_assoc()) {
                $data[] = $row; // Ajouter chaque ligne au tableau
            }
        
            // Convertir le tableau en JSON
            $json_data = json_encode($data);
        
            // Afficher le JSON
            echo $json_data;
        } else {
            echo "[]";
        }
    } else {
        $stmt->close();
        redirect();
    }

    $stmt->close();

} else {
    redirect();
}

?>