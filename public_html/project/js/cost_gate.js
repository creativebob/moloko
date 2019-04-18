
            function proCount(aaa, minlim, maxlim) {

                if (maxlim == null){maxlim = 10000000;};
                if (minlim == null){minlim = 0;};

                id_input = aaa.getAttribute('id');
                myinputCount = document.getElementById(id_input).value;


                    if((myinputCount.length > 1) && (myinputCount.substring(0, 1)) == "0"){ // Зырим чтоб нули не попадались в начале числа.
                        myinputCount = myinputCount.substring(1, myinputCount.length);
                        document.getElementById(id_input).value = myinputCount;
                    };

                    StatValid = checkNumberFields(myinputCount);
                    if (StatValid == false){
                        myinputCount = myinputCount.substring(0, myinputCount.length - 1);
                        document.getElementById(id_input).value = myinputCount;
                    };

                    if (myinputCount > maxlim){
                        myinputCount = "";
                        document.getElementById(id_input).value = myinputCount;
                    }; 

                    if (myinputCount < minlim){
                        myinputCount = "";
                        document.getElementById(id_input).value = myinputCount;
                    };
            };

            function checkNumberFields_simp(n){  // Дробное число
                  var reg=/^(\d{1,6}|\d\.?|\d\.\d?|\d{1,6}\.\d?|\d\.\d{2}?|\d{1,6}\.\d{2}?)$/
                  if (!reg.test(n)) return false;
            };


            function checkNumberFields(n){ // Обычное целое число
                  var reg=/^\d+$/
                  if (!reg.test(n)) return false;
            };

            function proFocus(x) {
                ModelGate(x);
            };


            function ModelGate(x){
                var canvas = document.getElementById("myCanvas");
                if (canvas.getContext){
                    var context = canvas.getContext("2d");
                    context.clearRect(0, 0, 400, 350);

                    var ks = 0.8; var co = 0.5;

                    context.lineWidth=1;
                    context.strokeStyle = "#bbbbbb";

                    // Рисуем ближний контур гаража
                    // context.rect(100*ks+co, 150*ks+co, 400*ks+co, 250*ks+co)

                    context.beginPath();
                    context.moveTo(0*ks+co, 0*ks+co);
                    context.lineTo(400*ks+co, 0*ks+co);
                    context.lineTo(400*ks+co, 250*ks+co);
                    context.lineTo(0*ks+co, 250*ks+co);
                    context.lineTo(0*ks+co, 0*ks+co);

                    // Рисуем дальний контур гаража
                    // context.rect(150*ks+co, 200*ks+co, 300*ks+co, 170*ks+co)

                    context.moveTo(50*ks+co, 50*ks+co);
                    context.lineTo(350*ks+co, 50*ks+co);
                    context.lineTo(350*ks+co, 220*ks+co);
                    context.lineTo(50*ks+co, 220*ks+co);
                    context.lineTo(50*ks+co, 50*ks+co);

                    // Рисуем линии замыкающие углы
                    context.moveTo(0*ks, 0*ks);
                    context.lineTo(50*ks, 50*ks);

                    context.moveTo(400*ks, 0*ks);
                    context.lineTo(350*ks, 50*ks);

                    context.moveTo(400*ks, 250*ks);
                    context.lineTo(350*ks, 220*ks);

                    context.moveTo(0*ks, 250*ks);
                    context.lineTo(50*ks, 220*ks);

                    // Рисуем проем (двери)
                    // context.rect(210*ks+co,250*ks+co, 180*ks+co, 120*ks+co);

                    context.moveTo(110*ks+co, 100*ks+co);
                    context.lineTo(290*ks+co, 100*ks+co);
                    context.lineTo(290*ks+co, 220*ks+co);

                    context.moveTo(110*ks+co, 220*ks+co);
                    context.lineTo(110*ks+co, 100*ks+co);


                    context.stroke();



                    context.beginPath();
                    context.lineWidth=3;
                    context.strokeStyle = "#ff3333";
                    context.font = 'normal 14px arial';



                    if(x==1){
                        // Рисуем линии измерений ШИРИНА ПРОЕМА
                        context.moveTo(110*ks+co, 160*ks+co);
                        context.lineTo(290*ks+co, 160*ks+co);
                        context.stroke();


                        context.beginPath();
                        context.strokeStyle = "#666";
                        context.fillText("W", 175*ks+co+15, 140*ks+co+10);
                    };

                    if(x==2){
                        // Рисуем линии измерений ВЫСОТА ПРОЕМА
                        context.moveTo(200*ks+co, 100*ks+co);
                        context.lineTo(200*ks+co, 220*ks+co);
                        context.stroke();

                        context.beginPath();
                        context.strokeStyle = "#666";
                        context.fillText("H", 185*ks+co+15, 150*ks+co+10);
                    };

                    if(x==3){
                        // Рисуем линии измерений ЛЕВЫЙ ПРИСТЕНОК
                        context.moveTo(50*ks+co, 160*ks+co);
                        context.lineTo(110*ks+co, 160*ks+co);
                        context.stroke();

                        context.beginPath();
                        context.strokeStyle = "#666";
                        context.fillText("w1", 63*ks+co+8, 140*ks+co+10);
                    };

                    if(x==4){
                        // Рисуем линии измерений ПРАВЫЙ ПРИСТЕНОК
                        context.moveTo(290*ks+co, 160*ks+co);
                        context.lineTo(350*ks+co, 160*ks+co);
                        context.stroke();

                        context.beginPath();
                        context.strokeStyle = "#666";
                        context.fillText("w2", 280*ks+co+22, 140*ks+co+10);
                    };

                    if(x==5){
                        // Рисуем линии измерений ПРИТОЛОКА
                        context.moveTo(200*ks+co, 50*ks+co);
                        context.lineTo(200*ks+co, 100*ks+co);
                        context.stroke();

                        context.beginPath();
                        context.strokeStyle = "#666";
                        context.fillText("h", 185*ks+co+15, 75*ks+co+5);
                    };

                    if(x==6){
                        // Рисуем линии измерений ДЛИНА ГАРАЖА
                        context.moveTo(350*ks+co, 160*ks+co);
                        context.lineTo(400*ks+co, 170*ks+co);
                        context.stroke();

                        context.beginPath();
                        context.strokeStyle = "#666";
                        context.fillText("l", 337*ks+co+25, 145*ks+co+10);
                    };

                }
                else {
                    alert('Ваш браузер не поддерживает canvas')
                };
            };

        ModelGate(0);

        $(function() {
            $('.phone_field').mask('8(000) 000-00-00');
        });