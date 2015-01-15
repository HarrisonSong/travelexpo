
function show(canvasId,imageArray,hasBeenList,wantToList,cityList,countryList){
	var ctx = $('#'+canvasId)[0].getContext("2d");
	ctx.fillStyle = "yellow";
	var imgArray = new Array();
	for(var i = 0;i<imageArray.length;i++){
		imgArray[i] = new Image();
		imgArray[i].onload = (function(index) {
			return function(){
			ctx.drawImage(this, 30, 130 + index*300,400,260);
			}
		})(i);
		ctx.font="30px Arial"; 
		ctx.fillText(cityList[i] + " , " + countryList[i],450,200+300*i);
		ctx.fillStyle = "#00CCCC";
		ctx.fillText(hasBeenList[i],450,240+300*i);
		ctx.fillText(wantToList[i],450,280+300*i);
		ctx.fillStyle = "white";
		ctx.fillText(" of your friends has been there",490,240+300*i);
		ctx.fillText(" of your friends want to go",490,280+300*i);
		ctx.fillText("Popularity:",450,320+300*i);
		imgArray[i].src = imageArray[i];
	}	
}

function wrapText(context, text, x, y, maxWidth, lineHeight) {
        var words = text.split(" ");
        var line = "";

        for(var n = 0; n < words.length; n++) {
          var testLine = line + words[n] + " ";
          var metrics = context.measureText(testLine);
          var testWidth = metrics.width;
          if(testWidth > maxWidth) {
            context.fillText(line, x, y);
            line = words[n] + " ";
            y += lineHeight;
          }
          else {
            line = testLine;
          }
        }
        context.fillText(line, x, y);
      }

function drawStarRating(canvasId,starNum,hasHalf,x,y,starURL,halfURL){
	var ctx = $('#'+canvasId)[0].getContext("2d");
	var starArray = new Array();
	for(var i = 0;i<starNum;i++){
		starArray[i] = new Image();
		starArray[i].onload = (function(index){
			return function(){
				ctx.drawImage(this,x+index*40,y,40,40);
			}
		})(i);
		starArray[i].src = starURL;
	}
	var xPos = x+starNum*40;
	if(hasHalf){
		var halfStar = new Image();
		halfStar.onload = function(){
				ctx.drawImage(this,xPos,y,40,40);
		};
		halfStar.src = halfURL;
	}
}
