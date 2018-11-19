<script type="text/javascript">

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
            // alert($(elem).val());
            let val = $(elem).val();

            // alert(val.match(/(\d{0,})/g));
            // let match = (/(\d)/g);
            // alert(match);
            let rega = new RegExp("(\d+)[^.]*((?:\.\d{0," + decimal + "})?)");
            alert(rega);
            let match = (rega).exec(elem.value.replace(/[^\d.]/g, ''));
            elem.value = match[1] + match[2];

            // var num = parseFloat($(elem).val());

            // // alert(num);
            // var cleanNum = num.toFixed(2);


            // $(elem).val(cleanNum);
            // if(num/cleanNum < 1){
            //     $('#error').text('Please enter only ' + 2 + ' decimal places, we have truncated extra points');
            // }

            // alert(min + ' ' + max);
            // alert(min + ' ' + cleanNum + ' ' + max);

            // if (val > max) {
            //     // alert(val.slice(-1));
            //     // $(elem).val(val.slice(-1));


            //     $(elem).val(max);
            // } else if (val < min) {
            //     // $(elem).val(val.slice(-1));
            //     $(elem).val(min);
            // }
        }
    }
</script>
