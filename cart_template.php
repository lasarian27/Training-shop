<?php require_once 'common.php'; ?>
<?php
$pageName = getPageName();
?>
<?php if (!empty($products)): ?>
    <table class="table">
        <thead>
        <tr>
            <th scope="col"> <?= ht(translate('image')) ?> </th>
            <th scope="col"> <?= ht(translate('name')) ?> </th>
            <th scope="col"> <?= ht(translate('description')) ?> </th>
            <th scope="col"> <?= ht(translate('price')) ?> </th>
            <th scope="col"> <?= ht(translate('actions')) ?> </th>
        </tr>
        </thead>
        <tbody>

        <!-- Generate the code for each product -->
        <?php foreach ($products as $product): ?>

            <!-- Skip the product if its already in $_SESSION['cart'] -->
            <?php $condition = $pageName === 'index' ? !in_array($product['id'], $_SESSION['cart']) : true; ?>
            <?php if ($condition): ?>
                <tr>
                    <td><img src="<?= ht($product['image']) ?>" style="width:50px; height:50px;"/></td>
                    <td><?= ht($product['title']) ?></td>
                    <td><?= ht($product['description']) ?></td>
                    <td><?= ht($product['price']) ?>$</td>

                    <!-- Depends on the page print specific code -->
                    <?php if ($pageName === "index"): ?>
                        <td><a href="index.php?action=add&id=<?= ht($product['id']) ?>"
                               class="btn btn-primary"><?= ht(translate('add')) ?></a></td>
                    <?php endif ?>

                    <?php if ($pageName === "products"): ?>
                        <td><a href="product.php?action=edit&id=<?= ht($product['id']) ?>"
                               class="btn btn-info"><?= ht(translate('edit')) ?></a></td>
                        <td><a href="product.php?action=delete&id=<?= ht($product['id']) ?>&image=<?= ht($product['image']) ?>"
                               class="btn btn-danger"><?= ht(translate('delete')) ?></a></td>
                    <?php endif ?>

                    <?php if ($pageName === "cart"): ?>
                        <td><a href="cart.php?action=remove&id=<?= $product['id'] ?>"
                               class="btn btn-danger"><?= ht(translate('remove')) ?></a></td>
                    <?php endif ?>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($pageName === "index"): ?>
    <a href="cart.php" class="btn btn-dark" style="margin: 10px;"> <?= ht(translate('go_to_cart')) ?> </a>

    <?php if (isset($_SESSION['login'])): ?>
        <a href="index.php?action=logout" class="btn btn-dark" style="margin: 10px;"> <?= ht(translate('logout')) ?> </a>

        <?php if (isset($_SESSION['admin'])): ?>
            <a href="products.php" class="btn btn-dark" style="margin: 10px;"> <?= ht(translate('products')) ?> </a>
        <?php endif ?>

    <?php else: ?>
        <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= ht(translate('login')) ?> </a>
    <?php endif ?>
<?php endif ?>

<?php if ($pageName === "products"): ?>
    <a href="product.php?action=create" class="btn btn-primary" style="margin: 10px;"> <?= ht(translate('add')) ?> </a>

    <?php if (isset($_SESSION['login'])): ?>
        <a href="index.php?action=logout" class="btn btn-dark" style="margin: 10px;"> <?= ht(translate('logout')) ?> </a>
        <a href="index.php" class="btn btn-info" style="margin: 10px;"> <?= ht(translate('go_home')) ?> </a>

    <?php else: ?>
        <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= ht(translate('login')) ?> </a>
    <?php endif ?>
<?php endif ?>
