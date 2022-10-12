<?php
include_once 'includes/header.php';
include_once "database/database.php";

$role = $_SESSION['role'] ?? '';
$db = new DB();

if(isset($_POST['type']) && $_POST['type']) {    
    $medicines = $db->select('medicine', [
        'type' => $_POST['type']
    ]);
} else {
    $medicines = $db->fetch('medicine');
}

$chunks = array_chunk($medicines, 4);
?>
<link rel="stylesheet" href="styles/card.css" />
<style>
    td {
        text-align : right;
    }
    a td {
        color:hotpink;
    }
</style>
</head>

<body>
<?php include_once 'includes/navbar.php'; ?>
<form action="medicine.php" method ="post">
    <label for="choose">Choose medicine type</label>
    <select name="type">
        <option value="">All</option>
        <?php foreach(['tablets', 'capsule'] as $type) { ?>
            <option value="<?= $type ?>" <?php if($type === @$_POST['type']) echo 'selected' ?>><?= ucfirst($type) ?></option>
        <?php } ?>
    </select>
    
    <input type="submit">
</form>
    <table>
        <?php foreach($chunks as $medicines) { ?>
            <tr>
                <?php foreach($medicines as $medicine) { ?>
                    <td style="text-align: right">
                        <div class="card">
                            <img src="picture/medicine.jpg" alt="Avatar" style="width : 100%">
                            <div class="container">
                                <h4><b><?= $medicine['name']; ?></b></h4>   
                                <table>
                                    <tr>
                                        <th>price:</th>
                                        <td><?= $medicine['price']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>type:</th>
                                        <td><?= $medicine['type']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>description:</th>
                                        <td> <?= $medicine['description']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>manufacter:</th>
                                        <td><?= $medicine['manufacter']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>expiry:</th>
                                        <td><?= $medicine['expiry']; ?></td>
                                    </tr>
                                </table>
                                <?php if($role === 'admin') { ?>
                                    <a href="updata-medicine.php?id=<?= $medicine['id']?>">UPDATA</a><br>
                                <?php } ?>
                                <?  else ?>
                                <form action="functions/add-to-card.php" method= "post">
                                    <input name="id" value="<?= $medicine['id']?>" hidden>
                                    <input name="name" value="<?= $medicine['name']?>" hidden>
                                    <?php if($role === 'user') { ?>
                                    <button>Add to card</button>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
<?php include_once 'includes/footer.php'; 

?>