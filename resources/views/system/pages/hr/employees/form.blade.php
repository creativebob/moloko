<div class="grid-x">
    <div class="cell small-12 large-5">

        <employees-component
            :employee="{{ $employee }}"
            :user="{{ $user }}"
            :employment-history='@json($employmentHistory)'
            :vacancies='@json($vacancies)'
        >
            @csrf
        </employees-component>
    </div>

</div>
