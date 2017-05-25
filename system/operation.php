<?php


$TYPE_IN = "In";
$TYPE_OUT = "Out";
$ACTION_BUY_ANIMALS = "Buy_Animal";
$ACTION_SELL_ANIMALS = "Sell_Animal";
$ACTION_BUY_FOOD = "Buy_Food";
$ACTION_SELL_FOOD = "Sell_Food";

function add_money($amount, $account) {
    $sql = "SELECT * FROM current_balance WHERE account = '$account'";
    $row = db_select_row($sql);
    if ($row) {
        $data = array(
//            'Id' => $row['Id'],
            'Account' => $row['Account'],
            'Balance' => $amount + $row['Balance']
        );
        if (db_update_by_value('current_balance', 'Account', $account, $data))
            return true;
        else {
            return false;
        }
    } else {
        return false;
    }
}



function sub_money($amount, $account) {
    $sql = "SELECT * FROM current_balance WHERE account = '$account'";
    $row = db_select_row($sql);
    if ($row) {
        if ($row[Balance] < $amount)
            return false;
        $data = array(
           // 'Id' => $row['Id'],
            'Account' => $row['Account'],
            'Balance' => $row['Balance'] - $amount
        );
        if (db_update_by_value('current_balance', 'Account', $account, $data))
            return true;
        else {
            return false;
        }
    } else {
        return false;
    }
}


function get_balance($account){
    $sql = "SELECT * FROM current_balance WHERE Account = '$account'";
    $row = db_select_row($sql);
    if(!empty($row)){
        if(isset($row['Balance']))
        return $row['Balance'];
        else return false;
    }
    else return false;
}



function check_money($animal_id, $source, $weight) {
    $sql = "SELECT * FROM animal_price WHERE Animal_ID = '$animal_id' AND Source = '$source'";
    $data = db_select_row($sql);
    $money = $weight * $data['Price_Per_Unit'];
    if ($money > 0)
        return $money;
    else
        return false;
}

function add_list_animals($number_animals, $source, $account, $animal_id, $sex, $min_health_index, $min_weight, &$amount) {

    $sql_balance = "SELECT * FROM current_balance WHERE Account = '$account' ";
    $balance_row = db_select_row($sql_balance);
    $balance = $balance_row['Balance'];
    if(empty($balance)) return "Balance not available";

    $total_money = 0;
    //$money = 0;
    $data = array();
    $data_final = array();
    $data['Animal_ID'] = $animal_id;
    $data['Sex'] = $sex;
    $data['Source'] = $source;
    $data['Account'] = $account;
    $data['Date_Import'] = date('d/m/Y');


    for ($i = 1; $i <= $number_animals; $i++) {
        $data['Health_Index'] = rand($min_health_index, 100);
        if ($animal_id == 1)
            $data['Weight'] = rand($min_weight, 150);
        else if ($animal_id == 2 || $animal_id == 3)
            $data['Weight'] = rand($min_weight, 600);
        else if ($animal_id == 4)
            $data['Weight'] = rand($min_weight, 5);
        else{
            $data['Weight'] = 50;
        }

        $money = check_money($animal_id, $source, $data['Weight']);
        if ($money != false) {
            $total_money = $total_money + $money;
            if ($total_money > $balance) {
                return "Not enough money";
            }
           // $data_final[] = $data;
            array_push($data_final, $data);
        }else continue;
    }

    for($i = 0; $i < count($data_final); $i++){
        db_insert('animals', $data_final[$i]);
    }
    sub_money($total_money, $account);
    global $TYPE_OUT;
    global $ACTION_BUY_ANIMALS;
    transaction($account, $total_money, $TYPE_OUT , $ACTION_BUY_ANIMALS );
    $amount = $total_money;
    return true;
}


function sell_animals($account, $array_id){

    global $TYPE_IN;
    global $ACTION_SELL_ANIMALS;
    $total_money = 0;
    foreach ($array_id as $item) {
        $sql = "SELECT * FROM "."animals WHERE Id = '$item'";
        $row = db_select_row($sql);
        $money = check_money($row['Animal_ID'], $row['Source'], $row['Weight']);
        if($money == false) return false;
        $total_money = $total_money + $money;
        db_delete_by_id('animals', 'Id', $item );
    }

    add_money($total_money, $account);
    transaction($account, $total_money, $TYPE_IN, $ACTION_SELL_ANIMALS);
    $result['delete'] = true;
    $result['total_money'] = "$total_money";
    return $result;
}

function transaction($account, $money ,$type, $action){
    if(strcmp($type,"In") != 0 && strcmp($type, "Out") != 0)return false;
    $data['Account'] = $account;
    $data['Money'] = $money;
    $data['Type'] = $type;
    $data['Action'] = $action;
    db_insert('transaction', $data);
    return true;
}



/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

