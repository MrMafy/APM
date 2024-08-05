@if ($project && $project->risks && $project->risks->first())

    <div class="mb-3">
        <select class="form-control d-none" id="locale">
            <option value="ru-RU">ru-RU</option>
        </select>
        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#risksModal">
            Добавить риски
        </button>

        <table id="risksTable" data-toolbar="#toolbar" data-search="true"
               data-show-refresh="true" data-show-toggle="true" data-show-fullscreen="true"
               data-show-columns="true" data-show-columns-toggle-all="true" data-detail-view="true"
               data-show-export="true" data-click-to-select="true"
               data-detail-formatter="detailFormatter" data-minimum-count-columns="12"
               data-show-pagination-switch="true" data-pagination="true" data-id-field="id"
               data-url="/getData_group_4" data-response-handler="responseHandler">

            <thead>
                <tr>
                    <th>№</th>
                    <th>Наименование риска</th>
                    <th>Причина риска</th>
                    <th>Последствия наступления риска</th>
                    <th>Вероятность</th>
                    <th>Влияние</th>
                    <th>Оценка</th>
                    <th>Стратегия</th>
                    <th>Противодействие риску</th>
                    <th>Срок</th>
                    <th>Отметка о реализации мероприятий в отношении рисков</th>
                    <th>Мероприятия при осуществлении риска</th>
                    <th>Ответственный за выполнение мероприятий</th>
                    <th>Срок</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($project->risks ?? [] as $item)
                    <tr data-id="{{ $item->id }}" data-project-id="{{ $project->id }}">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->risk_name }}</td>
                        <td>
                            <ol class="json_field">
                                @foreach (json_decode($item->risk_reason) as $reason)
                                    <li>{{ $reason->reasonRisk }}</li>
                                @endforeach
                            </ol>
                        </td>
                        <td>
                            <ol class="json_field">
                                @foreach (json_decode($item->risk_consequences) as $consequence)
                                    <li>{{ $consequence->conseqRiskOnset }}</li>
                                @endforeach
                            </ol>
                        </td>
                        <td>{{ $item->risk_probability }}</td>
                        <td>{{ $item->risk_influence }}</td>
                        <td>{{ $item->risk_estimate }}</td>
                        <td>{{ $item->risk_strategy }}</td>
                        <td>
                            <ol class="json_field">
                                @foreach (json_decode($item->risk_counteraction) as $counteraction)
                                    <li>{{ $counteraction->counteringRisk }}</li>
                                @endforeach
                            </ol>
                        </td>
                        <td>{{ $item->risk_term }}</td>
                        <td>{{ $item->risk_mark }}</td>
                        <td>
                            <ol class="json_field">
                                @foreach (json_decode($item->risk_measures) as $measure)
                                    <li>{{ $measure->riskManagMeasures }}</li>
                                @endforeach
                            </ol>
                        </td>
                        <td>{{ $item->risk_responsible }}</td>
                        <td>{{ $item->risk_endTerm }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a class="editProduct btn btn-xs btn-info" href="#" data-bs-toggle="modal"
                                    data-bs-target="#editRisks" data-id="{{ $item->id }}"><i
                                        class="fa-solid fa-edit"></i></a>
                                <a class="deleteProduct btn btn-xs btn-danger" href="#" data-bs-toggle="modal"
                                    data-bs-target="#confirmationModal" data-id="{{ $item->id }}"><i
                                        class="fa-solid fa-trash-can"></i></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{-- Модальное окно редактирования риска --}}
    <div class="modal fade" id="editRisks" tabindex="-1" role="dialog" aria-labelledby="editeRisksLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form id="editRisksForm" action="{{ route('risks-update', ['id' => $item->id]) }}" method="post">
                @csrf
                {{-- @method('put') --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRisksLabel">Редактирование риска "{{ $item->risk_name }}"</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="projectId" value="{{ $project->id }}">
                        <input type="hidden" name="editItemId" id="editItemId">
                        <input type="hidden" name="jsonData" id="jsonData">

                        <div class="form-group mb-3">
                            <label>Наименование риска:</label>
                            <p>{{ $item->risk_name }}</p>
                        </div>
                        <div class="form-group mb-3">
                            <label>Причина риска:</label>
                            <ol class="json_field">
                                @foreach (json_decode($item->risk_reason) as $reason)
                                    <li>{{ $reason->reasonRisk }}</li>
                                @endforeach
                            </ol>
                        </div>
                        <div class="form-group mb-3">
                            <label>Последствия наступления риска:</label>
                            <ol class="json_field">
                                @foreach (json_decode($item->risk_consequences) as $consequence)
                                    <li>{{ $consequence->conseqRiskOnset }}</li>
                                @endforeach
                            </ol>
                        </div>
                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_probability">Вероятность: </label>
                            <select name="risk_probability" id="risk_probability-select">
                                @foreach ([1, 2, 4, 8, 16] as $value)
                                    <option value="{{ $value }}"
                                        {{ $value == $item->risk_probability ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_influence">Влияние: </label>
                            <select name="risk_influence" id="risk_influence-select">
                                @foreach ([1, 2, 4, 8, 16] as $value)
                                    <option value="{{ $value }}"
                                        {{ $value == $item->risk_influence ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="risk_mark">Отметка о реализации мероприятий в отношении рисков:</label>
                            <select name="risk_mark" id="risk_mark-select">
                                <option value="Выполнено" {{ $item->risk_mark == 'Выполнено' ? 'selected' : '' }}>
                                    Выполнено</option>
                                <option value="Не выполнено"
                                    {{ $item->risk_mark == 'Не выполнено' ? 'selected' : '' }}>Не выполнено</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="risk_resp">Ответственный за выполнение мероприятий:</label>
                            <input type="text" class="form-control" name="risk_responsible" id="risk_resp"
                                placeholder="Введите ответственного" value="{{ $item->risk_responsible }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="risk_endTerm">Срок:</label>
                            <input type="text" class="form-control" name="risk_endTerm" id="risk_endTerm"
                                placeholder="Введите срок" value="{{ $item->risk_endTerm }}">
                        </div>
                    </div>
                    {{-- Кнопки --}}
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Модальное окно уведомление подтверждение об удалении записи --}}
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Подтверждение действия</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="projectId" class="modalId" data-id="{{ $project->id }}">
                    Вы уверены что хотите удалить риск "{{ $item->risk_name }}"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Удалить</button>
                </div>
            </div>
        </div>
    </div>
@else
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#risksModal">
        Добавить риски
    </button>

@endif


{{-- Модальное окно добавления рисков к карте проекта --}}
<div class="modal fade" id="risksModal" tabindex="-1" aria-labelledby="risksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('risks-store', $project->id) }}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="risksModalLabel">Добавление рисков к карте проекта</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="dependentFields" class="input-field">
                        <div class="form-group mb-3">
                            <label for="risk_name">Наименование риска</label>
                            <select class="form-select" name="risk_name" id="risk_name" required>
                                <option value="" disabled selected>Выберите наименование</option>
                                @foreach ($baseRisks as $baseRisk)
                                    <option value="{{ $baseRisk->nameRisk }}">
                                        {{ $baseRisk->nameRisk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3 hidden-field" style="display: none;">
                            <label for="risk_reason">Причина риска</label>
                            <ul class="json_field" id="reasonList"></ul>
                        </div>

                        <div class="form-group mb-3 hidden-field" style="display: none;">
                            <label for="risk_consequences">Последствия наступления риска</label>
                            <ul class="json_field" id="consequenceList"></ul>
                        </div>

                        <div class="form-group mb-3 hidden-field" style="display: none;">
                            <label for="risk_counteraction">Противодействие риску</label>
                            <ul class="json_field" id="counteringRiskList"></ul>
                        </div>

                        <div class="form-group mb-3 d-flex flex-column hidden-field" style="display: none;">
                            <label for="risk_term">Срок</label>
                            <input id="termList" type="text" class="input_editable" name="risk_term"
                                value="" readonly>
                        </div>

                        <div class="form-group mb-3 hidden-field" style="display: none;">
                            <label for="risk_measures">Мероприятия при осуществлении риска</label>
                            <ul class="json_field" id="riskManagMeasuresList"></ul>
                        </div>

                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_probability">Вероятность: </label>
                            <select name="risk_probability" id="risk_probability-select" required>
                                <option value="">Выберите вероятность</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="8">8</option>
                                <option value="16">16</option>
                            </select>
                        </div>

                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_influence">Влияние: </label>
                            <select name="risk_influence" id="risk_influence-select" required>
                                <option value="">Выберите влияние</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="8">8</option>
                                <option value="16">16</option>
                            </select>
                        </div>

                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_mark">Отметка о реализации мероприятий в отношении рисков: </label>
                            <select name="risk_mark" id="risk_mark-select" required>
                                <option value="">Выберите отметку</option>
                                <option value="Выполнено">Выполнено</option>
                                <option value="Не выполнено">Не выполнено</option>
                            </select>
                        </div>

                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_resp">Отвественный за выполнение мероприятий</label>
                            <input class="input_editable" id="risk_resp" type="text" name="risk_resp"
                                placeholder="Введите ФИО и должность" required>
                        </div>
                        <div class="form-group mb-3 d-flex flex-column">
                            <label for="risk_endTerm">Срок</label>
                            <input class="input_editable" id="risk_endTerm" type="text" name="risk_endTerm"
                                placeholder="Введите срок" required>
                        </div>

                    </div>
                </div>
                {{-- Кнопки --}}
                <div class="modal-footer d-flex justify-content-between">
                    <button type="submit" class="btn btn-success mt-3" id='submitBtn'>Сохранить</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // таблица DataTable
    $(document).ready(function() {

        $(function () {
            var $table = $('#risksTable');

            // инициализация таблицы и ее настроек
            function initTable($table) {
                $table.bootstrapTable({
                    locale: $('#locale').val(),
                    pagination: true,
                    pageNumber: 1,
                    pageSize: 5,
                    pageList: [5, 15, 50, 'all'],
                    columns:
                        [
                            {
                                field: 'id',
                                title: '№',
                                valign: 'middle',
                                sortable: true,
                            },
                            {
                                field: 'nameRisk',
                                title: 'Наименование риска',
                                valign: 'middle',
                                sortable: true,
                            },
                            {
                                field: 'reasonRisk',
                                title: 'Причина риска',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'conseqRiskOnset',
                                title: 'Последствия наступления риска',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_probability',
                                title: 'Вероятность',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_influence',
                                title: 'Влияние',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_estimate',
                                title: 'Оценка',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_strategy',
                                title: 'Стратегия',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'counteringRisk',
                                title: 'Противодействие риску',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_term',
                                title: 'Срок',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_mark',
                                title: 'Отметка о реализации мероприятий в отношении рисков',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'riskManagMeasures',
                                title: 'Мероприятия при осуществлении риска',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_responsible',
                                title: 'Ответственный за выполнение мероприятий',
                                valign: 'middle',
                                sortable: true
                            },
                            {
                                field: 'risk_endTerm',
                                title: 'Срок',
                                valign: 'middle',
                                sortable: true
                            }
                        ]
                });

                // привязываем обработчик событий к родительскому элементу таблицы
                $table.on('click', '.editProduct', function (event) {
                    event.preventDefault();
                    var itemId = $(this).closest('tr').data('id');
                    var nameRiskToEdit = $(this).closest('tr').find('[data-label="nameRisk"]').text();
                    // console.log(nameRiskToEdit);
                    // console.log(itemId);
                    let modal = $('#editBase');
                    modal.find('.modal-title').text(`Редактирование риска "${nameRiskToEdit}"`);
                    modal.data('nameRisk',
                        nameRiskToEdit); // Добавляем атрибут data-nameRisk к модальному окну
                    modal.find('#editItemId').val(itemId);
                    fillEditModal(itemId);
                    modal.modal('show');
                });
            }

            // Функция для заполнения модального окна данными
            function fillEditModal(itemId) {
                var modalIdRisks = '#editBase';
                var formActionRisks = '{{ route('baseRisks-update', ['id' => ':id']) }}'.replace(':id', itemId);

                $(modalIdRisks + ' #editItemId').val(itemId);
                $(modalIdRisks + ' #editBaseForm').attr('action', formActionRisks);

                // Отправляем AJAX-запрос для получения данных из базы данных
                $.ajax({
                    url: '/get-base-risk/' + itemId,
                    type: 'GET',
                    success: function (response) {
                        // console.log(response)
                        $(modalIdRisks + ' #nameRiskEdit').val(response.nameRisk);
                        $(modalIdRisks + ' #term_Edit').val(response.term);

                        // Преобразуем строки JSON в массивы объектов для каждого поля
                        var reasonRiskData = JSON.parse(response.reasonRisk);
                        var conseqRiskData = JSON.parse(response.conseqRiskOnset);
                        var counteringRiskData = JSON.parse(response.counteringRisk);
                        var measuresRiskData = JSON.parse(response.riskManagMeasures);

                        // Добавляем причины риска
                        var reasonRiskInputs = '';
                        $.each(reasonRiskData, function (index, reason) {
                            reasonRiskInputs +=
                                '<input type="text" class="form-control mb-2" name="reason_risk_edit[]" value="' +
                                reason.reasonRisk +
                                '" placeholder="Введите причину риска">';
                        });
                        $(modalIdRisks + ' #reasonRiskEdit').html(reasonRiskInputs);

                        // Добавляем последствия наступления риска
                        var conseqRiskInputs = '';
                        $.each(conseqRiskData, function (index, conseq) {
                            conseqRiskInputs +=
                                '<input type="text" class="form-control mb-2" name="conseq_risk_edit[]" value="' +
                                conseq.conseqRiskOnset +
                                '" placeholder="Введите последствия наступления риска">';
                        });
                        $(modalIdRisks + ' #conseqRiskOnsetEdit').html(conseqRiskInputs);

                        // Добавляем противодействие риску
                        var counteringRiskInputs = '';
                        $.each(counteringRiskData, function (index, countering) {
                            counteringRiskInputs +=
                                '<input type="text" class="form-control mb-2" name="countering_risk_edit[]" value="' +
                                countering.counteringRisk +
                                '" placeholder="Введите противодействие риску">';
                        });
                        $(modalIdRisks + ' #counteringRiskEdit').html(counteringRiskInputs);

                        // Добавляем мероприятия при осуществлении риска
                        var measuresRiskInputs = '';
                        $.each(measuresRiskData, function (index, measure) {
                            measuresRiskInputs +=
                                '<input type="text" class="form-control mb-2" name="measures_risk_edit[]" value="' +
                                measure.riskManagMeasures +
                                '" placeholder="Введите мероприятия при осуществлении риска">';
                        });
                        $(modalIdRisks + ' #riskManagMeasuresEdit').html(measuresRiskInputs);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Инициализация таблицы при загрузке страницы
            initTable($table);
        });




        // Обработчик изменений в поле выбора "Наименование риска"
        $('#risk_name').change(function() {
            // Очищаем списки
            $('#reasonList').empty();
            $('#consequenceList').empty();
            $('#counteringRiskList').empty();
            $('#riskManagMeasuresList').empty();

            // Получаем выбранное значение
            var selectedOption = $(this).find(':selected');
            var selectedRisk = selectedOption.val();

            // Запрос на сервер для получения данных по выбранному риску
            $.ajax({
                url: '/getRiskData',
                method: 'GET',
                data: {
                    risk: selectedRisk
                },
                success: function(response) {
                    $('.hidden-field').css("display", "block");
                    // Обновляем переменные и отображаем данные
                    response.reasonData.forEach(function(reason, index) {
                        $('#reasonList').append(
                            '<li class="mb-3"><input type="text" class="input_editable" required readonly name="risk_reason[' +
                            index + '][reasonRisk]" value="' + reason
                            .reasonRisk + '"</li>');
                    });

                    response.consequenceData.forEach((consequence, index) => {
                        $('#consequenceList').append(
                            '<li class="mb-3"><input type="text" class="input_editable" required readonly name="risk_consequences[' +
                            index + '][conseqRiskOnset]" value="' + consequence
                            .conseqRiskOnset + '"</li>');
                    });

                    if (Array.isArray(response.counteringRiskData)) {
                        response.counteringRiskData.forEach(function(counteringRisk,
                            index) {
                            $('#counteringRiskList').append(
                                '<li class="mb-3"><input type="text" class="input_editable" required readonly name="risk_counteraction[' +
                                index + '][counteringRisk]" value="' +
                                counteringRisk.counteringRisk + '"</li>');
                        });
                    }

                    if (Array.isArray(response.riskManagMeasuresData)) {
                        response.riskManagMeasuresData.forEach(function(measure, index) {
                            if (typeof measure === 'object') {
                                // Если это объект, выведите свойства объекта
                                for (var prop in measure) {
                                    if (measure.hasOwnProperty(prop)) {
                                        $('#riskManagMeasuresList').append(
                                            '<li class="mb-3"><input type="text" class="input_editable" required readonly name="risk_measures[' +
                                            index +
                                            '][riskManagMeasures]" value="' +
                                            measure[prop] + '"</li>');
                                    }
                                }
                            } else {
                                // В противном случае просто выведите значение
                                $('#riskManagMeasuresList').append('<li>' +
                                    measure + '</li>');
                            }
                        });
                    }

                    // Устанавливаем значение поля "Срок" из базы данных
                    $('#termList').val(response.term);
                },
                error: function(error) {
                    console.error('Error fetching risk data:', error);
                }
            });
        });


        // Подтверждение удаления
        let itemIdToDelete;
        $('#confirmationModal').on('show.bs.modal', function(event) {
            itemIdToDelete = $(event.relatedTarget).data('id');
            projId = $(".modalId").data('id');
        });
        $('#confirmDelete').click(function() {
            $.ajax({
                method: 'GET',
                url: `/project-maps/risk-delete/${itemIdToDelete}`,
                success: function(data) {
                    toastr.success('Запись была удалена', 'Успешно');
                    let projectId = data.projectId;
                    setTimeout(function() {
                        window.location.href = `/project-maps/all/${projId}/#risks`;
                    }, 1000);
                },
                error: function(error) {
                    if (error.responseText) {
                        toastr.error(error.responseText, 'Ошибка');
                    } else {
                        toastr.error('Ошибка удаления', 'Ошибка');
                    }
                }
            });
            $('#confirmationModal').modal('hide');
        });


        // Обязательные поля ввода
        function validateAndSubmit() {
            // Удаление предыдущих стилей ошибок
            $('.required-field').removeClass('required-field');
            $('.error-message').remove();

            // Проверка каждого обязательного поля
            $('#dependentFields :input[required]').each(function() {
                const fieldValue = $(this).val();
                if (!fieldValue.trim()) {
                    // Выделение пустого поля красной рамкой
                    $(this).addClass('required-field');

                    // Отображение сообщения об ошибке
                    const errorMessage = $(
                        '<div class="error-message">Обязательное поле для заполнения</div>');
                    $(this).parent().append(errorMessage);
                }
            });
        }
        // Привязка функции validateAndSubmit к событию клика кнопки отправки
        $('#submitBtn').click(function() {
            // console.log('Button clicked!');
            validateAndSubmit();
        });

    });
</script>
