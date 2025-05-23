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
    ],
    [
        "id" => 2,
        "name" => "ana",
    ],
    [
        "id" => 3,
        "name" => "luis",
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

(array) $logs = [];
(array) $currentlyUser = null;

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
            exit();
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
    global $currentlyUser;
    echo "Bem vindo " .  $currentlyUser['name'] . PHP_EOL;
    echo "1 - Vender" . PHP_EOL;
    echo "2 - Mostrar vendas" . PHP_EOL;
    echo "3 - Cadastrar novo cliente" . PHP_EOL;
    echo "4 - Verificar Logs" . PHP_EOL;
    echo "5 - Deslogar" . PHP_EOL;
    echo "0 - Sair" . PHP_EOL;
    $option = (int) readline("Escolha uma opção: ");
    switch ($option) {
        case 0: 
            exit();
        case 1:
            salesScreen();
            break;
        case 2:
            showSales();
            break;
        case 3:
            registerClientScreen();
            break;
        case 4:
            showLogs();
            break;
        case 5:
            logout();
            break;
        default:
            echo "Inválido";
            initialScreen();
            break;
    }
}

function salesScreen(){
    echo "Cadastrar nova venda" . PHP_EOL;
    $option = (string) readline("Digite o nome do produto: ");
    clientProductsRelationshipScreen($option);
}

function clientProductsRelationshipScreen(string $products){
    global $clients;
    echo "Clientes: " . PHP_EOL;
    for ($i=1; $i <= count($clients); $i++) { 
        echo $i . " - " . $clients[$i - 1]["name"] . PHP_EOL;
    }
    $option = readline("Selecione o cliente: ");
    $result = sales($clients[$option - 1]["id"], $products);
    if($result['status'] == false){
        echo "Erro: " . $result['message'] . PHP_EOL;
        
    }else{
        echo $result['message'] . PHP_EOL;
    }
    return initialScreen();
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

function registerClientScreen(){
    $name = (string) readline("Nome: ");
    $result = registerClient($name);
    if(!$result['status']){
        echo "Erro: " . $result['message'] . PHP_EOL;
    }else{
        echo $result['message'] . PHP_EOL;
    }
    return initialScreen();
}

function showLogs(){
    global $logs;
    echo "--------------------" . PHP_EOL;
    echo "Logs:" .PHP_EOL;
    foreach($logs as $log){
        echo $log[0] . PHP_EOL; 
    }
    echo "--------------------" . PHP_EOL;
    readline("Escreva qualquer coisa para sair");
    return initialScreen();
}

function showSales(){
    global $sales;
    global $client;
    echo "Vendas:" .PHP_EOL;
    foreach($sales as $sale){
        echo "--------------------" . PHP_EOL;
        $clientData = getClientDataById($sale["client_id"]);
        echo "Cliente: " . $clientData["data"]["name"] . PHP_EOL;
        echo "Produtos: " . $sale["products"] . PHP_EOL;
        echo "Data: " . $sale["date"] . PHP_EOL; 
    }
    echo "--------------------" . PHP_EOL;
    readline("Escreva qualquer coisa para sair");
    return initialScreen();
}

function sales(int $clientId, string $products){
    global $logs;
    global $sales;
    $clientData = getClientDataById($clientId);
    if(!$clientData){
        array_push($logs, [(count($logs) + 1) . "- Tentativa falha registrar produto no cliente: " . $clientData["data"]["name"]]);
        return ["status" => false, "message" => "Cliente não existe"];
    }
    array_push($sales, ["id"=> count($sales), "client_id" => $clientId, "products" => $products, "date" => date('d/m/Y H:i:s')]);
    array_push($logs, [(count($logs) + 1) . "- Produtos ". $products . " registrados no cliente: " . $clientData["data"]["name"]]);
    return ["status" => true, "message" => "Produtos ". $products . " registrados com sucesso em " . $clientData["data"]["name"]];
}

function login(string $name, string $password){
    global $logs;
    global $currentlyUser;
    if($currentlyUser){
        array_push($logs, [(count($logs) + 1) . "- Tentativa falha de login de usuário já logado:  " . $name]);
        return ["status" => false, "message" => "Usuário já logado"];
    }
    
    $userData = getUserDataByName($name);
    if(!$userData["status"] || $password != $userData["data"]["password"]){
        array_push($logs, [(count($logs) + 1) . "- Tentativa falha de login de usuário:  " . $name]);
        return ["status" => false, "message" => "Nome ou senha errados"];
    }
    $currentlyUser = ["id"=>$userData["data"]["id"], "name"=>$userData["data"]["name"]];
    array_push($logs, [(count($logs) + 1) . "- Login de usuário: " . $name]);
    return ["status" => true, "message" => "Login bem sucedido"];
}

function logout(){
    global $currentlyUser;
    $currentlyUser = null;
    return initialScreen();
}

function register(string $name, string $password, string $passwordConfirm){
    global $logs; 
    global $users;
    if($password != $passwordConfirm){
        return ["status" => false, "message" => "Senhas não são iguais"];
    }
    $userData = getUserDataByName($name);
    if($userData['status']){
        array_push($logs, [(count($logs) + 1) . "- Tentativa falha de registrar usuário:  " . $name]);
        return ["status"=>false, "message" => "Esse usuário já existe" ];
    }
    array_push($users, ["id" => count($users) + 1, "name" => $name, "password" => $password]);
    array_push($logs, [(count($logs) + 1) . "- Registro de novo usuário:  " . $name]);
    return ["status" => true, "message" => "Usuário " . $name . " criado com sucesso."];
}

function getClientDataById(int $clientId){
    global $clients;
    $clientData = null;
    foreach($clients as $client){
        if($client['id'] == $clientId){
            $clientData = $client;
            break;
        }
    } 
    if(!$clientData) {
        
        return ["status" => false, "message" => "Cliente não existe"];
    }
    return ["status" => true, "data"=> $clientData];
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

function getClientDataByName(string $name){
    global $clients;
    $clientData = null;
    foreach($clients as $client){
        if($client['name'] == $name){
            $clientData = $client;
            break;
        }
    } 
    if(!$clientData) {
        return ["status" => false, "message" => "Cliente não existe"];
    }
    return ["status" => true, "data"=> $clientData];
}



function registerClient(string $name){
    global $logs;
    global $clients;
    $clientData = getClientDataByName($name);
    if($clientData['status']){
        array_push($logs, [(count($logs) + 1) . "- Tentativa falha de registrar cliente:  " . $name]);
        return ["status"=>false, "message" => "Esse usuário já existe" ];
    }
    array_push($clients, ["id" => count($clients) + 1, "name" => $name, ]);
    array_push($logs, [(count($logs) + 1) . "- Registro de novo cliente:  " . $name]);
    return ["status" => true, "message" => "Cliente " . $name . " criado com sucesso."];
}

initialScreen();
