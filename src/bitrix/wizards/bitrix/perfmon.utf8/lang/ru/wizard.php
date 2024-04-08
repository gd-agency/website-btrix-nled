<?
$MESS["UTFWIZ_STEP1_TITLE"] = "Резервное копирование.";
$MESS["UTFWIZ_DATABASE_NOT_SUPPORTED"] = "Мастер не поддерживает установленный тип базы данных.";
$MESS["UTFWIZ_BACKUP_WARNING"] = "Перед началом конвертации необходимо выполнить резервное копирование файлов и базы данных.";
$MESS["UTFWIZ_BACKUP_CONSENT"] = "Я выполнил резервное копирование и умею восстанавливать сайт из резервной копии.";
$MESS["UTFWIZ_SITE_CLOSED_WARNING"] = "На время работы мастера публичная часть сайта будет закрыта.";
$MESS["UTFWIZ_CHECK_SITE_WARNING"] = "Перед тем как нажать \"Далее\" убедитесь что все файлы доступны для записи. Это можно сделать на странице \"Проверка системы\" во вкладке \"Проверка доступа\".";
$MESS["UTFWIZ_STEP2_TITLE"] = "Проверка настроек.";
$MESS["UTFWIZ_STEP2_DEFAULT_CHARSET"] = "Установите в файле настроек php.ini значение default_charset = \"utf-8\".";
$MESS["UTFWIZ_STEP2_BX_UTF_CONSTANT"] = "Добавьте в <a target=\"_blank\" href=\"#EDIT_HREF#\">/bitrix/php_interface/dbconn.php</a>: define('BX_UTF', true);";
$MESS["UTFWIZ_STEP2_SETLOCALE"] = "Удалите вызовы функции setlocale(LC_ALL, ...) в <a target=\"_blank\" href=\"#EDIT_HREF#\">/bitrix/php_interface/dbconn.php</a>.";
$MESS["UTFWIZ_CONVERT_NOTICE"] = "Обратите внимание, что для конвертации таблиц используется `ALTER TABLE ... CONVERT TO CHARACTER SET charset`, который в качестве стороннего эффекта увеличивает допустимую размерность некоторых символьных полей. Проверка сайта покажет несоответствие структуры базы данных и определений таблиц, как они заданы в модулях. Это несоответствие не является критическим для работы сайта и может быть исправлено выполнением `ALTER TABLE ... CHANGE COLUMN`, полученными из журнала проверки сайта.";
$MESS["UTFWIZ_STEP2_MB_INSTALLED"] = "Установлено расширение mbstring.";
$MESS["UTFWIZ_STEP2_MB_INTERNAL_ENCODING"] = "Удалите вызовы функции mb_internal_encoding() в <a target=\"_blank\" href=\"#EDIT_HREF#\">/bitrix/php_interface/dbconn.php</a>. Функция должна возвращать значение 'UTF-8'.";
$MESS["UTFWIZ_STEP2_MB_FUNC_OVERLOAD"] = "Настройка mbstring.func_overload = 0";
$MESS["UTFWIZ_STEP2_UTF_MODE"] = "Установите значение 'value' => true для utf_mode в файле <a target=\"_blank\" href=\"#EDIT_HREF#\">/bitrix/.settings.php</a> или добавьте: 'utf_mode' => ['value' => true, 'readonly' => true], в файл.";
$MESS["UTFWIZ_STEP3_TITLE"] = "Проверка кодировки базы данных и таблиц.";
$MESS["UTFWIZ_DB_CONVERT_MANUAL"] = "Я сконвертирую базу данных сам.";
$MESS["UTFWIZ_DB_CONVERT_WIZARD"] = "Сконвертировать кодировку базы данных мастером.";
$MESS["UTFWIZ_DB_CONVERT_ALREADY"] = "База данных сконвертирована.";
$MESS["UTFWIZ_STEP4_TITLE"] = "Конвертация базы данных.";
$MESS["UTFWIZ_RUN_SQL"] = "Выполните следующие запросы:";
$MESS["UTFWIZ_INIT"] = "Инициализация...";
$MESS["UTFWIZ_STEP5_TITLE"] = "Настройка подключения к базе данных.";
$MESS["UTFWIZ_EDIT_AFTER_CONNECT"] = "Настроить кодировку подключения можно в файле <a target=\"_blank\" href=\"#EDIT_HREF#\">/bitrix/php_interface/after_connect_d7.php</a>";
$MESS["UTFWIZ_CONNECTION_CHARSET"] = "Кодировка соединения с базой данных должна быть utf8.";
$MESS["UTFWIZ_CONNECTION_COLLATION"] = "Сравнение соединения с базой данных должно быть utf8_unicode_ci.";
$MESS["UTFWIZ_CHARSET_CONN_VS_RES"] = "Кодировка соединения (#CONN#) должна быть такой же как кодировка результата (#RES#)";
$MESS["UTFWIZ_STEP6_TITLE"] = "Проверка и исправление сериализованных данных.";
$MESS["UTFWIZ_CHOOSE"] = "Выберите исходную кодировку для конвертации в utf8";
$MESS["UTFWIZ_OR_OTHER"] = "или укажите требуемую";
$MESS["UTFWIZ_STEP7_TITLE"] = "Конвертация файлов.";
$MESS["UTFWIZ_SKIP_LINKS"] = "Пропускать символические линки.";
$MESS["UTFWIZ_EXCLUDE_MASK"] = "Маска исключения:";
$MESS["UTFWIZ_STEP8_TITLE"] = "Сброс кеша.";
$MESS["UTFWIZ_FINALSTEP_BUTTONTITLE"] = "Готово";
$MESS["UTFWIZ_CANCELSTEP_TITLE"] = "Работа мастера прервана";
$MESS["UTFWIZ_CANCELSTEP_BUTTONTITLE"] = "Закрыть";
$MESS["UTFWIZ_CANCELSTEP_CONTENT"] = "Работа мастера была прервана.";
$MESS["UTFWIZ_FINALSTEP_TITLE"] = "Работа мастера завершена";
$MESS["UTFWIZ_FINALSTEP_CONTENT"] = "Работа мастера была завершена.";
$MESS["UTFWIZ_FIX_AND_RETRY"] = "Произошла ошибка. Диагностируйте, исправьте и попробуйте ещё раз.";
$MESS["UTFWIZ_RETRYSTEP_BUTTONTITLE"] = "Повторить";
?>