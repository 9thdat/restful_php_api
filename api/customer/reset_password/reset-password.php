<?php
include_once("../../../config/db_azure.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');


$db = new db();
$conn = $db->connect();

$token = $_GET["token"];

$token_hash = hash("sha256", $token);


$query = "SELECT EMAIL, RESET_TOKEN_EXPIRES_AT FROM customer
        WHERE reset_token_hash = ?";

$stmt = $conn->prepare($query);

$stmt->bindParam(1, $token_hash);

$stmt->execute();

$num = $stmt->rowCount();
if ($num>0){
    foreach($stmt as $row){
        extract($row);
        echo $RESET_TOKEN_EXPIRES_AT;
        if (strtotime($RESET_TOKEN_EXPIRES_AT) <= time()) {
            die("token has expired");
        }
    }
}else{
    die("token not found");
}

// $result = $stmt->get_result();

// $user = $result->fetch_assoc();

// if ($user === null) {
//     die("token not found");
// }

// if (strtotime($user["reset_token_expires_at"]) <= time()) {
//     die("token has expired");
// }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <h1>Reset Password</h1>

    <form method="post" action="process-reset-password.php">

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" id="password" name="password">

        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation"
               name="password_confirmation">

        <button>Send</button>
    </form>

</body>
</html>