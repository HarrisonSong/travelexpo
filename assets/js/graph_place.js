
	function show(canvasId,percentageList,hasBeenList,cityList,countryList,crownSrc,starSrc,halfStarSrc,bgSrc){
	 var bgImg=new Image();
	 bgImg.onload=function(){
		
		var ctx = $("#"+canvasId)[0].getContext("2d");
		ctx.drawImage(this,0,0);
		var imgArray = new Array();
		for(var i = 0;i<percentageList.length;i++){
		/*imgArray[i] = new Image();
		imgArray[i].onload = (function(index) {
			return function(){
			ctx.drawImage(this, 50, 100 + index*115,130,80);
			}
		})(i);*/
		ctx.fillStyle = "#00CCCC";
		ctx.font="40px Arial"; 
		ctx.fillText(percentageList[i] + "%",50,160 + i*115);
		ctx.font="20px Arial"; 
		ctx.fillStyle = "white";
		ctx.fillText(" of your friends has been:",195,120+115*i);
		ctx.font = 'italic 25px Calibri bold';
		ctx.fillStyle = "#FF0066";
		ctx.fillText(cityList[i] + " , " + countryList[i],200,145+115*i);
		//.fillText(hasBeenList[i],200,145+115*i);
		ctx.font="20px Arial"; 
		ctx.fillStyle = "white";
		ctx.fillText("Popularity:",200,170+115*i);
		//imgArray[i].src = imageArray[i];
		if(percentageList[i] >= 40) drawStarRating("canvasPlace",5,false,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] <40 && percentageList[i] >=30) drawStarRating("canvasPlace",4,true,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] <30 && percentageList[i] >=25) drawStarRating("canvasPlace",4,false,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] <25 && percentageList[i] >=20) drawStarRating("canvasPlace",3,true,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] <20 && percentageList[i] >=15) drawStarRating("canvasPlace",3,false,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] <15 && percentageList[i] >=10) drawStarRating("canvasPlace",2,true,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] <10 && percentageList[i] >=5) drawStarRating("canvasPlace",2,false,300,155 + i*115,starSrc,halfStarSrc);
		else if(percentageList[i] < 5 && percentageList[i] > 0) drawStarRating("canvasPlace",1,false,300,155 + i*115,starSrc,halfStarSrc);
		else return;
		}
	 }
       bgImg.src=bgSrc;
	}
	
	function drawStarRating(canvasId,starNum,hasHalf,x,y,starSrc,halfStarSrc){
	var ctx = document.getElementById(canvasId).getContext("2d");
	var starArray = new Array();
	for(var i = 0;i<starNum;i++){
		starArray[i] = new Image();
		starArray[i].onload = (function(index){
			return function(){
				ctx.drawImage(this,x+index*20,y,20,20);
			}
		})(i);
		starArray[i].src = starSrc;
	}
	var xPos = x+starNum*20;
	if(hasHalf){
		var halfStar = new Image();
		halfStar.onload = function(){
				ctx.drawImage(this,xPos,y,20,20);
		};
		halfStar.src = halfStarSrc;
	}
}

	function preparePlaceData(data){
		var result = new Array([],[[],[]],[]);
		for(var i = 0; i<data.length;i++){
			result[0].push(data[i].percent);
			result[1][0].push(data[i].city);
			result[1][1].push(data[i].country);
			result[2].push(data[i].no_friends);
		}
		return result;
	}