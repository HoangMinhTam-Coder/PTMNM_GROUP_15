<?php
    require 'db_conn.php';
    page_protect();

    if (isset($_FILES['image'])) {
        $errors = array();
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];

        if ($file_size > 2097152) {
            $errors[] = 'File size must be excately 2 MB';
        }

        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, "../../uploads/" . $file_name);
            $link = "../../uploads/" . $file_name;
        } else {
            print_r($errors);
        }
    }

if (isset($_POST['p_name']) && isset($_POST['mem_type']) && isset($_POST['total']) && isset($_POST['age']) && isset($_POST['paid'])) {
    function getRandomWord($len = 3)
    {
        $word = array_merge(range('a', 'z'), range('0', '9'));
        shuffle($word);
        return substr(implode($word), 0, $len);
    }
    $mem_type = $_POST['mem_type'];

    $query1 = "select * from mem_types WHERE mem_type_id='$mem_type'";

    $result1 = mysqli_query($con, $query1);

    if (mysqli_affected_rows($con) == 1) {
        while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
            $name_type = $row1['name'];
            $details   = $row1['details'];
            $days      = $row1['days'];
        }
    }

    $proof = $_POST['proof'];

    if (isset($_POST['other_proof'])) {
        $other_proof = $_POST['other_proof'];
    } else {
        $other_proof = " ";
    }

    if (isset($_POST['result'])) {
        $img = $_POST['result'];
    } else {
        $img = " ";
    }

    $new_id    = rtrim($_POST['p_id']);
    $invoice   = substr(time(), 2, 10) . getRandomWord();
    $date      = $_POST['date'];
    $age       = rtrim($_POST['age']);
    $full_name = rtrim($_POST['p_name']);
    $email     = rtrim($_POST['email']);
    $address   = rtrim($_POST['add']);
    $zipcode   = rtrim($_POST['zipcode']);
    $birthdate   = $_POST['birthdate'];
    $contact   = rtrim($_POST['contact']);
    $sex       = rtrim($_POST['sex']);
    $height    = rtrim($_POST['height']);
    $weight    = rtrim($_POST['weight']);
    $nationality    = rtrim($_POST['nationality']);
    $facebookaccount    = rtrim($_POST['facebookaccount']);
    $twitteraccount    = rtrim($_POST['twitteraccount']);
    $contactperson    = rtrim($_POST['contactperson']);
    $previousgym    = rtrim($_POST['previousgym']);
    $yearstraining    = rtrim($_POST['yearstraining']);
    $total     = $_POST['total'];
    $paid      = $_POST['paid'];
    $mod_date  = strtotime($date . "+ $days days");
    $expiry    = date("Y-m-d", $mod_date);
    $wait      = "no";
    $time      = $days * 86400;

    $exp_time = $time + strtotime($date);

    $query_id = "select * from user_data WHERE newid ='$new_id'";

    $result_id = $con->query($query_id);
    if ($result_id->num_rows > 0) {
        echo "<head><script>alert('Profile NOT Added, Id Loop');</script></head></html>";
        echo "<meta http-equiv='refresh' content='0; url=new_entry.php'>";
    } else {
            $query_insert_user  = "INSERT INTO `user_data`(
            `id`, 
            `newid`, 
            `name`, 
            `address`, 
            `zipcode`,
            `birthdate`, 
            `contact`, 
            `email`, 
            `pic_add`, 
            `height`, 
            `weight`, 
            `nationality`, 
            `facebookaccount`, 
            `twitteraccount`, 
            `contactperson`, 
            `previousgym`, 
            `yearstraining`, 
            `joining`, 
            `age`, 
            `sex`, 
            `proof`, 
            `other_proof`) VALUES (
                '',
                '$new_id',
                '$full_name',
                '$address',
                '$zipcode',
                '$birthdate',
                '$contact',
                '$email',
                '$link',
                '$height',
                '$weight',
                '$nationality',
                '$facebookaccount',
                '$twitteraccount',
                '$contactperson',
                '$previousgym',
                '$yearstraining',
                '$date',
                '$age',
                '$sex',
                '$proof',
                '$other_proof');";
            
        if($con->query($query_insert_user) === true) {
            echo "<head><script>alert('User Added ,');</script></head></html>";
        }


        $bal = $total - $paid;

        $query_insert_sub = "INSERT INTO `subsciption`(
            `id`, 
            `mem_id`, 
            `name`, 
            `sub_type`, 
            `paid_date`, 
            `expiry`, 
            `total`, 
            `paid`, 
            `invoice`, 
            `sub_type_name`, 
            `bal`, 
            `exp_time`, 
            `renewal`) VALUES (
                '',
                '$new_id',
                '$full_name',
                '$mem_type',
                '$date',
                '$expiry',
                '$total',
                '$paid',
                '$invoice',
                '$name_type',
                '$bal',
                '$exp_time',
                'yes')";
        if($con->query($query_insert_sub) === true) {
            echo "<head><script>alert('Member Added ,');</script></head></html>";
        }
    }
} else {
    echo "<head><script>alert('Profile NOT Added, Check Again');</script></head></html>";
    echo "<meta http-equiv='refresh' content='0; url=new_entry.php'>";
}
?>
<!doctype html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <script type="text/javascript" src="jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="jquery-barcode.js"></script>
    <script type="text/javascript">
        function generateBarcode() {
            var value = "<?php
                            echo $invoice;
                            ?>";
            var btype = "code128";
            var renderer = "css";

            var quietZone = false;
            if ($("#quietzone").is(':checked') || $("#quietzone").attr('checked')) {
                quietZone = true;
            }

            var settings = {
                output: renderer,
                bgColor: $("#bgColor").val(),
                color: $("#color").val(),

                moduleSize: $("#moduleSize").val(),
                posX: $("#posX").val(),
                posY: $("#posY").val(),
                addQuietZone: $("#quietZoneSize").val()
            };
            if ($("#rectangular").is(':checked') || $("#rectangular").attr('checked')) {
                value = {
                    code: value,
                    rect: true
                };
            }
            if (renderer == 'canvas') {
                clearCanvas();
                $("#barcodeTarget").hide();
                $("#canvasTarget").show().barcode(value, btype, settings);
            } else {
                $("#canvasTarget").hide();
                $("#barcodeTarget").html("").show().barcode(value, btype, settings);
            }
        }

        function showConfig1D() {
            $('.config .barcode1D').show();
            $('.config .barcode2D').hide();
        }

        function showConfig2D() {
            $('.config .barcode1D').hide();
            $('.config .barcode2D').show();
        }

        function clearCanvas() {
            var canvas = $('#canvasTarget').get(0);
            var ctx = canvas.getContext('2d');
            ctx.lineWidth = 1;
            ctx.lineCap = 'butt';
            ctx.fillStyle = '#FFFFFF';
            ctx.strokeStyle = '#000000';
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeRect(0, 0, canvas.width, canvas.height);
        }

        $(function() {
            $('input[name=btype]').click(function() {
                if ($(this).attr('id') == 'datamatrix') showConfig2D();
                else showConfig1D();
            });
            $('input[name=renderer]').click(function() {
                if ($(this).attr('id') == 'canvas') $('#miscCanvas').show();
                else $('#miscCanvas').hide();
            });
            generateBarcode();
        });
    </script>
