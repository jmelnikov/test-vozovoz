<?php

/** @var yii\web\View $this */

$this->title = 'Vozovoz Test';
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto border border-secondary-subtle rounded-3 pt-3">
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label for="deliveryFrom" class="form-label">Откуда</label>
                        <input type="text" class="form-control" id="deliveryFrom" list="citiesFrom">
                        <datalist id="citiesFrom"></datalist>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label for="deliveryTo" class="form-label">Куда</label>
                        <input type="text" class="form-control" id="deliveryTo" list="citiesTo">
                        <datalist id="citiesTo"></datalist>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="radioFrom" id="radioFromAddress"
                               value="address">
                        <label class="form-check-label" for="radioFromAddress">
                            От адреса
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="radioFrom" id="radioFromTerminal"
                               value="terminal" checked>
                        <label class="form-check-label" for="radioFromTerminal">
                            От терминала
                        </label>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="radioTo" id="radioToAddress"
                               value="address">
                        <label class="form-check-label" for="radioToAddress">
                            До адреса
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="radioTo" id="radioToTerminal"
                               value="terminal" checked>
                        <label class="form-check-label" for="radioToTerminal">
                            До терминала
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="radioTo" id="radioToYandex"
                               value="yandex">
                        <label class="form-check-label" for="radioToYandex">
                            До пункта Яндекс
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="mb-3">
                                <label for="volume" class="form-label">Объём</label>
                                <input type="text" class="form-control" id="volume">
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="mb-3">
                                <label for="weight" class="form-label">Вес</label>
                                <input type="text" class="form-control" id="weight">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <h1 class="text-center">123456</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <button type="button" class="btn btn-primary w-100" id="calculateBtn">Рассчитать</button>
                </div>
                <div class="col-6 mb-3">
                    <small class="text-muted">*При заказе через личный кабинет, без учета параметров 1 места</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    let typingTimer;
    const typingDelay = 1000;

    document.getElementById('deliveryFrom').addEventListener('input', function () {
        clearTimeout(typingTimer);
        const inputElement = this;
        const enteredText = inputElement.value;

        if (enteredText.length >= 3) {
            document.getElementById('citiesFrom').innerHTML = '';
            typingTimer = setTimeout(() => {
                const formData = new FormData();
                formData.append('query', enteredText);

                fetch('/api/cities', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ошибка при получении списка городов');
                        }
                        return response.json();
                    })
                    .then(data => {
                        data.cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.name;
                            option.textContent = city.name;
                            document.getElementById('citiesFrom').appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Произошла ошибка при запросе городов:', error);
                    });
            }, typingDelay);
        }
    });

    document.getElementById('deliveryTo').addEventListener('input', function () {
        clearTimeout(typingTimer);
        const inputElement = this;
        const enteredText = inputElement.value;

        if (enteredText.length >= 3) {
            document.getElementById('citiesTo').innerHTML = '';
            typingTimer = setTimeout(() => {
                const formData = new FormData();
                formData.append('query', enteredText);

                fetch('/api/cities', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ошибка при получении списка городов');
                        }
                        return response.json();
                    })
                    .then(data => {
                        data.cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.name;
                            option.textContent = city.name;
                            document.getElementById('citiesTo').appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Произошла ошибка при запросе городов:', error);
                    });
            }, typingDelay);
        }
    });

    document.getElementById('calculateBtn').addEventListener('click', function () {
        const deliveryFrom = document.getElementById('deliveryFrom').value;
        const deliveryTo = document.getElementById('deliveryTo').value;

        const radioFrom = document.querySelector('input[name="radioFrom"]:checked')?.value || '';
        const radioTo = document.querySelector('input[name="radioTo"]:checked')?.value || '';

        const volume = document.getElementById('volume').value;
        const weight = document.getElementById('weight').value;

        const formData = new FormData();
        formData.append('deliveryFrom', deliveryFrom);
        formData.append('deliveryTo', deliveryTo);
        formData.append('radioFrom', radioFrom);
        formData.append('radioTo', radioTo);
        formData.append('volume', volume);
        formData.append('weight', weight);

        fetch('/api/calc', {
            method: 'POST',
            body: formData,
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка при расчете');
                }
                return response.json();
            })
            .then(data => {
                console.log('Результат:', data);
                // Here you can handle the returned calculation result
                // Example: Update an element with the result
                document.querySelector('.text-center').textContent = data.result || 'Нет данных';
            })
            .catch(error => {
                console.error('Произошла ошибка:', error);
                alert('Не удалось выполнить расчет. Проверьте данные или попробуйте позже.');
            });
    });
</script>
