<script type="application/javascript">

    'use strict';

    class MetricNumeric {

        constructor(decimal) {
            this.decimal = decimal;
            // this.min = min;
            // this.max = max;
        }

        check(elem) {
            let decimal = this.decimal;
            let min = $(elem).attr('min');
            let max = $(elem).attr('max');
            let val = $(elem).val();
            // alert();

            if (decimal == 0) {
                // alert(Number(val) > max);
                // alert(val);

                if (Number(val) > max) {
                    // alert(max);
                    $(elem).val(val.slice(0, -1));

                } else if (Number(val) < min) {
                    // alert(min);
                    $(elem).val(val.slice(0, -1));

                };
            } else if (decimal > 1) {

                let match;

                if (decimal == 1) {
                    match =  (/(\d+)[^.]*((?:\.\d{1})?)/).exec(elem.value.replace(/[^\d.]/g, ''));
                } else if (decimal == 2) {
                    match =  (/(\d+)[^.]*((?:\.\d{0,2})?)/).exec(elem.value.replace(/[^\d.]/g, ''));
                } else if (decimal == 3) {
                    match =  (/(\d+)[^.]*((?:\.\d{0,3})?)/).exec(elem.value.replace(/[^\d.]/g, ''));
                    // match =  (/(\d+)[^.]*((?:\.)?)[^\d]*((?:\d{0,3})?)/).exec(elem.value.replace(/[^\d.]/g, ''));
                } else if (decimal == 4) {
                    match =  (/(\d+)[^.]*((?:\.\d{0,4})?)/).exec(elem.value.replace(/[^\d.]/g, ''));
                }
                // alert(match[2]);

                let value = match[1] + match[2];
                // alert(value);

                if (value > Number(max)) {
                    // alert(max);
                    elem.value = value.slice(0, -1);

                } else if (val < Number(min)) {
                    // alert(min);
                    elem.value = value.slice(0, -1);

                } else {
                    elem.value = value;
                };
            };

            // let match =  regexp.exec(elem.value.replace(/[^\d.]/g, ''));
            // // let str = $(elem).val();
            // // let match =  (/(\d+)[^.]*((?:\.\d{0,3})?)/).exec(elem.value.replace(/[^\d.]/g, ''));
            // // alert(match);
            // elem.value = match[1] + match[2];
            // alert(regexp);
            // alert(str);
            // alert(res);
        }
    }

    class MetricList {

        constructor(id) {
            this.id = id;
        }

        check(elem) {

            let list = $(elem).closest('.checkbox-group');
            if (list.data('required') == 1) {
                let error = list.find("input:checkbox:checked").length;

                if (error == 0) {
                    $('div[data-toggle=metric-' + this.id + '-dropdown]').find('.form-error').show();
                } else {
                    $('div[data-toggle=metric-' + this.id + '-dropdown]').find('.form-error').hide();
                };
            }


            // $('#add-cur-goods').prop('disabled', error == 0);
        }
    }
</script>
