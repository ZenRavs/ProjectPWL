<?php
include 'data/scripts/dbconn.php';
if (isset($_SESSION['user'])) {
    $request = $_GET['req'];
    switch ($request) {
        case 'insert':
?>
            <div class="container-fluid mt-3">
                <form id="insertForm" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
                    <input type="hidden" id="page" name="page" value="<?= $_GET['page'] ?? 1 ?>">
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Course Code</label>
                        <div class="col-sm-10">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <select class="form-select" id="courseCode1" name="courseCode1" required>
                                        <option value="A11">A11</option>
                                        <option value="A12">A12</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="numeric" class="form-control" id="courseCode2" name="courseCode2" placeholder="00000" required>
                                </div>
                            </div>
                            <span id="courseCodeErr" class="text-warning"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-2 col-form-label">Course Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="name" name="name" placeholder="Course Name" required>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <label for="cType" class="col-sm-2 col-form-label">Course Type</label>
                        <div class="col-md-2">
                            <select class="form-select" id="cType" name="cType" required>
                                <option value="T">Theory</option>
                                <option value="P">Practice</option>
                                <option value="T/P">Theory/Practice</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <label for="sks" class="col-sm-2 col-form-label">SKS</label>
                        <div class="col-md-2">
                            <select class="form-select" id="sks" name="sks" required>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <label for="smt" class="col-sm-2 col-form-label">Semester</label>
                        <div class="col-md-2">
                            <select class="form-select" id="smt" name="smt" required>
                                <?php for ($smt = 1; $smt <= 8; $smt++) : ?>
                                    <option value="<?= $smt ?>"><?= $smt ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="col-sm-6 justify-content-start mb-3">
                        <button class="btn btn-sm btn-outline-danger" id="cancelButton">Cancel</button>
                        <input class="btn btn-primary" id="submitButton" type="submit" value="Submit"></input>
                    </div>
                </form>
            </div>
        <?php
            break;
        case 'update':
        ?>
            <div class="container-fluid mt-3">
                <form enctype="multipart/form-data" id="updateForm" onkeypress="return event.keyCode != 13;">
                    <input type="hidden" id="page" name="page" value="<?= $_GET['page'] ?? 1 ?>">
                    <input type="hidden" id="id" name="id" value="<?= $_GET['id'] ?? 'redirect' ?>">
                    <div class="mb-3 row">
                        <label for="courseCode" class="col-sm-2 col-form-label">Course Code</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control shadow-sm" id="courseCode" name="courseCode" value="<?= $_GET['cc'] ?? "redirect" ?>" title="courseCode cannot be edited for security reasons, must be re-entry." disabled>
                            <span id="courseCodeErr" class="text-warning"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-2 col-form-label">Course Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="name" name="name" value="<?= $_GET['cname'] ?>" placeholder="<?= $_GET['cname'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="cType" class="col-sm-2 col-form-label">Course Type</label>
                        <div class="col-md-2">
                            <select class="form-select" id="cType" name="cType" required>
                                <?php
                                $selectedOption = $_GET['cType'];
                                $options = array('T' => 'Theory', 'P' => 'Practice', 'T/P' => 'Theory/Practice');
                                foreach ($options as $value => $text) {
                                    $selected = ($value == $selectedOption) ? 'selected' : '';
                                    echo '<option value="' . $value . '" ' . $selected . '>' . $text . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="sks" class="col-sm-2 col-form-label">SKS</label>
                        <div class="col-md-2">
                            <?php
                            $selectedSKS = $_GET['SKS'];
                            ?>
                            <select class="form-select" id="sks" name="sks" required>
                                <option value="2" <?= $selectedSKS == 2 ? 'selected' : '' ?>>2</option>
                                <option value="3" <?= $selectedSKS == 3 ? 'selected' : '' ?>>3</option>
                                <option value="4" <?= $selectedSKS == 4 ? 'selected' : '' ?>>4</option>
                            </select> </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="smt" class="col-sm-2 col-form-label">Semester</label>
                        <div class="col-md-2">
                            <?php
                            $selectedSmt = $_GET['SMT'];
                            ?>
                            <select class="form-select" id="smt" name="smt" required>
                                <?php for ($smt = 1; $smt <= 8; $smt++) : ?>
                                    <option value="<?= $smt ?>" <?= $smt == $selectedSmt ? 'selected' : '' ?>><?= $smt ?></option>
                                <?php endfor; ?>
                            </select> </select>
                        </div>
                    </div>
                    <div class="col-sm-6 justify-content-start mb-3">
                        <button class="btn btn-sm btn-outline-danger" id="cancelButton">Cancel</button>
                        <input class="btn btn-primary" id="submitButton" type="submit" value="Submit"></input>
                    </div>
                </form>
            </div>
<?php
            break;
        default:
            echo '<div class="text-danger text-center">Invalid Argument!</div>';
            break;
    }
} else {
    echo 'Access denied!';
}
?>
<script>
    $(document).ready(function() {
        const id = $('#id').val();
        const courseCode = $('#update, #courseCode').val();
        if (id, courseCode == 'redirect') {
            alert('Invalid request!');
            window.location.href = 'mainpage.php?#';
        }

        function courseCodeCheck(courseCode) {
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=courseCodeCheck',
                data: {
                    courseCode: courseCode,
                    table: 'courses',
                    column: 'code'
                },
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons) {
                        $('#courseCodeErr').text('This courses already exists!');
                        $('#submitButton').prop('disabled', true);
                        $('#courseCode2').focus();
                    } else {
                        $('#courseCodeErr').text('');
                        $('#submitButton').prop('disabled', false);
                    }
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        }

        $('#courseCode2').on('keyup', function(e) {
            let courseCode1 = $('#courseCode1').val();
            let courseCode2 = $(this).val();
            let courseCode = courseCode1 + '.' + courseCode2;
            console.log(courseCode1);
            if (courseCode2.trim() !== ' ' && courseCode2.length == 5) {
                courseCodeCheck(courseCode);
            } else {
                $('#courseCodeErr').text('Must be 5 digit!');
                $('#submitButton').prop('disabled', true);
                $('#courseCode3').focus();
            }
        });

        $('#courseCode1').on('change', function(e) {
            let courseCode1 = $(this).val();
            let courseCode2 = $('#courseCode2').val();
            let courseCode = courseCode1 + '.' + courseCode2;
            console.log(courseCode);
            courseCodeCheck(courseCode);
        });

        $('#insertForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=insertCourse',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.status == 'success') {
                        alert('Data inserted successfully!');
                        window.location.href = 'mainpage.php?view=courses';
                    } else {
                        alert(respons.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error + status + xhr);
                }
            });
        });

        $('#updateForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            let cid = $('#id').val();
            console.log(formData.courseCode);
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=updateCourse&id=' + cid,
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.status == 'success') {
                        alert('Data updated successfully!');
                        window.location.href = 'mainpage.php?view=courses&page=' + respons.page;
                    } else {
                        alert('Error updating data!');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });

        $('#cancelButton').on('click', function(e) {
            e.preventDefault();
            history.back();
        });
    })
</script>