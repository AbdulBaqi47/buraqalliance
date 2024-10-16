
<select class="form-control kr-select2" required name="location" data-placeholder="Select a location">
    <option value=""></option>
    <option {{ isset($selected) && $selected === 'dubai' ? 'selected' : '' }} value="dubai">Dubai</option>
    <option {{ isset($selected) && $selected === 'sharjah' ? 'selected' : '' }} value="sharjah">Sharjah</option>
    <option {{ isset($selected) && $selected === 'abudhabi' ? 'selected' : '' }} value="abudhabi">Abu Dhabi</option>
    <option {{ isset($selected) && $selected === 'ummalqaiwain' ? 'selected' : '' }} value="ummalqaiwain">Umm al-Qaiwain</option>
    <option {{ isset($selected) && $selected === 'fujairah' ? 'selected' : '' }} value="fujairah">Fujairah</option>
    <option {{ isset($selected) && $selected === 'ajman' ? 'selected' : '' }} value="ajman">Ajman</option>
    <option {{ isset($selected) && $selected === 'rasalkhaimah' ? 'selected' : '' }} value="rasalkhaimah">Ra's al-Khaimah</option>
</select>