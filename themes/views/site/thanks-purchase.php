<?php $v->layout("admin/layouts/_scripts") ?>

<div class="thanks-container-purchase">
    <p><i class="fa-solid fa-circle-check"></i></p>
    <h1>Obrigado pela sua compra!</h1>
    <p><?= $message ?></p>
    <a href="<?= url("/admin/login") ?>">Faça login agora mesmo</a>
</div>