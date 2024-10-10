<?php


require 'zklibrary.php';
$zk = new ZKLibrary('192.168.21.152', 4370);
$zk->connect();
$zk->disableDevice();

$users = $zk->getUser();
$log_kehadiran = $zk->getAttendance();

echo "<pre>";
echo var_dump($log_kehadiran);
echo "</pre>"; 



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbabsen";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  exit();
}

  

foreach($log_kehadiran as $key => $data){

  $sql = "SELECT uid,tanggal FROM tbl_kehadiran where uid='$data[1]' and tanggal='$data[3]' ";


  $result =  $conn->query($sql);
  if ($result->num_rows > 0) {
    mysqli_query($conn, "UPDATE tbl_kehadiran SET uid='$data[1]', state='$data[2]', tanggal='$data[3]' where  uid='$data[1]' and tanggal='$data[3]' ");
  } else {
    mysqli_query($conn, "INSERT INTO tbl_kehadiran (uid, state, tanggal) VALUES ('$data[1]','$data[2]','$data[3]') ");
  }
    
}

$sql = "SELECT * FROM tbl_kehadiran";
$result =  $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      echo "id: " . $row["id_log"]. " - uid: " . $row["uid"]. " - state:  " . $row["state"]. " - tanggal:  " . $row["tanggal"]. "<br>";
    }
  } else {
    echo "0 results";
  }

  foreach($users as $key => $data){

    $sql = "SELECT * FROM tbl_karyawan where uid='$data[0]' and nama='$data[1]' ";
    
    $nama = $conn->real_escape_string($data[1]);
    echo $sql ;

    $result =  $conn->query($sql);
    if ($result->num_rows > 0) {
      mysqli_query($conn, "UPDATE tbl_karyawan SET uid='$data[0]', nama='$nama', role='$data[2]', passuser='$data[3]' where  uid='$data[0]' and nama='$data[1]' ");
    } else {
	
	$sqlinsert = "INSERT INTO tbl_karyawan (uid,nama ,role ,passuser ) VALUES ('$data[0]','$data[1]','$data[2]','$data[3]') ";

	echo $sqlinsert ;
      mysqli_query($conn, "INSERT INTO tbl_karyawan (uid,nama ,role ,passuser) VALUES ('$data[0]','$data[1]','$data[2]','$data[3]') ");
	  
    }  
  }
  

?>

<?php
$conn->close();
$zk->enableDevice();
$zk->disconnect();

?>