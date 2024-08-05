{{--страница личный кабинет --}}
@extends('layouts.app')
@section('title')
    {{ "APM | КСТ | Личный кабинет" }}
@endsection
@section('content')
    <div class="profile container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card p-3 py-4">
                    <div class="text-center">
                        {{-- <img src="{{ asset('/storage/user.png') }}" width="100" class="rounded-circle" alt="user">--}}
                        <svg id="Layer_1" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink">
                            <linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="0" x2="112" y1="256"
                                            y2="256">
                                <stop offset="0" stop-color="#33a8d9"/>
                                <stop offset="1" stop-color="#4273b8"/>
                            </linearGradient>
                            <path
                                d="m512 256c0-141.159-114.841-256-256-256s-256 114.841-256 256c0 139.212 111.697 252.818 250.176 255.926 1.938.049 3.879.074 5.824.074s3.886-.025 5.823-.074c138.48-3.108 250.177-116.714 250.177-255.926zm-480 0c0-123.514 100.486-224 224-224s224 100.486 224 224c0 60.333-23.985 115.163-62.911 155.479-8.997-31.029-26.903-59.063-51.679-80.411-14.827-12.775-31.673-22.741-49.699-29.607 25.813-18.603 42.651-48.916 42.651-83.091 0-56.443-45.92-102.363-102.362-102.363-56.443 0-102.363 45.92-102.363 102.363 0 34.175 16.839 64.489 42.651 83.091-18.026 6.866-34.872 16.832-49.699 29.607-24.776 21.348-42.682 49.381-51.679 80.41-38.925-40.316-62.91-95.145-62.91-155.478zm224 224c-1.634 0-3.262-.027-4.887-.063-47.651-1.156-92.503-19.21-127.601-51.296 13.633-61.999 68.1-106.179 132.488-106.179 64.387 0 118.854 44.18 132.487 106.179-35.101 32.086-79.953 50.14-127.6 51.296-1.626.036-3.253.063-4.887.063zm0-191.268c-38.798 0-70.363-31.564-70.363-70.362s31.565-70.363 70.363-70.363 70.362 31.565 70.362 70.363-31.564 70.362-70.362 70.362z"
                                fill="url(#SVGID_1_)"/>
                        </svg>
                    </div>
                    <div class="text-center mt-3">
                        <span class="fonts bg-secondary p-1 px-4 rounded text-white me-4">
                            Роль в системе:
                            @if(Auth::user()->role == 'admin')
                                Администратор
                            @elseif(Auth::user()->role == 'responsible')
                                Ответственный группы руководителей проектов
                            @elseif(Auth::user()->role == 'proj_manager')
                                Руководитель проектов
                            @else
                                Неопределено
                            @endif
                        </span>
                        <span class="fonts bg-secondary p-1 px-4 rounded text-white">
                            Группы:
                            @if(isset($groups) && count($groups) > 0)
                                @foreach($groups as $group)
                                    {{ $group->name }}
                                    @if(!$loop->last)
                                        , <!-- добавляем запятую после каждой группы, кроме последней -->
                                    @endif
                                @endforeach
                            @else
                                Не определено
                            @endif
                        </span>
                        <h5 class="mt-4 mb-4 text-start"><strong class="me-4">ФИО:</strong> {{ Auth::user()->name }}
                        </h5>
                        <h5 class="mt-2 mb-5 text-start"><strong class="me-4">Почта:</strong> {{ Auth::user()->email }}
                        </h5>
                        <div class="buttons mb-5 d-flex gap-3 justify-content-center">
                            <div class="buttons d-flex gap-3 justify-content-center">
                                <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal"
                                        data-bs-target="#editProfileModal">Изменить данные
                                </button>
                                <button type="button" class="btn btn-secondary px-4" data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal">Сменить пароль
                                </button>
                            </div>
                        </div>
                        @if(Auth::user()->role == 'admin')
                        <div class="alert alert-secondary  mb-3">
                            <div class="accordion" id="usersAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#userList" aria-expanded="true"
                                                aria-controls="userList">
                                            Список руководителей проектов
                                        </button>
                                    </h2>
                                    <div id="userList" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                         data-bs-parent="#usersAccordion">
                                        <div class="accordion-body">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th>ФИО</th>
                                                    <th>Группа</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td>{{ $user->name }}</td>
                                                        <td>
                                                            @foreach($user->groups as $group)
                                                                {{ $group->name }}
                                                                @if(!$loop->last)
                                                                    ,
                                                                    <!-- добавляем запятую после каждой группы, кроме последней -->
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <a class="editUser btn btn-xs btn-info" href="#"
                                                               data-bs-toggle="modal" data-name="{{ $user->name }}"
                                                               data-group="@foreach($user->groups as $group){{ $group->name }}@if(!$loop->last),@endif @endforeach"
                                                               data-group-id="@foreach($user->groups as $group){{ $group->id }}@if(!$loop->last),@endif @endforeach"
                                                               data-bs-target="#editUserModal">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-secondary">
                            <div class="accordion" id="groupsAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingGroups">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#groupList" aria-expanded="true"
                                                aria-controls="groupList">
                                            Список групп
                                        </button>
                                    </h2>
                                    <div id="groupList" class="accordion-collapse collapse"
                                         aria-labelledby="headingGroups" data-bs-parent="#groupsAccordion">
                                        <div class="accordion-body">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Название группы</th>
                                                    <th>Пользователи</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach(App\Models\Group::all() as $group)
                                                    <tr>
                                                        <td>{{ $group->name }}</td>
                                                        <td>
                                                            <ol>
                                                                @foreach($group->users as $user)
                                                                    <li>{{ $user->name }}</li>
                                                                @endforeach
                                                            </ol>
                                                        </td>
                                                        <td>
                                                            <a class="editGroup btn btn-xs btn-info" href="#"
                                                               data-name="{{ $group->name }}"
                                                               data-id="{{ $group->id }}"
                                                               data-members="@foreach($group->users as $user){{ $user->name }}@if(!$loop->last),@endif @endforeach"
                                                               data-members-id="@foreach($group->users as $user){{ $user->id }}@if(!$loop->last),@endif @endforeach"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#editGroupModal">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a class="deleteGroup btn btn-xs btn-danger" href="#" data-bs-toggle="modal"
                                                               data-bs-target="#deleteGroupModal" data-group-id="{{ $group->id }}">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <a class="addGroup btn btn-xs btn-info" href="#"
                                               data-bs-toggle="modal"
                                               data-bs-target="#addGroupModal">
                                                Создать новую группу
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--------------------- Редактирование данных пользователя ---------------------}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
         aria-hidden="true">
        <div class="modal-dialog w-25">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Редактирование профиля</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Фамилия Имя</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email адрес</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ auth()->user()->email }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--------------------- Смена пароля ---------------------}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
         aria-hidden="true">
        <div class="modal-dialog w-25">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Смена пароля</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="password" class="form-label">Новый пароль</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить пароль</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--------------------- Редактирование список рук.проектов ---------------------}}
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
         aria-hidden="true">
        <div class="modal-dialog w-25">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Редактировать пользователя </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="edit_user" action="{{ route('profile.change-user') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_name" class="form-label">ФИО</label>
                            <input type="text" class="form-control userName" id="user_name" name="user_name" value="">
                        </div>
                        <div class="mb-3">
                            <label for="group" class="form-label">Группы:</label>
                            <div class="user_groups d-flex flex-column gap-1">
                                {{-- список групп пользователя --}}
                            </div>
                            <hr>
                            <input type="hidden" name="deleted_group" value="">

                            <button class="btn add_group">+ добавить группу</button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--------------------- Редактирование список групп ---------------------}}
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog w-25">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupModalLabel">Редактировать группу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="edit_group" action="{{ route('profile.change-group') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">Название группы</label>
                            <input type="text" class="form-control groupName" id="group_name" name="group_name" value="">
                            <input type="hidden" name="group_id" value="">
                        </div>
                        <div class="mb-3">
                            <label for="members" class="form-label">Состав группы:</label>
                            <div class="group_members d-flex flex-column gap-1">
                                {{-- список групп пользователя --}}
                            </div>
                            <hr>
                            <input type="hidden" name="deleted_member" value="">
                            <input type="hidden" name="group_id" value="">

                            <button class="btn add_member">+ добавить пользователя</button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--------------------- Создание новой группы ---------------------}}
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog w-25">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupModalLabel">Создать новую группу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="add_Newgroup" action="{{ route('profile.add-group') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="Newgroup_name" class="form-label">Название группы</label>
                            <input type="text" class="form-control" id="Newgroup_name" name="Newgroup_name" placeholder="Введите название группы" value="">
                        </div>
                        <div class="mb-3">
                            <label>Выберите участников группы:</label>
                            @foreach($users as $user)
                                <div>
                                    <input type="checkbox" name="Newgroup_member[]" id="user_{{ $user->id }}" value="{{ $user->id }}">
                                    <label for="user_{{ $user->id }}">
                                        {{ $user->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--------------------- Удаление группы ---------------------}}
    <div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog w-25">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteGroupModalLabel">Удаление группы</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить эту группу?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <form id="deleteGroupForm" action="{{ route('profile.delete-group') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="group_id_to_delete" id="group_id_to_delete">
                        <button type="submit" class="btn btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- ------ редактирование списка руководителей проектов ------ --}}
    <script>
        $(function () {
            // редактирование пользователя (передача данных в модалку)
            $(".editUser").click(function () {
                event.preventDefault();
                var userName = $(this).data('name');
                $('.userName').val(userName);

                $('.user_groups').empty();
                var groupsData = $(this).data('group'); // Получаем данные о группах пользователя
                var groupIds = $(this).data('group-id').split(','); // Получаем идентификаторы групп
                var groups = groupsData.split(','); // Разбиваем данные о группах на массив
                groups.forEach(function (groupName, index) {
                    var groupId = groupIds[index]; // Получаем идентификатор группы
                    var groupInput = '<div class="d-flex gap-2 align-items-center">' +
                        '<input type="text" class="form-control" value="' + groupName + '" readonly>' + // Текстовое поле для названия группы
                        '<input type="hidden" name="user_group[]" value="' + groupId + '">' + // Скрытое поле для id группы
                        '<div class="remove_group"><i class="fa fa-minus-square" aria-hidden="true"></i></div></div>';
                    $('.user_groups').append(groupInput);
                });
            });
            // Уникальный счетчик для идентификаторов селектов
            var selectCounter = 1;
            // добавление группы (добавление поля с выбором в форму)
            $(".add_group").click(function() {
                event.preventDefault();
                // Генерируем уникальный идентификатор для селекта
                var selectId = 'select_group_' + selectCounter;
                // Генерируем HTML для селекта с вариантами выбора группы
                var selectHtml = '<div class="d-flex gap-2 align-items-center">' +
                    '<select class="form-select user_group" name="user_group[]" id="' + selectId + '" required>' +
                    '<option value="" disabled selected>Выберите группу</option>';
                // Добавляем варианты выбора группы из базы данных
                @foreach(App\Models\Group::all() as $group)
                    selectHtml += '<option value="{{ $group->id }}">{{ $group->name }}</option>';
                @endforeach
                    selectHtml += '</select>' +
                    '<i class="fa fa-minus-square remove_group" aria-hidden="true"></i></div>';
                // Заменяем кнопку "Добавить группу" на селект
                $('.user_groups').append(selectHtml);
                // Увеличиваем счетчик
                selectCounter++;
            });

            // Массив для хранения идентификаторов групп, которые нужно удалить
            var groupsToDelete = [];
            // При клике на иконку минус у группы удалять
            $(document).on('click', '.remove_group', function() {
                console.log("Remove group button clicked");
                var selectValue = $(this).prev('input[type="hidden"]').val(); // Получаем значение скрытого поля
                console.log("Removed group id:", selectValue); // Добавьте эту строку для отладки
                if (selectValue !== "") {
                    groupsToDelete.push(selectValue);
                    console.log("Groups to delete:", groupsToDelete); // Добавьте эту строку для отладки
                }
                $(this).closest('.d-flex').remove(); // Удаляем родительский элемент после получения значения скрытого поля
                $('#deleted_group').val(groupsToDelete.join(','));
            });
        });
    </script>
    {{-- ------ редактирование групп ------ --}}
    <script>
        $(function () {
            $(".editGroup").click(function () {
                event.preventDefault();
                var groupName = $(this).data('name');
                var groupId = $(this).data('id');
                $('.groupName').val(groupName);
                $('input[name="group_id"]').val(groupId);

                $('.group_members').empty();
                var membersData = $(this).data('members'); // Получаем данные о группах пользователя
                var memberIds = $(this).data('members-id').split(','); // Получаем идентификаторы групп
                var members = membersData.split(','); // Разбиваем данные о группах на массив
                members.forEach(function (memberName, index) {
                    var memberId = memberIds[index]; // Получаем идентификатор группы
                    var memberInput = '<div class="d-flex gap-2 align-items-center">' +
                        '<input type="text" class="form-control" value="' + memberName + '" readonly>' + // Текстовое поле для названия группы
                        '<input type="hidden" name="group_member[]" value="' + memberId + '">' + // Скрытое поле для id группы
                        '<div class="remove_member"><i class="fa fa-minus-square" aria-hidden="true"></i></div></div>';
                    $('.group_members').append(memberInput);
                });
            });

            var selectCounter2 = 1;
            $(".add_member").click(function() {
                event.preventDefault();
                var selectId = 'select_member_' + selectCounter2;
                var selectHtml = '<div class="d-flex gap-2 align-items-center">' +
                    '<select class="form-select group_member" name="group_member[]" id="' + selectId + '" required>' +
                    '<option value="" disabled selected>Выберите пользователя</option>';
                @foreach(App\Models\User::all() as $user)
                    selectHtml += '<option value="{{ $user->id }}">{{ $user->name }}</option>';
                @endforeach
                    selectHtml += '</select>' +
                    '<i class="fa fa-minus-square remove_member" aria-hidden="true"></i></div>';
                $('.group_members').append(selectHtml);
                selectCounter2++;
            });

            var membersToDelete = [];
            $(document).on('click', '.remove_member', function() {
                var selectValue = $(this).prev('input[type="hidden"]').val();
                console.log("Removed member id:", selectValue);
                if (selectValue !== "") {
                    membersToDelete.push(selectValue);
                    console.log("Members to delete:", membersToDelete);
                }
                $(this).closest('.d-flex').remove();
                $('input[name="deleted_member"]').val(membersToDelete.join(','));
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('.deleteGroup').click(function() {
                var groupId = $(this).data('group-id');
                $('#group_id_to_delete').val(groupId);
            });
        });
    </script>

@endsection
