<?php $v->layout("admin/layouts/_scripts") ?>
<div class="register-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>Assinatura Mensal</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Faça a sua assinatura do sistema contabil por R$ 208,75 por mês.</p>

            <form action="#" method="post" id="subscriptionForm">
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_name) ? "" : $customerData->customer_name ?>" name="fullName" class="form-control" placeholder="Nome completo ou razão social">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_document) ? "" : $customerData->customer_document ?>" name="document" class="form-control" placeholder="CPF ou CNPJ">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3 date" data-date-format="dd/mm/yyyy">
                    <input type="text" value="<?= empty($customerData->birth_date) ? "" : date("d/m/Y", strtotime($customerData->birth_date)) ?>" name="birthDate" id="birthDate" class="form-control" placeholder="Aniversário">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-cake-candles"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <select name="gender" class="form-control">
                        <option <?= empty($customerData->customer_gender) ? "selected" : "" ?> value="" disabled selected>Selecione seu gênero</option>
                        <option <?= !empty($customerData->customer_gender) && $customerData->customer_gender == 1 ? "selected" : "" ?> value="1">Masculino</option>
                        <option <?= !empty($customerData) && $customerData->customer_gender == 0 ? "selected" : "" ?> value="0">Feminino</option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-venus-mars"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_email) ? "" : $customerData->customer_email ?>" name="email" class="form-control" placeholder="E-mail" <?= !empty($customerData->customer_email) ? "readonly" : "" ?>>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_zipcode) ? "" : $customerData->customer_zipcode ?>" name="zipcode" class="form-control" placeholder="CEP">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_address) ? "" : $customerData->customer_address ?>" name="address" class="form-control" placeholder="Endereço">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_number) ? "" : $customerData->customer_number ?>" name="number" class="form-control" placeholder="Número">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_neighborhood) ? "" : $customerData->customer_neighborhood ?>" name="neighborhood" class="form-control" placeholder="Bairro">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_city) ? "" : $customerData->customer_city ?>" name="city" class="form-control" placeholder="Cidade">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_state) ? "" : $customerData->customer_state ?>" name="state" class="form-control" placeholder="Estado">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->customer_phone) ? "" : $customerData->customer_phone ?>" name="phone" class="form-control" placeholder="Telefone fixo">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($customerData->cell_phone) ? "" : $customerData->cell_phone ?>" name="cellPhone" class="form-control" placeholder="Telefone celular">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <i class="fa fa-mobile"></i>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" value="<?= empty($userData) ? "" : $userData->user_nick_name ?>" name="userName" class="form-control" placeholder="Nome de usuário">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Senha">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="confirmPassword" class="form-control" placeholder="Confirme a senha">
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div id="cardMount"></div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Comprar assinatura</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>