<?php
include 'dbconn.php';
session_start();
if (isset($_SESSION['user']) || isset($_GET['req']) || $_SERVER['REQUEST_METHOD'] == 'POST') {
    $requests = $_GET['req'];
    /// randomizing output file to prevent duplicates
    function randomizer($length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars);
        $randString = '';
        for ($i = 0; $i < $length; $i++) {
            $randString .= $chars[rand(0, $charsLength - 1)];
        }
        return $randString;
    }
    function duplicateCheck($table, $column, $value)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $column = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    /// using switch-case to select block of code by $_GET
    switch ($requests) {
            //
            // Students CRUD
            //
        case 'insertStudent':
            $nim1 = $_POST['nim1'];
            $nim2 = $_POST['nim2'];
            $nim3 = $_POST['nim3'];
            $nim = $nim1 . '.' . $nim2 . '.' . $nim3;
            $name = $_POST['name'];
            $email = $_POST['email'];
            /// file handler
            $targetdir = '../uploads/userpict/';
            $file = $targetdir . basename($_FILES['pict']['name']);
            $filetype = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $filename = substr(str_replace(' ', '', $name), 0, 6) . randomizer(12) . "." . $filetype;
            $fileuploads = $targetdir . $filename;
            $uploads = move_uploaded_file($_FILES['pict']['tmp_name'], $fileuploads);
            $stmt = $conn->prepare("INSERT INTO students (nim, name, email, pict) values (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nim, $name, $email, $filename);
            if ($uploads && $stmt->execute()) {
                $_SESSION['crud']['message'] = 'User: <b>' . $name . '</b> is registered successfully.';
                $response['message'] = 'success';
            } else {
                $_SESSION['crud']['message'] = 'An error occured! err: ' . error_get_last()['message'];
                $response['message'] = 'success';
            }
            echo json_encode($response);
            break;
            /// data edit method
        case 'updateStudent':
            try {
                $id = $_POST['id'];
                $nim = $_GET['nim'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $page = $_POST['page'];
                $stmt = $conn->prepare("SELECT pict FROM students WHERE id = ?");
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $oldPict = $row['pict'];
                /// file handler
                if (!empty($_FILES['pict']['name'])) { /// if file submission exist
                    /// delete exiting photo first
                    $filename = $oldPict;
                    $filePath = '../uploads/userpict/' . $filename;
                    $removeFile = (file_exists($filePath)) ? unlink($filePath) : true; // check pict avability first then delete if any
                    $removeFile;
                    /// then insert the new
                    $targetdir = '../uploads/userpict/';
                    $file = $targetdir . basename($_FILES['pict']['name']);
                    $filetype = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $filename = substr(str_replace(' ', '', $name), 0, 6) . randomizer(12) . "." . $filetype;
                    $fileuploads = $targetdir . $filename;
                    $uploads = move_uploaded_file($_FILES['pict']['tmp_name'], $fileuploads);
                    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, pict = ? WHERE id = ?");
                    $stmt->bind_param("ssss", $name, $email, $filename, $id);
                    $uploads;
                } else {
                    /// if file submission doesnt exist
                    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("sss", $name, $email, $id);
                }
                if ($stmt->execute()) {
                    $_SESSION['crud']['message'] = '<b>' . $nim . '</b> updated successfully.';
                    $response['status'] = 'success';
                    $response['page'] = $page;
                } else {
                    $_SESSION['crud']['message'] = 'Update Error! err: ' . error_get_last()['message'];
                    $response['message'] = 'Error!';
                    $response['status'] = 'error';
                }
            } catch (Exception $e) {
                $_SESSION['crud']['message'] = 'Update Error! err: ' . $e->getMessage();
                $response['message'] = 'Error';
                $response['status'] = 'error';
            }
            echo json_encode($response);
            break;
            /// data delete method
        case 'deleteStudent':
            try {
                $id = $_POST['id'];
                // Select statement
                $stmt = $conn->prepare("SELECT pict FROM students WHERE id = ?");
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $filename = $row['pict'];
                $filePath = '../uploads/userpict/' . $filename;
                $removeFile = (file_exists($filePath)) ? unlink($filePath) : true;
                // Delete statement
                $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
                $stmt->bind_param("s", $id);
                // File deletion
                if ($stmt->execute() && $removeFile) {
                    $response['status'] = 'success';
                    $response['message'] = 'Data deleted successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error deleting file: " . error_get_last()['message'];
                }
            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['message'] = "An error occurred: " . $e->getMessage();
            }
            echo json_encode($response);
            break;
            /// search data mhs
        case 'searchStudent':
            try {
                $page = $_POST['page'];
                $searchCategory = $_POST['searchCategory'];
                $searchInput = $_POST['searchInput'];
                // Prepare and execute SQL query with parameterized binding
                $sql = "SELECT * FROM students WHERE " . $searchCategory . " LIKE ?";
                $stmt = $conn->prepare($sql);
                $searchQuery = "%" . $searchInput . "%";
                $stmt->bind_param("s", $searchQuery);
                $stmt->execute();
                $result = $stmt->get_result();
                $html = "";
                $i = 0;
                // Fetch and display results
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $html .= '<tr>';
                        $html .= '<td class="text-center">' . $i + 1;
                        $html .= "<td>" . $row['nim'];
                        $html .= "<td>" . $row['name'];
                        $html .= '<a class="btn btn-sm btn-outline-success ms-2" id="krs_btn" href="?view=krs&req=showKrs&id=' . $row['id'] . '">View KRS</a>';
                        $html .= "<td>" . $row['email'];
                        $html .= "<td><img src='./data/uploads/userpict/" . $row['pict'] . "' alt='" . $row['name'] . "' style='width:100px; height:100px; object-fit:cover; display:block; margin:0 auto; border-radius:10px;'>";
                        $html .= '<td class="text-center align-middle">';
                        $html .= '<a class="btn btn-outline-info me-1" href="?view=edit-student&req=update&id=' . $row['id'] . '&nim=' . $row['nim'] . '&name=' . $row["name"] . '&email=' . $row['email'] . '&page=' . $page . '">Edit</a>';
                        $html .= '<button class="btn btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button>';
                        $i++;
                        $response['html'] = $html;
                        $response['status'] = 'success';
                    }
                } else {
                    $html .= "<tr><td colspan='6' class='text-center'>No records found.";
                    $response['html'] = $html;
                    $response['status'] = 'success';
                }
            } catch (Exception $e) {
                $response['message'] = "An error occurred: " . $e->getMessage();
                $response['status'] = 'error';
            }
            echo json_encode($response);
            break;
            /// fetch Data mhs
        case 'fetchStudents':
            // Get page and page size from the AJAX request
            $page = $_POST['page'];
            $maxRow = $_POST['maxRow'];
            // Calculate offset
            $offset = ($page - 1) * $maxRow;
            // SQL query to fetch data
            $sql = "SELECT * FROM students ORDER BY id DESC LIMIT ? OFFSET ? ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $maxRow, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            // Build the HTML table rows
            $html = "";
            if ($result->num_rows > 0) {
                // Total pages calculation
                $totalRows = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
                $pages = ceil($totalRows / $maxRow);
                $i = ($page - 1) * $maxRow;
                while ($row = $result->fetch_assoc()) {
                    $pictSrc = $row['pict'];
                    $filePath = '../uploads/userpict/';
                    $pictPath = './data/uploads/userpict/';
                    $pictEmpty = 'https://cdn-icons-png.freepik.com/512/3875/3875148.png?ga=GA1.1.599436757.1735230785';
                    $pict = (file_exists($filePath . $pictSrc)) ? $pictPath . $pictSrc : $pictEmpty;
                    $html .= '<tr>';
                    $html .= '<td class="text-center">' . $i + 1;
                    $html .= "<td>" . $row['nim'];
                    $html .= "<td>" . $row['name'];
                    $html .= '<a class="btn btn-sm btn-outline-success ms-2" id="krs_btn" href="?view=krs&req=showKrs&id=' . $row['id'] . '">View KRS</a>';
                    $html .= "<td>" . $row['email'];
                    $html .= "<td><img src='" . $pict . "' alt='" . $row['name'] . "' style='width:100px; height:100px; object-fit:cover; display:block; margin:0 auto; border-radius:10px;'>";
                    $html .= '<td class="text-center align-middle">';
                    $html .= '<a class="btn btn-outline-info me-1" href="?view=edit-student&req=update&id=' . $row['id'] . '&nim=' . $row['nim'] . '&name=' . $row["name"] . '&email=' . $row['email'] . '&page=' . $page . '">Edit</a>';
                    $html .= '<button class="btn btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button>';
                    $i++;
                    $response['html'] = $html;
                    $response['pages'] = $pages;
                }
                //echo $filePath;
            } else {
                $html .= "<tr><td colspan='6' class='text-center'>No records found.";
                $response['html'] = $html;
            }
            echo json_encode($response);
            break;

            //
            // Lecturer CRUD
            //
        case 'insertLecturer':
            // Insert lecturer data
            try {
                $npp1 = $_POST['npp1'];
                $npp2 = $_POST['npp2'];
                $npp3 = $_POST['npp3'];
                $npp = $npp1 . '.' . $npp2 . '.' . $npp3;
                $name = $_POST['name'];
                $homebase = $_POST['homebase'];
                $stmt = $conn->prepare("INSERT INTO lecturers (npp, name, homebase) values (?, ?, ?)");
                $stmt->bind_param("sss", $npp, $name, $homebase);
                if ($stmt->execute()) {
                    $_SESSION['crud']['message'] = 'Lecturer: <b>' . $name . '</b> is registered successfully.';
                    $response['status'] = 'success';
                } else {
                    $errorMsg = error_get_last()['message'];
                    $_SESSION['crud']['message'] = 'An error occured! err: ' . $errorMsg;
                    $response['message'] = $errorMsg;
                    $response['status'] = 'error';
                }
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
                $_SESSION['crud']['message'] = 'An error occured! err: ' . $errorMsg;
                $response['message'] = 'error: ' . $errorMsg;
            }
            echo json_encode($response);
            break;
        case 'updateLecturer':
            try {
                $id = $_POST['id'];
                $npp = $_GET['npp'];
                $name = $_POST['name'];
                $homebase = $_POST['homebase'];
                $page = $_POST['page'];
                $stmt = $conn->prepare("SELECT npp, name, homebase FROM lecturers WHERE id = ?");
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $oldNpp = $row['npp'];
                $oldName = $row['name'];
                $oldHomebase = $row['homebase'];
                if ($npp != $oldNpp || $name != $oldName || $homebase != $oldHomebase) {
                    $stmt = $conn->prepare("UPDATE lecturers SET npp = ?, name = ?, homebase = ? WHERE id = ?");
                    $stmt->bind_param("ssss", $npp, $name, $homebase, $id);
                    if ($stmt->execute()) {
                        $_SESSION['crud']['message'] = '<b>' . $npp . '</b> updated successfully.';
                        $response['status'] = 'success';
                        $response['page'] = $page;
                    } else {
                        $_SESSION['crud']['message'] = 'Update Error! err: ' . error_get_last()['message'];
                        $response['message'] = 'Error!';
                        $response['status'] = 'error';
                    }
                } else {
                    $_SESSION['crud']['message'] = 'No changes detected.';
                    $response['message'] = 'No changes detected.';
                    $response['status'] = 'success';
                }
            } catch (Exception $e) {
                $_SESSION['crud']['message'] = 'Update Error! err: ' . $e->getMessage();
                $response['message'] = 'Error';
                $response['status'] = 'error';
            }
            echo json_encode($response);
            break;
        case 'deleteLecturer':
            try {
                $id = $_POST['id'];
                // Delete statement
                $stmt = $conn->prepare("DELETE FROM lecturers WHERE id = ?");
                $stmt->bind_param("s", $id);
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Data deleted successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error deleting data: " . error_get_last()['message'];
                }
            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['message'] = "An error occurred: " . $e->getMessage();
            }
            echo json_encode($response);
            break;

        case 'searchLecturer':
            try {
                $page = $_POST['page'];
                $searchCategory = $_POST['searchCategory'];
                $searchInput = $_POST['searchInput'];
                // Prepare and execute SQL query with parameterized binding
                $sql = "SELECT * FROM lecturers WHERE " . $searchCategory . " LIKE ?";
                $stmt = $conn->prepare($sql);
                $searchQuery = "%" . $searchInput . "%";
                $stmt->bind_param("s", $searchQuery);
                $stmt->execute();
                $result = $stmt->get_result();
                $html = "";
                $i = 0;
                // Fetch and display results
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $html .= '<tr class="align-middle">';
                        $html .= '<td class="text-center">' . $i + 1;
                        $html .= "<td>" . $row['npp'];
                        $html .= "<td>" . $row['name'];
                        $html .= "<td>" . $row['homebase'];
                        $html .= '<td class="text-center align-middle">';
                        $html .= '<a class="btn btn-outline-info me-1" href="?view=edit-lecturer&req=update&id=' . $row['id'] . '&npp=' . $row['npp'] . '&name=' . $row['name'] . '&page=' . $page . '">Edit</a>';
                        $html .= '<button class="btn btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button>';
                        $i++;
                        $response['html'] = $html;
                        $response['status'] = 'success';
                    }
                } else {
                    $html .= "<tr><td colspan='6' class='text-center'>No records found.";
                    $response['html'] = $html;
                    $response['status'] = 'success';
                }
            } catch (Exception $e) {
                $response['message'] = "An error occurred: " . $e->getMessage();
                $response['status'] = 'error';
            }
            echo json_encode($response);
            break;
        case 'fetchLecturers':
            // Get page and page size from the AJAX request
            $page = $_POST['page'];
            $maxRow = $_POST['maxRow'];
            // Calculate offset
            $offset = ($page - 1) * $maxRow;
            // SQL query to fetch data
            $sql = "SELECT * FROM lecturers ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $maxRow, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            // Build the HTML table rows
            $html = "";
            if ($result->num_rows > 0) {
                // Total pages calculation
                $totalRows = $conn->query("SELECT COUNT(*) FROM lecturers")->fetch_row()[0];
                $pages = ceil($totalRows / $maxRow);
                $i = ($page - 1) * $maxRow;
                while ($row = $result->fetch_assoc()) {
                    $html .= '<tr class="align-middle">';
                    $html .= '<td class="text-center">' . $i + 1 . "</td>";
                    $html .= "<td>" . $row['npp'] . "</td>";
                    $html .= "<td>" . $row['name'] . "</td>";
                    $html .= "<td class='text-center'>" . $row['homebase'] . "</td>";
                    $html .= '<td class="text-center align-middle">';
                    $html .= '<a class="btn btn-outline-info me-1" href="?view=edit-lecturer&req=update&id=' . $row['id'] . '&npp=' . $row['npp'] . '&name=' . $row['name'] . '&page=' . $page . '">Edit</a>';
                    $html .= '<button class="btn btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button></td></tr>';
                    $i++;
                    $response['html'] = $html;
                    $response['pages'] = $pages;
                }
            } else {
                $html .= "<tr><td colspan='6' class='text-center'>No records found.";
                $response['html'] = $html;
            }
            echo json_encode($response);
            break;

            //
            // Courses CRUD
            //
        case 'insertCourse':
            try {
                $courseCode1 = $_POST['courseCode1'];
                $courseCode2 = $_POST['courseCode2'];
                $courseCode = $courseCode1 . '.' . $courseCode2;
                $name = $_POST['name'];
                $cType = $_POST['cType'];
                $sks = $_POST['sks'];
                $smt = $_POST['smt'];
                $stmt = $conn->prepare("INSERT INTO courses (code, name, type, sks, smt) values (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $courseCode, $name, $cType, $sks, $smt);
                if ($stmt->execute()) {
                    $_SESSION['crud']['message'] = 'Course: <b>' . $name . '</b> is added successfully.';
                    $response['status'] = 'success';
                } else {
                    $errorMsg = error_get_last()['message'];
                    $_SESSION['crud']['message'] = 'An error occured! err: ' . $errorMsg;
                    $response['message'] = $errorMsg;
                    $response['status'] = 'error';
                }
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
                $_SESSION['crud']['message'] = 'An error occured! err: ' . $errorMsg;
                $response['message'] = 'error: ' . $errorMsg;
            }
            echo json_encode($response);
            break;
        case 'updateCourse':
            try {
                $id = $_POST['id'];
                $name = $_POST['name'];
                $type = $_POST['cType'];
                $sks = $_POST['sks'];
                $smt = $_POST['smt'];
                $page = $_POST['page'];
                $stmt = $conn->prepare("SELECT code, name, type, sks, smt FROM courses WHERE id = ?");
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $oldCode = $row['code'];
                $oldName = $row['name'];
                $oldType = $row['type'];
                $oldSks = $row['sks'];
                $oldSmt = $row['smt'];
                if ($name != $oldName || $type != $oldType || $sks != $oldSks || $smt != $oldSmt) {
                    $stmt = $conn->prepare("UPDATE courses SET name = ?, type = ?, sks = ?, smt = ? WHERE id = ?");
                    $stmt->bind_param("sssss", $name, $type, $sks, $smt, $id);
                    if ($stmt->execute()) {
                        $_SESSION['crud']['message'] = '<b>' . $oldCode . '</b> updated successfully.';
                        $response['status'] = 'success';
                        $response['page'] = $page;
                    } else {
                        $_SESSION['crud']['message'] = 'Update Error! err: ' . error_get_last()['message'];
                        $response['message'] = 'Error!';
                        $response['status'] = 'error';
                        $response['page'] = $page;
                    }
                } else {
                    $_SESSION['crud']['message'] = 'No changes detected.';
                    $response['message'] = 'No changes detected.';
                    $response['status'] = 'success';
                    $response['page'] = $page;
                }
            } catch (Exception $e) {
                $_SESSION['crud']['message'] = 'Update Error! err: ' . $e->getMessage();
                $response['message'] = 'Error';
                $response['status'] = 'error';
                $response['page'] = $page;
            }
            echo json_encode($response);
            break;
        case 'deleteCourse':
            # code...
            try {
                $id = $_POST['id'];
                // Delete statement
                $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
                $stmt->bind_param("s", $id);
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Course deleted successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error deleting course: " . error_get_last()['message'];
                }
            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['message'] = "An error occurred: " . $e->getMessage();
            }
            echo json_encode($response);
            break;
        case 'searchCourse':
            try {
                $page = $_POST['page'];
                $searchCategory = $_POST['searchCategory'];
                $searchInput = $_POST['searchInput'];
                // Prepare and execute SQL query with parameterized binding
                $sql = "SELECT * FROM courses WHERE " . $searchCategory . " LIKE ?";
                $stmt = $conn->prepare($sql);
                $searchQuery = "%" . $searchInput . "%";
                $stmt->bind_param("s", $searchQuery);
                $stmt->execute();
                $result = $stmt->get_result();
                $html = "";
                $i = 0;
                // Fetch and display results
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $html .= '<tr class="align-middle">';
                        $html .= '<td class="text-center">' . $i + 1;
                        $html .= "<td>" . $row['npp'];
                        $html .= "<td>" . $row['name'];
                        $html .= "<td>" . $row['homebase'];
                        $html .= '<td class="text-center align-middle">';
                        $html .= '<a class="btn btn-outline-info me-1" href="?view=edit-course&req=update&id=' . $row['id'] . '&page=' . $page . '">Edit</a>';
                        $html .= '<button class="btn btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button>';
                        $i++;
                        $response['html'] = $html;
                        $response['status'] = 'success';
                    }
                } else {
                    $html .= "<tr><td colspan='6' class='text-center'>No records found.";
                    $response['html'] = $html;
                    $response['status'] = 'success';
                }
            } catch (Exception $e) {
                $response['message'] = "An error occurred: " . $e->getMessage();
                $response['status'] = 'error';
            }
            echo json_encode($response);
            break;

        case 'fetchCourses':
            // Get page and page size from the AJAX request
            $page = $_POST['page'];
            $maxRow = $_POST['maxRow'];
            // Calculate offset
            $offset = ($page - 1) * $maxRow;
            // SQL query to fetch data
            $sql = "SELECT * FROM courses ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $maxRow, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            // Build the HTML table rows
            $html = "";
            if ($result->num_rows > 0) {
                // Total pages calculation
                $totalRows = $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];
                $pages = ceil($totalRows / $maxRow);
                $i = ($page - 1) * $maxRow;
                while ($row = $result->fetch_assoc()) {
                    $courseCode = $row['code'];
                    $name = $row['name'];
                    $type = $row['type'];
                    $sks = $row['sks'];
                    $smt = $row['smt'];
                    $html .= '<tr class="align-middle">';
                    $html .= '<td class="text-center">' . ($i + 1) . '</td>';
                    $html .= '<td>' . $courseCode . '</td>';
                    $html .= '<td>' . $name . '</td>';
                    $html .= '<td>' . $type . '</td>';
                    $html .= '<td>' . $sks . '</td>';
                    $html .= '<td>' . $smt . '</td>';
                    $html .= '<td class="text-center align-middle">';
                    $html .= '<a class="btn btn-outline-info me-1" href="?view=edit-course&req=update&id=' . $row['id'] . '&cc=' . $courseCode . '&cname=' . $name . '&cType=' . $type . '&SKS=' . $sks . '&SMT=' . $smt . '&page=' . $page . '">Edit</a>';
                    $html .= '<button class="btn btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button>';
                    $i++;
                    $response['html'] = $html;
                    $response['pages'] = $pages;
                }
            } else {
                $html .= "<tr><td colspan='7' class='text-center'>No records found.";
                $response['html'] = $html;
            }
            echo json_encode($response);
            break;

            //
            // KRS offers CRUD
            //
        case 'insertOffer':
            $sched2 = $_GET['sched2isHidden'];
            $course = $_POST['courses'];
            $sks = $_POST['sks'];
            $lecturer = $_POST['lecturer'];
            $classGroup = $_POST['classGroup'];
            //first Schedule
            $daySched1 = $_POST['daySched1'];
            $hourSched1 = $_POST['hourSched1'];
            $floorSched1 = $_POST['floorSched1'];
            $roomSched1 = $_POST['roomSched1'];
            $classRoom1 = $floorSched1 . '.' . $roomSched1;
            //second Schedule
            if ($sched2 === 'true') {
                $daySched2 = 0;
                $hourSched2 =  0;
                $classRoom2 = 0;
            } else {
                $daySched2 = $_POST['daySched2'];
                $hourSched2 = $_POST['hourSched2'];
                $floorSched2 = $_POST['floorSched2'];
                $roomSched2 = $_POST['roomSched2'];
                $classRoom2 = $floorSched2 . '.' . $roomSched2;
            }
            $stmt = $conn->prepare("INSERT INTO krs_offers (course, lecturer, class_group, days_sched1, hours_sched1, class_room1, days_sched2, hours_sched2, class_room2) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $course, $lecturer, $classGroup, $daySched1, $hourSched1, $classRoom1, $daySched2, $hourSched2, $classRoom2);
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Offer added successfully.';
            } else {
                $response['status'] = 'error';
                $response['message'] = "Error adding offer: " . error_get_last()['message'];
            }
            echo json_encode($response);
            break;
        case 'deleteOffer':
            try {
                $id = $_POST['id'];
                // Delete statement
                $stmt = $conn->prepare("DELETE FROM krs_offers WHERE id = ?");
                $stmt->bind_param("s", $id);
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Offer deleted successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error deleting offer: " . error_get_last()['message'];
                }
            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['message'] = "An error occurred: " . $e->getMessage();
            }
            echo json_encode($response);
            break;

        case 'fetchOffers':
            // Get page and page size from the AJAX request
            $page = $_POST['page'];
            $maxRow = $_POST['maxRow'];
            // Calculate offset
            $offset = ($page - 1) * $maxRow;
            // SQL query to fetch data
            $sql = "SELECT * FROM krs_offers ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $maxRow, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            // Build the HTML table rows
            $html = "";
            if ($result->num_rows > 0) {
                // Total pages calculation
                $totalRows = $conn->query("SELECT COUNT(*) FROM krs_offers")->fetch_row()[0];
                $pages = ceil($totalRows / $maxRow);
                $i = ($page - 1) * $maxRow;
                while ($row = $result->fetch_assoc()) {
                    $course = $conn->prepare("SELECT * FROM courses WHERE code = ?");
                    $course->bind_param("s", $row['course']);
                    $course->execute();
                    $courseRow = $course->get_result();
                    ##
                    $courses = $courseRow->fetch_assoc();
                    $lecturer = $conn->prepare("SELECT * FROM lecturers WHERE npp = ?");
                    $lecturer->bind_param("s", $row['lecturer']);
                    $lecturer->execute();
                    $lecturerRow = $lecturer->get_result();
                    $lecturers = $lecturerRow->fetch_assoc();
                    $html .= '<tr class="align-middle">';
                    $html .= '<td class="text-center">' . $i + 1;
                    $html .= "<td>" . $courses['name'];
                    $html .= "<td class='text-center'>" . $courses['sks'];
                    $html .= "<td title='Lecturer only displayed on admin page.'>" . $lecturers['name'];
                    $html .= "<td>" . $row['class_group'];
                    $html .= "<td class='text-center'>" . $row['days_sched1'];
                    $html .= "<td class='text-center'>" . $row['hours_sched1'];
                    $html .= "<td class='text-center'>" . $row['class_room1'];
                    $html .= "<td class='text-center'>" . (!$row['days_sched2'] ? '-' : $row['days_sched2']);
                    $html .= "<td class='text-center'>" . (!$row['hours_sched2'] ? '-' : $row['hours_sched2']);
                    $html .= "<td class='text-center'>" . (!$row['class_room2'] ? '-' : $row['class_room2']);
                    $html .= '<td class="text-center align-middle">';
                    $html .= '<button class="btn btn-sm fs-6 btn-outline-info me-2" id="editBtn" data-id="' . $row['id'] . '">✎</button>';
                    $html .= '<button class="btn btn-sm fs-6 btn-outline-danger" id="deleteBtn" data-id="' . $row['id'] . '">&times;</button>';
                    $i++;
                    $response['html'] = $html;
                    $response['pages'] = $pages;
                }
            } else {
                $html .= "<tr><td colspan='12' class='text-center'>No records found.";
                $response['html'] = $html;
            }
            // Return the HTML and total pages as JSON
            echo json_encode($response);
            break;

            //
            // Another features
            //

            /// duplicated id_num check method
        case 'nimCheck':
            $nim = $_POST['nim'];
            $stmt = $conn->prepare("SELECT * FROM students WHERE nim = ?");
            $stmt->bind_param("s", $nim);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $response['message'] = 'true';
            } else {
                $response['message'] = 'false';
            }
            echo json_encode($response);
            break;
        case 'nppCheck':
            $table = $_POST['table'];
            $column = $_POST['column'];
            $value = $_POST['npp'];
            $response = duplicateCheck($table, $column, $value);
            echo json_encode($response);
            break;
        case 'courseCodeCheck':
            $table = $_POST['table'];
            $column = $_POST['column'];
            $value = $_POST['courseCode'];
            $response = duplicateCheck($table, $column, $value);
            echo json_encode($response);
            break;
        case 'offerCodeCheck':
            $table = $_POST['table'];
            $column = $_POST['column'];
            $value = $_POST['offerCode'];
            $response = duplicateCheck($table, $column, $value);
            echo json_encode($response);
            break;
        case 'courseOption':
            $courseCode = $_POST['courseCode'];
            $stmt = $conn->prepare("SELECT sks FROM courses WHERE code = ?");
            $stmt->bind_param("s", $courseCode);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($rows = $result->fetch_assoc()) {
                $response = $rows;
            }
            echo json_encode($response);
            break;
        case 'getData':
            $target = $_POST['target'];
            $stmt = $conn->prepare("SELECT * FROM $target");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($rows = $result->fetch_assoc()) {
                $response[$target][] = $rows;
            }
            echo json_encode($response);
            break;
            /// user login method
        case 'userLogin':
            $userid = $_POST['userid'];
            $user_passwd = $_POST['password'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE userid = ?");
            $stmt->bind_param("s", $userid);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $passwd = $row['password'];
                if (password_verify($user_passwd, $passwd)) {
                    $_SESSION['user']['userid'] = $row['userid'];
                    $_SESSION['user']['name'] = $row['name'];
                    $_SESSION['user']['role'] = $row['role'];
                    $_SESSION['user']['pict'] = $row['pict'];
                    print_r($_SESSION);
                    header("location: ../../mainpage.php");
                } else {
                    $_SESSION['error'] = '[user_login] Invalid username or password';
                    header("location: ../../");
                }
            } else {
                $_SESSION['error'] = '[user_login] Invalid username or password';
                header("location: ../../");
            }
            break;
        case 'userLogout':
            try {
                unset($_SESSION);
                unset($_POST['userid']);
                unset($_POST['password']);
                session_destroy();
                $response['message'] = "success";
            } catch (Exception $e) {
                $response['message'] = "error!" . $e;
            }
            echo json_encode($response);
            break;
        default:
            echo 'invalid requests!';
            break;
    }
} else {
    echo 'something broken! contact your god. <a href="broken.html">here is';
}
