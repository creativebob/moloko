<search-city-component
        :city="{{ $city }}"
        :required="{{ isset($required) ? 'true' : 'false' }}"
        name="{{ isset($name) ? $name : 'city_id' }}"
        :start-cities='@json($cities)'
></search-city-component>
