@extends('sharp::cms/cmslayout')

@section('viewname') sharp-list @stop

@section('navcol')
    @include("sharp::cms.navcol_partial", ["category"=>$category, "entityKey"=>$entityKey])
@stop

@section('contextbar')
    <p class="navbar-text">
        {{ Lang::choice('sharp::ui.list_title', $totalCount) }}
        @if($pagination && $pagination->getLastPage() > 1)
            {{ trans('sharp::ui.list_pagination', ['current'=>$pagination->getCurrentPage(), 'total'=>$pagination->getLastPage()]) }}
        @endif
    </p>

    {{-- Commands --}}
    @if(sizeof($entity->commands->data) && sizeof($entity->commands->list->data) && \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'update', $entityKey))
    <div class="dropdown navbar-right normal-mode">
        <a class="btn navbar-btn" data-toggle="dropdown" data-target="#"><i class="fa fa-caret-down"></i></a>
        <ul class="dropdown-menu">
            @foreach($entity->commands->list as $command)
            <li>
                <a href="{{ route('cms.listCommand', array_merge([$category->key, $entityKey, $command], Input::all())) }}" {{ $entity->commands->list->$command->type=="view" ? 'target="_blank"' : ''}}>
                {{ $entity->commands->list->$command->text }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Create button --}}
    @if($entity->list_template->creatable && \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'create', $entityKey))
        <a href="{{ route('cms.create', [$category->key, $entityKey]) }}" class="btn navbar-btn navbar-right normal-mode">
            <i class="fa fa-plus"></i> {{ trans('sharp::ui.list_newBtn') }}
        </a>
    @endif

    {{-- Reorder --}}
    @if($entity->list_template->reorderable
            && \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'update', $entityKey)
            && (!$entity->list_template->searchable || !Input::get('search')))
        <a id="sharp-reorder" class="btn navbar-btn navbar-right normal-mode"><i class="fa fa-sort"></i> {{ trans('sharp::ui.list_reorderBtn') }}</a>
        <a id="sharp-reorder-ok" href="{{ route('cms.reorder', [$category->key, $entityKey]) }}" class="btn navbar-btn navbar-right reorder-mode"><i class="fa fa-check"></i> {{ trans('sharp::ui.list_reorderOkBtn') }}</a>
        <a href="{{ URL::full() }}" class="btn navbar-btn navbar-right reorder-mode"><i class="fa fa-times"></i> {{ trans('sharp::ui.list_reorderCancelBtn') }}</a>
    @endif

    {{-- Sublist --}}
    @if($subList)
        <div class="dropdown navbar-right normal-mode">
            <a class="btn navbar-btn btn-sublist" data-toggle="dropdown" data-target="#">{{ $subLists[$subList] }} <span class="caret"></span></a>
            <ul class="dropdown-menu">
                @foreach($subLists as $idsl => $sl)
                    <li><a href="{{ URL::route('cms.list', ["category"=>$category->key, "entity"=>$entityKey, "sub"=>$idsl]) }}">{{ $sl }}</a></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Quick search --}}
    @if($entity->list_template->searchable)
        <form role="search" class="navbar-form navbar-right normal-mode" id="search" method="get">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="{{ trans('sharp::ui.list_searchPlaceholder') }}" value="{{ Input::get('search') }}">
                <span class="input-group-btn">
                    <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
            @foreach(Input::except(['search','page']) as $qs => $qsVal)
                {{ Form::hidden($qs, $qsVal) }}
            @endforeach
        </form>
    @endif

@stop

@section('content')

<table class="table table-responsive table-striped" id="entity-list">
    <thead>
    <tr>
        @foreach($entity->list_template->columns as $colkey => $col)
            <th class="col-xs-{{ $col->width }}">

                @if($col->sortable)
                    @if($sortedColumn == $colkey)
                        <a class="sort current"
                           href="{{ URL::route('cms.list', array_merge([$category->key, $entityKey], Input::except(['page']), ['sort'=>$colkey, 'dir'=>$sortedDirection=='asc'?'desc':'asc'])) }}">
                            {{ $col->header }} <i class="fa fa-angle-{{ $sortedDirection=='asc'?'up':'down' }}"></i>
                        </a>
                    @else
                        <a class="sort" href="{{ URL::route('cms.list', array_merge([$category->key, $entityKey], Input::except(['page','dir']), ['sort'=>$colkey])) }}">
                            {{ $col->header }} <i class="fa fa-angle-up"></i>
                        </a>
                    @endif
                @else
                    {{ $col->header }}
                @endif

            </th>
        @endforeach
        <th class="col-xs-2"></th>
    </tr>
    </thead>
    <tbody>
        @foreach($instances as $instance)
        <tr class="entity-row" data-entity_id="{{ $instance->id }}">
            @foreach($entity->list_template->columns as $colKey => $col)
                <td class="entity-data"
                    data-link="{{ \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'update', $entityKey) ? route('cms.edit', [$category->key, $entityKey, $instance->id]) : '' }}">
                    @if($col->renderer)
                        {{ \Dvlpp\Sharp\ListView\Renderers\SharpColumnRendererManager::render($col, $colKey, $instance) }}
                    @else
                        {{ $instance->$colKey }}
                    @endif
                </td>
            @endforeach

            <td class="actions">

                <div class="normal-mode">
                    @if($entity->active_state_field && \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'update', $entityKey))
                        <span class="state {{ $instance->{$entity->active_state_field}?'state-active':'state-inactive' }}">
                            <a href="{{ route('cms.deactivate', [$category->key, $entityKey, $instance->id]) }}"
                               class="btn btn-state-active ajax"
                               data-success="deactivate"><i class="fa fa-star"></i></a>
                            <a href="{{ route('cms.activate', [$category->key, $entityKey, $instance->id]) }}"
                               class="btn btn-state-inactive ajax"
                               data-success="activate"><i class="fa fa-star-o"></i></a>
                        </span>
                    @endif

                    {{-- Entity commands --}}
                    @if(sizeof($entity->commands->data) && sizeof($entity->commands->entity->data) && \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'update', $entityKey))
                        <div class="btn-group normal-mode">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                @foreach($entity->commands->entity as $command)
                                    <li>
                                        <a href="{{ route('cms.entityCommand', array_merge([$category->key, $entityKey, $command, $instance->id], Input::all())) }}" {{ $entity->commands->entity->$command->type=="view" ? 'target="_blank"' : ''}}>
                                            {{ $entity->commands->entity->$command->text }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

                @if(\Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'update', $entityKey) && $entity->list_template->reorderable)
                    <div class="reorder-mode">
                        <a href="#" class="btn reorder-handle"><i class="fa fa-sort"></i></a>
                    </div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($pagination)
    {{ $pagination->appends(Input::except(['page']))->links() }}
@endif

@stop

@section("scripts")
@parent
<script src="/packages/dvlpp/sharp/js/sharp.list.min.js"></script>
@stop