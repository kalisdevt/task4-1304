const express = require('express');
const app = express();
const port = 6854;
const cors = require('cors');

const allowedOrigins = ['http://kubsu-dev.ru', 'http://u82282.kubsu-dev.ru', 'http://u82282.kubsu-dev.ru/task3-2203', 'http://127.0.0.1:5500']

const corsOptions = {
    origin: function (origin, callback) {
        if (allowedOrigins.includes(origin) || !origin) {
            callback(null, true);
        }
        else {
            callback(new Error(`Not allowed by CORS: ${origin}`));
        }
    },
    credentials: true,
};

app.use(cors(corsOptions));
app.use(express.json());

const mysql = require('mysql2');
const connection = mysql.createConnection({
    host: '192.168.199.8',
    user: 'u82282',
    password: '9786483',
    database: 'u82282'
}).promise();

app.listen(port, () => {
    console.log('Server started and listening on port 6854');
});

connection.connect()
    .then(res => {
        console.log('Подключение к базе MySQL произошло успешно!');
    })
    .catch(err => console.log('ОШИБКА ПОДКЛЮЧЕНИЯ К БД', err));

app.get('/', (req, res) => {
    res.send('OK');
});

app.post('/send-form', async (req, res) => {
    try {
        const { fio, email, phone_number, sex, biography, favourite_langs, birth_date } = req.body;
        console.log(birth_date, email);

        const emailRegEx = /^\S+@\S+\.\S+$/;
        if (!emailRegEx.test(email)) return res.status(400).json({ emailTest: false, message: 'Неверно указана почта' });

        const fioRegEx = /^(?=.{1,40}$)[а-яёА-ЯЁ]+(?:[-' ][а-яёА-ЯЁ]+)*$/;
        if (!fioRegEx.test(fio)) return res.status(400).json({ fioTest: false, message: 'Неверно указано поле ФИО' });

        const phoneReg = /^\+?[1-9][0-9]{7,14}$/;
        if (!phoneReg.test(phone_number)) return res.status(400).json({ phoneTest: false, message: 'Неверно указан номер телефона' });

        if (sex != 'male' && sex != 'female') return res.status(400).json({ sexTest: false, message: 'Пол: неверное значение' });

        const dateReg = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateReg.test(birth_date)) return res.status(400).json({ birthDateTest: false, message: 'Не указана дата рождения' });

        if (favourite_langs.length === 0) return res.status(400).json({ favouriteLangsTest: false, message: 'Необходимо выбрать хотя бы 1 язык программирования' });

        if (!biography || biography.trim().length < 100) return res.status(400).json({ biographyTest: false, message: 'В поле биографии должно быть не менее 100 символов' });

        let langIds = [];
        const [langs] = await connection.query('SELECT * FROM languages')
            .catch(err => console.log(err));

    
        for (const lang of req.body.favourite_langs) {
            langIds.push(langs.find(l => l.lang_name == lang).lang_id);
        }

        let query = 'INSERT INTO application(fio, phone_number, email, sex, biography, birth_date) VALUES (?, ?, ?, ?, ?, ?);';
        let data = [fio, phone_number.replace('+7', ''), email, sex, biography, birth_date];
        await connection.query(query, data)
            .catch(err => console.log(err));

        const [id] = await connection.query('SELECT id from application ORDER BY id DESC LIMIT 1')
            .catch(err => console.log(err));

        for (const lang of langIds) {
            const query = 'INSERT INTO record_langs(id, lang_id) VALUES (?, ?)';
            const data = [id[0].id, lang];
            await connection.query(query, data)
                .catch(err => console.log(err));
        }

        return res.status(200).json({ message: 'SUCCESS' });
    } catch (err) {
        console.log(err);
        return res.status(400).json({ message: 'Произошла ошибка при попытке сохранения в базу данных' });
    }
});
