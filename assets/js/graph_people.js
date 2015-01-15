	function drawEmptyCanvas(bgSrc,canvasId){
		var ctx= document.getElementById(canvasId).getContext('2d');
		var width=document.getElementById(canvasId).width;
		var height=document.getElementById(canvasId).height;
		var text="Sorry, we can not have enough data to calculate this stastic, please try to help us loading more data by click the link below ";
		var bg_img=new Image();
		var maxWidth=width-60;
		var lineHeight=20;
		
		bg_img.onload = function() {
			ctx.drawImage(bg_img,0,0);
			ctx.font = "bold 16pt Arial";
			ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
			wrapText(ctx, text, (width-maxWidth)/2+1, height/2+1, maxWidth, lineHeight);
			ctx.fillStyle = "rgba(255, 255, 255, 0.9)";
			wrapText(ctx, text, (width-maxWidth)/2, height/2, maxWidth, lineHeight);
		};
		bg_img.src=bgSrc;
	
	}
	
	function drawGraph(id, dataUrl) {
		img_srcs[id] = dataUrl;
		count++;
		if (count == 6) {
		//draw here!!!
			draw(img_srcs,stat_friend_data,background_src,crown_src,callback,canvasId);
		}
	};
	
	String.prototype.format = function() {
		if (arguments.length == 0)
			return this;
		for (var s = this, i = 0; i < arguments.length; i++)
			s = s.replace(new RegExp("\\{" + i + "\\}", "g"), arguments[i]);
		return s;
	};

	function prepareData(data){
		var result=new Array([],[[],[],[]],[]);
		
		for(var i=0;i< data.length;i++){
			result[0].push(data[i]['fid']);
			result[1][0].push(data[i]['no_places']);
			result[1][1].push(data[i]['no_cities']);
			result[1][2].push(data[i]['no_countries']);
			result[2].push(data[i]['name']);
		}
		return result;
	}
	function preparePicURL(data){
		var pic_link = "http://graph.facebook.com/{0}/picture?width=70&height=70";
		var pic_big_link = "http://graph.facebook.com/{0}/picture?width=150&height=150";
		var result=new Array();
		for(var i=0;i<data.length;i++){
			result.push(pic_link.format(data[i]));
		}
		//add big profile link
		result.push(pic_big_link.format(data[0]));
		return result;
	}

	function convertLinks(links,callbackName) {
		for(var i=0;i<links.length;i++){
			var script = document.createElement("script");
			script.setAttribute("src", "http://src.sencha.io/data."+callbackName+"-" + i + "/" + links[i]);
			script.setAttribute("type", "text/javascript");
			document.head.appendChild(script);
		}
	}
	
	function draw(img_srcs,data, background_src, crown_src,callback,canvasId) {
		var ctx = document.getElementById(canvasId).getContext('2d');
		var graph = new BarGraph(ctx);
		//graph params settings
		graph.margin = 13;
		//defining barchart drawing area
		graph.profileUrl = img_srcs[5];
		graph.total_width = 700;
		graph.total_height = 462;
		graph.width = 440;
		graph.height = 290;
		graph.x = 90;
		graph.y = 30;
		graph.xAxisLabelArr = data[2];//names
		graph.callback = callback;
		graph.texts = ["Your friend " + data[2][0] + " really travels a lot! ", "Unbelievable, your friend " + data[2][0] + " has been to so many places!!", "Okay, next time get travel advices from your friend " + data[2][0]];
		graph.backgroundUrl=background_src;
		graph.crownSrc=crown_src;
		graph.values=data[1];//values
		graph.imgSrcs=img_srcs;
		graph.update();
	};
	


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
	  
