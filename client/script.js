const sendBtn = document.getElementById('sendBtn');
const fio = document.getElementById('fio');
const phoneNumber = document.getElementById('phoneNumber');
const email = document.getElementById('email');
const birthDate = document.getElementById('birthDate');
const sex = document.getElementsByName('sex');
const favouriteLangs = document.getElementById('favouriteLangs');
const biography = document.getElementById('biography');
const successContract = document.getElementById('successContract');
const alertBanner = document.getElementById('alert');

const cookies = document.cookie;
if (cookies.includes('error')) {
    alertBanner.classList.remove('d-none');
    alertBanner.innerHTML = JSON.parse(cookies.split('=')[1]).message;
} else if (cookies.includes('form')) {
    const payload = JSON.parse(cookies.split('=')[1]);
    fio.value = payload.fio;
    phoneNumber.value = payload.phone_number;
    email.value = payload.email;
    birthDate.value = payload.birth_date;
    if (payload.sex == 'male') sex[0].checked = true;
    else sex[1].checked = true;
    biography.value = payload.biography;
}

sendBtn.addEventListener('click', async () => {
    alertBanner.classList.add('d-none');
    alertBanner.innerHTML = '';
    fio.classList.remove('is-invalid');
    phoneNumber.classList.remove('is-invalid');
    email.classList.remove('is-invalid');
    birthDate.classList.remove('is-invalid');
    biography.classList.remove('is-invalid');

    let langs = [];
    let sexValue = null;

    for (let i = 0; i < favouriteLangs.length; i++) {
        if (favouriteLangs[i].selected) langs.push(favouriteLangs[i].value);
    }


    if (!successContract.checked) {
        alertBanner.innerHTML = 'Отметьте галочкой, что Вы ознакомились с контрактом'
        alertBanner.classList.remove('d-none');
        return;
    }

    if (fio.value.trim().length === 0 || phoneNumber.value.trim().length === 0 || email.value.trim().length === 0 || birthDate.value.trim().length === 0 || biography.value.trim().length === 0 || phoneNumber.value.trim().replace('+7', '').length !== 10) {
        alertBanner.innerHTML = 'Заполните все поля'
        alertBanner.classList.remove('d-none');
        return;
    }

    const emailRegEx = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailRegEx.test(email.value)) {
        alertBanner.innerHTML = 'Неверный формат почты'
        alertBanner.classList.remove('d-none');
        return;
    }

    if (isNaN(phoneNumber.value.replace('+7', ''))) {
        alertBanner.innerHTML = 'Неверный формат номера телефона'
        alertBanner.classList.remove('d-none');
        return;
    }

    if (!sex[0].checked && !sex[1].checked) {
        alertBanner.innerHTML = 'Необходимо выбрать пол'
        alertBanner.classList.remove('d-none');
        return;
    }

    if (sex[0].checked) sexValue = 'male';
    else sexValue = 'female';

    const payload = {
        fio: fio.value,
        phone_number: phoneNumber.value,
        email: email.value,
        birth_date: birthDate.value,
        sex: sexValue,
        favourite_langs: langs,
        biography: biography.value
    };

    await axios.post('http://192.168.199.8:6854/send-form', payload)
    .then(res => {
        if (res.data.message == 'SUCCESS') {
            alertBanner.innerHTML = 'Данные успешно отправлены в базу';
            alertBanner.classList.remove('alert-danger');
            alertBanner.classList.add('alert-success');
            alertBanner.classList.remove('d-none');

            document.cookie = `form=${JSON.stringify(payload)}; max-age=31536000;`;
            return;
        } 
    })
    .catch(err => {
        console.log(err);
        alertBanner.innerHTML = err.response.data.message;
        alertBanner.classList.remove('d-none');

        if (err.response.data.fioTest === false) fio.classList.add('is-invalid');
        if (err.response.data.phoneTest === false) phoneNumber.classList.add('is-invalid');
        if (err.response.data.emailTest === false) email.classList.add('is-invalid');
        if (err.response.data.birthDateTest === false) birthDate.classList.add('is-invalid');
        if (err.response.data.biographyTest === false) biography.classList.add('is-invalid');

        document.cookie = `error=${JSON.stringify(err.response.data)}`;

        return;
    });
});
