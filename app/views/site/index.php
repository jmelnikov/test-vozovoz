<?php

/** @var yii\web\View $this */

$this->title = 'Vozovoz Test';
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto border border-secondary-subtle rounded-3 pt-3">
            <div class="row">
                <div class="col-6">
                    <div class="mb-3 position-relative">
                        <label for="deliveryFrom" class="form-label">Откуда</label>
                        <input type="hidden" id="cityFrom" value="e90f1820-0128-11e5-80c7-00155d903d03">
                        <input type="text" class="form-control" id="deliveryFrom" value="Москва">
                        <ul class="suggestions-list" id="citiesFrom" style="display: none;">
                            <li>
                                Название
                                <small class="small text-muted">
                                    Описание
                                </small>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3 position-relative">
                        <label for="deliveryTo" class="form-label">Куда</label>
                        <input type="hidden" id="cityTo" value="e90f19de-0128-11e5-80c7-00155d903d03">
                        <input type="text" class="form-control" id="deliveryTo" value="Санкт-Петербург">
                        <ul class="suggestions-list" id="citiesTo" style="display: none;">
                            <li>
                                Название
                                <small class="small text-muted">
                                    Описание
                                </small>
                            </li>
                        </ul>
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
                    <!--                    Яндекс убрал потому что не смог найти в документации пример его расчёта -->
                    <!--                    <div class="form-check form-check-inline">-->
                    <!--                        <input class="form-check-input" type="radio" name="radioTo" id="radioToYandex"-->
                    <!--                               value="yandex">-->
                    <!--                        <label class="form-check-label" for="radioToYandex">-->
                    <!--                            До пункта Яндекс-->
                    <!--                        </label>-->
                    <!--                    </div>-->
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="mb-3">
                                <label for="volume" class="form-label">Объём</label>
                                <input type="text" class="form-control" id="volume" value="0.1"
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/(^0+)(\d)/, '$2');">
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="mb-3">
                                <label for="weight" class="form-label">Вес</label>
                                <input type="text" class="form-control" id="weight" value="0.1"
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/(^0+)(\d)/, '$2');">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <h1 class="text-center">
                        <span class="text-muted" id="price-old"
                              style="text-decoration: line-through;">740₽</span>
                        <span id="price-new">730₽</span>
                        *
                    </h1>
                    <p>Доставка: <span class="text-danger" id="delivery-period">на следующий день</span></p>
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

        if (this.value.length >= 3) {
            document.getElementById('citiesFrom').innerHTML = '';
            typingTimer = setTimeout(() => {
                fetch('/api/cities?' + new URLSearchParams({query: this.value}), {
                    method: 'GET',
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ошибка при получении списка городов');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success === true) {
                            data.cities.forEach(city => {
                                const li = document.createElement('li');
                                li.innerHTML = '<span>' + city.name + '</span><br/><small class="small text-muted">' + city.region + '</small>';
                                li.addEventListener('click', () => {
                                    citiesFromClick(city);
                                });
                                document.getElementById('citiesFrom').appendChild(li);
                            });
                            document.getElementById('citiesFrom').style.display = 'block';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Произошла ошибка при запросе городов:', error);
                    });
            }, typingDelay);
        }
    });

    function citiesFromClick(city) {
        document.getElementById('deliveryFrom').value = city.name;
        document.getElementById('cityFrom').value = city.id;

        document.getElementById('citiesFrom').innerHTML = '';
        document.getElementById('citiesFrom').style.display = 'none';
    }

    document.getElementById('deliveryTo').addEventListener('input', function () {
        clearTimeout(typingTimer);

        if (this.value.length >= 3) {
            document.getElementById('citiesTo').innerHTML = '';
            typingTimer = setTimeout(() => {
                fetch('/api/cities?' + new URLSearchParams({query: this.value}), {
                    method: 'GET',
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ошибка при получении списка городов');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success === true) {
                            data.cities.forEach(city => {
                                const li = document.createElement('li');
                                li.innerHTML = '<span>' + city.name + '</span><br/><small class="small text-muted">' + city.region + '</small>';
                                li.addEventListener('click', () => {
                                    citiesToClick(city);
                                });
                                document.getElementById('citiesTo').appendChild(li);
                            });
                            document.getElementById('citiesTo').style.display = 'block';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Произошла ошибка при запросе городов:', error);
                    });
            }, typingDelay);
        }
    })
    ;

    function citiesToClick(city) {
        document.getElementById('deliveryTo').value = city.name;
        document.getElementById('cityTo').value = city.id;

        document.getElementById('citiesTo').innerHTML = '';
        document.getElementById('citiesTo').style.display = 'none';
    }

    document.getElementById('calculateBtn').addEventListener('click', function () {
        document.getElementById('delivery-period').innerHTML = 'Идёт расчёт доставки...';
        document.getElementById('price-old').innerHTML = '';
        document.getElementById('price-new').innerHTML = '-';
        fetch('/api/calc/form?' + new URLSearchParams({
            deliveryFrom: document.getElementById('cityFrom').value,
            deliveryTo: document.getElementById('cityTo').value,
            radioFrom: document.querySelector('input[name="radioFrom"]:checked')?.value || '',
            radioTo: document.querySelector('input[name="radioTo"]:checked')?.value || '',
            volume: document.getElementById('volume').value,
            weight: document.getElementById('weight').value,
        }), {
            method: 'GET',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка при расчете');
                }
                return response.json();
            })
            .then(data => {
                if (data.success === true) {
                    console.log('Результат:', data);
                    document.getElementById('price-old').textContent = `${data.price.basePrice}₽`;
                    document.getElementById('price-new').textContent = `${data.price.price}₽`;
                    document.getElementById('delivery-period').textContent = getDeliveryTimeText(data.price.deliveryTime.from, data.price.deliveryTime.to);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Произошла ошибка:', error);
                alert(error);
            });
    });


    function getDeliveryTimeText(daysFrom, daysTo) {
        if (daysFrom === daysTo) {
            if (daysFrom === 1) {
                return 'на следующий день';
            }

            return `${daysFrom} ${daysFrom === 1 ? 'день' : (daysFrom >= 2 && daysFrom <= 4 ? 'дня' : 'дней')}`;
        }

        return `от ${daysFrom} до ${daysTo} дней`;
    }
</script>
