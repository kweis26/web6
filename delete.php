<?php
extract($_POST);
try {
    $user = 'u20292';
    $password = '1232183';
    $db = new PDO('mysql:host=localhost;dbname=u20292', $user, $password);
    $login = $_POST['save'];
    $sth = $db->prepare("DELETE FROM anketa WHERE login=:login");
    $sth->bindParam(':login', $login);
    $sth->execute();
    header('Location: admin.php');
}
catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
}
?>