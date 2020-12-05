@if($clientsData['mode'] == 1)
    <lead-search-in-data-component
        :lead="{{ $lead }}"
        :cities='@json($cities)'
        :city='@json($city)'
        :users='@json($clientsData->users)'
        :companies='@json($clientsData->companies)'
        :legal-forms='@json($legalForms)'
        :mailings='@json($mailings)'
    ></lead-search-in-data-component>
@else
    <lead-search-in-db-component
        :lead="{{ $lead }}"
        :cities='@json($cities)'
        :city='@json($city)'
        :legal-forms='@json($legalForms)'
        :mailings='@json($mailings)'
    ></lead-search-in-db-component>
@endif


















