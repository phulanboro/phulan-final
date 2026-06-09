<?php
session_start();
include "config.php";

if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit();
}

/* ADD BOOK */
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $year = $_POST['year'];

    $conn->query("INSERT INTO books (name,author,isbn,year,status,borrowerName,borrowerId)
    VALUES ('$name','$author','$isbn','$year','Available','-','-')");
}

/* DELETE */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM books WHERE id=$id");
}

/* RETURN */
if(isset($_GET['return'])){
    $id = $_GET['return'];
    $conn->query("UPDATE books SET 
        status='Available',
        borrowerName='-',
        borrowerId='-'
        WHERE id=$id");
}

/* BORROW */
if(isset($_POST['borrow'])){
    $id = $_POST['id'];
    $bname = $_POST['borrowerName'];
    $bid = $_POST['borrowerId'];

    $conn->query("UPDATE books SET 
        status='Borrowed',
        borrowerName='$bname',
        borrowerId='$bid'
        WHERE id=$id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-bg">

<div class="container">
    <h1>📚 Library Management System</h1>
    <a href="logout.php"><button style="float:right;">Logout</button></a>

    <!-- Add Book -->
    <div class="add-book">
        <form method="POST">
            <input type="text" name="name" placeholder="Book Name" required>
            <input type="text" name="author" placeholder="Author Name" required>
            <input type="text" name="isbn" placeholder="ISBN Number" required>
            <input type="number" name="year" placeholder="Year" required>
            <button type="submit" name="add">Add Book</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Book</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Year</th>
                <th>Status</th>
                <th>Borrower Name</th>
                <th>Borrower ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

<?php
$result = $conn->query("SELECT * FROM books");

while($row = $result->fetch_assoc()){
?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['author']; ?></td>
    <td><?php echo $row['isbn']; ?></td>
    <td><?php echo $row['year']; ?></td>

    <td style="color:<?php echo $row['status']=='Borrowed'?'red':'green'; ?>">
        <?php echo $row['status']; ?>
    </td>

    <td><?php echo $row['borrowerName']; ?></td>
    <td><?php echo $row['borrowerId']; ?></td>

    <td>
        <!-- Borrow -->
        <button onclick="borrowBook(<?php echo $row['id']; ?>)">Borrow</button>

        <!-- Return -->
        <a href="?return=<?php echo $row['id']; ?>">
            <button>Return</button>
        </a>

        <!-- Delete -->
        <a href="?delete=<?php echo $row['id']; ?>">
            <button>Delete</button>
        </a>
    </td>
</tr>
<?php } ?>

        </tbody>
    </table>
</div>

<script>
function borrowBook(id){

    let borrowerName = prompt("Enter Borrower Name:");
    if(!borrowerName) return;

    let borrowerId = prompt("Enter Borrower ID:");
    if(!borrowerId) return;

    let form = document.createElement("form");
    form.method = "POST";
    form.style.display = "none";

    let inputId = document.createElement("input");
    inputId.name = "id";
    inputId.value = id;

    let inputName = document.createElement("input");
    inputName.name = "borrowerName";
    inputName.value = borrowerName;

    let inputBorrowId = document.createElement("input");
    inputBorrowId.name = "borrowerId";
    inputBorrowId.value = borrowerId;

    let submit = document.createElement("input");
    submit.type = "hidden";
    submit.name = "borrow";

    form.appendChild(inputId);
    form.appendChild(inputName);
    form.appendChild(inputBorrowId);
    form.appendChild(submit);

    document.body.appendChild(form);
    form.submit();
}
</script>

</body>
</html>