</head>

<body>
    <header>
        <a href="new_entry.php">
            <h1>Invoice (New Registration)</h1>
        </a>
        <address>
            <p></p>
            <p>California Gym</p>
            <p>Landline: +84 586 001</p>
            <p>Tầng 4 Nha Trang Center, 20 trần phú Nha Trang Khánh Hòa</p><br>
            <p>
            <div id="barcodeTarget" class="barcodeTarget"></div>
            <canvas id="canvasTarget"></canvas> </span>
        </address>
        <span><img alt="" src="../../img/bg.png" width="200">
    </header>
    <article>
        <table class="meta">
            <img alt="" src="pic1.jpg" width="100" height="100">
            <tr>
                <th><span>Invoice #</span></th>
                <td><span><?php
                            echo $invoice;
                            ?></span></td>
            </tr>
            <tr>
                <th><span>Date</span></th>
                <td><span><?php
                            echo $date;
                            ?></span></td>
            </tr>
            <tr>
                <th><span>Member ID / Reg ID</span></th>


                <td><?php
                    $regid = substr($new_id, 6, 10);
                    echo $new_id . " / " . $regid;
                    ?></span></td>
            </tr>
        </table>
        <table class="meta">
            <tr>
                <th><span>Name</span></th>
                <td><span><?php
                            echo $full_name;
                            ?></span></td>
            </tr>
            <tr>
                <th><span>Age, Sex</span></th>
                <td><span><?php
                            echo $age . " / " . $sex;
                            ?></span></td>
            </tr>
            <tr>
                <th><span>Height / Weight</span></th>
                <td><?php
                    echo $height . "  FEET / " . $weight . " Kg";
                    ?></span></td>
            </tr>
        </table>
        <table class="inventory">
            <thead>
                <tr>
                    <th><span>Membership Type</span></th>
                    <th><span>Details</span></th>
                    <th><span>Subscription Expiry</span></th>

                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><span><?php
                                echo $name_type;
                                ?></span></td>
                    <td><span><?php
                                echo $details . " For " . $days;
                                ?></span></td>
                    <td><span><?php
                                echo $expiry;
                                ?></span></td>
                </tr>
            </tbody>
        </table>



        <table class="balance">
            <tr>
                <th><span>Total</span></th>
                <td><span data-prefix></span><span><?php
                                                        echo $total;
                                                        ?></span></td>
            </tr>
            <tr>
                <th><span>Paid</span></th>
                <td><span data-prefix></span><span><?php
                                                        echo $paid;
                                                        ?></span></td>
            </tr>
            <tr>
                <th><span>Due</span></th>
                <td><span data-prefix></span><span><?php
                                                        echo $total - $paid;
                                                        ?></span></td>
            </tr>

        </table>
    </article>
    <center><br><br><a href="view_mem.php">California Gym</center>
</body>

</html>