dojo.provide("dojox.date.HebrewNumerals");
dojo.experimental("dojox.date.HebrewNumerals");

//Conversion from "Hindi" numerals to Hebrew numerals and vice versa

(function(){  

	var _DIG=["א","ב","ג","ד","ה","ו","ז","ח","ט"];
	var	_TEN=["י","כ","ל","מ","נ","ס","ע","פ","צ"];
	var	_HUN=["ק","ר","ש","ת"];
	var	_REP=["יה","יו", "טו", "טז"];
	var _MONTHS =["א'","ב'","ג'","ד'","ה'","ו'","ז'","ח'","ט'","י'","י\"א","י\"ב","י\"ג"];		

	var	_GERESH=["'"];

	dojox.date.HebrewNumerals.getYearHebrewLetters = function(/*Number */ year){
		// summary: This function return year written in Hebrew numbers-letters, 
		//
		// example:
		// |		var date1 = new dojox.date.HebrewDate();
		// |
		// |		document.writeln(dojox.date.HebrewNumerals.getYearHebrewLetters(date1.getFullYear());	
		var str  = "", str2 = "";
	
		year = year%1000;
		
		var i=0, n=4,j=9;
		while(year){ 
			if(year >= n*100){
				str=str.concat(_HUN[n-1]);
				year -= n*100;
				continue;
			}else if(n > 1){
				n--;
				continue;
			}else if(year >= j*10){
				str=str.concat(_TEN[j-1]);
				year -= j*10;
			}else if (j >1){
				j--;
				continue;
			}else if(year > 0){
				str=str.concat(_DIG[year-1]);
				year=0;
			}		
		}
		
		
		var str1 = "";
		var ind = str.indexOf(_REP[0]);
	
		if(ind > -1){
			str = str1.concat(str.substr(str[0], ind), _REP[2], str.substr(str[ind+2], str.length-ind-2));
		}else if( ( ind=str.indexOf(_REP[1]) ) > -1){
			str = str1.concat(str.substr(str[0], ind), _REP[3], str.substr(str[ind+2], str.length-ind-2));
		}
	
		if(str.length > 1){
			var last = str.charAt(str.length - 1);
			str = str2.concat(str.substr(0, str.length-1), '"', last);
		}else{
			str = str.concat(_GERESH[0]);
		} 
		return str;	 
	};
	
	dojox.date.HebrewNumerals.parseYearHebrewLetters  = function(/*String hebrew year*/ year){
		// summary: This function return year in format number from  the year written in Hebrew numbers-letters
		//                   
		// example:
		// |		var date = new dojox.date.HebrewDate();
		// |        	date.setYear(dojox.date.HebrewNumerals.parseYearHebrewLetters('תשס"ח'));	
		// |		
	
		var nYear = 0, i=0, j=0;
		for(j=0; j < year.length; j++){
			for(i=1; i <= 5; i++){
				if(year.charAt(j) == _HUN[i-1]){
					nYear += 100*i;
					continue;
				}
			}
			for(i=1; i <= 9; i++){
				if(year.charAt(j) == _TEN[i-1]){
					nYear += 10*i;
					continue;
				}
			}
			for(i=1; i <= 9; i++){
				if(year.charAt(j) == _DIG[i-1]){
					nYear += i;
				}
			}						
		} 
		return  nYear+5000;
	};
	
	dojox.date.HebrewNumerals.getDayHebrewLetters =  function(day, /*bool ?*/ nogrsh){
		// summary: This function return date written in Hebrew numbers-letter,  can be in format א or א' (with geresh)
		//
		// example:
		// |		var date1 = new dojox.date.HebrewDate();
		// |
		// |		document.writeln(dojox.date.HebrewNumerals.getDayHebrewLetters(date1.getDay());
		var str = "";
		var j=3;
		while(day){ 
			if(day >= j*10){
				str=str.concat(_TEN[j-1]);
				day -= j*10;
			}else if (j >1){
				j--;
				continue;
			}else if(day > 0){
				str=str.concat(_DIG[day-1]);
				day=0;
			}		
		}
		var str1 = "";
		var ind = str.indexOf(_REP[0]);
	
		if(ind > -1){
			str = str1.concat(str.substr(str[0], ind), _REP[2], str.substr(str[ind+2], str.length-ind-2));
		}else if( ( ind=str.indexOf(_REP[1]) ) > -1){
			str = str1.concat(str.substr(str[0], ind), _REP[3], str.substr(str[ind+2], str.length-ind-2));
		}
		if(!nogrsh){
			var str2 = "";
			if(str.length > 1){
				var last = str.charAt(str.length - 1);
				str = str2.concat(str.substr(0, str.length-1), '"', last);
			}else{
				str = str.concat(_GERESH[0]);
			}
		}
	
		return str;	 		
	};
	
	dojox.date.HebrewNumerals.parseDayHebrewLetters =  function(/*String hebrew*/ day){
		// summary: This function return date in format number from  the date written in Hebrew numbers-letter
		//
		// example:
		// |		var date1 = new dojox.date.HebrewDate();
		// |
		// |		date1.setDate(dojox.date.HebrewNumerals.parseDayHebrewLetters('א'));	
		var nDay = 0, i=0;
		for (var j=0; j < day.length; j++){
			for(i=1; i <= 9; i++){
				if(day.charAt(j) == _TEN[i-1])
				{
					nDay += 10*i;
					continue;
				}
			}
			for(i=1; i <= 9; i++){
				if(day.charAt(j) == _DIG[i-1])
					nDay += i;
			}						
		} 
		//if (nDay > this.getDaysInHebrewMonth(_month, this._year)){
		//	nDay = this.getDaysInHebrewMonth(this._month, this._year);
		//}
		return nDay;		
	};
	
	dojox.date.HebrewNumerals.getMonthHebrewLetters =  function(monthNum, /* bool hebrew numbers ?*/ isNum, /*Number ?*/ year){
		// summary: This function return month written in Hebrew numerals
		//
		// example:
		// |		var date1 = new dojox.date.HebrewDate();
		// |
		// |		document.writeln(dojox.date.HebrewNumerals.getMonthHebrewLetters(date1.getMonth());
		return _MONTHS[monthNum];
	};	
	
	dojox.date.HebrewNumerals.parseMonthHebrewLetters = function(monthStr){
		// summary: This function return month in format number from  the month written in Hebrew  word  or numbers-letters
		//                   the return number is index in month name array, to use it for setMont, do correction for leap year
		// example:
		// |		var date = new dojox.date.HebrewDate();
		// |            var number = dojox.date.HebrewNumerals.parseMonthHebrewLetters("תמוז");
		// |		if ( !date.isLeapYear(date.getFullYear())  &&  number >5) {number--;}
		// |		date.setMonth(number);	
		// |		
			
		//month number from 0 to 12
		var monnum = dojox.date.HebrewNumerals.parseDayHebrewLetters(monthStr) - 1;

		if(monnum == -1){
			console.warn("The month name is incorrect , set 0"); // TODO: perhaps throw instead?
			monnum = 0;
		}
		return monnum;
	};
	
})();
