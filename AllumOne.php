<?php

$pyra = array(1=>1);
$pyra_vide = array(1=>0);

function checkWin($player){
    require_once('vendor/autoload.php');
    $climate = new League\CLImate\CLImate;

    global $pyra;

    $i=0;
    foreach($pyra as $key => $ligne){
        if($ligne == 0){
            $i ++;
        }
    }

    if ($i == $key){
        if($player == 0){
            $climate->red()->draw('bender');
            exit (2);
        }else{
            $climate->green()->draw('404');
            exit (1);
        }
    }
}

function checkLine($line, $limit, $ligne){
    global $pyra;
    global $pyra_vide;

    echo "\e[33mCombien shoutez vous enlever d'alummette ? ($limit): \e[0m";
    $jeux = readline();
    if ($jeux > 0 && $jeux <= $limit && $jeux <= $pyra[$ligne]){
        $pyra[$ligne] = $pyra[$ligne] - $jeux;
        $pyra_vide[$ligne] += $jeux;
        echo "\e[106m\e[30mVous jouez a la ligne $ligne et enlever $jeux.\e[0m\n";
    }elseif($jeux == "stop"){
        exit;
    }else{
        echo "\e[31mEntré incorect veuillez un nombre entre 1 et $limit ou stop pour interompre \e[0m\n";
        checkLine($line, $limit, $ligne);
    }
}

function playerHuman($line, $limit){
    global $pyra;

    echo "\e[33mA quelle ligne sohaitez vous jouer ? ($line max): \e[0m";
    $ligne = readline();
    if ($ligne != 0 && $ligne <= $line && isset($pyra[$ligne]) && $pyra[$ligne] > 0){
        checkLine($line, $limit, $ligne);
    }elseif($ligne == "stop"){
        exit;
    }else{
        echo "\e[31mEntré incorect veuillez un nombre entre 1 et $line ou stop pour interompre \e[0m\n";
        playerHuman($line, $limit);
    }
}

function checkPNJLine($line, $limit, $ligne){
    global $pyra;
    global $pyra_vide;
    
    $jeux = rand(1,$limit);
    if ( $jeux <= $pyra[$ligne]){
        $pyra[$ligne] = $pyra[$ligne] - $jeux;
        $pyra_vide[$ligne] += $jeux;
        echo"\e[103m\e[30mPNJ joue la ligne $ligne et enléve $jeux allumette!\e[0m\n";
    }else{
        checkPNJLine($line, $limit, $ligne);
    }
}

function playerPNJ($line, $limit){
    global $pyra;
    
    $ligne = rand(1,$line);
    if (isset($pyra[$ligne]) && $pyra[$ligne] > 0){
        checkPNJLine($line, $limit, $ligne);
    }else{
        playerPNJ($line, $limit);
    }
}

function play($line, $limit){
    static $player = 0;

    if($player == 0){
        playerHuman($line, $limit);
        checkWin($player);
        $player = 1;
    }else{
        playerPNJ($line, $limit);
        checkWin($player);
        $player = 0;
    }
    
    printTurn($line, $limit);
}

function lineSE($large){
    echo "\e[47m\e[30m";
    for($i = 0; $i < $large; $i++){
        echo"*";
    }
    echo "\e[0m\n";
}

function printTurn($line, $limit){
    global $pyra;
    global $pyra_vide;

    lineSE($line *2 + 1);
    for($i = 1; $i <= $line; $i++){
        echo"\e[47m\e[30m*";
        for($j = 0; $j < $line - $i; $j++){
            echo " ";
        }
        for($j = 0; $j < $pyra[$i]; $j++){
            echo "|";
        }
        for($j = 0; $j < $line - $i + $pyra_vide[$i]; $j++){
            echo " ";
        }
       echo"*\e[0m ligne $i reste ".$pyra[$i]."\n";
    }
    lineSE($line *2 + 1);
    play($line, $limit);
}

function setPyra($line){
    global $pyra;
    global $pyra_vide;

    $ii = 1;
    for($i = 2; $i <= $line; $i++){
        $pyra[$i] = $ii * 2 + 1;
        $pyra_vide[$i] = 0;
        $ii += 1;
    }    
}

function defAllum($texte2){
    echo $texte2;
    $limit = readline();
    if ($limit > 0 && $limit <= 25){
        return $limit;
    }elseif($limit == "stop"){
        exit;
    }else{
        $texte2 = "\e[31mEntré incorect veuillez un nombre entre 1 et 25 ou stop pour interompre : \e[0m";
        $limit = defAllum($texte2);
        return $limit;
    }
}

function start($texte){
    echo $texte;
    $line = readline();
    if ($line > 0 && $line < 100){
        $texte2 = "\e[33mCombien voulez vous pouvoir enlever d'allumette aux maximum (entre 1 et 25) : \e[0m";
        $limit = defAllum($texte2);
              
        setPyra($line);
        printTurn($line, $limit);
        
    }elseif($line == "stop"){
        exit;
    }else{
        $texte = "\e[31mEntré incorect veuillez un nombre entre 1 et 99 ou stop pour interompre : \e[0m";
        start($texte);
    }
}

$texte = "\e[33mCombien voulez vous de ligne (entre 1 et 99) : \e[0m";

start($texte);