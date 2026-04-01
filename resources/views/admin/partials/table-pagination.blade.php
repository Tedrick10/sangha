@php
    $allowed = [10, 15, 25, 50, 100];
    $currentPerPage = $paginator->perPage();
    $total = $paginator->total();
    $from = $total > 0 ? $paginator->firstItem() : 0;
    $to = $paginator->lastItem() ?? 0;
    $query = request()->except(['page', 'per_page']);
@endphp
<div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 dark:border-slate-700 pt-4">
    <div class="flex flex-wrap items-center gap-4">
        <form method="GET" action="{{ route($routeName) }}" class="flex items-center gap-2">
            @foreach($query as $key => $value)
                @if(is_array($value))
                    @foreach($value as $k => $v)
                        <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <label for="per_page" class="text-sm text-slate-600 dark:text-slate-400">{{ t('per_page') }}</label>
            <select name="per_page" id="per_page" onchange="this.form.submit()" class="admin-select py-2 px-4 min-w-0 w-auto">
                @foreach($allowed as $n)
                    <option value="{{ $n }}" {{ (int) $currentPerPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </form>
        <span class="text-sm text-slate-600 dark:text-slate-400">
            @if($total > 0)
                {{ str_replace([':from', ':to', ':total'], [$from, $to, $total], t('showing_x_to_y_of_z')) }}
            @else
                {{ t('no_results') }}
            @endif
        </span>
    </div>
    @if($paginator->hasPages())
        <div class="flex items-center">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
