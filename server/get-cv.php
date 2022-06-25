<?php
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

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $res_obj = array();

    $my_data = json_decode($_POST["my_data"]);
    if (empty($my_data[0]) or empty($my_data[1])) returnResponse(400, array("Error: Empty fields for credentials are not allowed!"), "", false);

    $db = new mysqli("localhost", "root", "", "user_data");
    if ($db->connect_errno) returnResponse(400, array("Error: Failed to connect to MySQL: ") . $db->connect_error, "", false);
    else {
        $get_user = "SELECT `Id` FROM personal_data WHERE username='$my_data[0]' AND password='$my_data[1]'";

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
} else {
    echo "GET: Invalid Request. Unsupported Request!" . "\n";
}
