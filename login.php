<?php include_once 'includes/header.php'; ?>
<link rel="stylesheet" href="styles/form.css" />
</head>
<body>
<?php include_once 'includes/navbar.php'; ?>

<div class="login-form">
    <h1>Login form</h1>
    <?php include_once 'includes/message.php'; ?>
    <form action="functions/login.php" method="post">
        <p>User Name</p>
        <input type="text" name="user" placeholder="User name" />
        <p>Password</p>
        <input
            type="password"
            name="password"
            placeholder="password"
        />
        <button type="submit" name="submit">Login</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>