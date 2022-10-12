
<?php 
include_once 'includes/header.php';
include_once 'includes/verify.php';
include_once "database/database.php";

verify('admin');
if(isset($_GET['message'])) echo $_GET['message'];
?>
</head>
<body>
<?php include_once 'includes/navbar.php'; 

$db = new DB();
$users  = $db->fetch('users');
$carts = $db->fetch('cart');
$total = 0;
?>

<table  border = 2px>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Email</th>
        <th>First name</th>
        <th>Last name</th>
        <th>DOB</th>
        <th>Phone number</th>
        <th>Address</th>
        <th>cart</th>
    </tr>
    <tr>
        
            <?php foreach($users as $user) { 
            if($user['usertype'] == 'user') { 
                $total++
            ?>
            
            <td> <?php echo $user['id'] ?> </td> 
            <td> <?php echo $user['username'] ?> </td> 
            <td> <?php echo $user['email'] ?> </td>  
            <td> <?php echo $user['firstname'] ?> </td> 
            <td> <?php echo $user['lastname'] ?> </td> 
            <td> <?php echo $user['dob'] ?> </td> 
            <td> <?php echo $user['phonenumber'] ?> </td> 
            <td> <?php echo $user['address'] ?> </td> 
            <td>
                <table>
                <tr>
                    <th>Product name</th>
                    
                    <th>quantity</th>
                </tr>

                
            <?php foreach($carts as $cart) { 
            if($user['id'] == $cart['user_id']) {  ?>

                <tr>
                    <td> <?php echo $cart['name'] ?> </td> 
                    <td> <?php echo $cart['quantity'] ?> </td> 
                    
                </tr>
                <?php } ?>
                <?php } ?>
            </td>

            </table>
            <?php } ?>   
            
    </tr>
        <?php } ?>
        <tr>
                <td colspan = "7"> total user</td>
                <td><?php echo $total ?></td>
            </tr>
</table>










