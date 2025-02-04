</td>
<?php if ($_SESSION['user']['role'] == 'admin') : ?>
    <td>
        <div class="d-flex justify-content-center">
            <?php
            if ($_SESSION['user']['status'] == 'active' && $row['userid'] == $_SESSION['user']['userid']) {
                echo 'n/a';
            } elseif ($row['role'] == $_SESSION['user']['role'] && $_SESSION['user']['role'] == 'admin') {
            ?>
                <a class="btn btn-info me-1" href="data/dataForms.php?req=Update&id=<?= $row['id']; ?>&name=<?= $row["name"]; ?>">Edit</a>
            <?php
            } elseif ($_SESSION['user']['role'] == 'admin') {
            ?>
                <a class="btn btn-info me-1" href="data/dataForms.php?req=Update&id=<?= $row['id']; ?>&name=<?= $row["name"]; ?>">Edit</a>
                <button class="btn btn-danger" id="delete_btn" data-id="<?= $row['id']; ?>">&times;</button>
            <?php
            } else {
                echo 'n/a';
            }
            ?>
        </div>
    </td>
<?php endif; ?>
</tr>