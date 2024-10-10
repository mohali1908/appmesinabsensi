<?php


$servername_hosting = "srv158.niagahoster.com";
$username_hosting = "u1795453_ali";
$password_hosting = "Apbatech57!";
$dbname_hosting = "u1795453_dbabsenapp";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbabsen";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn_hosting = new mysqli($servername_hosting, $username_hosting, $password_hosting, $dbname_hosting);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  exit();
}

  
//Insert atau Update data karyawan

$sql_select = "SELECT *,CURRENT_TIMESTAMP() AS created_at FROM tbl_karyawan";
$result = $conn->query($sql_select);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['uid'];
        $nama = $conn->real_escape_string($row['nama']); 
        $position_id = 1;
        $role_id = 3;
        $email = $row['nama']."@apbatech.com" ;
        $password = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi";
        $created_at =  $row['created_at'];

        // Check if the record already exists in tbl_karyawan
        $sql_check = "SELECT * FROM users WHERE id = '$user_id'";
        $check_result = $conn_hosting->query($sql_check);

        echo $sql_check ;

        if ($check_result->num_rows > 0) {
            // Update the existing record
            $sql_update = "
                UPDATE users SET
                    name = '$nama'
                WHERE
                    id = '$user_id'
            ";
            if ($conn_hosting->query($sql_update) === TRUE) {
                echo "Record updated successfully for id $user_id.<br>";
            } else {
                echo "Error updating record for user id $user_id: " . $conn->error . "<br>";
            }
        } else {
            // Insert a new record
            $sql_insert = "
                INSERT INTO users (id, name, email, password,created_at,updated_at)
                VALUES ('$user_id', '$nama','$email', '$password','$created_at','$created_at' )
            ";

            echo " $sql_insert";
            if ($conn_hosting->query($sql_insert) === TRUE) {
                echo "New record created successfully for id $id.<br>";
            } else {
                echo "Error inserting record for id $id: " . $conn->error . "<br>";
            }
        }
    }
} else {
    echo "0 results from tbl_karyawan.";
}



?>

<?php
$conn->close();


?>
