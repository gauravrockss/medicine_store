<?php
include_once 'includes/header.php'; 
include_once 'database/database.php';

$user_id = $_SESSION['id'];

$db = new DB();

$medicines = $db->fire("SELECT * FROM `cart` INNER JOIN `medicine` WHERE cart.product_id=medicine.id AND cart.user_id='$user_id' AND cart.status = 0");
$total = 0;
?>
<body>
    
<?php include_once 'includes/navbar.php'; ?>
        <h1 class =" heading"> My Card</h1>
        <table border = "2px">
            <tr>
                <th>Product name</th>
                <th>price</th>
                <th>type</th>
                <th>quantity</th>
                <th>Total</th>
                <th>delete</th>
                
            </tr>
            <?php foreach($medicines as $medicine) { 
                $total = $total + $medicine['price'] * $medicine['quantity']; ?>
                <tr>
                    <td><?= $medicine['name']?></td>
                    <td><?= $medicine['price'] ?></td>
                    <td><?= $medicine['type']?></td>
                    <td>
                        <form action="functions/quantity-update.php" method = "post">
                        <input  type="number" name = 'qty' value = "<?= $medicine['quantity'] ?>" min = '1' max = '10' style = "width:40px" >
                        <input name="id" value="<?= $medicine['id']?>" hidden>
                        
                        <button>Update</button></td>
                        </form>
                    <td><?= $medicine['price'] * $medicine['quantity']  ?></td>
                    <td><a href="functions/delete-cart.php?id=<?= $medicine['id'] ?>">Delete</a><td>  <!-- Flaw -->
                    <?php } ?>
                </tr>
            
            
            <tr>
                <td colspan = "4"> Sub total</td>
                <td><?php echo $total ?></td>
            </tr>
            <tr>
                <td colspan = "4"> GST(18%) </td>
                <td><?php  echo  (18*$total)/100 ;   ?></td>
            </tr>
            <tr>
                <td colspan = "4"> Total</td>
                <td><?php  echo (18*$total)/100 + $total ?></td>
            </tr>
            <?php    
            if(count($medicines) > 0) { ?> 
            <tr>
                <td colspan = "5">
                    <form action="functions/place_order.php" method="post">
                        <label>Address</label>
                        <input type="text" name="address" value="">

                        <label >cash on delevery</label>
                        <input type="radio" value="cash on delevery" name="mode">
                        
                        <input type="submit" name="submit" value="Place Order">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php  if(isset($_GET['message'])) { ?>
    <h3> <?php echo $_GET['message'];} ?> </h3> 
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
