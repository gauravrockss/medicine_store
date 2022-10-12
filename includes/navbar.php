<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;500&display=swap");
    * {
        margin: 0;
        font-family: "Poppins", sans-serif;
    }
    a:hover {
        color: rgba(14, 83, 8, 0.733);
    }
    a {
        color: rgb(77, 172, 216);
    }
</style>

<div class="navbar">
    <h1>Project 1</h1>
    <a href="medicine.php">list of medicine</a>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>

    <?php
    
if(isset($_SESSION['user']) && $_SESSION['user']) { 

    include_once"database/database.php";

    $db = new DB();
    
    $user_id = $_SESSION['id'];

    $db->fetch('users',[
        'id' => $user_id
    ]);
    
    if($db->getNumRows()) {
        $user = $db->getSingleRow();
    
    if($user['usertype'] == 'user') { ?>
        <a href="data.php">Profile</a> 
        <a href="functions/logout.php">Logout</a>
        <a href="update.php">Update</a>
        <a href="cart.php">My card</a>
        <a href="placed_order.php">My order</a>
        

    <?php  } elseif ($user['usertype'] == 'admin') { ?>
        <a href="insert-medicine.php">Insert medicine</a>
        <a href="user-information.php">User information</a>
        <a href="data.php">Profile</a> 
        <a href="functions/logout.php">Logout</a>
        <a href="update.php">Update</a>

    <?php  }
    }
}
?>
</div>
<div class="content"></div>

