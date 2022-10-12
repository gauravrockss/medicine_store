<?php 
include_once 'includes/header.php';
include_once 'includes/verify.php';
verify('admin');
?>
</head>
<link rel="stylesheet" href="styles/form.css" />
<body>
<?php include_once 'includes/navbar.php'; 
include_once "database/database.php";
$db = new DB();
$id = $_GET['id'];
$db->fetch('medicine',[
    'id' => $id
]);
$data = $db->getSingleRow();
?>
<div class="main-form">
    <h1>Updata medicine</h1>
    <form action="functions/update-medicine.php" method="post">
        <input name="id" value = "<?php echo $data['id'] ?>" hidden />
        <p>medicinename</p>
        <input type="text" name="name" value = "<?php echo $data['name'] ?>" placeholder="medicine name" />
        <p>price</p>
        <input type="text" name="price" value = "<?php echo $data['price'] ?>" placeholder="price" />
        <p>tablets or capsule</p>
        <input type="text" name="type"  value = "<?php echo $data['type'] ?>"placeholder="tablets or capsule">  
        <p>description</p>
        <input type="text" name="description" value = "<?php echo $data['description'] ?>" placeholder="description" />
        <p>manufacter</p>
        <input type="text" name="start" value = "<?php echo $data['manufacter'] ?>" placeholder="manufacter" />
        <p>expiry</p>
        <input type="text" name="end"  value = "<?php echo $data['expiry'] ?>"placeholder="expiry" />
        <button type="submit" name="update">Updata</button>
    </form>
</div>
<?php include_once 'includes/footer.php'; ?>