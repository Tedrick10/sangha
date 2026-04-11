@php
    $allowed = [10, 15, 25, 50, 100];
    $currentPerPage = $paginator->perPage();
    $total = $paginator->total();
    $from = $total > 0 ? $paginator->firstItem() : 0;
    $to = $paginator->lastItem() ?? 0;
    $query = request()->except(['page', 'per_page']);
    $routeParams = $routeParams ?? [];
@endphp
<div class="mt-4 flex flex-col gap-3 border-t border-slate-200 dark:border-slate-700 pt-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
    <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
        <form method="GET" action="{{ route($routeName, $routeParams) }}" class="flex items-center gap-2">
            @foreach($query as $key => $value)
                @if(is_array($value))
                    @foreach($value as $k => $v)
                        <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <label for="per_page" class="shrink-0 text-sm font-medium text-slate-600 dark:text-slate-400">{{ t('per_page') }}</label>
            <select name="per_page" id="per_page" onchange="this.form.submit()" class="admin-select min-h-11 py-2.5 px-4 min-w-0 w-full max-w-[11rem] sm:w-auto">
                @foreach($allowed as $n)
                    <option value="{{ $n }}" {{ (int) $currentPerPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </form>
        <span class="text-sm leading-snug text-slate-600 dark:text-slate-400 sm:max-w-[min(100%,28rem)]">
            @if($total > 0)
                {{-- Replace :total before :to — literal ":to" is a substring of ":total" and breaks with str_replace array order. --}}
                @php
                    $__show = t('showing_x_to_y_of_z');
                    $__show = str_replace(':total', (string) $total, $__show);
                    $__show = str_replace(':from', (string) $from, $__show);
                    $__show = str_replace(':to', (string) $to, $__show);
                @endphp
                {{ $__show }}
            @else
                {{ t('no_results') }}
            @endif
        </span>
    </div>
    @if($paginator->hasPages())
        <div class="flex w-full min-w-0 justify-center overflow-x-auto pb-0.5 sm:w-auto sm:justify-end [-webkit-overflow-scrolling:touch]">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
