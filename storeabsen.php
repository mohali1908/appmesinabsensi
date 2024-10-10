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

  
$absensql = "
SELECT 
    tbl_karyawan.uid,
    1 AS attendance_id,
    DATE(MIN(b.tanggal)) AS tglmasuk,
    TIME(MIN(b.tanggal)) AS jammasuk,
    IF(CAST(MAX(b.tanggal) AS TIME) > CAST('14:01:00' AS TIME), DATE(MAX(b.tanggal)), NULL) AS tglpulang,
    IF(CAST(MAX(b.tanggal) AS TIME) > CAST('14:01:00' AS TIME), TIME(MAX(b.tanggal)), NULL) AS jampulang,
    CURRENT_TIMESTAMP() AS created_at
FROM 
    tbl_karyawan
LEFT JOIN 
    (SELECT uid, tanggal FROM tbl_kehadiran WHERE DATE_FORMAT(tanggal, '%Y-%m-%d') = CURDATE()) b
ON 
    b.uid = tbl_karyawan.uid 
WHERE 
    b.tanggal IS NOT null
GROUP BY 
    tbl_karyawan.uid
ORDER BY 
    tbl_karyawan.nama
";

$result = $conn->query($absensql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $uid = $row["uid"];
        $attendance_id = $row["attendance_id"];
        $tglmasuk = $row["tglmasuk"];
        $jammasuk = $row["jammasuk"] !== NULL ? "'" . $row["jammasuk"] . "'" : "NULL";
        $tglpulang = $row["tglpulang"] !== NULL ? "'" . $row["tglpulang"] . "'" : "NULL";
        $jampulang = $row["jampulang"] !== NULL ? "'" . $row["jampulang"] . "'" : "NULL";
        $presenceenterfrom = $row["jampulang"] !== NULL ? "'1'" : "NULL";
        $created_at = $row["created_at"];


         // Check if the record already exists
         $sql_check = "SELECT * FROM presences WHERE user_id = '$uid' AND attendance_id = '$attendance_id' AND presence_date ='$tglmasuk'";
         //echo $sql_check ;
         
         $check_result = $conn_hosting->query($sql_check);
        
         if ($check_result->num_rows > 0) {
            // Update the existing record
           
            $sql_update = "
                UPDATE presences SET
                    presence_date = '$tglmasuk',
                    presence_enter_time = $jammasuk,
                    presence_out_time = $jampulang,
                    presence_from = 1,
                    presence_enter_from = 1,
                    presence_out_from = $presenceenterfrom,
                    updated_at = '$created_at'
                WHERE
                    user_id = '$uid' AND attendance_id = '$attendance_id' AND  presence_date = '$tglmasuk' 
            ";

            //echo "$sql_update </br>";

            
            $conn_hosting->query($sql_update);
        } else {
            // Insert a new record

            
            $sql_insert = "
                INSERT INTO presences (user_id, attendance_id, presence_date, presence_enter_time, presence_out_time,presence_from,presence_enter_from,presence_out_from, created_at, updated_at)
                VALUES ('$uid', '$attendance_id', '$tglmasuk',$jammasuk,$jampulang,1,1,$presenceenterfrom,'$created_at','$created_at')
            ";

            //echo $sql_insert;

            $conn_hosting->query($sql_insert);
        }
    }
    echo "Insert dan update Data Absen  sukses. <br>";
} else {
    echo "0 results";
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