function BarGraph(ctx) {
	var that = this;
	// Draw method updates the canvas with the current display
	var draw=function(arr,picArr,bg_src,profile_src){
		var bg_img=new Image();
		bg_img.onload = function() {
			ctx.drawImage(bg_img,0,0);
			drawProfile(arr,picArr,profile_src);
		};
		bg_img.src=bg_src;
	}
	
	var drawProfile=function(arr,picArr,profile_src){
		var top_margin=100;
		var left_margin=30;
		//draw text below profile_img
		var maxWidth = 170;
        var lineHeight = 18;
        var x = that.width+left_margin+20;
        var y = top_margin+180;
		var texts=that.texts;
		var text=texts[Math.floor((Math.random()*3))];
		ctx.font = "bold 10pt Arial";
		ctx.fillStyle = "rgba(255, 255, 255, .9)";
		 wrapText(ctx, text, x+1, y-1, maxWidth, lineHeight);
		//draw profile img
		var profile_img=new Image();
		
		profile_img.onload=function(){
			ctx.drawImage(profile_img, x , top_margin);
			var crownImg=new Image();
			crownImg.onload=function(){
				ctx.drawImage(crownImg, x-45 , top_margin-45);
			drawBar(arr,picArr);
			}
			crownImg.src=that.crownSrc;
		}
		profile_img.src=profile_src;
	}
	function prepareValues (a1,a2){
		var result=new Array();
		for(var i=0;i<a1.length;i++){
			var value=(a1[i]*3+a2[i]*1)/4;
			result.push(value);
		}
		return result;
	}
	var drawBar= function(values, picArr) {//draw country
        var arr=prepareValues(values[2],values[1]);
		var font = 11;
		var numOfBars = arr.length;
		var barWidth;
		var barHeight;
		var border = 0;
		var ratio;
		var maxBarHeight;
		var gradient;
		var largestValue;
		var graphAreaX = that.x;
		var graphAreaY = that.y;
		var graphAreaWidth = that.width;
		var graphAreaHeight = that.height;
		var i;
		var location = new Array();
		var profilePicHeight=that.picHeight;
		// Restrict the drawing area by minusing out label and profile pic height
		graphAreaHeight = graphAreaHeight-profilePicHeight;

		// Calculate dimensions of the bar
		barWidth = graphAreaWidth / numOfBars - 2*that.margin;
		maxBarHeight = graphAreaHeight ;

		// Determine the largest value in the bar array
		var largestValue = 0;
		for ( i = 0; i < arr.length; i += 1) {
			if (arr[i] > largestValue) {
				largestValue = arr[i];
			}
		}
	
		// For each bar
		for ( i = 0; i < arr.length; i += 1) {
			// Set the ratio of current bar compared to the maximum
			if (that.maxValue) {
				ratio = arr[i] / that.maxValue;
			} else {
				ratio = arr[i] / largestValue;
			}

			barHeight = ratio * maxBarHeight;

			// Turn on shadow
			//ctx.shadowOffsetX = 2;
			//ctx.shadowOffsetY = 2;
			//ctx.shadowBlur = 4;
			//ctx.shadowColor = "#FFFFFF";

			// Draw bar background
			//ctx.fillStyle = "#FFFFFF";
			//ctx.fillRect(that.y+that.margin + i * that.width / numOfBars, that.x+graphAreaHeight - barHeight , barWidth, barHeight);

			// Turn off shadow
			ctx.shadowOffsetX = 0;
			ctx.shadowOffsetY = 0;
			ctx.shadowBlur = 0;

			// Draw bar color if it is large enough to be visible
			if (barHeight >= border * 2) {
				// Create gradient
				gradient = ctx.createLinearGradient(0, 0, 0, graphAreaHeight);
				//gradient.addColorStop(1-ratio, that.colors[i % that.colors.length]);
				gradient.addColorStop(1, "#FFFFFF");

				ctx.fillStyle =  "rgba(255, 255, 255, .7)";
				// Fill rectangle with gradient
				if(barHeight!=0){
				ctx.fillRect(that.y+that.margin + i * that.width / numOfBars + border-2, that.x+graphAreaHeight - barHeight + border, barWidth - border * 2, barHeight - border * 2);
				}else{
				ctx.fillRect(that.y+that.margin + i * that.width / numOfBars + border-2, that.x+graphAreaHeight - barHeight + border, barWidth - border * 2, 2);	
				}
			}
			
			var valueFont=30;
			var textFont=12;
			var value_textMargin=16;
			var sectionHeight=valueFont+textFont+value_textMargin;
			
			if(arr[i]>0){
			// Write bar value
				ctx.font = "bold "+valueFont+"px Arial";
				ctx.textAlign = "center";
				// Use try / catch to stop IE 8 from going to error town
					ctx.fillStyle = "rgba(0, 0, 0, .7)";
					ctx.fillText(parseInt(values[2][i], 10), that.y+i * that.width / numOfBars + (that.width / numOfBars) / 2-2,that.x+graphAreaHeight - barHeight*0.5-valueFont-textFont-value_textMargin+30);
					ctx.font = "bold "+textFont+"px Arial";
					ctx.fillText("Countries", that.y+i * that.width / numOfBars + (that.width / numOfBars) / 2,that.x+graphAreaHeight - barHeight*0.5-valueFont-textFont+30);
					ctx.font = "bold "+valueFont+"px Arial";
					ctx.fillText(parseInt(values[1][i], 10), that.y+i * that.width / numOfBars + (that.width / numOfBars) / 2-2,that.x+graphAreaHeight - barHeight*0.5-textFont+30);
					ctx.font = "bold "+textFont+"px Arial";
					ctx.fillText("Cities", that.y+i * that.width / numOfBars + (that.width / numOfBars) / 2+1,that.x+graphAreaHeight - barHeight*0.5+value_textMargin-textFont+30);
				
			}else{
			
				ctx.font = "bold "+valueFont+"px Arial";
				ctx.textAlign = "center";
				// Use try / catch to stop IE 8 from going to error town
				ctx.fillStyle = "rgba(0, 0, 0, .7)";
				ctx.fillText(parseInt(values[2][i], 10), that.y+i * that.width / numOfBars + (that.width / numOfBars) / 2,that.x+graphAreaHeight - barHeight*0.5);
			}
			
			
			
			
			// Draw bar label if it exists
			
				// Use try / catch to stop IE 8 from going to error town
				ctx.fillStyle =  "rgba(255, 255, 255, .9)";
				ctx.font = "bold " + font + "px Arial";
				ctx.textAlign = "center";
				try {
					ctx.fillText(that.xAxisLabelArr[i], that.y+i * that.width / numOfBars + (that.width / numOfBars) / 2, that.total_height-font-30);
					location[i] = (i * that.width / numOfBars + (that.width / numOfBars) / 2 -10);
				} catch (ex) {
				}
			

		}


		var profileY=that.x+graphAreaHeight + border+18;
		var img1=new Image();
		img1.onload=function(){
		 var img2=new Image();
		 img2.onload=function (){
		  var img3=new Image();
		  img3.onload=function(){
		    var img4=new Image();
			img4.onload=function(){
			 var img5=new Image;
			 img5.onload=function(){
			    ctx.drawImage(img5, location[4],profileY,72,72);
			 };
			 img5.src = picArr[4];
			 ctx.drawImage(img4, location[3], profileY,72,72);
			};
			img4.src = picArr[3];
			ctx.drawImage(img3, location[2], profileY,72,72);
		  };
		  img3.src = picArr[2];
		  ctx.drawImage(img2, location[1], profileY,72,72);
		 };
		 img2.src = picArr[1];
		 ctx.drawImage(img1, location[0],  profileY,72,72);
		};
		img1.src = picArr[0];
		
	
	};

	// Public properties with defaults and methods
	//bar graph starting point
	this.x=0;
	this.y=0;
	//canvas size
	this.total_width;
	this.total_height;
	//bar graph part size
	this.width=0;
	this.height =0;
	//pic inside bargraph size
	this.picHeight=70;
	this.maxValue;
	this.margin = 0;
	this.xAxisLabelArr = [];
	this.values;
	this.backgroundUrl;
	this.profileUrl;
	this.callback;
	this.texts;
	this.imgSrcs;
	this.crownSrc;
	//draw function
	this.update = function() {
			draw(this.values, this.imgSrcs,this.backgroundUrl,this.profileUrl);
	}
}