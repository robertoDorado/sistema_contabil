<?php $v->layout("admin/layouts/_scripts") ?>
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>Laborcode</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Faça login para iniciar a sessão</p>

      <form id="loginForm" action="#" method="post">
        <div class="input-group mb-3">
          <input type="text" value="<?= empty($_COOKIE["user_email"]) ? '' : $_COOKIE["user_email"] ?>" class="form-control" name="userData" placeholder="E-mail ou usuário">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" value="<?= empty($_COOKIE["user_password"]) ? '' : $_COOKIE["user_password"] ?>" class="form-control" name="userPassword" placeholder="Senha">
          <input type="hidden" name="csrfToken" value="<?= session()->csrf_token ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <select name="userType" id="userTyoe" class="form-control">
            <option value="" disabled selected>Selecione o tipo de usuário</option>
            <option value="0">Cliente</option>
            <option value="1">Suporte</option>
          </select>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input name="remember" type="checkbox" id="remember">
              <label for="remember">
                Lembrar-me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4 ml-auto">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </div>
          <!-- /.col -->
        </div>
        <a href="<?= url("/customer/subscribe") ?>">Faça uma assinatura</a>
      </form>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->