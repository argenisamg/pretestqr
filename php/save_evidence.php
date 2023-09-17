<?php            
    include_once("ConnectionDB.php");
    $json = "";    
    $response = array(); 
    if ($_SERVER["REQUEST_METHOD"] === "POST") {        
        if (isset($_POST["json"])) {                                                                   
            $json = $_POST["json"];                   
            insertMethod($json);                                                                                                                                                                                  
        } else {
            $response = array("boolean" => false, "msg" => "JSON is empty.");                                           
            echo json_encode($response);                          
            exit();
        }
    } else {
        $response = array("boolean" => false, "msg" => "Empty POST request.");                                           
        echo json_encode($response);                          
        exit();
    }
         
#Recuperar el json:
function insertMethod($jsonparam) {            
    $saveImage = saveImage();
    date_default_timezone_set('America/Denver');
    $serverDate = date('Y-m-d H:i:s');    

    try {
        $connection = new ConnectionDB();
        $conexionMySQL = $connection->conectarMySQL();                          
        $jsonDecoded = json_decode($jsonparam, true);                
        $scannresult="";
        $date="";
        $evidence="";
        $employee="";
        $description="";       

        foreach ($jsonDecoded as $key => $value) {
            if ($key == "scannresult") {
                $scannresult = $value;
                $date = $serverDate;
            } elseif ($key == "fileevidence") {
                $evidence = $saveImage;                
            } elseif ($key == "employee") {
                $employee = $value;
            } elseif ($key == "shift") {
                $shift = $value;            
            } elseif ($key == "inputText") {
                $description = $value;
            }
        }
        
        $arrJson = array($scannresult, $date, $evidence, $employee, $shift, $description);                                 
        $insertQuery = "INSERT INTO `pretest_evidence`(`qr_serial`, `date`, `evidence`, `employee`, `shift`, `description`) VALUES (?,?,?,?,?,?)";                   
        $stmt = mysqli_prepare($conexionMySQL, $insertQuery);                
        mysqli_stmt_bind_param($stmt, "ssssss", ...$arrJson);
        mysqli_stmt_execute($stmt);                                                        
        mysqli_stmt_close($stmt);            
            
    } catch (\Throwable $th) {
        if ($conexionMySQL) {            
            $connection->cerrarConexionMySQL();
        }
        $response = array("boolean" => false, "msg" => $th->getMessage());                                           
        echo json_encode($response);                          
        exit();
    }

    $connection->cerrarConexionMySQL();
    $response = array("boolean" => true, "msg" => "succes");
    echo json_encode($response);
                                                        
} # end function getJSON         

function saveImage() {    
    date_default_timezone_set('America/Denver');    
    $datenow = date('YmdHis') . "_";      
    $return = "";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tempPath = $_FILES['image']['tmp_name'];
            // $uploadDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
            $uploadDir = "../assets/uploads/";
            $targetPath = $uploadDir . $datenow. $_FILES['image']['name'];  
            if (move_uploaded_file($tempPath, $targetPath)) {
                $return = $datenow. $_FILES['image']['name'];                
            } else {
                $return = "not saved";                
            }
        } else {
            $return = "Error while chargin the image";            
        }
    }        
    return $return;
} # end saveImage

?>