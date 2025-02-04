<?php
if (isset($_SESSION['user'])) {
    $_SESSION['table']['page'] = 1;
    $page = $_GET['page'] ?? 0;
?>
    <div class="row g-4 mb-3 me-1">
        <div class="col-sm-6 justify-content-start">
            <div class="row">
                <form class="col input-group" id="searchBox">
                    <select class="form-select border-light" id="searchCategory">
                        <option value="name">Name</option>
                        <option value="code">Code</option>
                    </select>
                    <input type="text" class="form-control border-light" id="searchInput" placeholder="Search..." aria-label="Search" style="width: 290px;" required>
                    <button type="button" class="btn btn-sm btn-secondary" id="resetButton">Reset</button>
                    <button class="btn btn-dark" type="submit" id="searchButton">Search</button>
                </form>
            </div>
        </div>
        <div class="col-sm-1 d-flex justify-content-center">
            <a type="button" href="data/scripts/reporting/mpdfCourses.php" class="btn btn-dark">&#128462; Print</a>
        </div>
        <div class="col-sm-3">
            <div class="row align-items-center d-flex justify-content-center">
                <label for="maxRow" class="col-sm-2">Show:</label>
                <div class="col-sm-4">
                    <select class="form-select border-dark" id="maxRow">
                        <option value="2">2</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-sm-2 justify-content-end align-center">
            <div class="row">
                <a type="button" id="prevBtn" class="col btn btn-dark">&lt;</a>
                <div class="col-sm-6">
                    <select class="form-select border-dark" id="pageOption"></select>
                </div>
                <a type="button" id="nextBtn" class="col btn btn-dark">&gt;</a>
            </div>
        </div>
    </div>
    <div>
        <?php
        if (isset($_SESSION['crud'])) {
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['crud']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['crud']);
        } ?>
    </div>
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th scope="col" class="text-center" style="width: 50px;">#</th>
                <th scope="col" style="width: 100px;">Code</th>
                <th scope="col" style="width: 600px;">Name</th>
                <th scope="col" style="width: 80px;">Type</th>
                <th scope="col" style="width: 80px;">SKS</th>
                <th scope="col" style="width: 80px;">SMT</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody id="tableBody"></tbody>
    </table>

    <script>
        $(document).ready(function() {
            /// Initial page and page size
            const urlParams = new URLSearchParams(window.location.search);
            var page = $('select#pageOption').val() ? parseInt(urlParams.get('page')) : 1;
            var maxRow = $('select#maxRow').val();
            var pages = 0;
            loadData(page, maxRow);

            /// Function to fetch and display data
            function loadData(page, maxRow) {
                $("#tableBody").empty();
                $.ajax({
                    url: 'data/scripts/requestbase.php?req=fetchCourses',
                    type: 'POST',
                    data: {
                        page: page,
                        maxRow: maxRow
                    },
                    success: function(response) {
                        let data = JSON.parse(response);
                        $('#tableBody').html(data.html);
                        pages = data.pages;
                        loadOption(pages);
                        console.log(data.pages);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            }

            function loadOption(pages) {
                $("#pageOption").empty();
                for (let i = 1; i <= pages; i++) {
                    $('#pageOption').append('<option value=' + i + '>' + i + '</option>');
                }
                $('#pageOption').find('option[value="' + page + '"]').attr('selected', true);
            }

            $('#maxRow').on('change', function() {
                $("#tableBody").empty();
                maxRow = $('#maxRow').val();
                $('this, option').removeAttr('selected');
                $(this).find('option[value="' + $(this).val() + '"]').attr('selected', true);
                page = 1;
                loadData(page, maxRow);
            })

            $('#pageOption').on('change', function() {
                $("#tableBody").empty();
                var selectedOption = $(this).val();
                page = selectedOption;
                loadData(page, maxRow);
                $('this, option').removeAttr('selected');
                $(this).find('option[value="' + selectedOption + '"]').attr('selected', true);
                console.log('maxRow: ' + maxRow);
                console.log('page: ' + page);

            });

            $('#prevBtn').on('click', () => {
                if (page > 1) {
                    page--;
                    loadData(page, maxRow);
                } else {
                    alert("You're already at the first page!");
                }
            });

            $('#nextBtn').on('click', () => {
                if (page < pages) {
                    page++;
                    loadData(page, maxRow);
                } else {
                    alert("Last page reached!");
                }
            });

            $('#resetButton').on('click', () => {
                $("#tableBody").empty();
                $('#searchInput').val('');
                page = 1;
                loadData(page, maxRow);
            })

            /// table data search function
            $('#searchBox').on('submit', function(e) {
                e.preventDefault();
                var searchCategory = $('#searchCategory').val();
                var searchInput = $('#searchInput').val();
                $("#tableBody").empty();
                $("#pagination").empty();
                $.ajax({
                    url: 'data/scripts/requestbase.php?req=searchCourse',
                    type: 'POST',
                    data: {
                        page: page,
                        searchCategory: searchCategory,
                        searchInput: searchInput
                    },
                    success: function(response) {
                        var respons = JSON.parse(response);
                        if (respons.status == 'error') {
                            alert(respons.message);
                        } else {
                            $('#tableBody').html(respons.html);
                        }
                    },
                    error: function() {
                        alert("Server error. [req: searchMhs]");
                    }
                })
                console.log(searchCategory, searchInput);
            });
            /// row delete method
            $('#tableBody').on('click', '#deleteBtn', function() {
                var id = $(this).data('id');
                if (confirm("Are you sure to delete this row?")) {
                    $.ajax({
                        url: 'data/scripts/requestbase.php?req=deleteCourse',
                        type: 'POST',
                        data: {
                            id: id,
                        },
                        success: function(response) {
                            var respons = JSON.parse(response);
                            alert(respons.message);
                            console.log(respons.status);
                            loadData(page, maxRow);
                        },
                        error: function() {
                            alert("Server error. [req: delete]");
                        }
                    });
                }
            });
        })
    </script>
<?php
} else {
    echo "Access denied!";
}
