<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Форма заявки</title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Inter:100,200,300,regular,500,600,700,800,900,100italic,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic);

        body { 
            font-family: "Inter", sans-serif; 
            max-width: 600px; 
            margin: 50px auto; 
            padding: 20px;
            background: #f5f5f5;
        }
        h1 { color: #333; }
        label { 
            display: block; 
            margin: 15px 0 5px; 
            font-weight: bold;
        }
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 10px; 
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea { height: 100px; resize: vertical; }
        select[multiple] { height: 150px; }
        button { 
            background: #4CAF50; 
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover { background: #45a049; }
        .error { 
            color: #d32f2f; 
            background: #ffebee; 
            padding: 10px; 
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #d32f2f;
        }
        .success { 
            color: #388e3c; 
            background: #e8f5e9; 
            padding: 10px; 
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #388e3c;
        }
        .error-field { 
            border: 2px solid #d32f2f !important;
            background: #ffebee;
        }
        .radio-group, .checkbox-group { 
            margin: 10px 0; 
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .radio-group label, .checkbox-group label {
            display: inline;
            font-weight: normal;
            margin-right: 15px;
        }
        #messages { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Форма заявки</h1>
    
    <?php if (!empty($messages)): ?>
    <div id="messages">
        <?php foreach ($messages as $message): ?>
            <?= $message ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php">
        
        <!-- ФИО -->
        <label>ФИО:
            <input type="text" 
                   name="fio" 
                   value="<?= htmlspecialchars($values['fio']) ?>"
                   class="<?= $errors['fio'] ? 'error-field' : '' ?>">
        </label>
        
        <!-- Телефон -->
        <label>Телефон:
            <input type="tel" 
                   name="phone" 
                   value="<?= htmlspecialchars($values['phone']) ?>"
                   class="<?= $errors['phone'] ? 'error-field' : '' ?>">
        </label>
        
        <!-- Email -->
        <label>E-mail:
            <input type="email" 
                   name="email" 
                   value="<?= htmlspecialchars($values['email']) ?>"
                   class="<?= $errors['email'] ? 'error-field' : '' ?>">
        </label>
        
        <!-- Дата рождения -->
        <label>Дата рождения:
            <input type="date" 
                   name="birth_date" 
                   value="<?= htmlspecialchars($values['birth_date']) ?>"
                   class="<?= $errors['birth_date'] ? 'error-field' : '' ?>">
        </label>
        
        <!-- Пол -->
        <label>Пол:</label>
        <div class="radio-group">
            <input type="radio" 
                   name="gender" 
                   value="male"
                   id="male"
                   <?= $values['gender'] == 'male' ? 'checked' : '' ?>
                   class="<?= $errors['gender'] ? 'error-field' : '' ?>">
            <label for="male">Мужской</label>
            
            <input type="radio" 
                   name="gender" 
                   value="female"
                   id="female"
                   <?= $values['gender'] == 'female' ? 'checked' : '' ?>
                   class="<?= $errors['gender'] ? 'error-field' : '' ?>">
            <label for="female">Женский</label>
        </div>
        
        <!-- Языки программирования -->
        <label>Любимый язык программирования (выберите несколько):</label>
        <select name="languages[]" multiple 
                class="<?= $errors['languages'] ? 'error-field' : '' ?>">
            <?php
            $langs = array('Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go');
            foreach ($langs as $lang):
                $selected = in_array($lang, $values['languages']) ? 'selected' : '';
                print("<option value=\"$lang\" $selected>$lang</option>");
            endforeach;
            ?>
        </select>
        
        <!-- Биография -->
        <label>Биография:
            <textarea name="bio" 
                      class="<?= $errors['bio'] ? 'error-field' : '' ?>"><?= htmlspecialchars($values['bio']) ?></textarea>
        </label>
        
        <!-- Контракт -->
        <div class="checkbox-group">
            <input type="checkbox" 
                   name="contract" 
                   value="1"
                   id="contract"
                   <?= $values['contract'] ? 'checked' : '' ?>
                   class="<?= $errors['contract'] ? 'error-field' : '' ?>">
            <label for="contract">С контрактом ознакомлен(а)</label>
        </div>
        
        <button type="submit">Сохранить</button>
    </form>
</body>
</html>

