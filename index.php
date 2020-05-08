<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');
session_start();
// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
// Массив для временного хранения сообщений пользователю.
$messages = array();
// В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
// Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf("Вы можете <a href='login.php'>Войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong> для изменения данных.",
      strip_tags($_COOKIE['login']),
      strip_tags($_COOKIE['pass']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();

  $errors['field-name'] = !empty($_COOKIE['field-name_error']);
  $errors['field-email'] = !empty($_COOKIE['field-email_error']);
  $errors['field-date'] = !empty($_COOKIE['field-date_error']);
  $errors['radio-sex'] = !empty($_COOKIE['radio-sex_error']);
  $errors['radio-kon'] = !empty($_COOKIE['radio-kon_error']);
  $errors['inSuperpowers'] = !empty($_COOKIE['inSuperpowers_error']);
  $errors['field-name-2'] = !empty($_COOKIE['field-name-2_error']);
  $errors['checker'] = !empty($_COOKIE['checker_error']);
 
  // Выдаем сообщения об ошибках.
  if ($errors['field-name']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('field-name_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя.</div>';
  }

  if ($errors['field-email']) {
      setcookie('field-email_error', '', 100000);
      $messages[] = '<div class="error">Заполните email в правильной форме</div>';
  }

  if ($errors['field-date']) {
      setcookie('field-date_error', '', 100000);
      $messages[] = '<div class="error">Заполните дату.</div>';
  }

  if ($errors['radio-sex']) {
      setcookie('radio-sex_error', '', 100000);
      $messages[] = '<div class="error">Выберите пол.</div>';
  }

  if ($errors['radio-kon']) {
      setcookie('radio-kon_error', '', 100000);
      $messages[] = '<div class="error">Выберите кол-во конечностей.</div>';
  }

  if ($errors['inSuperpowers']) {
      setcookie('inSuperpowers_error', '', 10000);
      $messages[] = '<div class="error">Выберите способность.</div>';
  }
  
  if ($errors['field-name-2']) {
    setcookie('field-name-2_error', '', 100000);
    $messages[] = '<div class="error">Введите сообщение.</div>';
  }

  if ($errors['checker']) {
    setcookie('checker_error', '', 100000);
    $messages[] = '<div class="error">Ознакомьтесь с контрактом.</div>';
  }

  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  
  // Складываем предыдущие значения полей в массив, если есть.
  $values = array();
  $values['field-name'] = empty($_COOKIE['field-name_value']) ? '' : strip_tags($_COOKIE['field-name_value']);
  $values['field-email'] = empty($_COOKIE['field-email_value']) ? '' : strip_tags($_COOKIE['field-email_value']);
  $values['field-date'] = empty($_COOKIE['field-date_value']) ? '' : strip_tags($_COOKIE['field-date_value']);
  $values['radio-sex'] = empty($_COOKIE['radio-sex_value']) ? '' :strip_tags($_COOKIE['radio-sex_value']);
  $values['radio-kon'] = empty($_COOKIE['radio-kon_value']) ? '' : strip_tags($_COOKIE['radio-kon_value']);
  $values['super1'] = empty($_COOKIE['super1_value']) ? '' : strip_tags($_COOKIE['super1_value']);
  $values['super2'] = empty($_COOKIE['super2_value']) ? '' : strip_tags($_COOKIE['super2_value']);
  $values['super3'] = empty($_COOKIE['super3_value']) ? '' : strip_tags($_COOKIE['super3_value']);
  $values['field-name-2'] = empty($_COOKIE['field-name-2_value']) ? '' :strip_tags($_COOKIE['field-name-2_value']);
  $values['checker'] = empty($_COOKIE['checker_value']) ? '' : strip_tags($_COOKIE['checker_value']);

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.

  if (!empty($_SESSION['login'])) {
    // TODO: загрузить данные пользователя из БД  
    $db = new PDO('mysql:host=localhost;dbname=u20292', 'u20292', '1232183');
    try{
    	$row=$db->query("SELECT * FROM anketa where login='".$_SESSION['login']."'")->fetch();
            $values['field-name'] = $row['name'];
            $values['field-email'] = $row['email'];
            $values['field-date'] = $row['date'];
            $values['radio-sex'] = $row['gender'];
            $values['radio-kon'] = $row['limb'];
            $values['super1'] = strip_tags($row['super1']);
            $values['super2'] = strip_tags($row['super2']);
            $values['super3'] = strip_tags($row['super3']);
            $values['field-name-2'] = strip_tags($row['message']);
            $values['checker'] = strip_tags($row['checker']);
    }
		catch(PDOException $e){}
		$db = null;
    // и заполнить переменную $values,
    // предварительно санитизовав.
    printf('Вход с логином %s, uid %d.', $_SESSION['login'], $_SESSION['uid']);
  }
    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода 
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else{
  $action = $_POST['save'];
    if($_POST['save'] =='выйти' || $_POST['save'] =='Создать нового пользователя'){//выходим из сессии и возвращаемся к index.php
      $values = array();
      $values['field-name'] = null;
      $values['field-email'] = null;
      $values['field-date'] = null;
      $values['radio-sex'] = null;
      $values['radio-kon'] = null;
      $values['super1'] = null;
      $values['super2'] = null;
      $values['super3'] = null;
      $values['field-name-2'] = null;
      $values['checker'] = null;
      if(!empty($_SESSION['login'])){
          setcookie('save', '', 100000);
          setcookie('login', '', 100000);
          setcookie('pass', '', 100000);
          setcookie('field-name_value', '', 100000);
          setcookie('field-email_value', '', 100000);
          setcookie('field-date_value', '', 100000);
          setcookie('radio-sex_value', '', 100000);
          setcookie('radio-kon_value', '', 100000);
          setcookie('super1_value', '', 100000);
          setcookie('super2_value', '', 100000);
          setcookie('super3_value', '', 100000);
          setcookie('field-name-2_value', '', 100000);
          setcookie('checker_value', '', 100000);
          $_COOKIE=array();
      }
      session_destroy();
      header('Location: index.php');
    }
     if($_POST['save'] =='войти'|| $_POST['save'] == 'Войти как пользователь'){//выходим из сессии и логинимся в login.php
      session_destroy();
      header('Location: login.php');
    }
    if($_POST['save'] =='сохранить'){//сохраняем данные 
      // Проверяем ошибки.
      $errors = FALSE;  
      $messages[]='ok';
      if (empty($_POST['field-name'])) {
        setcookie('field-name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
      }
      else {
          setcookie('field-name_value', $_POST['field-name'], time() + 30 * 24 * 60 * 60);
      }

      if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $_POST['field-email'])) {
          setcookie('field-email_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
      else {
          setcookie('field-email_value', $_POST['field-email'], time() + 30 * 24 * 60 * 60);
      }

      if (empty($_POST['field-date'])) {
          setcookie('field-date_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
      else {
          setcookie('field-date_value', $_POST['field-date'], time() + 30 * 24 * 60 * 60);
      }   

      if (empty($_POST['radio-sex'])) {
          setcookie('radio-sex_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
      else {
          setcookie('radio-sex_value', $_POST['radio-sex'], time() + 30 * 24 * 60 * 60);
      }

      if (empty($_POST['radio-kon'])) {
          setcookie('radio-kon_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
      else {
          setcookie('radio-kon_value', $_POST['radio-kon'], time() + 30 * 24 * 60 * 60);
      }

      if (!isset($_POST['super1'])
       && !isset($_POST['super2'])
       && !isset($_POST['super3'])) {
          setcookie('inSuperpowers_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        }
      else {
        setcookie('super1_value', isset($_POST['super1']) ? $_POST['super1'] : '', time() + 365 * 30 * 24 * 60 * 60);
        setcookie('super2_value', isset($_POST['super2']) ? $_POST['super2'] : '', time() + 365 * 30 * 24 * 60 * 60);
        setcookie('super3_value', isset($_POST['super3']) ? $_POST['super3'] : '', time() + 365 * 30 * 24 * 60 * 60);
      }

      if (empty($_POST['field-name-2'])) {
          setcookie('field-name-2_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
      else {
          setcookie('field-name-2_value', $_POST['field-name-2'], time() + 30 * 24 * 60 * 60);
      }

      if (empty($_POST['checker'])) {
          setcookie('checker_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
      else {
          setcookie('checker_value', $_POST['checker'], time() + 30 * 24 * 60 * 60);
      }

    if ($errors) {
      // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
      header('Location: index.php');
      exit();
    }
    else{
      // Удаляем Cookies с признаками ошибок.
      setcookie('field-name_error', '', 100000);
      setcookie('field-email_error', '', 100000);
      setcookie('field-date_error', '', 100000);
      setcookie('radio-sex_error', '', 100000);
      setcookie('radio-kon_error', '', 100000);
      setcookie('inSuperpowers_error', '', 100000);
      setcookie('field-name-2_error', '', 100000);
      setcookie('checker_error', '', 100000);
    }
    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
      //если залогинились и изменяем данные
      if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
        setcookie('login', $login);
        setcookie('pass', $pass);
        extract($_POST);
        $user = 'u20292';
        $password = '1232183';
        $db = new PDO('mysql:host=localhost;dbname=u20292', $user, $password);
        extract($_POST);
        $login = $_SESSION['login'];
        $name = $_POST['field-name'];
        $email = $_POST['field-email'];
        $date = $_POST['field-date'];
        $gender = $_POST['radio-sex'];
        $limb = $_POST['radio-kon'];
        if(!empty( $_POST['super1'])){
          $super1 = $_POST['super1'];
        }
        else{
          $super1 = '';
        }
        if(!empty( $_POST['super2'])){
          $super2 = $_POST['super2'];
        }
        else{
          $super2 = '';
        }
        if(!empty( $_POST['super3'])){
          $super3 = $_POST['super3'];
        }
        else{
          $super3 = '';
        }
        $message = $_POST['field-name-2'];
        $checker = $_POST['checker'];
        try {
          $sth = $db->prepare("UPDATE anketa SET name=:name, email=:email, date=:date, gender=:gender, limb=:limb, super1=:super1, super2=:super2, super3=:super3, message=:message, checker=:checker WHERE login=:login");
          $sth->bindParam(':login', $login);
          $sth->bindParam(':name', $name);
          $sth->bindParam(':email', $email);
          $sth->bindParam(':date', $date);
          $sth->bindParam(':gender', $gender);
          $sth->bindParam(':limb', $limb);
          $sth->bindParam(':super1', $super1);
          $sth->bindParam(':super2', $super2);
          $sth->bindParam(':super3', $super3);
          $sth->bindParam(':message', $message);
          $sth->bindParam(':checker', $checker);
          $sth->execute();
        }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }  
      // Сохраняем куку с признаком успешного сохранения.
      setcookie('save', '1');
      $messages[] = 'Спасибо, результаты сохранены.';
      header('Location: index.php');
      }
      else {//если НОВЫЕ данные
        $user = 'u20292';
        $password = '1232183';
        $db = new PDO('mysql:host=localhost;dbname=u20292', $user, $password);
        extract($_POST);
        // Генерируем уникальный логин и пароль.
        $b=TRUE;
        try {
          while($b){
            $login = (string)rand(1, 200);
            $pass = (string)rand(1, 100);
            $b=FALSE;
            foreach($db->query('SELECT login FROM anketa') as $row){
              if($row['login']==$login){
                $b=TRUE;
              }
            }
          }
        }
        catch(PDOException $e){
          print('Error : ' . $e->getMessage());
          setcookie('save', '1');
          exit();
        }
        // Сохраняем в Cookies.
        setcookie('login', $login);
        setcookie('pass', $pass);
        extract($_POST);
        $user = 'u20292';
        $password = '1232183';
        $db = new PDO('mysql:host=localhost;dbname=u20292', $user, $password);
        /*хэширование пароля*/
        $hash = (string)md5($pass);
        $name = $_POST['field-name'];
        $email = $_POST['field-email'];
        $date = $_POST['field-date'];
        $gender = $_POST['radio-sex'];
        $limb = $_POST['radio-kon'];
        if(!empty( $_POST['super1'])){
          $super1 = $_POST['super1'];
        }
        else{
          $super1 = '';
        }
        if(!empty( $_POST['super2'])){
          $super2 = $_POST['super2'];
        }
        else{
          $super2 = '';
        }
        if(!empty( $_POST['super3'])){
          $super3 = $_POST['super3'];
        }
        else{
          $super3 = '';
        }
        $message = $_POST['field-name-2'];
        $checker = $_POST['checker'];
        try {
          $sth = $db->prepare("INSERT INTO anketa (login, password, name, email, date, gender, limb, super1, super2, super3, message, checker) VALUES (:login, :pass, :name, :email, :date, :gender, :limb, :super1, :super2, :super3, :message, :checker)");
          $sth->bindParam(':login', $login, PDO::PARAM_INT);
          //внесение в базу хэшированого пароля
          $sth->bindParam(':pass', $hash);
          //$sth->bindParam(':pass', $pass);
          $sth->bindParam(':name', $name);
          $sth->bindParam(':email', $email);
          $sth->bindParam(':date', $date);
          $sth->bindParam(':gender', $gender);
          $sth->bindParam(':limb', $limb);
          $sth->bindParam(':super1', $super1);
          $sth->bindParam(':super2', $super2);
          $sth->bindParam(':super3', $super3);
          $sth->bindParam(':message', $message);
          $sth->bindParam(':checker', $checker);
          $sth->execute();
        }
        catch(PDOException $e){
          print('Error : ' . $e->getMessage());
          exit();
        }
      }  
      // Сохраняем куку с признаком успешного сохранения.
      setcookie('save', '1');
      $messages[] = 'Спасибо, результаты сохранены.';
      header('Location: index.php');
    }
  }
