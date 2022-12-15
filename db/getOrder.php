<?php
require_once('../v1/controller/db.php');
$incoming = json_decode(file_get_contents('php://input'));

try {
    $conn = DB::connectReadDB();
    $where = " where id=\"" . $incoming->orderId . "\" ";

    $stmt = "select * from orders " . $where . ";";

    // echo "STMT=" . $stmt . "<BR>";
    $stmtOrder = $conn->query($stmt);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    $stmt = "select * from payments where orderId=" . $incoming->orderId . ";";

    // echo "STMT=" . $stmt . "<BR>";
    $stmtPayments = $conn->query($stmt);
    $payments = $stmtPayments->fetchAll(PDO::FETCH_ASSOC);

    // Close connection
    unset($conn);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
function isDateToday($dateString){
    $today = date('Y-m-d');
    if(strncmp($dateString, $today, 10)){
        return false;
    }else{
        return true;
    }
}
?>
