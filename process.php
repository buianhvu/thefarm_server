<?PHP

define('SYSPATH', 'system/');
require SYSPATH . "database.php";
require SYSPATH . "client.php";
require SYSPATH . "operation.php";
$METHOD_LOGIN = 1;
$METHOD_REGISTER = 2;
$METHOD_GET_ANIMALS = 3;
$METHOD_DELETE_LIST = 4;
$METHOD_ADD_ANIMAL = 5;
$METHOD_ADD_MONEY_TO_ACCOUNT = 6;
$METHOD_WITHDRAW = 7;
$METHOD_ADD_LIST = 8;
$METHOD_CURRENT_BALANCE = 9;
$METHOD_SELL_ANIMALS = 10;
$PIG_ID = 1;
$BUFFALO_ID = 2;
$COW_ID = 3;
$CHICKEN_ID = 4;
$result = null;

//if(input_post('method')){
//    $method = input_post('method');
//    $account = input_post('Account');
//    $password = input_post('Password');
//    $animal_id = input_post('Animal_ID');
//    $id = (int) input_post('Id');
//    $sex = (int) input_post('Sex');
//    $health_index = (int) input_post('Health_Index');
//    $weight = (int) input_post('Weight');
//    $source = input_post('Source');
//    $date_import = input_post('Date_Import');
//    
//}
//else{
$inputJSON = file_get_contents('php://input');
$input= json_decode( $inputJSON, TRUE ); //convert JSON into array

$method = (int) $input['method'];
$account = $input['Account'];
$password = $input['Password'];
$animal_id = (int) $input['Animal_ID'];
$id = (int) $input['Id'];
$sex = (int) $input['Sex'];
$health_index = (int) $input['Health_Index'];
$weight = (int) $input['Weight'];
$source = $input['Source'];
$date_import = $input['Date_Import'];
$amount = $input['Amount'];
$number_animals = (int)$input['Number_Animals'];
$array_id = json_decode($input['array_id']);
//}
//connect to database
db_connect();

// for test from web browser
//$test = input_get('test');
//if ($test == true) {
//    $account = input_get('account');
//    $method = $METHOD_GET_ANIMALS;
//}

if ($method == $METHOD_LOGIN) {
    global  $result;
    $sql = "SELECT * FROM users WHERE account = '$account'";

    $row = db_select_row($sql);
    $result['Balance'] = get_balance($account);
    if($result['Balance'] == false) $result['Balance'] = "0";
    if(empty($row)) $result['login'] = false;
    if ($row['Password'] == $password) {
        $result['login'] = true;
    } else {
        $result['login'] = false;
    }
    
}

if ($method == $METHOD_REGISTER) {
    global $result;
    $data = array();
    $data1 = array();
    $data2 = array(
        'Food_ID' => 0,
        'Account' => $account,
        'Quantity' => 0
    );
    $data['Account'] = $account;
    $data['Password'] = $password;
    $rs = db_insert('users', $data);
    if ($rs) {
        $sql = "SELECT * FROM users WHERE account = '$account'";
        $row = db_select_row($sql);
       // $data1['Id'] = $row['Id'];
        $data1['Account'] = $account;
        $data1['Balance'] = 0;
        if (db_insert('current_balance', $data1)) {
            $result['register'] = true;
            for ($i = 1; $i <= 4; $i++) {
                $data2['Food_ID'] = $i;                
                db_insert('food', $data2);
            }
        } else
            $result['register'] = false;
    } else {
        $result['register'] = false;
    }
}

if ($method == $METHOD_GET_ANIMALS) {
    global $result;
    $animal_kind = (int) $animal_id;
    $sql = "SELECT * FROM animals WHERE Account = '$account' AND Animal_ID = '$animal_kind'";
    $result = db_select_list($sql);
}
//
//if ($method == $METHOD_DELETE_ANIMAL) {
//    global $result;
//    if (db_delete_by_id('animals', 'Id', $id)) {
//        $result['delete'] = true;
//    } else {
//        $result['delete'] = false;
//    }
//}
if ($method == $METHOD_ADD_ANIMAL) {
    global $result;
    $data = array(
        'Animal_ID' => $animal_id,
        'Sex' => $sex,
        'Health_Index' => $health_index,
        'Weight' => $weight,
        'Source' => $source,
        'Account' => $account,
        'Date_Import' => $date_import
    );
    if (db_insert('animals', $data)) {
        $result['add'] = true;
    } else {
        $result['add'] = false;
    }
}

if ($method == $METHOD_ADD_MONEY_TO_ACCOUNT) {
    $result['add_money'] = add_money($amount, $account);
    $result['new_balance'] = get_balance($account);
}
if ($method == $METHOD_WITHDRAW){
    $result['withdraw'] = sub_money($amount, $account);
    $result['new_balance'] = get_balance($account);
}
if($method == $METHOD_ADD_LIST){
    $result['add_list'] = add_list_animals($number_animals, $source, $account, $animal_id, $sex, $health_index, $weight, $amount);
    if($result['add_list'] != true){
        $result['mess'] = $result['add_list'];
        $result['add_list'] = false;
    }
    else    $result['mess'] = "Thank you";
    $result['amount'] = "$amount";
}
if($method == $METHOD_DELETE_LIST) {
   // $input['array_id'] = json_decode($input['array_id']);
    for($i= 0; $i < count($array_id); $i++ ){
        db_delete_by_id('animals', 'Id', $array_id[$i]);
    }
    $result['delete'] = true;
}

if($method == $METHOD_CURRENT_BALANCE){
    $result['Balance'] = get_balance($account);
    if($result['Balance'] == false) $result['Balance'] = "0";
}

if($method == $METHOD_SELL_ANIMALS){
    $result = sell_animals($account, $array_id);
}


$json = json_encode($result, JSON_PRETTY_PRINT);
// $json = json_encode($result); // use on hostinger
print_r($json);
db_disconnect();



