<?php
session_start();
include "db.php";
$query = "SELECT cid, name, email, phone, address FROM customer";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Customer List</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="viewcustomer.css">
</head>
<body>
    <nav class="navbar">
        <a href="admin.php">Dashboard</a></li>
        <a href="viewcustomers.php">View Customers</a></li>
        <a href="logout.php">Logout</a></li>
    </nav>

    <header>
        <h1>All Customers</h1>
    </header>

    <div class="customer-list">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['cid']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['phone']); ?></td>
                            <td><?= htmlspecialchars($row['address']); ?></td>
                            <td>
                                <a href="my_account.php?id=<?= $row['cid']; ?>" class="button">Edit</a>
                                <a href="delete_customer.php?id=<?= $row['cid']; ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">No customers found.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
