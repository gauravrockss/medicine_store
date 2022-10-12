<?php 
include_once 'includes/header.php';
include_once 'includes/verify.php';

verify('admin');
if(isset($_GET['message'])) echo $_GET['message'];
?>
</head>
<body>
<?php include_once 'includes/navbar.php'; 
?>
<div class="main-form">
    <h1>Insert medicine</h1>
    <form action="functions/insert-medicine.php" method="post">
        <p>medicinename</p>
        <input type="text" name="mname" placeholder="medicine name" />
        <p>price</p>
        <input type="text" name="price" placeholder="price" />
        <p>tablets or capsule</p>
        <input type="text" name="type" placeholder="tablets or capsule">  
        <p>description</p>
        <input type="text" name="description" placeholder="description" />
        <p>manufacter</p>
        <input type="text" name="start" placeholder="manufacter" />
        <p>expiry</p>
        <input type="text" name="end" placeholder="expiry" />
        <button type="submit" name="submit">submit</button>
    </form>
</div>
<?php include_once 'includes/footer.php'; ?>