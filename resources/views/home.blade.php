@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-18">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2>Реестр</h2>
                </div>

                <div class="card__main">
                    <ul class="nav nav-mytabs" id="myTab" role="tablist">
                        <!-- Первая вкладка (активная) -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 1')) ||
                          ($RegSInteg->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 1')))
                            <li class="nav-item flex-sm-fill">
                                <a class="nav-link border-0 text-uppercase font-weight-bold active" id="SInteg-tab"
                                   data-bs-toggle="tab" href="#SInteg" role="tab" data-toggle="tab"
                                   aria-controls="SInteg"
                                   aria-selected="true">Группа 1</a>
                            </li>
                        @endif
                        <!-- Вторая вкладка -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 2')) ||
                          ($RegEOB->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 2')))
                            <li class="nav-item flex-sm-fill">
                                <a class="nav-link border-0 text-uppercase font-weight-bold" id="EOB-tab"
                                   data-bs-toggle="tab"
                                   href="#EOB" role="tab" data-toggle="tab" aria-controls="EOB"
                                   aria-selected="false">Группа 2</a>
                            </li>
                        @endif
                        <!-- Третья вкладка -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 3')) ||
                          ($RegNHRS->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 3')))
                            <li class="nav-item flex-sm-fill">
                                <a class="nav-link border-0 text-uppercase font-weight-bold" id="NHRS-tab"
                                   data-bs-toggle="tab"
                                   href="#NHRS" role="tab" data-toggle="tab" aria-controls="NHRS"
                                   aria-selected="false">Группа 3</a>
                            </li>
                        @endif
                        <!-- Четвёртая вкладка -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 4')) ||
                          ($RegOther->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 4')))
                            <li class="nav-item flex-sm-fill">
                                <a class="nav-link border-0 text-uppercase font-weight-bold" id="Other-tab"
                                   data-bs-toggle="tab"
                                   href="#Other" role="tab" data-toggle="tab" aria-controls="Other"
                                   aria-selected="false">Группа 4</a>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="select">
                            <select class="form-control d-none" id="locale">
                                <option value="ru-RU">ru-RU</option>
                            </select>
                        </div>
                        <!-- Содержимое первой вкладки -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 1')) ||
                         ($RegSInteg->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 1')))
                            <div class="tab-pane fade show active" id="SInteg" role="tabpanel"
                                 aria-labelledby="SInteg-tab">
                                <div class="card-body">
                                    <table id="table" data-toolbar="#toolbar" data-search="true"
                                           data-show-refresh="true"
                                           data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true"
                                           data-show-columns-toggle-all="true" data-detail-view="true"
                                           data-show-export="true"
                                           data-click-to-select="true" data-detail-formatter="detailFormatter"
                                           data-minimum-count-columns="12" data-show-pagination-switch="true"
                                           data-pagination="true"
                                           data-id-field="id" data-url="/getData_group_1"
                                           data-response-handler="responseHandler">
                                    </table>
                                </div>
                            </div>
                        @endif
                        <!-- Содержимое второй вкладки -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 2')) ||
                         ($RegEOB->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 2')))
                            <div class="tab-pane fade" id="EOB" role="tabpanel" aria-labelledby="EOB-tab">
                                <div class="card-body">
                                    <table id="table_eob" data-toolbar="#toolbar" data-search="true"
                                           data-show-refresh="true"
                                           data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true"
                                           data-show-columns-toggle-all="true" data-detail-view="true"
                                           data-show-export="true"
                                           data-click-to-select="true" data-detail-formatter="detailFormatter"
                                           data-minimum-count-columns="12" data-show-pagination-switch="true"
                                           data-pagination="true"
                                           data-id-field="id" data-url="/getData_group_2"
                                           data-response-handler="responseHandler">
                                    </table>
                                </div>
                            </div>
                        @endif
                        <!-- Содержимое третьей вкладки -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 3')) ||
                         ($RegNHRS->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 3')))
                            <div class="tab-pane fade" id="NHRS" role="tabpanel" aria-labelledby="NHRS-tab">
                                <div class="card-body">
                                    <table id="table_nhrs" data-toolbar="#toolbar" data-search="true"
                                           data-show-refresh="true" data-show-toggle="true" data-show-fullscreen="true"
                                           data-show-columns="true" data-show-columns-toggle-all="true"
                                           data-detail-view="true"
                                           data-show-export="true" data-click-to-select="true"
                                           data-detail-formatter="detailFormatter" data-minimum-count-columns="12"
                                           data-show-pagination-switch="true" data-pagination="true" data-id-field="id"
                                           data-url="/getData_group_3" data-response-handler="responseHandler">
                                    </table>
                                </div>
                            </div>
                        @endif
                        <!-- Содержимое четвёртой вкладки -->
                        @if($user->role === 'admin' || ($user->role === 'proj_manager' && $user->groups->contains('name', 'Группа 4')) ||
                         ($RegOther->contains('projectManager', $user->name)) || ($user->role === 'responsible' && $user->groups->contains('name', 'Группа 4')))
                            <div class="tab-pane fade" id="Other" role="tabpanel" aria-labelledby="Other-tab">
                                <div class="card-body">
                                    <table id="table_other" data-toolbar="#toolbar" data-search="true"
                                           data-show-refresh="true" data-show-toggle="true" data-show-fullscreen="true"
                                           data-show-columns="true" data-show-columns-toggle-all="true"
                                           data-detail-view="true"
                                           data-show-export="true" data-click-to-select="true"
                                           data-detail-formatter="detailFormatter" data-minimum-count-columns="12"
                                           data-show-pagination-switch="true" data-pagination="true" data-id-field="id"
                                           data-url="/getData_group_4" data-response-handler="responseHandler">
                                    </table>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let $table = $('#table');
        let $tableEob = $('#table_eob');
        let $tableNHRS = $('#table_nhrs');
        let $tableOther = $('#table_other');

        function cellStyle(value, row, index, field) {
            if (value === 1) {
                return {
                    classes: 'red-cell'
                };
            }
            return {};
        }
        let allProjects = [];
        $(function () {
            // Отправляем AJAX запрос и при получении данных инициализируем таблицу
            $.get('/getData_group_1', function (data) {
                initTable($table, data);
                // Получаем общее количество строк и устанавливаем атрибут data-total-rows
                let totalRows = data.length;
                $table.attr('data-total-rows', totalRows);
            });

            // Отправляем AJAX запрос и при получении данных инициализируем вторую таблицу
            $.get('/getData_group_2', function (data) {
                initTable($tableEob, data);
                // Получаем общее количество строк и устанавливаем атрибут data-total-rows
                let totalRows = data.length;
                $tableEob.attr('data-total-rows', totalRows);
            });

            // Отправляем AJAX запрос и при получении данных инициализируем вторую таблицу
            $.get('/getData_group_3', function (data) {
                initTable($tableNHRS, data);
                // Получаем общее количество строк и устанавливаем атрибут data-total-rows
                let totalRows = data.length;
                $tableNHRS.attr('data-total-rows', totalRows);
            });
            // Отправляем AJAX запрос и при получении данных инициализируем вторую таблицу
            $.get('/getData_group_4', function (data) {
                initTable($tableOther, data);
                // Получаем общее количество строк и устанавливаем атрибут data-total-rows
                let totalRows = data.length;
                $tableOther.attr('data-total-rows', totalRows);
            });
        });

        function detailFormatter(index, row) {
            let fieldNames = {
                'vnNum': 'Вн. Номер',
                'purchaseName': 'Хэштэг',
                'delivery': 'Поставка',
                'pir': 'ПИР',
                'kd': 'КД',
                'prod': 'Пр-во',
                'shmr': 'ШМР',
                'pnr': 'ПНР',
                'po': 'ПО',
                'smr': 'СМР',
                'purchaseOrg': 'Наим. орг. закупки',
                'object': 'объект',

                'proj_cost': 'Цена проекта',
                'profit': 'Прибыль',
                'marginality': 'Маржинальность',
                'remark': 'Примечание',
                'date_start': 'Дата начала',
                'date_end': 'Дата окончания',
            };
            let html = [];
            $.each(row, function (key, value) {
                let fieldName = fieldNames[key] ||
                    key; // Получаем название поля из объекта fieldNames или используем ключ, если название не найдено
                if (key !== 'area' && key !== 'id' && key !== 'tech' && key !== 'primeCost' && key !== 'tkpCost' &&
                    key !== 'notes' && key !== 'created_at' && key !== 'updated_at') {
                    html.push('<p><b>' + fieldName + ':</b> ' + value + '</p>');
                }
            });
            return html.join('');
        }

        // function getProjectIdByVnNum(vnNum) {
        //     let projectId = null;
        //     $.ajax({
        //         url: '/get-project-id/' + vnNum,
        //         type: 'GET',
        //         async: false, // Делаем запрос синхронным, чтобы получить результат перед возвратом
        //         success: function (data) {
        //             projectId = data;
        //         }
        //     });
        //     return projectId;
        // }
        // Загружаем все проекты и сохраняем в глобальную переменную
        $.get('/get-all-projects', function (data) {
            allProjects = data;
            initTables();
        });

        function getProjectIdByVnNum(vnNum) {
            let project = allProjects.find(proj => proj.projNum === vnNum);
            return project ? project.id : null;
        }

        function initTable($table, data) {
            $table.bootstrapTable('destroy').bootstrapTable({
                // height: 550,
                locale: $('#locale').val(),
                pagination: true,
                pageNumber: 1,
                pageSize: 10,
                pageList: [10, 25, 50, 'all'],
                columns: [
                    // [   {
                    //     title: 'Вн. Номер',
                    //     field: 'vnNum',
                    //     rowspan: 2,
                    //     align: 'center',
                    //     valign: 'middle',
                    //     sortable: true,
                    //     formatter: function (value, row, index, field) {
                    //         let vnNum = value; // Получаем значение поля 'vnNum'
                    //         // Преобразуем значение vnNum в JSON и передаем в JavaScript
                    //         // Вызываем JavaScript функцию для получения projectId
                    //         let projectId = getProjectIdByVnNum(vnNum);
                    //         // Создаем ссылку
                    //         let href = "/project-maps/all/" + projectId + '#calculation';
                    //         // Используем значение поля vnNum в качестве текста ссылки
                    //         return '<a href="' + href + '">' + vnNum + '</a>';
                    //     }
                    //     },
                    [{
                        title: 'Вн. Номер',
                        field: 'vnNum',
                        rowspan: 2,
                        align: 'center',
                        valign: 'middle',
                        sortable: true,
                        formatter: function (value, row, index, field) {
                            let projectId = getProjectIdByVnNum(value);
                            let href = "/project-maps/all/" + projectId + '#calculation';
                            return '<a href="' + href + '">' + value + '</a>';
                        }
                    },
                        {
                            field: 'purchaseName',
                            title: 'Хэштэг',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            field: 'proj_cost',
                            title: 'Цена проекта',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            field: 'profit',
                            title: 'Прибыль',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            field: 'payment',
                            title: 'Оплата факт. руб. без НДС',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            field: 'marginality',
                            title: 'Маржинальность %',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            field: 'remark',
                            title: 'Примечание',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true,
                            formatter: function (value, row, index, field) {
                                return '<div class="editable" data-field="' + field + '" data-id="' + row.id + '" contenteditable>' + value + '</div>';
                            }
                        },
                        {
                            title: 'Наим. орг. закупки',
                            field: 'purchaseOrg',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            title: 'Объект',
                            field: 'object',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            title: 'Руководитель проекта',
                            field: 'projectManager',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        },
                        {
                            title: 'Виды работ',
                            colspan: 8,
                            align: 'center'
                        },
                        {
                            field: 'date_start',
                            title: 'Дата начала',
                            rowspan: 2,
                            align: 'center',
                            valign: 'middle',
                            sortable: true
                        }, {
                        field: 'date_end',
                        title: 'Дата окончания',
                        rowspan: 2,
                        align: 'center',
                        valign: 'middle',
                        sortable: true
                    }],
                    [{

                        field: 'delivery',
                        title: 'Поставка',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return '';
                        }
                    }, {
                        field: 'pir',
                        title: 'ПИР',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return '';
                        }
                    }, {
                        field: 'kd',
                        title: 'КД',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return '';
                        }
                    }, {
                        field: 'prod',
                        title: 'Пр-во',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return '';
                        }
                    }, {
                        field: 'shmr',
                        title: 'ШМР',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return '';
                        }
                    }, {
                        field: 'pnr',
                        title: 'ПНР',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return ''; // скрываем значение
                        }
                    }, {
                        field: 'po',
                        title: 'ПО',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return ''; // скрываем значение
                        }
                    }, {
                        field: 'smr',
                        title: 'СМР',
                        sortable: true,
                        cellStyle: cellStyle,
                        formatter: function (value, row, index, field) {
                            return ''; // скрываем значение
                        }
                    }]
                ],
                data: data,
                ajaxOptions: {
                    success: function (data) {
                        $table.bootstrapTable('load', data);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                }
            });
        }
    </script>
@endsection
