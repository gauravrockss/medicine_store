<div class="<?= isset($_GET['error']) ? 'error' : 'success' ?>">
    <?php
        if(isset($_GET['error'])) echo $_GET['error'];
        if(isset($_GET['success'])) echo $_GET['success'];
    ?>
</div>