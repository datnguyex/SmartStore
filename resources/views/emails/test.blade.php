<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .text-email {
            width: 500px;
            margin: 0 auto;
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="text-email">
    <?php
    if(session()->has('nameCustomerUser')){
        $name = session()->get('nameCustomerUser');
    }
    else if(session()->has('nameSeller')){
        $name = session()->get('nameSeller');
    }
    else if(session()->has('nameAdmin')){
        $name = session()->get('nameAdmin');
    }
    ?>
    <h2>Xin Chào <?= $name ?></h2>
    <br>
    <p>
        <?php 
        $codeResetPassword = session()->get("codeResetPassword");
        echo $codeResetPassword; ?> là mã xác minh của bạn;
    </p>
</div>

</body>
</html>