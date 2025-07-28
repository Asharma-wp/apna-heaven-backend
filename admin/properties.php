<?php
session_start();
include("../includes/auth.php"); // Secure this page
include("../config/database.php");

// Fetch properties from database
$sql = "SELECT * FROM properties";
$result = $conn->query($sql);
?>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= $row["title"] ?></td>
            <td><?= $row["price"] ?></td>
            <td>
                <a href="edit_property.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_property.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>