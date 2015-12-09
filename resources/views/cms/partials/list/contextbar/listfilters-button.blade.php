@foreach($list->listFilterCurrents() as $listFilterKey=>$listFilterInstanceId)

    <div class="dropdown pull-right normal-mode">

        <a class="btn navbar-btn btn-sublist" data-toggle="dropdown" data-target="#">
            {{ $list->listFilterContents()[$listFilterKey][$listFilterInstanceId] }}
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu pull-right">

            @foreach($list->listFilterContents()[$listFilterKey] as $listFilterId => $listFilterValue)
                <li>
                    <a href="{{ route('cms.list', ["category"=>$category->key(), "entity"=>$entity->key(), "sub"=>$listFilterKey.".".$listFilterId]) }}">
                        {{ $listFilterValue }}
                    </a>
                </li>
            @endforeach

        </ul>

    </div>

@endforeach
