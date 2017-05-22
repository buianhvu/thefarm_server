<?php

function add_money($amount, $account) {
    $sql = "SELECT * FROM current_balance WHERE account = '$account'";
    $row = db_select_row($sql);
    if ($row) {
        $data = array(
            'Id' => $row['Id'],
            'Account' => $row['Account'],
            'Balance' => $amount + $row['Balance']
        );
        if (db_update_by_id('current_balance', 'Account', $account, $data))
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
            'Id' => $row['Id'],
            'Account' => $row['Account'],
            'Balance' => $row['Balance'] - $amount
        );
        if (db_update_by_id('current_balance', 'Account', $account, $data))
            return true;
        else {
            return false;
        }
    } else {
        return false;
    }
}




function check_money($animal_id, $source, $weight) {
    $sql = "SELECT * FROM animal_price WHERE Animal_ID = '$animal_id' AND Source = '$source'";
    $data = db_select_row($sql);
    $money = $weight * $data['Price_Per_Unit'];
    if ($money > 0)
        return money;
    else
        return false;
}

function add_list_animals($number_animals, $source, $account, $animal_id, $sex, $min_health_index, $min_weight) {

    $sql_balance = "SELECT * FROM current_balance WHERE Account = '$account' ";
    $balance_row = db_select_row($sql_balance);
    $balance = $balance_row['Balance'];

    $total_money = 0;
    //$money = 0;
    $data = array();
    $data_final = array();
    $data['Animal_ID'] = $animal_id;
    $data['Sex'] = $sex;
    $data['Source'] = $source;
    $data['Account'] = $account;
    $data['Date_Import'] = getdate();


    for ($i = 1; $i <= $number_animals; $i++) {
        $data['Health_Index'] = rand($min_health_index, 100);
        if ($animal_id == 1)
            $data['Weight'] = rand($min_weight, 100);
        if ($animal_id == 2 || $animal_id == 3)
            $data['Weight'] = rand($min_weight, 200);
        if ($animal_id == 4)
            $data['Weight'] = rand($min_weight, 5);
        else
            $data['Weight'] = 50;

        $money = check_money($animal_id, $source, $data['Weight']);
        if ($money != false) {
            $total_money = $total_money + $money;
            if ($total_money > $balance)
                return false;
           // $data_final[] = $data;
            array_push($data_final, $data);
        }else continue;
    }

    for($i = 0; $i < $number_animals; $i++){
        db_insert('animals', (string)$data_final[$i]);
    }
    return true;
}



/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

