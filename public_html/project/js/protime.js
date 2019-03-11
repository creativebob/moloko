
            function proTime(aaa) {

                id_input = aaa.getAttribute('id');

                time = document.getElementById(id_input).value;
                len_time = time.length;

                if (len_time > 4){

                    StatValid = CNF(time);
                    if (StatValid == false){
                        document.getElementById(id_input).value = "";
                    };  
                };

            };

            function CNF(n){
                  var reg=/^([0-1][0-9]|[2][0-3]):[0-5][0-9]$/
                  if (!reg.test(n)) return false;
            };

            function tomin(z){
                        var mymass = z.split(":");
                        h = mymass[0] * 1;
                        m = mymass[1] * 1;

                        min_begin = (h*60) + m;
                        return min_begin;
            };

            function getDiffer(){
                document.getElementById('tz-differ').value =  "";
                var tb = document.getElementById('tz-begin').value;
                var te = document.getElementById('tz-end').value;

                var tblen = tb.length;
                var telen = te.length;

                if((tblen == telen) && (tblen == 5)){

                var tbm = tomin(tb);
                var tem = tomin(te);
                if(tbm <= tem)
                    {
                        var tdiffer = tem - tbm;
                        document.getElementById('tz-differ').value = tdiffer;                      
                    };

                };

            };

             function getDifferTwo(){


                var tb = document.getElementById('tz-begin').value;
                var tdiffer = document.getElementById('tz-differ').value;

                var mymass = tb.split(":");
                var h = mymass[0] * 1;
                var m = mymass[1] * 1;

                var dt = new Date();
                dt.setHours(h);
                dt.setMinutes(m);

                var add = new Date(0, 0, 0, 0, Math.abs(dt.getTimezoneOffset()) + tdiffer, 0);
                dt.setTime(dt.getTime() + add.getTime());

                // var minutes = d.getMinutes();

                var t = checkTime(dt.getHours()) + ":" + checkTime(dt.getMinutes());
                if(isNaN(dt.getHours())){t = ""};
                if(isNaN(dt.getMinutes())){t = ""};
                document.getElementById('tz-end').value = t;

            };

            var mysettime = document.getElementById('tz-set').value;
            document.getElementById('tz-differ').value = mysettime;
            getDifferTwo();


            function checkTime(i)
            {
                if (i<10)
                    {
                        i="0" + i;
                    }
                return i;
            };