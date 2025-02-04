<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://cdn-icons-png.freepik.com/512/7021/7021308.png?ga=GA1.1.599436757.1735230785" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

</html>
<div style="font-family: 'Trebuchet MS', sans-serif;">
    <?php
    include 'data/scripts/dbconn.php';
    session_start();
    if (isset($_SESSION['user'])) {
        if ($_SESSION['user']['role'] == "admin") {
            $_SESSION['user']['status'] = 'active';
            include 'data/mainView.php';
        } elseif ($_SESSION['user']['role'] == "dosen" || $_SESSION['user']['role'] == "mahasiswa") {
            $_SESSION['user']['status'] = 'inactive';
            echo '<p>This page still under developement</p><hr>';
        } else {
            $_SESSION['error'] = 'Operation not Premitted [main_page.php]';
            header("location: index.php");
        }
    } else {
        $_SESSION['error'] = 'Operation not Premitted [main_page.php]';
        header("location: index.php");
    }
    ?>
    <div class="fixed-bottom bg-transparent ms-1">
        <div style="color: grey; font-size: 9px;">
            <?php echo "userid: " . SHA1($_SESSION['user']['userid']) ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#logout').on('click', function() {
            if (confirm("Are you sure to Logout?")) {
                $.ajax({
                    url: 'data/scripts/requestbase.php?req=userLogout',
                    success: function(response) {
                        let respons = JSON.parse(response);
                        if (respons.message == 'success') {
                            window.location.href = 'index.php';
                        } else {
                            alert(respons.message);
                        }
                    },
                    error: function() {
                        alert("Server error. [req: userlogt]");
                    }
                });
            }
        })
    })
</script>