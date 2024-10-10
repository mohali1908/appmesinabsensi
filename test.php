<?php

require 'zklibrary.php';

$zk = new ZKLibrary('192.168.21.152', 4370);
$zk->connect();
$zk->disableDevice();

$users = $zk->getUser();
$log_kehadiran = $zk->getAttendance();

echo "<pre>";
echo var_dump($users);
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

  

foreach($users as $key => $data){

  $sql = "SELECT uid,tanggal FROM tbl_karyawan where uid='$data[0]' and nama='$data[1]' ";
  $result =  $conn->query($sql);
  if ($result->num_rows > 0) {
    mysqli_query($conn, "UPDATE tbl_karyawan SET uid='$data[0]', nama='$data[1]', role='$data[2]', passuser='$data[3]' where  uid='$data[0]' and nama='$data[1]' ");
  } else {
    echo "id: " . $row["uid"]. " - role: " . $row["role"]. " - passuser:  " . $row["passuser"]. "Insert karyawan <br>";
    mysqli_query($conn, "INSERT INTO tbl_karyawan (uid,nama ,role ,passuser ) VALUES ('$data[0]','$data[1]','$data[2]','$data[3]') ");
  }  
}

$sql = "SELECT * FROM tbl_karyawan";
$result =  $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      echo "id: " . $row["uid"]. " - role: " . $row["role"]. " - passuser:  " . $row["passuser"]. "<br>";
    }
  } else {
    echo "0 results";
  }
?>


?>
<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
<thead>
  <tr>
    <td width="25">No</td>
    <td>UID</td>
    <td>ID</td>
    <td>Name</td>
    <td>Role</td>
    <td>Password</td>
  </tr>
</thead>
<tbody>
<?php
$no = 0;
foreach($users as $key => $user)
{
  $no++;
  ?>
  <tr>
    <td align="right"><?php echo $no; ?></td>
    <td><?php echo $key; ?></td>
    <td><?php echo $user[0]; ?></td>
    <td><?php echo $user[1]; ?></td>
    <td><?php echo $user[2]; ?></td>
    <td><?php echo $user[3]; ?></td>
  </tr>
  <?php
}
?>
</tbody>
</table>
<?php

$zk->enableDevice();
$zk->disconnect();

?>
