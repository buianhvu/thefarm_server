<?php

$conn = null;

function db_connect(){
    global $conn;
    $conn = mysqli_connect('mysql.hostinger.vn','u162050273_data1','123456', 'u162050273_data');
}

function db_select_list($sql){
    global $conn;
    $result = mysqli_query($conn, $sql);
    if(!$result){
        die('Cau truy van bi sai');
    }
    $list = array();
    while( $row = mysqli_fetch_array($result)){
    $list[] = $row;    
    }
    mysqli_free_result($result);
    return $list;
    
}
function db_select_row($sql){
    global $conn;
    $result = mysqli_query($conn, $sql);
    if(!$result){
        die('Cau truy van bi sai');
    }
    $row = mysqli_fetch_array($result);
    mysqli_free_result($result);
    return $row;
}

//INSERT INTO `menucha` (`id_cha`, `TenMenuCha`) VALUES ('3', 'RAM');
function db_insert($table, $data = array()){
    global $conn;
    $field = "";
    $value = "";
        foreach($data as $key => $var){
        $field .= $key.',';
        $value .= "'". mysqli_escape_string($conn, $var)."'".",";
    }
    $sql = 'INSERT INTO `'.$table.'` ('.trim($field,',').') VALUES('. trim($value, ',').')';
    return mysqli_query($conn, $sql);
}

function db_update_by_id($table, $idfield, $idvalue, $data = array() ){
    $tmp_sql = '';
    global $conn;
    foreach ($data as $key => $value) {
        $tmp_sql .= '`'.$key.'`'.' = '.'\''.mysqli_escape_string($conn, $value).'\',';
    }

    $sql = 'UPDATE `'.$table.'` SET  '.trim($tmp_sql, ',').' WHERE '.$idfield.' = '.trim($idvalue).'';

    return mysqli_query($conn, $sql);

}



function db_update_by_value($table, $idfield, $idvalue, $data = array() ){

    global $conn;
    $sql_tmp = '';
    foreach($data as $key => $val) {
        $sql_tmp .= $key.' = '.'\''.$val.'\',';
    }
    $sql = "update ".$table." set ".trim($sql_tmp, ',')." where ".$idfield." ='".trim($idvalue)."'";
    return mysqli_query($conn, $sql);
}

function db_delete_by_id($table, $idfield, $idvalue){
    global $conn;
    $sql = 'DELETE FROM `'.$table.'` WHERE '.$idfield.' = '.(int)$idvalue.'';
    return mysqli_query($conn, $sql);
}

//
////function db_count($sql, $count_as){
//    // select count(*) as num_count from table
//    global $conn;
//    $result = mysqli_query($conn, $sql);
//    if(!$result)
//    {
//        echo 'Cau truy van sai';
//    }
//    $row = mysqli_fetch_array($result);
//    if(!$row)
//    {
//        mysqli_free_result($result);
//        return $row[.$count_as.];
//    }
//    return 0;
//}

function db_disconnect(){
    global $conn;
    mysqli_close($conn);
            
}