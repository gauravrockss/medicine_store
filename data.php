<?php
include_once 'includes/header.php';
include_once 'includes/verify.php';
include_once 'database/database.php';
$db = new DB();
$user = $_SESSION['user'];

$data = $db->fetch('users',[
  'username' => $user 
]);
$data = $db->getSingleRow();


if($data['usertype'] === "admin")
verify('admin');
else
verify('user');
?>
<link rel="stylesheet" href="styles/card.css" />
</head>
<body>

<?php include_once 'includes/navbar.php'; ?>
    <table style="margin: 24px;" cellspacing='20px' border = 2px>
        <tr>
            <th>id</th>
            <th>username</th>
            <th>password</th>
            <th>Email</th>
            <th>firstname</th>
            <th>lastname</th>
            <th>DOB</th>
            <th>phonenumber</th>
            <th>address</th>
      </tr>
   <?php /*   <tr> 
            <td><div class="card">
 <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Avatar" style="width:100%">
  <div class="container">
    <h4><b>John Doe</b></h4>
    <p>Architect & Engineer</p>
  </div>
</div></td>
      </tr> */ ?> 
      <tr>
      <?php
                echo "
                    <td> ".$data['id']." </td>
                    <td> ".$data['username']." </td>
                    <td> ".$data['password']." </td>
                    <td> ".$data['email']." </td>
                    <td> ".$data['firstname']." </td>
                    <td> ".$data['lastname']." </td>
                    <td> ".$data['dob']." </td>
                    <td> ".$data['phonenumber']." </td>
                    <td> ".$data['address']." </td>
                ";
            ?>
      </tr>
    </table>
<?php  include_once 'includes/footer.php'; ?>
