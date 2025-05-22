<?php

$users = [
    [
        "id" => 1,
        "name" => "eu",
        "password" => "123456"
    ],
    [
        "id" => 2,
        "name" => "jorge",
        "password" => "123456"
    ],
    [
        "id" => 3,
        "name" => "vinicius",
        "password" => "123456"
    ]
];

$clients = [
    [
        "id" => 1,
        "name" => "pedro",
        "password" => "123456"
    ],
    [
        "id" => 2,
        "name" => "ana",
        "password" => "123456"
    ],
    [
        "id" => 3,
        "name" => "luis",
        "password" => "123456"
    ]
];

$sales = [
    [
        "id" => 1,
        "client_id" => 2,
        "products" => "beringela",
        "date" => "22/05/2025 14:35:08"
    ],
    [
        "id" => 2,
        "client_id" => 3,
        "products" => "carne",
        "date" => "22/06/2025 11:30:12"
    ],
    [
        "id" => 3,
        "client_id" => 3,
        "products" => "arroz, feijao, brocolis",
        "date" => "21/05/2025 14:25:08"
    ]
];

$currentlyUser = null;

function initialScreen(){
    global $currentlyUser;
    if(!$currentlyUser){
        unLoggedScreen();
    }
    loggedScreen();
}

function unLoggedScreen(){
    echo "Você está deslogado, faça login ou registre-se para usar o sistema." . PHP_EOL;
    echo "1 - Login" . PHP_EOL;
    echo "2 - Registre-se" . PHP_EOL;
    echo "0 - Sair" . PHP_EOL;
    $option = (int) readline("Escolha uma opção: ");
    switch ($option) {
        case 0: 
            return;
        case 1:
            loginScreen();
            break;
        case 2:
            registerScreen();
            break;
        default:
            echo "Inválido";
            initialScreen();
            break;
    }
}

function loggedScreen(){
    echo "Você está deslogado, faça login ou registre-se para usar o sistema." . PHP_EOL;
    echo "1 - Vender" . PHP_EOL;
    echo "2 - Saida" . PHP_EOL;
    echo "3 - Cadastrar novo usuário" . PHP_EOL;
    echo "4 - Verificar Log" . PHP_EOL;
    echo "5 - Deslogar" . PHP_EOL;
    echo "0 - Sair" . PHP_EOL;
    $option = (int) readline("Escolha uma opção: ");
    switch ($option) {
        case 0: 
            return;
        case 1:
            loginScreen();
            break;
        case 2:
            registerScreen();
            break;
        default:
            echo "Inválido";
            initialScreen();
            break;
    }
}

function loginScreen(){
    $name = (string) readline("Nome: ");
    $password = (string) readline("Senha: ");
    $result = login($name,$password);
    if($result['status'] == false){
        echo "Erro: " . $result['message'] . PHP_EOL;
        
    }else{
        echo $result['message'] . PHP_EOL;
    }
    return initialScreen();
}

function registerScreen(){
    $name = (string) readline("Nome: ");
    $password = (string) readline("Senha: ");
    $passwordConfirm = (string) readline("Confirmar Senha: ");
    $result = register($name, $password, $passwordConfirm);
    if(!$result['status']){
        echo "Erro: " . $result['message'] . PHP_EOL;
    }else{
        echo $result['message'] . PHP_EOL;
    }
    return initialScreen();
}

function login(string $name, string $password){
    global $currentlyUser;
    if($currentlyUser){
        return ["status" => false, "message" => "Usuário já logado"];
    }
    
    $userData = getUserDataByName($name);
    if(!$userData["status"] || $password != $userData["data"]["password"]){
        return ["status" => false, "message" => "Nome ou senha errados"];
    }
    $currentlyUser = ["id"=>$userData["data"]["id"], "name"=>$userData["data"]["name"]];
    return ["status" => true, "message" => "Login bem sucedido"];
}

function register(string $name, string $password, string $passwordConfirm){
    global $users;
    if($password != $passwordConfirm){
        return ["status" => false, "message" => "Senhas não são iguais"];
    }
    $userData = getUserDataByName($name);
    if($userData['status']){
        return ["status"=>false, "message" => "Esse usuário já existe" ];
    }
    array_push($users, ["id" => count($users), "name" => $name, "password" => $password]);
    return ["status" => true, "message" => "Usuário " . $name . " criado com sucesso."];
}

function getUserDataByName(string $name){
    global $users;
    $userData = null;
    foreach($users as $user){
        if($user['name'] == $name){
            $userData = $user;
            break;
        }
    } 
    if(!$userData) {
        return ["status" => false, "message" => "Usuário não existe"];
    }
    return ["status" => true, "data"=> $userData];
}

initialScreen();
