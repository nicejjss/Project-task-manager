@include('layouts.app')
@include('layouts.sidebar')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="/css/page/tasklist.css">
<div class="container">
    <div class="task-management">
        <!-- Search, Sort, and Filter Section inside a form -->
        <form id="search-form" method="GET">
            <div class="search-block">
                <!-- Search input -->
                <input name="title" type="text" id="task-title" placeholder="Search by task title"/>

                <!-- Sort dropdown -->
                <select id="sort-options" name="sort">
                    <option value="0">Sắp Xếp</option>
                    <option value="1">Thời Hạn ↑</option>
                    <option value="2">Thời Hạn ↓</option>
                    <option value="3">Độ Ưu Tiên ↑</option>
                    <option value="4">Độ Ưu Tiên ↓</option>
                </select>

                <!-- Parent filter dropdown (as a navigation-like button) -->
                <div class="filter-parent" id="filter-parent-dropdown" onclick="toggleFilterSection()">
                    Bộ Lọc
                </div>

                <!-- Search button -->
                <button type="submit" class="search-button">Tìm Kiếm</button>
            </div>
            <!-- Filter Section (appears below the Filter Tasks button) -->
            <div class="filter-section" id="filter-section" style="display: none;"> <!-- Initially hidden -->
                <!-- Assignee Filter -->
                <div class="filter-group">
                    <label for="assignee">Người Thực Hiện</label>
                    <div class="member-dropdown-btn" onclick="toggleDropdown('assigneeDropdown')">
                        <span id="assigneeDisplay">Nguời Thực Hiện</span>
                        <span>▼</span>
                    </div>
                    <div id="assigneeDropdown" class="member-dropdown-content" style="display: none;">
                        <input id="assignee" type="text" class="search-input" placeholder="Tìm kiếm..."
                               onkeyup="filterDropdown('assigneeDropdown', this.value)"/>
                        <input type="hidden" name="assignee" id="assigneeHidden" value=""/>
                        <ul id="assigneeList">
                            <li class="member-item" data-value="Tất Cả" onclick="setInputValue('assignee', '0', 'Tất Cả')">Tất Cả</li>
                            @if(count($members))
                                @foreach($members as $member)
                                    <li class="member-item" data-value="{{$member['name']}}" onclick="setInputValue('assignee', {{$member['id']}}, '{{$member['name']}}')">{{$member['name']}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Creator Filter -->
                <div class="filter-group">
                    <label for="creator">Người Tạo</label>
                    <div class="member-dropdown-btn" onclick="toggleDropdown('creatorDropdown')">
                        <span id="creatorDisplay">Người Tạo</span>
                        <span>▼</span>
                    </div>
                    <div id="creatorDropdown" class="member-dropdown-content" style="display: none;">
                        <input id="creator" type="text" class="search-input" placeholder="Tìm kiếm..."
                               onkeyup="filterDropdown('creatorDropdown', this.value)"/>
                        <input type="hidden" name="creator" id="creatorHidden" value=""/>
                        <ul id="creatorList">
                            <li class="member-item" data-value="Tất Cả" onclick="setInputValue('creator', '0', 'Tất Cả')">Tất Cả</li>
                            @if(count($members))
                                @foreach($members as $member)
                                    <li class="member-item" data-value="{{$member['name']}}" onclick="setInputValue('creator', {{$member['id']}}, '{{$member['name']}}')">{{$member['name']}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Task Type Filter -->
                <div class="filter-group">
                    <label for="task-type">Loại Công Việc</label>
                    <div class="member-dropdown-btn" onclick="toggleDropdown('taskTypeDropdown')">
                        <span id="taskTypeDisplay">Loại Công Việc</span>
                        <span>▼</span>
                    </div>
                    <div id="taskTypeDropdown" class="member-dropdown-content" style="display: none;">
                        <input id="taskType" type="text" class="search-input" placeholder="Tìm kiếm..."
                               onkeyup="filterDropdown('taskTypeDropdown', this.value)"/>
                        <input type="hidden" name="taskType" id="taskTypeHidden" value=""/>
                        <ul id="taskTypeList">
                            <li class="member-item" data-value="Tất Cả" onclick="setInputValue('taskType', '0', 'Tất Cả')">Tất Cả</li>
                            @if(count($types))
                                @foreach($types as $type)
                                    <li class="member-item" data-value="{{$type['tasktype_name']}}" onclick="setInputValue('taskType', {{$type['tasktype_id']}}, '{{$type['tasktype_name']}}')">{{$type['tasktype_name']}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Task Status Filter -->
                <div class="filter-group">
                    <label for="task-status">Trạng Thái</label>
                    <div class="member-dropdown-btn" onclick="toggleDropdown('taskStatusDropdown')">
                        <span id="taskStatusDisplay">Trạng Thái</span>
                        <span>▼</span>
                    </div>
                    <div id="taskStatusDropdown" class="member-dropdown-content" style="display: none;">
                        <input style="display: none" id="statusSearch" type="text" class="search-input" placeholder="Tìm kiếm..."/>
                        <input type="hidden" name="status" id="statusHidden" value=""/>
                        <ul id="taskStatusList">
                            <li class="member-item" data-value="Tất Cả" onclick="setInputValue('status', -99, 'Tất Cả')">Tất Cả</li>
                            <li class="member-item" data-value="Cần Thực Hiện" onclick="setInputValue('status', 0, 'Cần Thực Hiện')">Cần Thực Hiện</li>
                            <li class="member-item" data-value="Đang Thực Hiện" onclick="setInputValue('status', 1, 'Đang Thực Hiện')">Đang Thực Hiện</li>
                            <li class="member-item" data-value="Chờ Phê Duyệt" onclick="setInputValue('status', 2, 'Chờ Phê Duyệt')">Chờ Phê Duyệt</li>
                            <li class="member-item" data-value="Hoàn Thành" onclick="setInputValue('status', 3, 'Hoàn Thành')">Hoàn Thành</li>
                            <li class="member-item" data-value="Đã Đóng" onclick="setInputValue('status', 4, 'Đã Đóng')">Đã Đóng</li>
                        </ul>
                    </div>
                </div>
            </div>


        </form>

        <!-- Task List Section -->
        <div class="task-list" id="task-list">
            <div id="header">
                <!-- Table headers -->
                <div class="task-header task-header-title">Tên Công Việc</div>
                <div class="task-header task-max-width">Người Thực Hiện</div>
                <div class="task-header task-max-width">Thời Hạn</div>
                <div class="task-header task-max-width">Trạng Thái</div>
                <div class="task-header task-max-width">Độ Ưu Tiên</div>
            </div>

            <div id="result">
                @if(count($tasks))
                    @foreach($tasks as $task)
                        <a href="/project/{{$projectId}}/task/{{$task['task_id']}}" id="result-item">
                            <!-- Task row 1 -->
                            <div class="task-item task-item-title">
                                <h4>{{$task['title']}}</h4>
                            </div>
                            <div class="task-item task-max-width">
                                <img class="avatar-app" src="{{ $task['avatar'] ?? asset('avatar.png') }}" alt="{{$task['name']}}">
                                <p class="task-name">{{$task['name']}}</p>
                            </div>
                            <div class="task-item task-max-width">
                                <p class="{{ $task['isDeadline'] ? 'deadline-overdue' : 'deadline-upcoming' }}">
                                    {{$task['deadline']}}
                                </p>
                            </div>
                            <div class="task-item task-max-width">
                                <p class="status-{{$task['status']}}">{{$task['statusMessage']}}</p> <!-- Status with color -->
                            </div>
                            <div class="task-item task-max-width">
                                <p class="priority-{{$task['priority']}}">{{$task['priorityMessage']}}</p> <!-- Priority with color -->
                            </div>
                        </a>
                    @endforeach
                @else
                    <div id="result-item">Không Có Kết Quả</div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Include jQuery and Select2 JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="/js/page/tasklist.js"></script>
@include('layouts.footer')
