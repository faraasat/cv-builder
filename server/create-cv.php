<?php
function createTables($db)
{
    $pd_table = "CREATE TABLE IF NOT EXISTS `personal_data` (
        `Id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(30) NOT NULL,
        `password` varchar(25) NOT NULL,
        `full_name` varchar(30) NOT NULL,
        `phone` varchar(15) NOT NULL,
        `email` varchar(35) NOT NULL,
        `gender` varchar(6) NOT NULL,
        `dob` date NOT NULL,
        `address` varchar(50) NOT NULL,
        `img_url` varchar(65) NOT NULL,
        PRIMARY KEY (`Id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($db, $pd_table);
    $wd_table = "CREATE TABLE IF NOT EXISTS `work_details` (
        `Id` int(11) NOT NULL AUTO_INCREMENT,
        `comp_name` varchar(25) NOT NULL,
        `curr_desig` varchar(15) NOT NULL,
        `tot_exp` int(11) NOT NULL,
        `det_exp` text NOT NULL,
        `p_id` int(11) NOT NULL,
        PRIMARY KEY (`Id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($db, $wd_table);
    $ed_table = "CREATE TABLE IF NOT EXISTS `edu_details` (
        `Id` int(11) NOT NULL AUTO_INCREMENT,
        `deg_pro` varchar(35) NOT NULL,
        `deg_year` int(11) NOT NULL,
        `obt_marks` int(11) NOT NULL,
        `p_id` int(11) NOT NULL,
        PRIMARY KEY (`Id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($db, $ed_table);
}

function checkFile()
{
    $fileName = $_FILES['file']['name'];
    $fileSize = $_FILES['file']['size'];
    $tmp = explode('.', $fileName);
    $fileExtension = strtolower(end($tmp));

    if (!in_array($fileExtension, ['jpeg', 'jpg', 'png'])) returnResponse(400, array("Error: This file extension is not allowed. Please upload a JPEG or PNG file!"), "", false);

    if ($fileSize > 4000000) returnResponse(400, array("Error: File exceeds maximum size (4MB)"), "", false);
}

function uploadFile()
{
    if (empty(checkFile())) {
        $fileName = $_FILES['file']['name'];
        $uploadDirectory = DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
        $fileTmpName  = $_FILES['file']['tmp_name'];

        if (!is_dir(getcwd() . $uploadDirectory)) mkdir(getcwd() . $uploadDirectory, 0770);

        $uploadPath = getcwd() . $uploadDirectory . basename($fileName);
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if (!$didUpload) returnResponse(500, array("Error: Please contact the administrator!"), "", false);
    }
}

function returnResponse($statusCode, $error, $result, $success)
{
    header('content-type: application/json',  true, $statusCode);
    echo json_encode(array(
        "result" => empty($error) ? $result : "",
        "error" => empty($error) ? "" : $error,
        "success" => $success
    ), true);
    exit();
}

function returnResult($db, $username, $password)
{
    $get_user = "SELECT `Id` FROM personal_data WHERE username='$username' AND password='$password'";

    $res = mysqli_query($db, $get_user);
    if (!$res) returnResponse(400, array("Error: Failed to run Query: " . $db->connect_error), "", false);

    $res1 = mysqli_fetch_assoc($res);
    if (!$res1) returnResponse(404, array("Error: Invalid username/password"), "", false);

    $result = $res1["Id"];
    $p_data = "SELECT `full_name`, `phone`, `email`, `gender`, `dob`, `address`, `img_url` FROM `personal_data` WHERE `id`='$result'";
    $wd_data = "SELECT `comp_name`, `curr_desig`, `tot_exp`, `det_exp` FROM `work_details` WHERE `p_id`='$result'";
    $ed_data = "SELECT `deg_pro`, `deg_year`, `obt_marks` FROM `edu_details` WHERE `p_id`='$result'";

    $p_data_query = mysqli_query($db, $p_data);
    $p_data_res = mysqli_fetch_array($p_data_query);

    $wd_data_res = array();
    $wd_data_query = mysqli_query($db, $wd_data);
    while ($row = mysqli_fetch_array($wd_data_query)) $wd_data_res[] = $row;

    $ed_data_res = array();
    $ed_data_query = mysqli_query($db, $ed_data);
    while ($row = mysqli_fetch_array($ed_data_query)) $ed_data_res[] = $row;

    $res_obj = array($p_data_res, $wd_data_res, $ed_data_res);
    returnResponse(200, array(), $res_obj, true);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $uploadPath = str_replace("\\", "\\\\", DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . basename($_FILES['file']['name']));
    // $file_tmp = $_FILES['file']['tmp_name'];
    // $imagedata = file_get_contents($file_tmp);
    // $tmp = explode('.', $_FILES['file']['name']);
    // $fileExtension = strtolower(end($tmp));
    // $base64 = 'data:image/' . $fileExtension . ';base64,' . base64_encode($imagedata);

    $my_data = json_decode($_POST["my_data"]);
    $personalDetails = $my_data->personalDetails;
    $workDetails = $my_data->workDetails;
    $eduDetails = $my_data->eduDetails;
    $credDetails = $my_data->credDetails;

    if (empty($credDetails[0]) or empty($credDetails[1])) returnResponse(400, array("Error: No Empty value accepted for 'Credential's field'!"), "", false);

    if (empty($personalDetails[0]) or empty($personalDetails[1]) or empty($personalDetails[2]) or empty($personalDetails[3]) or empty($personalDetails[4]) or empty($personalDetails[5])) returnResponse(400, array("Error: No Empty value accepted for 'Personal Details'"), "", false);

    foreach ($workDetails as $value) {
        if (empty($value[0]) or empty($value[1]) or empty($value[2]) or empty($value[3])) returnResponse(400, array("Error: No Empty Values Allowed for 'Work Details'"), "", false);
    }

    foreach ($eduDetails as $value) {
        if (empty($value[0]) or empty($value[1]) or empty($value[2])) returnResponse(400, array("Error: No Empty Values Allowed for 'Education Details'"), "", false);
    }

    uploadFile();

    $db = new mysqli("localhost", "root", "");
    if ($db->connect_errno) returnResponse(500, array("Error: Failed to connect to MySQL: " . $db->connect_error), "", false);

    $db_selected = mysqli_select_db($db, "user_data");
    if (!$db_selected) {
        $sql = 'CREATE DATABASE user_data';
        if (!mysqli_query($db, $sql)) returnResponse(500, array("Error: creating database: " . mysqli_error($db)), "", false);
    }

    createTables($db);

    $record_id = 0;
    $pd_table_insert = "INSERT INTO `personal_data`(`username`, `password`, `full_name`, `phone`, `email`, `gender`, `dob`, `address`, `img_url`) VALUES ('$credDetails[0]','$credDetails[1]','$personalDetails[0]','$personalDetails[1]','$personalDetails[2]','$personalDetails[3]','$personalDetails[4]','$personalDetails[5]','$uploadPath')";

    if (mysqli_query($db, $pd_table_insert)) $record_id = mysqli_insert_id($db);
    else returnResponse(500, array("Error: " . $sql . "<br>" . mysqli_error($conn)), "", false);

    foreach ($workDetails as $value) {
        $wd_table_insert = "INSERT INTO `work_details`(`comp_name`, `curr_desig`, `tot_exp`, `det_exp`, `p_id`) VALUES ('$value[0]','$value[1]','$value[2]','$value[3]','$record_id')";
        if (!mysqli_query($db, $wd_table_insert)) returnResponse(500, array("Error: " . $sql . "<br>" . mysqli_error($conn)), "", false);
    }
    foreach ($eduDetails as $value) {
        $ed_table_insert = "INSERT INTO `edu_details`(`deg_pro`, `deg_year`, `obt_marks`, `p_id`) VALUES ('$value[0]','$value[1]','$value[2]','$record_id')";
        if (!mysqli_query($db, $ed_table_insert)) returnResponse(500, array("Error: " . $sql . "<br>" . mysqli_error($conn)), "", false);
    }

    returnResult($db, $credDetails[0], $credDetails[1]);
} else echo "GET: Invalid Request. Unsupported Request!" . "\n";
