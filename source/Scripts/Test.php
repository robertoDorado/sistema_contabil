<?php
$data = [
    "nome_empresa" => "Empresa ABC", // seu nome de empresa
    "tipo_inscricao" => 2, // 1 para cpf, 2 cnpj 
    "numero_inscricao"=> "06.964.474/0001-30", // seu cpf ou cnpj completo
    "agencia" => "1234", // agencia sem o digito verificador 
    "agencia_dv" => 1, // somente o digito verificador da agencia 
    "conta" => "12345", // número da conta
    "conta_dv" => 1, // digito da conta
    "codigo_beneficiario" => "123456", // codigo fornecido pelo banco
    "numero_sequencial_arquivo" => 1, // sequencial do arquivo um numero novo para cada arquivo gerado
    "detalhe" => [
        'codigo_movimento' => 1, //1 = Entrada de título, para outras opçoes ver nota explicativa C004 manual Cnab_SIGCB na pasta docs
        'nosso_numero' => 50, // numero sequencial de boleto
        'seu_numero' => 43, // se nao informado usarei o nosso numero 
    
        /* campos necessarios somente para itau e siccob,  não precisa comentar se for outro layout    */
        'carteira_banco' => 109, // codigo da carteira ex: 109,RG esse vai o nome da carteira no banco
        'cod_carteira' => "01", // I para a maioria ddas carteiras do itau
        /* ----------------------------------------------------------------------------------------    */
    
        'especie_titulo' => "DM", // informar dm e sera convertido para codigo em qualquer laytou conferir em especie.php
        'valor' => "100.00", // Valor do boleto como float valido em php
        'emissao_boleto' => 2, // tipo de emissao do boleto informar 2 para emissao pelo beneficiario e 1 para emissao pelo banco
        'protestar' => 3, // 1 = Protestar com (Prazo) dias, 3 = Devolver após (Prazo) dias
        'prazo_protesto' => 5, // Informar o numero de dias apos o vencimento para iniciar o protesto
        'nome_pagador' => "JOSÉ da SILVA ALVES", // O Pagador é o cliente, preste atenção nos campos abaixo
        'tipo_inscricao' => 1, //campo fixo, escreva '1' se for pessoa fisica, 2 se for pessoa juridica
        'numero_inscricao' => '06.964.474/0001-30', //cpf ou ncpj do pagador
        'endereco_pagador' => 'Rua dos developers,123 sl 103',
        'bairro_pagador' => 'Bairro da insonia',
        'cep_pagador' => '12345-123', // com hífem
        'cidade_pagador' => 'Londrina',
        'uf_pagador' => 'PR',
        'data_vencimento' => '2016-04-09', // informar a data neste formato
        'data_emissao' => '2016-04-09', // informar a data neste formato
        'vlr_juros' => 0.15, // Valor do juros de 1 dia'
        'data_desconto' => '2016-04-09', // informar a data neste formato
        'vlr_desconto' => '0', // Valor do desconto
        'baixar' => 1, // codigo para indicar o tipo de baixa '1' (Baixar/ Devolver) ou '2' (Não Baixar / Não Devolver)
        'prazo_baixar'  => 90, // prazo de dias para o cliente pagar após o vencimento
        'mensagem' => 'JUROS de R$0,15 ao dia' . PHP_EOL . "Não receber apos 30 dias",
        'email_pagador' => 'rogerio@ciatec.net', // data da multa
        'data_multa' => '2016-04-09', // informar a data neste formato, // data da multa
        'vlr_multa' => 30.00, // valor da multa
    
        // campos necessários somente para o sicoob
        'taxa_multa' => 30.00, // taxa de multa em percentual
        'taxa_juros' => 30.00, // taxa de juros em percentual
    ]
];

echo json_encode($data) . PHP_EOL;