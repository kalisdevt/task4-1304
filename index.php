<?php
header('Content-Type: text/html; charset=UTF-8');

// Подключение к БД (выносим в начало, чтобы было доступно везде)
$pdo = new PDO('mysql:host=localhost;dbname=u82282', 'u82282', '9786483');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // ========== ПОКАЗАТЬ ФОРМУ ==========
    $messages = array();
    
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = '<div class="success">Данные анкеты успешно отправлены.</div>';
    }
    
    $errors = array();
    $fields = array('fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'bio', 'contract');
    
    foreach ($fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field . '_error']);
        if ($errors[$field]) {
            setcookie($field . '_error', '', 100000);
            $msg = isset($_COOKIE[$field . '_error_msg']) ? $_COOKIE[$field . '_error_msg'] : 'Ошибка в поле';
            $messages[] = '<div class="error">' . $msg . '</div>';
            setcookie($field . '_error_msg', '', 100000);
        }
    }
    
    $values = array();
    $values['fio'] = $_COOKIE['fio_value'] ?? '';
    $values['phone'] = $_COOKIE['phone_value'] ?? '';
    $values['email'] = $_COOKIE['email_value'] ?? '';
    $values['birth_date'] = $_COOKIE['birth_date_value'] ?? '';
    $values['gender'] = $_COOKIE['gender_value'] ?? '';
    $values['bio'] = $_COOKIE['bio_value'] ?? '';
    $values['contract'] = $_COOKIE['contract_value'] ?? '';
    $values['languages'] = isset($_COOKIE['languages_value']) ? unserialize($_COOKIE['languages_value']) : array();
    
    include('form.php');
}
else {
    // ========== ОБРАБОТАТЬ ДАННЫЕ (POST) ==========
    $errors = FALSE;
    
    // ----- ВАЛИДАЦИЯ ВСЕХ ПОЛЕЙ -----
    // ФИО
    if (empty($_POST['fio']) || !preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$/u', $_POST['fio'])) {
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        setcookie('fio_error_msg', 'ФИО должно содержать только буквы и пробелы (1-150 символов)', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('fio_value', $_POST['fio'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // Телефон
    if (empty($_POST['phone']) || !preg_match('/^[\d\s\-\+\(\)]{10,20}$/', $_POST['phone'])) {
        setcookie('phone_error', '1', time() + 24 * 60 * 60);
        setcookie('phone_error_msg', 'Телефон должен содержать 10-20 цифр и символов + - () пробелы', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('phone_value', $_POST['phone'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // Email
    if (empty($_POST['email']) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $_POST['email'])) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        setcookie('email_error_msg', 'Введите корректный email адрес', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('email_value', $_POST['email'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // Дата рождения
    if (empty($_POST['birth_date'])) {
        setcookie('birth_date_error', '1', time() + 24 * 60 * 60);
        setcookie('birth_date_error_msg', 'Укажите дату рождения', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('birth_date_value', $_POST['birth_date'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // Пол
    if (empty($_POST['gender']) || !in_array($_POST['gender'], array('male', 'female'))) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        setcookie('gender_error_msg', 'Выберите пол', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('gender_value', $_POST['gender'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // Языки
    $allowed_languages = array('Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go');
    if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
        setcookie('languages_error', '1', time() + 24 * 60 * 60);
        setcookie('languages_error_msg', 'Выберите хотя бы один язык', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        foreach ($_POST['languages'] as $lang) {
            if (!in_array($lang, $allowed_languages)) {
                setcookie('languages_error', '1', time() + 24 * 60 * 60);
                setcookie('languages_error_msg', 'Недопустимый язык', time() + 24 * 60 * 60);
                $errors = TRUE;
                break;
            }
        }
    }
    setcookie('languages_value', isset($_POST['languages']) ? serialize($_POST['languages']) : '', time() + 365 * 24 * 60 * 60);
    
    // Биография
    if (!empty($_POST['bio']) && strlen($_POST['bio']) > 1000) {
        setcookie('bio_error', '1', time() + 24 * 60 * 60);
        setcookie('bio_error_msg', 'Биография не более 1000 символов', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('bio_value', $_POST['bio'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // Контракт
    if (empty($_POST['contract'])) {
        setcookie('contract_error', '1', time() + 24 * 60 * 60);
        setcookie('contract_error_msg', 'Согласитесь с контрактом', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('contract_value', $_POST['contract'] ?? '', time() + 365 * 24 * 60 * 60);
    
    // ----- РЕШЕНИЕ -----
    if ($errors) {
        header('Location: index.php');
        exit();
    }
    else {
        // УДАЛЯЕМ ОШИБКИ
        foreach ($fields as $field) {
            setcookie($field . '_error', '', 100000);
            setcookie($field . '_error_msg', '', 100000);
        }
        
        // ========== СОХРАНЕНИЕ В БД  ==========
        $fio = $_POST['fio'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $birth_date = $_POST['birth_date'];
        $gender = $_POST['gender'];
        $languages = $_POST['languages'];
        $bio = $_POST['bio'] ?? '';
        
        // Вставляем заявку
        $stmt = $pdo->prepare("INSERT INTO application (fio, phone_number, email, birth_date, sex, biography) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fio, $phone, $email, $birth_date, $gender, $bio]);
        $application_id = $pdo->lastInsertId();
        
        // Вставляем выбранные языки
        foreach ($languages as $lang) {
            $stmt = $pdo->prepare("SELECT id FROM languages WHERE name = ?");
            $stmt->execute([$lang]);
            $lang_id = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("INSERT INTO application_languages (id, lang_id) VALUES (?, ?)");
            $stmt->execute([$application_id, $lang_id]);
        }
        // ========== КОНЕЦ СОХРАНЕНИЯ В БД ==========
        
        setcookie('save', '1');
        header('Location: index.php');
    }
}
?>
