<?php		
include_once("ConnectionDB.php");	
     
    if ($_SERVER["REQUEST_METHOD"] === "GET") {       
        $api = new ConsultPretest();                             
        $data = $api->consultApproved();                
        echo json_encode($data);            
        exit();              
    } else {
        $response = array("boolean" => false, "msg" => "Empty POST request.");                                           
        echo json_encode($response);                          
        exit();
    }

class ConsultPretest {
    private $response = array();

    function consultApproved() {
        $arrayFromDb = array();        
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
                $this->response = $arrayFromDb;
                //exit();                                 
            } else {
                //$response = array("boolean" => false, "msg" => "Empty data.");  
                $connection->cerrarConexionMySQL();                                                                  
                $arrayFromDb = [];
                echo json_encode($arrayFromDb);
                exit();
            } 
        } catch (\Throwable $th) {
            $this->response = array("boolean" => false, "msg" => $th->getMessage());                                                                            
            return $this->response;            
        }                          
        
        return $this->response;
    } # end consultApproved      	                                    	    

} # end Class


 ?>