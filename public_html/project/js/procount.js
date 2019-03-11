 function proCount(aaa,val,maxlim,minlim,x) {

  if (maxlim == null){maxlim = 10000000;};
  if (minlim == null){minlim = 0;};

  id_input = aaa.getAttribute('id');
  myinputCount = document.getElementById(id_input).value;

  //Смотрим какая валидация нам нужна:

                if ((val == "1") || (val == "2")){ // Если нужно просто целое число

                    if((val == "1") && (myinputCount.length > 1) && (myinputCount.substring(0, 1) == "0")){ // Зырим чтоб нули не попадались в начале числа.
                      myinputCount = myinputCount.substring(1, myinputCount.length);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    if((val == "2") && (myinputCount.length == 1) && (myinputCount.substring(0, 1) == "0")){ // Зырим чтоб нули не попадались в начале числа.
                      myinputCount = myinputCount.substring(1, myinputCount.length);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    StatValid = checkNumberFields(myinputCount);
                    if (StatValid == false){
                    	myinputCount = myinputCount.substring(0, myinputCount.length - 1);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    if (myinputCount > maxlim){
                      myinputCount = maxlim;
                      document.getElementById(id_input).value = myinputCount;
                    }; 

                    if (myinputCount < minlim){
                      myinputCount = minlim;
                      document.getElementById(id_input).value = myinputCount;
                    };

                  };

                if ((val == "3") || (val == "4")){ // Если нужен процент с двумя знаками после точки и не больше 100
                    if((myinputCount.length > 1) && (myinputCount.substring(0, 1) == "0") && (myinputCount.substring(0, 2) != "0.")){ // Зырим чтоб нули не попадались в начале числа.
                      myinputCount = myinputCount.substring(1, myinputCount.length);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    StatValid = checkNumberFields_3(myinputCount);
                    if (StatValid == false){
                    	myinputCount = myinputCount.substring(0, myinputCount.length - 1);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    if (myinputCount > maxlim){
                      myinputCount = maxlim;
                      document.getElementById(id_input).value = myinputCount;
                    }; 

                    if (myinputCount < minlim){
                      myinputCount = minlim;
                      document.getElementById(id_input).value = myinputCount;
                    };

                  };

                if (val == "5"){ // Обычное дробное число

                    if((myinputCount.length > 1) && (myinputCount.substring(0, 1) == "0") && (myinputCount.substring(0, 2) != "0.")){ // Зырим чтоб нули не попадались в начале числа.
                      myinputCount = myinputCount.substring(1, myinputCount.length);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    StatValid = checkNumberFields_simp(myinputCount);
                    if (StatValid == false){
                      myinputCount = myinputCount.substring(0, myinputCount.length - 1);
                      document.getElementById(id_input).value = myinputCount;
                    };

                    if (myinputCount > maxlim){
                      myinputCount = maxlim;
                      document.getElementById(id_input).value = myinputCount;
                    }; 

                    if (myinputCount < minlim){
                      myinputCount = minlim;
                      document.getElementById(id_input).value = myinputCount;
                    };
                  };

                };


            function checkNumberFields(n){ // Обычное целое число
              var reg=/^\d+$/
              if (!reg.test(n)) return false;
            };

            function checkNumberFields_3(n){  // Процент с двумя знаками после точки
              var reg=/^(\d{1,3}|\d\.?|\d\.\d?|\d{1,3}\.\d?|\d\.\d{2}?|\d{1,3}\.\d{2}?)$/
              if (!reg.test(n)) return false;
            };

            function checkNumberFields_simp(n){  // Дробное число
              var reg=/^(\d{1,6}|\d\.?|\d\.\d?|\d{1,6}\.\d?|\d\.\d{2}?|\d{1,6}\.\d{2}?)$/
              if (!reg.test(n)) return false;
            };