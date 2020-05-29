<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');


include('config.php'); // подключаем настройки бота
include('functions.php'); // подключаем  функциями
include('mysql.php'); // подключаем  функциями

echo get($url.'setWebhook?url='.$webhook);



if (($json = valid()) == false) { exit(); }
  
  $uid = $json['message']['from']['id'];         
  $first_name = $json['message']['from']['first_name'];
  $username = $json['message']['from']['username'];
  $date = $json['message']["date"];
  $msgid = $json['message']['message_id'];
  $text = $json['message']['text'];
  
$message_array = array(
    "user_id" => $uid,
    "username" => $username, 
    "first_name" => $first_name, 
    "message_id" => $msgid, 
    "text" => $text, 
    "date" => $date,
    "ndate" =>  gmdate("d.m.Y H:i:s", $date)
  ); 
  
  addUser($uid,$username,$first_name);
  MessageSave($message_array);  
  
  // Проверим статус пользователя
 $user_info =  userget($uid); // извлекаем информацию о пользователе из базы данных
 $user_status = $user_info['status']; // извлекаем статус из массива
  
//  sendMessage($uid, "Твой статус на момент обработки запроса: ".$user_status);
  
  
    $ANSWER = "Вы не являетесь администратором, ".$first_name;
    
  
  switch($text){
   
case ($uid == '430239172' and $user_status == '100'):
$users = getUsers('users');

$ANSWER = "Отлично, рассылаю текст:\n\n".$text."\n
на всех пользователей.\n\nВсего пользователей: ".count($users);
sendMessage($uid,$ANSWER);
      

   updateColumns(array("status" => "0"),$uid);
  for ($i = 0; $i < count($users); $i++) {
    sendMessage($users[$i],$text);
  }
$ANSWER = "Рассылка успешно отправлена!";
break;
      
		case ($text == 'Отправить сообщение'  and $uid == '430239172'):
      $ANSWER = 'Пришлите текст для рассылки...';
       updateColumns(array("status" => "100"),$uid);
    break;
      
    case ($text == '/help' || $text == 'Помощь'):
     echo '<a href="vstu.php?id=main"><img src="1.jpg" /></a>';
      $keyboard = keyboard();      
      updateColumns(array("status" => "0"),$uid);
    break;
      
    case ($text=='Админ' and $uid == '430239172'):
      $ANSWER = 'Здравствуй, администратор!';
      $keyboard = keyboard_admin(); 
      updateColumns(array("status" => "2"),$uid);
    break;
      
    case '/reset':
      $ANSWER = "Клавиатура сброшена!";
      $keyboard = delete_keyboard();
    break;
      
    case '/byby':
      $ANSWER = "До встречи!😁🚅🐝💸";
    break;      

    case 'ИТ':
      $ANSWER = "Ты в разделе Обучения.";
      $keyboard = keyboard_learning();    
      updateColumns(array("status" => "2"),$uid);
    break;      
      
      
    case 'ИТ-5':
      $ANSWER = "Вот ссылка на фото рассписания занятий https://t.me/fvvvbbvvv/2";
      $keyboard = keyboard_instagram();
      updateColumns(array("status" => "3"),$uid);
    break;  
     
// если статус = 2, значит он в разделе "Обучение", вернем его в основное меню и обновим статус на 0
//Если статус = 3, значит он в разделе Instagram, вернем его в раздел Обучения и дадим статус 2
     case 'Назад':
        $ANSWER = "Отлично! Верну тебя на один пункт назад";
      
        if ($user_status == 2)
        {
          $keyboard = keyboard();  
          updateColumns(array("status" => "0"),$uid); 
        } else if ($user_status == 3) 
        {
          $keyboard = keyboard_learning(); 
          updateColumns(array("status" => "2"),$uid);
        }    
    break;       
      
    case '/start':
     $ANSWER = "Добро пожаловать в раздел Помощи! Твой идентификатор: ".$uid;
      $keyboard = keyboard();      
      updateColumns(array("status" => "0"),$uid);
    break;      
  }
if ($PHOTO) {sendPhoto($uid,$PHOTO,$hello); exit();}
sendMessage($uid,$ANSWER, $keyboard);
  
    // Проверим статус пользователя
 //$user_info =  userget($uid); // извлекаем информацию о пользователе из базы данных
 //$user_status = $user_info['status']; // извлекаем статус из массива
 // sendMessage($uid, "Твой статус после обработки запроса: ".$user_status);
  



?>