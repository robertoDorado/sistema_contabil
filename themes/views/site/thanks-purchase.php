<?php $v->layout("admin/layouts/_scripts") ?>
<!-- Event snippet for Cliente assinou o sistema conversion page -->
<script>
  gtag('event', 'conversion', {'send_to': 'AW-17009805739/2DmNCP2HuLkaEKuT9K4_'});
</script>


<div class="thanks-container-purchase">
    <p><i class="fa-solid fa-circle-check"></i></p>
    <h1>Obrigado pela sua compra!</h1>
    <p><?= $message ?></p>
    <a href="<?= url("/admin/login") ?>">Faça login agora mesmo</a>
</div>