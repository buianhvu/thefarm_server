<?PHP

define('SYSPATH', 'system/');
require SYSPATH . "database.php";
require SYSPATH . "client.php";
$METHOD_LOGIN = 1;
$METHOD_REGISTER = 2;
$METHOD_GET_ANIMALS = 3;
$METHOD_DELETE_ANIMAL = 4;
$METHOD_ADD_ANIMAL = 5;

$PIG_ID = 1;
$BUFFALO_ID = 2;
$COW_ID = 3;
$CHICKEN_ID = 4;
$result = null;

if(input_post('method')){
    $method = input_post('method');
    $account = input_post('Account');
    $password = input_post('Password');
    $animal_id = input_post('Animal_ID');
    $id = (int) input_post('Id');
    $sex = (int) input_post('Sex');
    $health_index = (int) input_post('Health_Index');
    $weight = (int) input_post('Weight');
    $source = input_post('Source');
    $date_import = input_post('Date_Import');
    
}
else{
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
}

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
    if ($row['Password'] == $password) {
        $result['login'] = true;
    } else {
        $result['login'] = false;
    }
}
 
 if($method == $METHOD_REGISTER) {
     global $result;
     $data = array();
        $data['Account'] = $account;
        $data['Password'] = $password; 
        $rs = db_insert('users', $data);
        if($rs){
            $result['register'] = true;
        }else{
            $result['register'] = false;
        }
    }
    
if($method == $METHOD_GET_ANIMALS){
    global $result;
    $animal_kind = (int) $animal_id;
    $sql = "SELECT * FROM animals WHERE Account = '$account' AND Animal_ID = '$animal_kind'";
    $result = db_select_list($sql);     
}

if($method == $METHOD_DELETE_ANIMAL){
    global $result;
    if(db_delete_by_id('animals', 'Id', $id)){
        $result['delete'] = true;
        
    }
    else{
        $result['delete'] = false;
    }
    
}
if($method == $METHOD_ADD_ANIMAL){
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
    if(db_insert('animals', $data)){
        $result['add'] = true;
    }
    else {
        $result['add'] = false;
    }
}
    
$json = json_encode($result, JSON_PRETTY_PRINT); 
    // $json = json_encode($result); // use on hostinger
    print_r($json);  
    db_disconnect();