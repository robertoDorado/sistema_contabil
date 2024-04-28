<?php $v->layout("admin/layouts/_admin") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Formulário de atualização de dados do cliente</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url("/admin") ?>">Página Inicial</a></li>
                        <li class="breadcrumb-item active">Formulário de atualização de dados do cliente</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Formulário do cliente</h3>
                        </div>
                        <form id="customerUpdateForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="fullName">Nome do cliente</label>
                                    <input type="text" name="fullName" value="<?= !empty($customerData->customer_name) ? $customerData->customer_name : "" ?>" id="fullName"class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="userName">Nome de usuário</label>
                                    <input type="text" name="userName" value="<?= !empty(session()->user->user_nick_name) ? session()->user->user_nick_name : "" ?>" id="userName"class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="document">CPF ou CNPJ</label>
                                    <input type="text" name="document" value="<?= !empty($customerData->customer_document) ? $customerData->customer_document : "" ?>" id="document" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="birthDate">Aniversário</label>
                                    <input type="text" name="birthDate" value="<?= !empty($customerData->birth_date) ? date("d/m/Y", strtotime($customerData->birth_date)) : "" ?>" id="birthDate" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gênero</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="" disabled>Qual é o seu gênero?</option>
                                        <option value="1" <?= !empty($customerData->customer_gender) && $customerData->customer_gender == 1 ? "selected" : "" ?>>Masculino</option>
                                        <option value="0" <?= !empty($customerData) && $customerData->customer_gender == 0 ? "selected" : "" ?>>Feminino</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="text" name="email" value="<?= !empty($customerData->customer_email) ? $customerData->customer_email : "" ?>" id="email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="zipcode">CEP</label>
                                    <input type="text" name="zipcode" value="<?= !empty($customerData->customer_zipcode) ? $customerData->customer_zipcode : "" ?>" id="zipcode" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="address">Endereço</label>
                                    <input type="text" name="address" value="<?= !empty($customerData->customer_address) ? $customerData->customer_address : "" ?>" id="address" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="number">Número do endereço</label>
                                    <input type="text" name="number" value="<?= !empty($customerData->customer_number) ? $customerData->customer_number : "" ?>" id="number" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="neighborhood">Bairro</label>
                                    <input type="text" name="neighborhood" value="<?= !empty($customerData->customer_neighborhood) ? $customerData->customer_neighborhood : "" ?>" id="neighborhood" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="city">Cidade</label>
                                    <input type="text" name="city" value="<?= !empty($customerData->customer_city) ? $customerData->customer_city : "" ?>" id="city" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="state">Estado</label>
                                    <input type="text" name="state" value="<?= !empty($customerData->customer_state) ? $customerData->customer_state : "" ?>" id="state" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Telefone</label>
                                    <input type="text" name="phone" value="<?= !empty($customerData->customer_phone) ? $customerData->customer_phone : "" ?>" id="phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="cellPhone">Celular</label>
                                    <input type="text" name="cellPhone" value="<?= !empty($customerData->cell_phone) ? $customerData->cell_phone : "" ?>" id="cellPhone" class="form-control">
                                    <input type="hidden" name="csrfToken" value="<?= session()->csrf_token ?>">
                                </div>
                                <div class="form-group">
                                    <label for="password">Senha</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirme a sua senha</label>
                                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-control">
                                </div>
                            </div>
    
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>