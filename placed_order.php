<?php 
include_once 'includes/header.php';
include_once 'includes/verify.php';

verify('user'); ?>
</head>
<body>
<?php include_once 'includes/navbar.php'; 

include_once "database/database.php"; 

$db = new DB();

$name = $_SESSION['user'];

$data = $db->fetch('order_user'); ?>
<h1 style= "text-align:center">My Order</h1>
<table  border = 2px>
    <tr>
        <th>product order</th>
        <th>Quantity</th>
    </tr>
    <?php foreach($data as $data) {  ?>
    <tr>
        <td><?= $data['product_ids'] ?> </td>
        <td><?= $data['quantity']?></td>
    </tr> 
    <?php } ?>  

    
</table>

