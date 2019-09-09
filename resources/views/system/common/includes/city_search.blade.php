<city-search-component
        :city="{{ $city }}"
        :required="{{ isset($required) ? 'true' : 'false' }}"
        name="{{ isset($name) ? $name : 'city_id' }}"
></city-search-component>