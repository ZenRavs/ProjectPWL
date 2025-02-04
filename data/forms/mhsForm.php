<?php
include 'scripts/dbconn.php';
if (isset($_SESSION['user']) && $_GET['req']) {
?>
    <div class="container main_form">
        <?php if ($_GET['req'] == 'mhs-registration') : ?>
            <form class="mt-3" id="insertForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
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
                                        <?php for ($year = 2010; $year <= 2024; $year++) : ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="nim3" name="nim3" placeholder="00000" required>
                                </div>
                            </div>
                            <span id="nim_err" class="text-danger"></span>
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
                            <span id="file_err" class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <button class="btn btn-danger me-2" id="cancel_btn">Cancel</button>
                    <button class="btn btn-primary" id="submit_button" type="submit">Submit</button>
                </div>
            </form>
        <?php elseif ($_GET['req'] == 'Update') : ?>
            <div class="card shadow" style="background-color: white;">
                <?php if (isset($_GET['name'])): ?>
                    <div class="card-header mt-3">
                        <h2 style="font-weight: bold;"><?php echo $_GET['req'] . ' "' . $_GET['name'] . '"' ?></h2>
                    </div>
                <?php endif ?>
                <div class="card-body">
                    <?php
                    $id = $_GET['id'];
                    $stmt = $conn->prepare("SELECT * FROM students_dat WHERE id = ?");
                    $stmt->bind_param("s", $id);
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                    }
                    ?>
                    <form id="editForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="page" name="page" value="<?= $_GET['page'] ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="form-group">
                            <div class="mb-3 row">
                                <label for="userid" class="col-sm-2 col-form-label">NIM</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control shadow-sm" id="nim" name="nim" value="<?= $row['nim'] ?>" placeholder="<?= $row['nim'] ?>" required>
                                    <span id="nim_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control shadow-sm" id="name" name="name" placeholder="<?= $row['name'] ?>" value="<?= $row['name'] ?>" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-5">
                                    <input type="email" class="form-control shadow-sm" id="email" name="email" placeholder="<?= $row['email'] ?>" value="<?= $row['email'] ?>" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="pict" class="col-sm-2 col-form-label">Photo</label>
                                <div class="col-sm-5">
                                    <input class="form-control shadow-sm" type="file" name="pict" id="pict">
                                    <input type="hidden" name="pict0" value="<?= $row['pict'] ?>">
                                    <span id="file_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start mb-3">
                                <button class="btn btn-primary" id="submit_button" style="width: 100px;" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-sm btn-danger" id="cancel_btn">Cancel</button>
                <?php endif ?>
                </div>
            </div>
    </div>
<?php
} else {
    echo "U're not suppoused to be here!";
}
?>
<script>
    $(document).ready(function() {
        /// USERID length and is_taken checker only for insert
        $('#insert_form, #edit_form').ready(function() {
            $('#regist_number').on('blur', function() {
                var majority = $('#majority').val();
                var gen_year = $('#gen_year').val();
                var regist_number = $('#regist_number').val();
                console.log(regist_number);
                if (regist_number.trim() !== '') {
                    if (regist_number.length !== 6) {
                        $('#nim_err').text('6 character is allowed.');
                        $('#regist_number').focus();
                    } else if (nimValidate(majority, gen_year, regist_number) == true) {
                        $('#nim_err').text('NIM already taken!');
                        $('#regist_number').focus();
                    } else {
                        $('#regist_number').text('All good');
                    }
                } else {
                    $('#nim_err').text('');
                }
            })
        });
        /// file photo validaiton
        $('#pict').on('change', function() {
            var file = $(this)[0].files[0];
            if (file) {
                var allowedExtensions = ['jpg', 'jpeg', 'png'];
                var fileExtension = file.name.split('.').pop().toLowerCase();
                if (allowedExtensions.indexOf(fileExtension) === -1) {
                    $('#file_err').text('Invalid file type. Please upload a JPG, JPEG, or PNG file.');
                    return false;
                } else if (file.size > 2000000) { // 2MB limit
                    $('#file_err').text('File size exceeds the 2MB limit.');
                    return false;
                } else {
                    $('#file_err').text('');
                }
            } else {
                $('#file_err').text('');
            }
        });

        $('#insertForm').submit(function(e) {
            let insertData = new FormData($(this)[0]);
            if (true) {
                $.ajax({
                    url: 'data/scripts/requestbase.php?req=insertMhs',
                    type: 'POST',
                    data: insertData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        window.location.href = 'mainpage.php?view=mhs';
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            } else {
                e.preventDefault();
            }
        });

        $('#editForm').submit(function(e) {
            let page = $('#page').val();
            let editData = new FormData($(this)[0]);
            if (validateSubmission() == true) {
                $.ajax({
                    url: 'data/scripts/requestbase.php?req=updateMhs',
                    type: 'POST',
                    data: editData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        window.location.href = 'mainpage.php?view=mhs&page=' + page;
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            } else {
                e.preventDefault();
            }
        });
        /// submit prevention function
        function validateSubmission() {
            var passwd = $('#password_err').text().trim() !== '';
            var uid = $('#userid_err').text().trim() !== '';
            var file = $('#file_err').text().trim() !== '';
            if (passwd || uid || file) {
                return false;
            } else {
                return true;
            }
        }

        function nimValidate(majority, gen_year, regist_number) {
            $.ajax({
                url: 'data/scripts/requestbase.php?req=useridchck',
                type: 'POST',
                data: {
                    majority: majority,
                    gen_year: gen_year,
                    regist_number: regist_number,
                },
                success: function(response) {
                    const message = JSON.parse(response);
                    console.log(response);
                    if (message.message == 'taken') {
                        return true;
                    } else {
                        return false;
                    }
                },
                error: function() {
                    alert("Server error. [req: useridchck]");
                }
            });
        }

        /// cancel button
        $('#cancel_btn').on('click', function() {
            history.back();
        });
        /// password confirmation
        $('#password, #passwordConfirm').on('keyup', function(event) {
            event.preventDefault();
            var password = $('#password').val();
            var confirmPassword = $('#passwordConfirm').val();
            if (password.trim() !== '' || confirmPassword.trim() !== '') {
                if (password.length < 6 || confirmPassword.length < 6) {
                    $('#password_err').text('Password must be at least 6 characters.');
                    return false;
                } else if (password !== confirmPassword) {
                    $('#password_err').text("Passwords doesn't match.");
                    return false;
                } else {
                    $('#password_err').text('');
                    return true;
                }
            } else {
                $('#password_err').text('');
            }
        });
    });
</script>