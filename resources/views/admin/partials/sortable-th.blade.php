{{--
    Sortable column header.
    Usage: @include('admin.partials.sortable-th', ['key' => 'name', 'label' => 'Name'])
    Optional: 'class' => 'w-[18%] min-w-[140px]', 'dataColumn' => 'name' (for column visibility)
--}}
@php
    $sort = request('sort', '');
    $order = request('order', 'asc');
    $isActive = $sort === $key;
    $nextOrder = $isActive && $order === 'asc' ? 'desc' : 'asc';
    $params = request()->except(['sort', 'order', 'page']);
    $params['sort'] = $key;
    $params['order'] = $nextOrder;
    $routeName = Request::route()->getName();
    $routeParams = Request::route()->parameters();
    $url = route($routeName, array_merge($routeParams, $params));
    $thClass = $class ?? '';
    $dataColumn = $dataColumn ?? $key ?? null;
@endphp
<th @if($dataColumn) data-column="{{ $dataColumn }}" @endif class="{{ $thClass }}">
    <a href="{{ $url }}" class="inline-flex items-center gap-1 group hover:text-amber-600 transition-colors">
        <span>{{ $label }}</span>
        <span class="flex flex-col">
            @if($isActive)
                @if($order === 'asc')
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    <svg class="w-3.5 h-3.5 -mt-1.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                @else
                    <svg class="w-3.5 h-3.5 -mt-1.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                @endif
            @else
                <svg class="w-3.5 h-3.5 -mt-1.5 text-slate-300 group-hover:text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            @endif
        </span>
    </a>
</th>
