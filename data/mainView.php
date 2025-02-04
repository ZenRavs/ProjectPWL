<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        a,
        h2,
        hr,
        label {
            color: rgba(255, 255, 255, 0.72);
        }

        select {
            color: white;
            background-color: #000000;
        }
    </style>
</head>

<body>
    <div>
        <div class="row g-0">
            <div class="col-md-2">
                <div class="container-fluid shadow sticky-top pt-2" id="sidebar" style="height: 100vh; background-color:rgb(18, 30, 49);">
                    <div class="card shadow bg-light align-middle">
                        <a class="link-underline link-underline-opacity-0" href="?">
                            <div class="card-body p-2">
                                <div class="row g-3">
                                    <div class="col-sm-3">
                                        <img src="data/uploads/userpict/<?= $_SESSION['user']['pict'] ?>" class="rounded-circle border border-1 object-fit-cover shadow bg-primary" alt="<?= $_SESSION['user']['pict'] ?>" width="50" height="50">
                                    </div>
                                    <div class="col">
                                        <div class="text-secondary">
                                            Welcome!
                                        </div>
                                        <hr class="hr border border-secondary m-0">
                                        <div class="text-dark">
                                            <?= $_SESSION['user']['name'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <nav class="navbar navbar-dark" id="navbar">
                        <div class="container-fluid">
                            <ul class="navbar-nav nav-hover" id="sideBarOption" style="width: 100%;">
                                <li class="nav-item">
                                    <a class="nav-link" href="?view=dashboard">Dashboard</a>
                                </li>
                                <hr class="hr border-light m-0">
                                <li class="nav-item">
                                    <a class="nav-link" href="?view=students">Students</a>
                                </li>
                                <hr class="hr border-light m-0">
                                <li class="nav-item">
                                    <a class="nav-link" href="?view=lecturers">Lecturers</a>
                                </li>
                                <hr class="hr border-light m-0">

                                <li class="nav-item">
                                    <a class="nav-link" href="?view=courses">Course</a>
                                </li>
                                <hr class="hr border-light m-0">
                                <li class="nav-item">
                                    <a class="nav-link" href="?view=krs-offers">KRS Offers</a>
                                </li>
                                <hr class="hr border-light m-0">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Insert
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="?view=insert-student&req=insert">+ Student</a></li>
                                        <li><a class="dropdown-item" href="?view=insert-lecturer&req=insert">+ Lecturer</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="?view=insert-course&req=insert">+ Course</a></li>
                                        <li><a class="dropdown-item" href="?view=offering-krs&req=insert">+ Offering KRS</a></li>
                                    </ul>
                                </li>
                                <hr class="hr border-light m-0">
                                <li>
                                    <a class="nav-link text-danger" id="logout" href="#">Logout</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="col">
                <div class="container-fluid overflow-y-auto" id="main_content" style="background-color:rgb(22, 27, 37);height: 100vh; padding: 35px 10px 5px 10px">
                    <?php
                    $_SESSION['user']['status'] = 'active';
                    $view = $_GET['view'] ?? 'dashboard';
                    switch ($view) {
                        case 'dashboard':
                            echo "<h2 class='ms-2'><b>Dashboard</b></h2>";
                            echo "<hr>";
                            include 'welcomeView.html';
                            echo '<title>Data FIK</title>';
                            echo "<hr>";
                            break;
                        case 'students':
                            echo "<h2 class='ms-2'><b>STUDENTs Data</b></h2>";
                            echo '<title>Data Mahasiswa FIK</title>';
                            echo "<hr>";
                            include 'studentsView.php';
                            break;
                        case 'lecturers':
                            echo "<h2 class='ms-2'><b>LECTURERs Data</b></h2>";
                            echo '<title>Data Dosen FIK</title>';
                            echo "<hr>";
                            include 'lecturersView.php';
                            break;
                        case 'courses':
                            echo '<h2 class="ms-2"><b>COURSEs DATA</b></h2>';
                            echo '<title>Data Matkul FIK</title>';
                            echo "<hr>";
                            include 'coursesView.php';
                            break;
                        case 'krs-offers':
                            echo '<h2 class="ms-2"><b>OFFERED KRS</b></h2>';
                            echo "<hr>";
                            include 'krsOffersView.php';
                            break;
                        case 'insert-student':
                            echo '<h2 class="ms-2"><b>Add New STUDENT Data</b></h2>';
                            echo "<hr>";
                            include 'forms/studentForms.php';
                            echo "<hr>";
                            break;
                        case 'insert-lecturer':
                            echo '<h2 class="ms-2"><b>Add New LECTURER Data</b></h2>';
                            echo "<hr>";
                            include 'forms/lecturerForms.php';
                            echo "<hr>";
                            break;
                        case 'insert-course':
                            echo '<h2 class="ms-2"><b>Add New COURSE</b></h2>';
                            echo "<hr>";
                            include 'forms/courseForm.php';
                            echo "<hr>";
                            break;
                        case 'offering-krs':
                            echo '<h2 class="ms-2"><b>Add New OFFER</b></h2>';
                            echo "<hr>";
                            include 'forms/krsOfferForm.php';
                            echo "<hr>";
                            break;
                        case 'edit-student':
                            echo '<h2 class="ms-2"><b>Update STUDENT Data</b></h2>';
                            echo "<hr>";
                            include 'forms/studentForms.php';
                            echo "<hr>";
                            break;
                        case 'edit-lecturer':
                            echo '<h2 class="ms-2"><b>Update LECTURER Data</b></h2>';
                            echo "<hr>";
                            include 'forms/lecturerForms.php';
                            echo "<hr>";
                            break;
                        case 'edit-course':
                            echo '<h2 class="ms-2"><b>Update COURSE Data</b></h2>';
                            echo "<hr>";
                            include 'forms/courseForm.php';
                            echo "<hr>";
                            break;
                        case 'edit-krs-offer':
                            echo '<h2 class="ms-2"><b>Update OFFER Data</b></h2>';
                            echo "<hr>";
                            include 'forms/krsOfferForm.php';
                            echo "<hr>";
                            break;
                        default:
                            echo '<h2 class="text-center">404 Not Found!</h2>';
                            echo '<title>Data FIK</title>';
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
</script>

</html>