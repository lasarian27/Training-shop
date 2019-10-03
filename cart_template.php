<?php require_once 'common.php'; ?>
<table class="table"> 
    <thead>
        <tr>
            <th scope="col"> <?= translate('image') ?> </th>
            <th scope="col"> <?= translate('name') ?> </th>
            <th scope="col"> <?= translate('description') ?> </th>
            <th scope="col"> <?= translate('price') ?> </th>
            <th scope="col"> <?= translate('actions') ?> </th>
        </tr>
    </thead>
    <tbody>
        <!-- Generate the code for each product -->
        <?php foreach($products as $product): ?>
            <!-- Skip the product if its already in $_SESSION['cart'] -->
            <?php $condition = $pageName === 'index' ? !in_array($product['id'], $cart) : true; ?>
            <?php if($condition): ?>
                <tr>
                    <td><img src="<?= $product['image']?>" style="width:50px; height:50px;"/></td>
                    <td><?= $product['title'] ?></td>
                    <td><?= $product['description'] ?></td>
                    <td><?= $product['price'] ?>$</td>
                    <!-- Depends on the page print specific code -->
                    <?php if($pageName === "index"): ?>
                        <td><a href="index.php?action=add&id=<?= $product['id'] ?>" class="btn btn-primary"><?= translate('add') ?></a></td>
                    <?php endif ?>
                    
                    <?php if($pageName === "products"): ?>
                        <td><a href="product.php?action=edit&id=<?= $product['id'] ?>" class="btn btn-info"><?= translate('edit') ?></a></td>
                        <td><a href="product.php?action=delete&id=<?= $product['id'] ?>&image=<?= $product['image'] ?>" class="btn btn-danger"><?= translate('delete') ?></a></td>
                    <?php endif ?>

                    <?php if($pageName === "cart"): ?>
                        <td><a href="cart.php?action=remove&id=<?= $product['id'] ?>" class="btn btn-danger"><?= translate('remove') ?></a></td>
                    <?php endif ?>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
    </tbody>
</table>

<?php if($pageName === "index"): ?>
    <a href="cart.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('go_to_cart') ?> </a>

    <?php if(isset($_SESSION['loggedin'])): ?>
        <a href="index.php?action=logout" class="btn btn-dark" style="margin: 10px;"> <?= translate('logout') ?> </a>

        <?php if(isset($_SESSION['admin'])): ?>
            <a href="products.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('products') ?> </a>
        <?php endif ?>

    <?php else: ?>
        <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('login') ?> </a>
    <?php endif ?>
<?php endif ?>

<?php if($pageName === "products"): ?>
    <a href="product.php?action=create" class="btn btn-primary" style="margin: 10px;"> <?= translate('add') ?> </a>

    <?php if(isset($_SESSION['loggedin'])): ?>
        <a href="index.php?action=logout" class="btn btn-dark" style="margin: 10px;"> <?= translate('logout') ?> </a>
        <a href="index.php" class="btn btn-info" style="margin: 10px;"> <?= translate('go_home') ?> </a>

        <?php else: ?>
        <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('login') ?> </a>
    <?php endif ?>
<?php endif ?>
