<?php
require_once DIR . '/common.php';

$pageName = getPageName();
?>
<?php if (!empty($products)) : ?>
    <table class="table">
        <thead>
        <tr>
            <th><?= validation(translate('image')) ?></th>
            <th><?= validation(translate('name')) ?></th>
            <th><?= validation(translate('description')) ?></th>
            <th><?= validation(translate('price')) ?></th>
            <th><?= validation(translate('actions')) ?></th>
        </tr>
        </thead>
        <tbody>

        <!-- Generate the code for each product -->
        <?php foreach ($products as $product): ?>

            <!-- Skip the product if its already in $_SESSION['cart'] -->
            <?php $condition = $pageName === 'index' ? !in_array($product['id'], $_SESSION['cart']) : true; ?>
            <?php if ($condition): ?>
                <tr>
                    <td><img src="<?= validation($product['image']) ?>" style="width:50px; height:50px;"/></td>
                    <td><?= validation($product['title']) ?></td>
                    <td><?= validation($product['description']) ?></td>
                    <td><?= validation($product['price']) ?>$</td>

                    <!-- Depends on the page print specific code -->
                    <?php if ($pageName === "index"): ?>
                        <td>
                            <a href="index.php?action=add&id=<?= validation($product['id']) ?>" class="btn btn-primary">
                                <?= validation(translate('add')) ?>
                            </a>
                        </td>
                    <?php endif ?>

                    <?php if ($pageName === "products"): ?>
                        <td>
                            <a href="product.php?action=edit&id=<?= validation($product['id']) ?>" class="btn btn-info">
                                <?= validation(translate('edit')) ?>
                            </a>
                        </td>
                        <td>
                            <a href="product.php?action=delete&id=<?= validation($product['id']) ?>&image=<?= validation($product['image']) ?>" class="btn btn-danger">
                                <?= validation(translate('delete')) ?>
                            </a>
                        </td>
                    <?php endif ?>

                    <?php if ($pageName === "cart"): ?>
                        <td>
                            <a href="cart.php?action=remove&id=<?= $product['id'] ?>" class="btn btn-danger">
                                <?= validation(translate('remove')) ?>
                            </a>
                        </td>
                    <?php endif ?>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($pageName === "index"): ?>
    <a href="cart.php" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('go.to.cart')) ?> </a>

    <?php if (isset($_SESSION['login'])): ?>
        <a href="index.php?action=logout" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('logout')) ?> </a>

        <?php if (isset($_SESSION['admin'])): ?>
            <a href="products.php" class="btn btn-dark" style="margin: 10px;">
                <?= validation(translate('products')) ?>
            </a>
        <?php endif ?>

    <?php else: ?>
        <a href="login.php" class="btn btn-dark" style="margin: 10px;">
            <?= validation(translate('login')) ?>
        </a>
    <?php endif ?>
<?php endif ?>

<?php if ($pageName === "products"): ?>
    <a href="product.php?action=create" class="btn btn-primary" style="margin: 10px;">
        <?= validation(translate('add')) ?>
    </a>

    <?php if (isset($_SESSION['login'])): ?>
        <a href="index.php?action=logout" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('logout')) ?> </a>
        <a href="index.php" class="btn btn-info" style="margin: 10px;"> <?= validation(translate('go.home')) ?> </a>

    <?php else: ?>
        <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('login')) ?> </a>
    <?php endif ?>
<?php endif ?>
