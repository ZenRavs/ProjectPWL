<?php
include 'data/scripts/dbconn.php';
if (isset($_SESSION['user'])) {
    $request = $_GET['req'];
    switch ($request) {
        case 'insert':
?>
            <div class="container-fluid mt-3">
                <form method="POST" id="insertForm" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
                    <input type="hidden" id="page" name="page" value="<?= $_GET['page'] ?? 1 ?>">
                    <div class="mb-3 row">
                        <label class="col-sm-1 col-form-label">NIM</label>
                        <div class="col-sm-10">
                            <div class="row g-3" id="nim">
                                <div class="col-md-2">
                                    <select class="form-select" id="nim1" name="nim1" required>
                                        <option value="A11">A11</option>
                                        <option value="A12">A12</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" id="nim2" name="nim2" required>
                                        <?php for ($year = 2020; $year <= 2024; $year++) : ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="nim3" name="nim3" placeholder="00000" required>
                                </div>
                            </div>
                            <span id="nimErr" class="text-warning"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-1 col-form-label">Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="name" name="name" placeholder="Fullname" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-1 col-form-label">Email</label>
                        <div class="col-sm-6">
                            <input type="email" class="form-control shadow-sm" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="pict" class="col-sm-1 col-form-label">Photo</label>
                        <div class="col-sm-6">
                            <input class="form-control shadow-sm" type="file" name="pict" id="pict" required>
                            <span id="fileErr" class="text-danger"></span>
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
        case 'update':
        ?>
            <div class="container-fluid mt-3">
                <form method="POST" enctype="multipart/form-data" id="updateForm" onkeypress="return event.keyCode != 13;">
                    <input type="hidden" id="page" name="page" value="<?= $_GET['page'] ?? 1 ?>">
                    <input type="hidden" id="id" name="id" value="<?= $_GET['id'] ?? 'redirect' ?>">
                    <div class="mb-3 row">
                        <label for="nim" class="col-sm-1 col-form-label">NIM</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="nim" name="nim" value="<?= $_GET['nim'] ?>" title="NIM cannot be edited for security reasons, must be re-entry." disabled>
                            <span id="nimErr" class="text-warning"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-1 col-form-label">Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="name" name="name" placeholder="<?= $_GET['name'] ?>" value="<?= $_GET['name'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-1 col-form-label">Email</label>
                        <div class="col-sm-6">
                            <input type="email" class="form-control shadow-sm" id="email" name="email" placeholder="<?= $_GET['email'] ?>" value="<?= $_GET['email'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="pict" class="col-sm-1 col-form-label">Photo</label>
                        <div class="col-sm-6">
                            <input class="form-control shadow-sm" type="file" name="pict" id="pict">
                            <span id="fileErr" class="text-danger"></span>
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
        if (id == 'redirect') {
            alert('Invalid request!');
            window.location.href = 'mainpage.php?#';
        }

        var nimtest = $('#nim').val();
        console.log(nimtest);


        function nimCheck(nim) {
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=nimCheck',
                data: {
                    nim: nim
                },
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.message == 'true') {
                        $('#nimErr').text('NIM already exists!');
                        $('#submitButton').prop('disabled', true);
                        $('#nim3').focus();
                    } else {
                        $('#nimErr').text('');
                        $('#submitButton').prop('disabled', false);
                    }
                    console.log(respons);
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        }

        $('#nim3').on('keyup', function(e) {
            let nim3Length = $(this).val();
            let nim1 = $('#nim1').val();
            let nim2 = $('#nim2').val();
            let nim3 = $('#nim3').val();
            let nim = nim1 + '.' + nim2 + '.' + nim3;
            console.log(nim + ':Nim | Length: ' + nim3Length.length == 5);
            if (nim3Length.trim() !== ' ' && nim3Length.length == 5) {
                nimCheck(nim);
            } else {
                $('#nimErr').text('Must be 5 digit!');
                $('#submitButton').prop('disabled', true);
                $('#nim3').focus();
            }
        });

        $('#insertForm').on('keyup', '#name', function() {
            let email = $(this).val() + '@';
            let newEmail = email.replace(/\s/g, '.');
            $('#email').val(newEmail);
        })

        $('#nim1, #nim2').on('change', function(e) {
            let nim1 = $('#nim1').val();
            let nim2 = $('#nim2').val();
            let nim3 = $('#nim3').val();
            let nim = nim1 + '.' + nim2 + '.' + nim3;
            console.log(nim);
            nimCheck(nim);
        });

        $('#insertForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=insertStudent',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.message == 'success') {
                        alert('Data inserted successfully!');
                        window.location.href = 'mainpage.php?view=students';
                    } else {
                        alert('Error inserting data!');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });

        $('#updateForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            let nim = $('#nim').val();
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=updateStudent&nim=' + nim,
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.status == 'success') {
                        alert('Data updated successfully!');
                        window.location.href = 'mainpage.php?view=students&page=' + respons.page;
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