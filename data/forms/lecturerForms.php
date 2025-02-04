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
                        <label class="col-sm-1 col-form-label">NPP</label>
                        <div class="col-sm-10">
                            <div class="row g-3" id="npp">
                                <div class="col-md-1">
                                    <input type="text" class="form-control" id="npp1" name="npp1" value="0686" readonly>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" id="npp2" name="npp2" required>
                                        <?php for ($year = 2020; $year <= 2024; $year++) : ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="npp3" name="npp3" placeholder="00000" required>
                                </div>
                            </div>
                            <span id="nppErr" class="text-warning"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-1 col-form-label">Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="name" name="name" placeholder="Fullname" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-1 col-form-label">Homebase</label>
                        <div class="col-md-2">
                            <select class="form-select" id="homebase" name="homebase" required>
                                <option value="">Select...</option>
                                <option value="A11">A11</option>
                                <option value="A12">A12</option>
                            </select>
                        </div>
                    </div>
                    <div class="col d-flex mb-3">
                        <button class="btn btn-outline-danger me-3" id="cancelButton">Cancel</button>
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
                        <label for="npp" class="col-sm-1 col-form-label">NPP</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control shadow-sm" id="npp" name="npp" value="<?= $_GET['npp'] ?? "redirect" ?>" title="NPP cannot be edited for security reasons, must be re-entry." disabled>
                            <span id="nppErr" class="text-warning"></span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-1 col-form-label">Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control shadow-sm" id="name" name="name" placeholder="<?= $_GET['name'] ?>" value="<?= $_GET['name'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="homebase" class="col-sm-1 col-form-label">Homebase</label>
                        <div class="col-md-2">
                            <select class="form-select" id="homebase" name="homebase" on required>
                                <option value="">Select..</option>
                                <option value="A11">A11</option>
                                <option value="A12">A12</option>
                            </select>
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
        const npp = $('#update, #npp').val();
        if (id, npp == 'redirect') {
            alert('Invalid request!');
            window.location.href = 'mainpage.php?#';
        }

        function nppCheck(npp) {
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=nppCheck',
                data: {
                    npp: npp,
                    table: 'lecturers',
                    column: 'npp'
                },
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons) {
                        $('#nppErr').text('NPP already exists!');
                        $('#submitButton').prop('disabled', true);
                        $('#npp3').focus();
                    } else {
                        $('#nppErr').text('');
                        $('#submitButton').prop('disabled', false);
                    }
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        }

        $('#npp3').on('keyup', function(e) {
            let npp3Length = $(this).val();
            let npp1 = $('#npp1').val();
            let npp2 = $('#npp2').val();
            let npp3 = $('#npp3').val();
            let npp = npp1 + '.' + npp2 + '.' + npp3;
            console.log(npp);
            if (npp3Length.trim() !== ' ' && npp3Length.length == 5) {
                nppCheck(npp);
            } else {
                $('#nppErr').text('Must be 5 digit!');
                $('#submitButton').prop('disabled', true);
                $('#npp3').focus();
            }
        });

        $('#npp1, #npp2').on('change', function(e) {
            let npp1 = $('#npp1').val();
            let npp2 = $('#npp2').val();
            let npp3 = $('#npp3').val();
            let npp = npp1 + '.' + npp2 + '.' + npp3;
            console.log(npp);
            nppCheck(npp);
        });

        $('#insertForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=insertLecturer',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.status == 'success') {
                        alert('Data inserted successfully!');
                        window.location.href = 'mainpage.php?view=lecturers';
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
            let npp = $('#npp').val();
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: 'data/scripts/requestbase.php?req=updateLecturer&npp=' + npp,
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    let respons = JSON.parse(response);
                    if (respons.status == 'success') {
                        alert('Data updated successfully!');
                        window.location.href = 'mainpage.php?view=lecturers&page=' + respons.page;
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