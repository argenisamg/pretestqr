<?php		
include_once("ConnectionDB.php");	

    $response = array();
    $arrayFromDb = array(); 
    if ($_SERVER["REQUEST_METHOD"] === "POST") {      
        // $data = json_decode(file_get_contents('php://input')); 
        // $id = $data->id;        
        // if ($id > 0) {                          
        consultApproved();
        // } else {
        //     $response = array("boolean" => false, "msg" => "The param is empty.");                                           
        //     echo json_encode($response);                          
        //     exit();
        // }
    } else {
        $response = array("boolean" => false, "msg" => "Empty POST request.");                                           
        echo json_encode($response);                          
        exit();
    }

function consultApproved() {
    try {
        #Create an instance of ConnectionDB:
        $connection = new ConnectionDB();
        $conexionMySQL = $connection->conectarMySQL();    
        $selectQuery= "SELECT * FROM `pretest_evidence`";
        $stmt = mysqli_prepare($conexionMySQL, $selectQuery);                                                
        mysqli_stmt_execute($stmt);
        $executeQuery = mysqli_stmt_get_result($stmt);
                
        if (mysqli_num_rows($executeQuery) > 0) {
            $rowCount = 0;
            while ($row = mysqli_fetch_array($executeQuery)) {       
                $rowCount++;                                                                                                                                   
                $arrayFromDb[] = array(                                
                    'row' => $rowCount,
                    'id' => $row['id'],
                    'qr' => $row['qr_serial'],
                    'date' => $row['date'],
                    'evidence' => './assets/uploads/' . $row['evidence'],                                 
                    'employee' => $row['employee'],
                    'shift' => $row['shift'],
                    'description' => $row['description']                                                                   
                );
                // 'evidence' => $row['evidence'],       
            } #end while                                     

            // $response = array("boolean" => true, "msg" => "success", "data" => $arrayFromDb); 
            $connection->cerrarConexionMySQL();                                                                   
            echo json_encode($arrayFromDb);
            exit();                                 
        } else {
            //$response = array("boolean" => false, "msg" => "Empty data.");  
            $connection->cerrarConexionMySQL();                                                                  
            echo json_encode($arrayFromDb);
            exit();
        } 
    } catch (\Throwable $th) {
        $response = array("boolean" => false, "msg" => $th->getMessage());                                                                            
        echo json_encode($response);
        exit();
    }                             
} # end consultApproved      	                                    	    
 ?>