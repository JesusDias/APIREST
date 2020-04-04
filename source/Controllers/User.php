<?php

namespace Source\Controller;

use Source\Models\User;
use Source\Models\Validations;

//sai da pasta Controller, sai da pasta source e pega o vendor
require_once "../../vendor/autoload.php";
//sai da pasta Controller e pega o config
require_once "../Config.php";



switch($_SERVER["REQUEST_METHOD"]){
    case "POST":
        //lê o que foi enviado e transforma em objeto
        $data = json_decode(\file_get_contents("php://input"),false);

        //vê se requisição não foi mandada vazia
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Nenhum dado informado"));
            exit;
        }

        //pega esse objeto e testa as posições validando cada uma
        //se alguma não passar, jogue no array de erros.
        $erros = array();
        if(!Validations::validationString($data->first_name)){
            array_push($erros, "Nome");
        }
        if(!Validations::validationString($data->last_name)){
            array_push($erros, "Sobrenome");
        }
        if(!Validations::validationEmail($data->email)){
            array_push($erros, "Email");
        }
        
        //Se houver erros mostre para o usuário quais são os erros e pare a aplicação
        if(count($erros) > 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Há campos inválidos", "fields" => $erros));
            exit;
        }
        //Unico lugar onde o DataLayer está atuando
        //===========================================================================
        $user = new User();
        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->save();
        //============================================================================

        //Se houver algum erro ao salvar no banco, me retorne a mensagem de erro
        //***Ao usar o DataLayer, temos métods disponíveis, tais como (save()) e o 
        //(fail()), usando o fail() se houver uma PDOexception ele ja pode nos retornar
        //a mensagem de erro */
        if($user->fail()){
            header("HTTP/1.1 500 Internal server Error");
            echo json_encode(array("response" => $user->fail()->getMessage()));
            exit;
        }
        header("HTTP/1.1 201 Created");
        echo \json_encode(array("response" => "Usuário criado com sucesso"));
    break;
    case "GET":
        header("HTTP/1.1 200 OK");
        $users = new User();
        if($users->find()->Count()>0){
            $return = array();
            foreach($users->find()->fetch(true) as $user){
                //tratamento dos dados vindos do banco
                array_push($return, $user->data());
            }
            echo json_encode(array("response" => $return));
        } else {
            echo json_encode(array("response" => "Nenhum usuário localizado"));
        }
    break;
    case "PUT":
        //pega pra mim o id que foi passado nessa requisiçao
        $userId = filter_input(INPUT_GET,"id");
        //Se o id não tiver sido passado mande essa resposta pro usuáro
        if(!$userId){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Nenhum id foi informado."));
            exit;
        }
        //Le o que veio na url e converte pra um objeto
        $data = json_decode(file_get_contents('php://input'), false);
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Nenhum dado informado."));
            exit;
        }

        //Se os dados foram passados então valida eles e se algum não passar
        //jogue no array de erros
        $erros = array();
        if(!Validations::validationString($data->first_name)){
            array_push($erros, "Nome Inválido");
        }
        if(!Validations::validationString($data->last_name)){
            array_push($erros, "Sobrenome Inválido");
        }
        if(!Validations::validationEmail($data->email)){
            array_push($erros, "Email Inválido");
        }

       //Se houver erros mostre para o usuário quais são os erros e pare a aplicação
       if(count($erros) > 0){
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("response" => "Há campos inválidos", "fields" => $erros));
        exit;
        }

        //===========================DATALAYER======================================
        //instancia e ja me retorna o usuário onde o id for igual ao informado na url
        //na variável $userId
        $user = (new User())->findById($userId);
        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->save();
        //==========================================================================

        
        //se houver alguma falha lance a mensagem
        if($user->fail()){
            header("HTTP/1.1 500 Internal server Error");
            echo json_encode(array("response" => $user->fail()->getMessage()));
            exit;
        }

        header("HTTP/1.1 201 Created");
        echo json_encode(array("response" => "Usuário atualizado com sucesso"));
    break;
    default:
        //definindo um cabeçalho
        header("HTTP/1.1 401 Unauthorized");
        echo \json_encode(array("response" => "Método não previsto na API")); 
    break;
}