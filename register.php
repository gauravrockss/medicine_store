<?php



include_once 'includes/header.php'; ?>
<link rel="stylesheet" href="styles/form.css" />
</head>
<body>

<?php include_once 'includes/navbar.php'; ?>
    <div class="login-form">
        <h1>Register Form</h1>
        <?php include_once 'includes/message.php'; ?>
        <form action="functions/register.php" method="post">
        <p>User Name</p>
            <input type="text" name="user" placeholder="User name" required />
            <p>Password</p>
            <input type="password" name="password" placeholder="password" required />
            <p>email</p>
            <input type="text" name ="email" placeholder = "Email" required />
            <p>Firstname</p>
            <input type="text" name="firstname" placeholder="firstname"required  />
            <p>Lastname</p>
            <input type="text" name="lastname" placeholder="lastname"required  />
            <p>DOB</p>
            <input type="text" name="dob" placeholder="DOB"required  />
            <p>Phone number</p>
            <input type="text" name="Phonenumber" placeholder="Phonenumber" required />
            <p>Address</p>
            <input type="text" name="address" placeholder="address"required >
            <button type="submit" name="submit">Register</button>
        </form>
    </div>
<?php include_once 'includes/footer.php'; ?>