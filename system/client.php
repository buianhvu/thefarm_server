<?php


function input_get($key) {
    return isset($_GET[$key]) ? $_GET[$key] : FALSE;
}

function input_post($key) {
    return isset($_POST[$key]) ? $_POST[$key] : FALSE;
}

function load_db($name) {
    require 'db/' . $name . '.php';
}

?>