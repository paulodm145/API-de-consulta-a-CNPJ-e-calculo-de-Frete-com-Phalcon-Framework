<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Url as UrlResolver;


$di = new FactoryDefault();

/** INjeta a Url da Aplicação */

$di->setShared('response', function () {
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');
  
        return $response;
    }
  );

$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    
    $url->setBaseUri('http://localhost/apifrete/phalcon');

    return $url;
});

// Injetando as diretivas na Aplicação
$app = new Micro($di);

/**Definindo as Rotas */

/* BUscar um CNPJ - Expressão regular para validação do Número do CNPJ*/
$app->get('/api/cnpj/{cnpj:([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})}',
    
    function ( $cnpj ) use ($app) {

            //echo $cnpj;
            /** Realizar consulta na API: https://receitaws.com.br/api  */
            /** https://www.receitaws.com.br/v1/cnpj/[cnpj] */

            $urlPesquisa = "https://www.receitaws.com.br/v1/cnpj/".$cnpj;

            /* Inicia a sessão cURL */
            $ch = curl_init();
                
            /*  Informa a URL onde será enviada a requisição */
            curl_setopt($ch, CURLOPT_URL, $urlPesquisa);
                
            /* Se true retorna o conteúdo em forma de string para uma variável */
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
            /* Envia a requisição */
            $result = curl_exec($ch);
                
            /* Finaliza a sessão */
            
            curl_close($ch);

            $empresa = json_decode($result);

            $retorno_empresa = array(
                 "empresa" => array(

                                    "cnpj" => $empresa->cnpj,
                                    "ultima_atualizacao" =>  $empresa->ultima_atualizacao,
                                    "abertura" =>  $empresa->abertura,
                                    "nome" =>  $empresa->nome,
                                    "fantasia" =>  $empresa->fantasia,
                                    "status" => $empresa->status,
                                    "tipo" => $empresa->tipo,
                                    "situacao" => $empresa->situacao,
                                    "capital_social" => $empresa->capital_social
                 ),
                 "endereco" =>array(
                                    "bairro" => $empresa->bairro,
                                    "logradouro" => $empresa->logradouro,
                                    "numero" =>$empresa->numero,
                                    "cep" => $empresa->cep,
                                    "municipio" => $empresa->municipio,
                                    "uf" => $empresa->uf,
                                    "complemento" => $empresa->complemento,
                 ),
                 "contato" => array(
                                    "telefone" => $empresa->telefone,
                                    "email" => $empresa->email
                 ),
                "atividade_principal" => $empresa->atividade_principal
                 );

            return json_encode( $retorno_empresa );

    }
);

/** CALCULO DE FRETE */
/**
 * URL para requisições deste desafio:
 * https://freterapido.com/api/external/embarcador/v1/quote-simulator
 * ○ CNPJ Remetente: 17.184.406/0001-74
 * ○ Token autenticação: c8359377969ded682c3dba5cb967c07b
 * ○ Código Plataforma: 588604ab3
 */
$app->post('/api/quote', function () use ($app){

    $dados = $app->request->getJsonRawBody();

    $listaVolumes = $dados->volumes;

    $arrayDados = array(
        "remetente" => array(
            "cnpj" => "17184406000174"
        ),
        "destinatario" => array(
            "tipo_pessoa" => 2,
            "cnpj_cpf" => "69111653000144",
            "inscricao_estadual" => "123456",
            "endereco" => array(
                "cep" => $dados->destinatario->endereco->cep
            )
            ),
        "volumes" => $listaVolumes
        ,
        "tipo_frete"  => 1,
        "codigo_plataforma" => "588604ab3",
        "token" => "c8359377969ded682c3dba5cb967c07b"
    );
    
    $data_string = json_encode( $arrayDados );                                                                                   

    // URL para onde será enviada a requisição GET
    $url_data = "https://freterapido.com/api/external/embarcador/v1/quote-simulator";
    
    // Inicia a sessão cURL
    $ch = curl_init();
    
    // Informa a URL onde será enviada a requisição
    curl_setopt( $ch, CURLOPT_URL, $url_data);
    
    // Seta a requisição como sendo do tipo POST
    curl_setopt ($ch, CURLOPT_POST, 1);
    
    // Monta os parâmetros da requisição
    $parametros =  $data_string ;
    
    // Seta os parâmetros para session cURL
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $parametros);
    
    // Se true retorna o conteúdo em forma de string para uma variável
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Envia a requisição
    $result = curl_exec($ch);
    
    // Finaliza a sessão
    curl_close($ch);

    /**retorna o json */
    $arrayRetornos = json_decode($result);

    $arrayMontarLista = array();

    foreach($arrayRetornos->transportadoras as $transportadora){

        $arrayDetalhe = array( "nome" => $transportadora->nome,
                               "servico" => $transportadora->servico,
                               "prazo_entrega" => $transportadora->prazo_entrega,
                               "preco_frete" => $transportadora->preco_frete
                            );

        array_push( $arrayMontarLista, $arrayDetalhe );

    }

    $arrayFinal = array("transportadoras" => $arrayMontarLista );
    return json_encode( $arrayFinal );

});

//Verifica a ocorrência de alguma excessão.
try {
    
    $app->handle();
    
} catch (\Exception $e) {

    $response = new Response();

    $response->setStatusCode(422, 'Conflict');
 

    
    return $response->send();
}




