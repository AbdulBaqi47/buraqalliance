{{-- <input type="hidden" name="kra_type" value="{{$type}}">
<input type="hidden" name="kra_alies" value="@isset($alies){{$alies}}@else{{__('amount')}}@endisset"> --}}


@isset($dropdown)
    <div kr-accounts-dropdown-wrapper @isset($name)kr-accounts-name="{{$name}}"@endisset @if(isset($selected) && $selected!=='')kr-accounts-selected="{{$selected}}"@endif @if(isset($unselect_by_default) && $unselect_by_default!=='')kr-accounts-unselect="{{$unselect_by_default}}"@endif></div>
@else
    <div kr-accounts-wrapper></div>
@endisset
