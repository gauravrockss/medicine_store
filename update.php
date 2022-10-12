<?php include_once 'includes/header.php'; ?>
<?php
include_once 'database/database.php';
include_once 'includes/verify.php';
$db = new DB();
$id = $_SESSION['user'];
$db->fetch('users',[
    'username' => $id
]);
$data = $db->getSingleRow();
if($data['usertype'] === "admin")
verify('admin');
else
verify('user');
?>

?>
<link rel="stylesheet" href="styles/form.css" />
</head>
<body>
<?php include_once 'includes/navbar.php'; ?>
    <div class="login-form">
        <h1>Updata form</h1>
        <?php include_once 'includes/message.php'; ?>
        <form action="functions/update.php" method="post">
        <p>User Name</p>
            <input type="text" name="user"  value = "<?php echo $data['username']; ?>" placeholder="User name" />
            <p>Password</p>
            <input type="password" name="password" value = "<?php echo $data['password']; ?>" placeholder="password" />
            <p>email</p>
            <input type="text" name ="email"value = "<?php echo $data['email']; ?>" placeholder = "Email"/>
            <p>Firstname</p>
            <input type="text" name="firstname" value = "<?php echo $data['firstname']; ?>"placeholder="firstname" />
            <p>Lastname</p>
            <input type="text" name="lastname" value = "<?php echo $data['lastname']; ?>"placeholder="lastname" />
            <p>DOB</p>
            <input type="text" name="dob" value = "<?php echo $data['dob']; ?>"placeholder="DOB" />
            <p>Phone number</p>
            <input type="text" name="Phonenumber" value = "<?php echo $data['phonenumber']; ?>"placeholder="Phonenumber" />
            <p>Address</p>
            <input type="text" name="address"value = "<?php echo $data['address']; ?>" placeholder="address">
            <button type="submit" name="update">Updata</button>
        </form>
    </div>
<?php include_once 'includes/footer.php'; ?>