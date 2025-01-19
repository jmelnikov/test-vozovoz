<?php

/** @var yii\web\View $this */

$this->title = 'Vozovoz Terminals';
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-md-8 mx-auto border border-secondary-subtle rounded-3 pt-3">
            <form class="d-flex">
                <div class="input-group mb-3">
                    <label for="cityName"></label>
                    <input type="text" class="form-control" placeholder="Название города" id="cityName">
                    <button class="btn btn-outline-primary" type="submit" id="searchBtn">Искать</button>
                </div>
            </form>
        </div>
    </div>
    <div id="terminals">

    </div>
</div>

<div class="row mb-3" id="terminal-info" style="display: none">
    <div class="col-md-8 mx-auto border border-secondary-subtle rounded-3 pt-3">
        <h3 class="mb-0 terminal-name">Строителей пр. 39 А</h3>
        <p class="mb-0 terminal-address">Саратов, пр. Строителей, д. 39 А</p>
        <p class="terminal-id text-muted">43ba2e9d-c94d-11e4-80bf-00155d903d03</p>

        <p class="mb-0">
            <strong>Описание:</strong>
            <span class="terminal-description">макс. вес 1-го места 1500 кг при габаритах 12,9м * 2,4м * 2,4м</span>
        </p>
        <p>
            <strong>Примечание:</strong>
            <span class="terminal-note">тел.скада:+7-987-820-14-63</span>
        </p>
        <p class="mb-0"><strong>Ограничения:</strong></p>
        <p class="mb-0">
            <em>Ширина:</em> от <span class="min-width">0</span> до <span class="max-width">2.4</span>
        </p>
        <p class="mb-0">
            <em>Длина:</em> от <span class="min-length">0</span> до <span class="max-length">2.4</span>
        </p>
        <p>
            <em>Высота:</em> от <span class="min-height">0</span> до <span class="max-height">2.4</span>
        </p>
    </div>
</div>


<script>
    document.getElementById('searchBtn').addEventListener('click', async function () {
        event.preventDefault();

        const query = document.getElementById('cityName').value.trim();
        let terminals = [];

        if (!query) {
            alert('Введите название города!');
            return;
        }

        const terminalsContainer = document.getElementById('terminals');
        terminalsContainer.innerHTML = 'Ищем терминалы...';

        try {
            await fetch(`/api/terminals?query=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка получения данных с API');
                    }
                    return response.json();
                })
                .then(data => {
                    terminals = data.terminals;
                    terminalsContainer.innerHTML = '';
                });

            if (!terminals.length) {
                const noResults = document.createElement('p');
                noResults.innerText = 'Терминалы не найдены.';
                terminalsContainer.appendChild(noResults);

                return;
            }

            terminals.forEach((terminal) => {
                const terminalInfo = document.getElementById('terminal-info').cloneNode(true);
                terminalInfo.style.display = 'block';

                terminalInfo.querySelector('.terminal-name').innerText = terminal.name || 'Нет названия';
                terminalInfo.querySelector('.terminal-address').innerText = terminal.address || 'Нет адреса';
                terminalInfo.querySelector('.terminal-id').innerText = terminal.id || 'Нет идентификатора';
                terminalInfo.querySelector('.terminal-description').innerText = terminal.description || 'Нет описания';
                terminalInfo.querySelector('.terminal-note').innerText = terminal.note || 'Нет примечаний';

                terminalInfo.querySelector('.min-width').innerText = terminal.conditions?.width?.min;
                terminalInfo.querySelector('.max-width').innerText = terminal.conditions?.width?.max;
                terminalInfo.querySelector('.min-length').innerText = terminal.conditions?.length?.min;
                terminalInfo.querySelector('.max-length').innerText = terminal.conditions?.length?.max;
                terminalInfo.querySelector('.min-height').innerText = terminal.conditions?.height?.min;
                terminalInfo.querySelector('.max-height').innerText = terminal.conditions?.height?.max;

                terminalsContainer.appendChild(terminalInfo);
            });
        } catch (error) {
            terminalsContainer.innerHTML = '<p>Терминалы не найдены</p>';
        }
    });
</script>
